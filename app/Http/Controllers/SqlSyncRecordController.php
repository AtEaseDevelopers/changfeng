<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\SqlSyncRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
class SqlSyncRecordController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'target_id'   => 'required',
            'action'      => 'required|string',
            'target_name' => 'required|string',
            'details'     => 'nullable|string',
            'status'      => 'required|in:pending,success,failed,expired',
            'remark'      => 'nullable|string',
            'response'    => 'nullable|array',
        ]);

        $data = $request->all();

        if ($validator->fails()) {
            // Capture full input as remark
            $data['remark'] = json_encode($request->all(), JSON_UNESCAPED_UNICODE);

            // Capture only validation errors in response
            $data['response'] = $validator->errors()->messages();

            // Force failed status
            $data['status'] = 'failed';
        }

        // Always save response JSON to be traced in orders
        $sqlSyncRespond = $data['response'] ?? ['Empty ' . env('APP_NAME') .' Respond'];

        // Process SQL Sync Record
        $record = SqlSyncRecord::logSyncResult($data);

        $response = $sqlSyncRespond[$data['target_id']] ?? [];
        $isSuccess = ($data['status'] ?? '') === 'success'
            && ($response['status'] ?? '') === 'success';

        $update = [
            'sql_sync_status'  => strtoupper($response['status'] ?? 'FAILED'),
            'sql_sync_respond' => json_encode($sqlSyncRespond, JSON_UNESCAPED_UNICODE),
            'updated_at'       => now(),
        ];

        if ($isSuccess) {
            //$update['status'] = 'completed';
            $update['sql_sync_status'] = 'SUCCESS';
            $update['invoiceno']  = $data['target_name'] ?? null;
        }

        DB::table('invoices')
            ->where('id', $data['target_id'])
            ->update($update);

        return response()->json([
            'success' => true,
            'record' => $record
        ], 201);
    }


    public function index()
    {
        // Get pending sync records only
        $records = SqlSyncRecord::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(env('SQL_SYNC_RECORD_LIMIT')) // optional: limit only pending
            ->get();

        if ($records->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No pending records found',
                'data' => []
            ]);
        }

        // Extract order IDs from queued jobs
        $orderIds = $records->pluck('target_id')->filter()->unique()->values()->toArray();

        // Prepare actual order data using your helper
        $ordersData = Invoice::prepareSyncInvoices($orderIds);

        // Combine the two datasets into a single API response
        $responseData = $records->map(function ($record) use ($ordersData) {
            $orderId = $record->target_id;

            return [
                'record_id' => $record->id,
                'action' => $record->action,
                'status' => $record->status,
                'created_at' => $record->created_at,
                'order' => $ordersData[$orderId] ?? null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'pending_count' => count($responseData),
            'data' => $responseData,
        ]);
    }

}
