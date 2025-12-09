<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Helper extends Model
{
    // public static function member_url($route="") {
    //     return config('app.url')."/".$route;
    // }

    // public static function admin_url($route="") {
    //     return config('app.admin_url')."/".$route;
    // }

    // public static function query_params($query=[]) {
    //     return "?".http_build_query($query);
    // }

    // public static function generateRandomString($length = 30, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
    //     $randomString = '';
    
    //     for ($i = 0; $i < $length; $i++) {
    //         $randomString .= $characters[rand(0, strlen($characters) - 1)];
    //     }
    
    //     return $randomString;
    // }

    // public static function areaList() {
    //     return [
    //         "Alam", "Ayer Itam", "Bagan Serai", "Batu Kawan", "Bayan Lepas", "Bedong", "Bertam", "Bukit Mertajam", "Bukit Minyak", "Bukit Tengah", "Butterworth", "Gelugor", "GeorgeTown", "Gurun", "Jawi", "Jelutong", "Juru", "Kota Permai", "Kuala Kurau", "Kuala Muda", "Kulim", "Nibong Tebal", "Padang Serai", "Pantai Remis", "Parit Buntar", "Perai", "Selama", "Serdang", "SG Ara", "Simpang Ampat", "Sungai Petani", "Tambun", "Tanjung Tokong", "Tasek Gelugor"
    //     ];
    // }

    public static function sql_sync_status_badge($status = null, $message = null)
    {
        // Define possible status → label/color mappings
        $statusMap = [
            'SUCCESS' => ['label' => 'Success', 'color' => 'success'],
            'ERROR'   => ['label' => 'Error', 'color' => 'danger'],
            'FAILED'  => ['label' => 'Failed', 'color' => 'warning'],
            'DELETE'  => ['label' => 'Deleted', 'color' => 'info'],
        ];

        // If unknown status, fallback to default
        $label = $statusMap[$status]['label'] ?? ucfirst(strtolower($status));
        $color = $statusMap[$status]['color'] ?? 'secondary';

        // Escape tooltip text to avoid HTML/JS injection
        $tooltip = htmlspecialchars($message ?? 'No message available', ENT_QUOTES, 'UTF-8');

        // Return a Bootstrap badge with tooltip
        return sprintf(
            '<span class="badge bg-%s" data-bs-toggle="tooltip" title="%s">%s</span>',
            $color,
            $tooltip,
            e($label)
        );
    }

    /**
     * Safely retrieve a value (supports arrays, objects, or direct values).
     *
     * @param mixed $source Array, object, or value.
     * @param string|int|null $key Key or property name (if array/object).
     * @param mixed $default Default value if missing/invalid.
     * @param bool $trim Whether to trim string values.
     * @param bool $allow_zero Whether numeric 0 counts as valid.
     * @return mixed
     */
    public static function safe_get($source, $key = null, $default = '', $trim = true, $allow_zero = true)
    {
        // Step 1: Determine the value to check
        if (is_array($source) && $key !== null) {
            $value = isset($source[$key]) ? $source[$key] : null;
        } elseif (is_object($source) && $key !== null) {
            $value = isset($source->$key) ? $source->$key : null;
        } else {
            $value = $source;
        }

        // Step 2: Null or unset
        if (!isset($value) || is_null($value)) {
            return $default;
        }

        // Step 3: Trim strings
        if (is_string($value) && $trim) {
            $value = trim($value);
        }

        // Step 4: Empty values (string, array, or false)
        if ($value === '' || $value === [] || $value === false) {
            return $default;
        }

        // Step 5: Numeric zero handling
        if (!$allow_zero && is_numeric($value) && (float)$value == 0) {
            return $default;
        }

        return $value;
    }
}