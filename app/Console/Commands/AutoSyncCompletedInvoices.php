<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\InvoiceController; 
use Illuminate\Support\Facades\App;  
use Illuminate\Http\Request;  
use App\Models\LogAction;  
use App\Models\Invoice;

class AutoSyncCompletedInvoices extends Command
{
    protected $signature = 'sync:completed-invoices';

    protected $description = 'Syncs completed invoices to SQL records';

    public function handle()
    {
        echo "Starting Getting Invoices ... ";
        $logData = [
            'action_by'     => 'System : ' . now()->timestamp,  
            'action_name'   => 'Sync Completed Invoices',
            'action_ref_no' => 'Invoice IDs : ' . $this->getCompletedInvoiceIds(),  
            'request'       => [
                'invoices_id' => $this->getCompletedInvoiceIds(), 
            ],
            'remark'        => 'Sync operation started',
        ];

        $log = LogAction::log($logData);

        try {
            $invoiceController = App::make(InvoiceController::class);

            $request = new Request([
                'invoices_id' => $this->getCompletedInvoiceIds(),
            ]);

            $response = $invoiceController->sync_invoice($request);

            $logData['respond'] = $response->getData();
            $logData['remark']  = 'Sync operation completed';

            LogAction::updateLogResponse($log->id, $logData['action_ref_no'], $logData['respond'], $logData['remark']);

            $this->info('Sync completed successfully.');
            echo "End Getting Invoices ... ";

        } catch (\Exception $e) {
            LogAction::updateLogResponse($log->id, $logData['action_ref_no'], null, 'Error: ' . $e->getMessage());
            $this->error('Sync failed: ' . $e->getMessage());
        }
    }

    private function getCompletedInvoiceIds()
    {
        $completedInvoices = Invoice::where('status', '1')->where('sql_sync_status', 'PENDING')->get();
        return $completedInvoices->pluck('id')->implode(',');
    }
}
