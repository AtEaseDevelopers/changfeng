<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAction extends Model
{
    use HasFactory;

    protected $table = 'log_action';

    protected $fillable = [
        'action_by',
        'action_name',
        'action_ref_no',
        'request',
        'headers',
        'body',
        'respond',
        'remark',
    ];

    public static function log(array $data)
    {
        return self::create([
            'action_by'     => $data['action_by'] ?? auth()->id(),
            'action_name'   => $data['action_name'] ?? null,
            'action_ref_no' => $data['action_ref_no'] ?? null,
            'request'       => isset($data['request']) ? json_encode($data['request']) : null,
            'headers'       => isset($data['headers']) ? json_encode($data['headers']) : null,
            'body'          => isset($data['body']) ? json_encode($data['body']) : null,
            'respond'       => isset($data['respond']) ? json_encode($data['respond']) : null,
            'remark'        => $data['remark'] ?? null,
        ]);
    }

    public static function updateLogResponse(int $id, $action_ref_no = null, $response = null, $remark = null): bool
    {
        return self::where('id', $id)->update([
            'respond' => isset($response) ? json_encode($response) : null,
            'action_ref_no' => $action_ref_no,
            'remark'  => $remark,
            'updated_at' => now(),
        ]);
    }

}
