<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Config extends Model
{
    use HasFactory;

    protected $table = 'config';

    protected $fillable = [
        'key',
        'value',
        'status'
    ];

    public $timestamps = true; // enables created_at and updated_at

    public static function addConfig($key, $value, $status = 1)
    {
        return self::create([
            'key'    => $key,
            'value'  => $value,
            'status' => $status
        ]);
    }

    public static function updateConfig($key, $value)
    {
        $config = self::where('key', $key)->first();
        if ($config != null) {
            $config->update([
                'value' => $value
            ]);
            return true;
        }
        return false;
    }
}
