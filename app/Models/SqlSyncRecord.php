<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SqlSyncRecord extends Model
{
    protected $table = 'sql_sync_records';

    protected $fillable = [
        'target_id',
        'action',
        'target_name',
        'details',
        'response',
        'status',
        'remark',
    ];

    protected $casts = [
        'details' => 'array',
        'response' => 'array',
    ];

    /**
     * Expire old active sync jobs for the same target & action
     */
    public static function expireOld($targetId, $action, $targetName)
    {
        return self::where('target_id', $targetId)
            ->where('action', $action)
            ->where('target_name', $targetName)
            ->where('status', 'pending')
            ->update([
                'status' => 'expired',
                'remark' => 'Replaced by new sync request',
            ]);
    }

    /**
     * Create pending sync job after expiring old ones
     */
    public static function queue($data)
    {
        self::expireOld(
            $data['target_id'],
            $data['action'],
            $data['target_name']
        );

        return self::create(array_merge($data, [
            'status' => 'pending',
            'remark' => 'Waiting to sync queue execution',
        ]));
    }

    public static function logSyncResult(array $data)
    {
        $query = self::where('target_id', $data['target_id'])
            ->where('action', $data['action'])
            ->where('target_name', $data['target_name'])
            ->latest('id');

        $record = $query->first();

        if ($record) {
            // Update latest record only
            $record->update([
                'status'   => $data['status'],
                'details'  => $data['details'] ?? $record->details,
                'remark'   => $data['remark'] ?? $record->remark,
                'response' => $data['response'] ?? $record->response,
            ]);
        } else {
            // Create new if not exist
            $record = self::create([
                'target_id'   => $data['target_id'],
                'action'      => $data['action'],
                'target_name' => $data['target_name'],
                'details'     => $data['details'] ?? [],
                'status'      => $data['status'],
                'remark'      => $data['remark'] ?? null,
                'response'    => $data['response'] ?? null,
            ]);
        }

        return $record;
    }

}
