<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'invoices';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public const STATUS_SYNCED_TO_XERO = 1;
    public const STATUS_VOIDED = 2;

    public $fillable = [
        'invoiceno',
        'date',
        'customer_id',
        'driver_id',
        'kelindan_id',
        'agent_id',
        'supervisor_id',
        'paymentterm',
        'status',
        'remark',
        'chequeno'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'invoiceno' => 'string',
        'date' => 'datetime:d-m-Y H:i:s',
        'customer_id' => 'integer',
        'driver_id' => 'integer',
        'kelindan_id' => 'integer',
        'agent_id' => 'integer',
        'supervisor_id' => 'integer',
        'paymentterm' => 'integer',
        'status' => 'integer',
        'remark' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'invoiceno' => 'nullable|string|max:255|string|max:255',
        'date' => 'required',
        'customer_id' => 'required',
        'paymentterm' => 'required',
        'status' => 'required',
        'remark' => 'nullable|string|max:255|string|max:255',
        'created_at' => 'nullable|nullable',
        'updated_at' => 'nullable|nullable'
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class, 'customer_id', 'id');
    }

    public function driver()
    {
        return $this->belongsTo(\App\Models\Driver::class, 'driver_id', 'id');
    }

    public function kelindan()
    {
        return $this->belongsTo(\App\Models\Kelindan::class, 'kelindan_id', 'id');
    }

    public function agent()
    {
        return $this->belongsTo(\App\Models\Agent::class, 'agent_id', 'id');
    }

    public function supervisor()
    {
        return $this->belongsTo(\App\Models\Supervisor::class, 'supervisor_id', 'id');
    }

    public function invoicedetail()
    {
        return $this->hasMany(\App\Models\InvoiceDetail::class);
    }

    public function invoicepayment()
    {
        return $this->hasMany(\App\Models\InvoicePayment::class);
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }

    public function updateOrderStatus($order, $status)
    {
        $order->status = $status;
        $order->update();
        return true;
    }
    
    public static function getInvoicesWithCustomer(array $invoiceIds)
    {
        return DB::table('invoices')
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->whereIn('invoices.id', $invoiceIds)
            ->select(
                'invoices.*',
                'customers.company as customer_name',
                'customers.code as sql_customer_code',
                'customers.address as billing_address',
                'customers.phone as customer_phone',
            )
            ->get();
    }

    public static function getInvoiceDetails(array $invoiceIds)
    {
        return DB::table('invoice_details')
            ->join('products', 'invoice_details.product_id', '=', 'products.id')
            ->whereIn('invoice_details.invoice_id', $invoiceIds)
            ->select(
                'invoice_details.invoice_id',
                'invoice_details.product_id',
                'invoice_details.quantity',
                'invoice_details.price as unit_price',
                'invoice_details.totalprice as amount',
                'invoice_details.remark',
                'products.name as product_name',
                'products.sku as product_sku',
                'products.code as product_code',
                'products.uom as uom_name'
            )
            ->get()
            ->groupBy('invoice_id');
    }

    public static function prepareSyncInvoices(array $invoiceIds)
    {
        $invoices = self::getInvoicesWithCustomer($invoiceIds);

        $invoiceIdList = $invoices->pluck('id')->filter()->unique()->all();
        $detailsMap = self::getInvoiceDetails($invoiceIdList);

        return $invoices->map(function ($inv) use ($detailsMap) {
            return [
                'id'                => $inv->id,
                'do_no'             => $inv->invoiceno,
                'do_date'           => $inv->date,
                'attn_name'         => $inv->customer_name,
                'attn_contact'      => $inv->customer_phone,
                'billing_address'   => $inv->billing_address,
                'payment_method'    => '',//$inv->payment_method,
                'sql_sync_status'   => $inv->sql_sync_status,
                'sql_sync_respond'  => $inv->sql_sync_respond,
                'user_name'         => '',//$inv->user_name,
                'user_email'        => '',//$inv->user_email,
                'sql_customer_code' => $inv->sql_customer_code,
                'status'            => $inv->status,
                'cart_id'           => '',//$inv->cart_id,
                'items'             => $detailsMap[$inv->id] ?? [],
            ];
        })->keyBy('id');
    }



}
