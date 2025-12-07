<?php

namespace App\Http\Controllers;

use App\DataTables\InvoicePaymentDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateInvoicePaymentRequest;
use App\Http\Requests\UpdateInvoicePaymentRequest;
use App\Repositories\InvoicePaymentRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;
use Illuminate\Support\Facades\Crypt;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;
use Exception;
use App\Models\Customer;

class InvoicePaymentController extends AppBaseController
{
    /** @var InvoicePaymentRepository $invoicePaymentRepository*/
    private $invoicePaymentRepository;

    public function __construct(InvoicePaymentRepository $invoicePaymentRepo)
    {
        $this->invoicePaymentRepository = $invoicePaymentRepo;
    }

    /**
     * Display a listing of the InvoicePayment.
     *
     * @param InvoicePaymentDataTable $invoicePaymentDataTable
     *
     * @return Response
     */
    public function index(Request $req, InvoicePaymentDataTable $invoicePaymentDataTable)
    {
        $this->upsertDetail(Session::get('is_store'), $req);

        return $invoicePaymentDataTable->render('invoice_payments.index');
    }

    /**
     * Show the form for creating a new InvoicePayment.
     *
     * @return Response
     */
    public function create()
    {
        return view('invoice_payments.create');
    }

    /**
     * Store a newly created InvoicePayment in storage.
     *
     * @param CreateInvoicePaymentRequest $request
     *
     * @return Response
     */
    public function store(CreateInvoicePaymentRequest $request)
    {
        $input = $request->all();
        
        if($input['type'] == 1 && !isset($input['status'])){
            $input['status'] = 1;
            $input['approve_by'] = Auth::user()->email;
            $input['approve_at'] = gmdate("Y-m-d H:i:s");
        }

        if($input['type'] == 2 && !isset($input['status'])){
            $input['status'] = 0;
            $input['approve_by'] = null;
            $input['approve_at'] = null;
        }

        if(isset($input['status'])){
            if($input['status'] == 1){
                $input['approve_by'] = Auth::user()->email;
                $input['approve_at'] = gmdate("Y-m-d H:i:s");
            }else{
                $input['approve_by'] = null;
                $input['approve_at'] = null;
            }
        }

        if($request->file('attachment') != null){
            $path = 'assets/img/invoicepayment/'.uniqid();
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            $input['attachment'] = $request->file('attachment')->store($path);
        }

        $input['user_id'] = Auth::user()->id;
        
        Session::put('is_store', true);
        Session::put('invoice_payment_data', $input);

        $res = $this->upsertDetail(true, $request);
        if ($res != null) {
            return $res;
        }

        return redirect(route('invoicePayments.index'));
    }

    /**
     * Display the specified InvoicePayment.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $invoicePayment = $this->invoicePaymentRepository->find($id);

        if (empty($invoicePayment)) {
            Flash::error(__('invoice_payments.payment_not_found'));

            return redirect(route('invoicePayments.index'));
        }

        return view('invoice_payments.show')->with('invoicePayment', $invoicePayment);
    }

    /**
     * Show the form for editing the specified InvoicePayment.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $invoicePayment = $this->invoicePaymentRepository->find($id);

        if (empty($invoicePayment)) {
            Flash::error(__('invoice_payments.payment_not_found'));

            return redirect(route('invoicePayments.index'));
        }

        // if($invoicePayment->status == 1){
        //     Flash::error('Cannot edit completed Payment!');

        //     return redirect(route('invoicePayments.index'));
        // }

        return view('invoice_payments.edit')->with('invoicePayment', $invoicePayment);
    }

    /**
     * Update the specified InvoicePayment in storage.
     *
     * @param int $id
     * @param UpdateInvoicePaymentRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateInvoicePaymentRequest $request)
    {
        $id = Crypt::decrypt($id);
        $invoicePayment = $this->invoicePaymentRepository->find($id);

        if (empty($invoicePayment)) {
            Flash::error(__('invoice_payments.payment_not_found'));

            return redirect(route('invoicePayments.index'));
        }

        $input = $request->all();

        if($input['type'] == 1 && !isset($input['status'])){
            $input['status'] = 1;
            $input['approve_by'] = Auth::user()->email;
            $input['approve_at'] = gmdate("Y-m-d H:i:s");
        }

        if($input['type'] == 2 && !isset($input['status'])){
            $input['status'] = 0;
            $input['approve_by'] = null;
            $input['approve_at'] = null;
        }

        if(isset($input['status'])){
            if($input['status'] == 1){
                $input['approve_by'] = Auth::user()->email;
                $input['approve_at'] = gmdate("Y-m-d H:i:s");
            }else{
                $input['approve_by'] = null;
                $input['approve_at'] = null;
            }
        }

        if($request->file('attachment') != null){
            $path = 'assets/img/invoicepayment/'.uniqid();
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            $input['attachment'] = $request->file('attachment')->store($path);
        }
        
        $input['edit_id'] = $id;
        
        Session::put('is_store', false);
        Session::put('invoice_payment_data', $input);

        $res = $this->upsertDetail(false, $request);
        if ($res != null) {
            return $res;
        }

        return redirect(route('invoicePayments.index'));
    }

    /**
     * Remove the specified InvoicePayment from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        $invoicePayment = $this->invoicePaymentRepository->find($id);

        if (empty($invoicePayment)) {
            Flash::error(__('invoice_payments.payment_not_found'));

            return redirect(route('invoicePayments.index'));
        }

        if($invoicePayment->status == 1){
            Flash::error(__('invoice_payments.cannot_delete_completed_payment'));

            return redirect(route('invoicePayments.index'));
        }

        $this->invoicePaymentRepository->delete($id);

        Flash::success(__('invoice_payments.payment_deleted_successfully'));

        return redirect(route('invoicePayments.index'));
    }
    
    public function massupdatestatus(Request $request)
    {
        $data = $request->all();
        $ids = $data['ids'];
        $status = $data['status'];
        if($status == 1){
            $count = InvoicePayment::whereIn('id',$ids)->update(['status'=>$status,'approve_by'=>Auth::user()->email,'approve_at'=>gmdate("Y-m-d H:i:s")]);
        }else{
            $count = InvoicePayment::whereIn('id',$ids)->update(['status'=>$status,'approve_by'=>null,'approve_at'=>null]);
        }
    
        return $count;
    }
    
    public function getpayment($id)
    {
        $id = Crypt::decrypt($id);
        $invoicePayment = $this->invoicePaymentRepository->find($id);

        if (empty($invoicePayment)) {
            return response()->json(['status' => false, 'message' => 'Payment not found!']);
        }

        if($invoicePayment->status == 1){
            return response()->json(['status' => false, 'message' => 'Payment had been approved!']);
        }

        return response()->json(['status' => true, 'message' => 'Payment found!', 'data' => $invoicePayment]);
    
    }
    
    public function updatepayment(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $invoicePayment = $this->invoicePaymentRepository->find($id);

        if (empty($invoicePayment)) {
            Flash::error(__('invoice_payments.payment_not_found'));
            return redirect(route('invoicePayments.index'));
        }

        if($invoicePayment->status != 0){
            Flash::error(__('invoice_payments.payment_had_been_completed'));

            return redirect(route('invoicePayments.index'));
        }

        $input = $request->all();
        
        if($input['status'] == 1){
            $invoicePayment->approve_by = Auth::user()->email;
            $invoicePayment->approve_at = gmdate("Y-m-d H:i:s");
        }
        $invoicePayment->status = $input['status'];
        $invoicePayment->remark = $input['remark'];
        $invoicePayment->save();

        Flash::success(__('invoice_payments.payment_updated_successfully'));

        return redirect(route('invoicePayments.index'));
    }
    
    public function getinvoice($id)
    {
        $invoice = Invoice::with('invoicedetail')->where('id',$id)->first();

        if (empty($invoice)) {
            return response()->json(['status' => false, 'message' => 'Invoice not found!']);
        }

        return response()->json(['status' => true, 'message' => 'Invoice found!', 'data' => $invoice]);
    
    }

    public function print()
    {
        return view('invoice_payments.print');    
    }

    public function getReceiptViewPDF($id,$function)
    {
        $id = Crypt::decrypt($id);
        $invoice = InvoicePayment::where('id',$id)
        ->with('customer')
        ->first();

        if (empty($invoice)) {
            abort('404');
        }

        $min = 450;
        $each = 23;

        $creditQuery = "
            SELECT 
                (COALESCE(total_invoiced.totalprice, 0) - COALESCE(paymentsummary.amount, 0)) as credit
            FROM 
                customers
            LEFT JOIN (
                SELECT 
                    customer_id, 
                    SUM(invoice_details.totalprice) AS totalprice 
                FROM 
                    invoices 
                    LEFT JOIN invoice_details ON invoices.id = invoice_details.invoice_id 
                WHERE 
                    invoices.status = 1
                    AND invoices.updated_at <= ?
                GROUP BY 
                    customer_id
            ) AS total_invoiced ON customers.id = total_invoiced.customer_id 
            LEFT JOIN (
                SELECT 
                    customer_id, 
                    SUM(COALESCE(amount, 0)) AS amount 
                FROM 
                    invoice_payments 
                WHERE 
                    status = 1
                    AND updated_at <= ?
                GROUP BY 
                    customer_id
            ) AS paymentsummary ON customers.id = paymentsummary.customer_id 
            WHERE 
                customers.id = ?
            GROUP BY 
                customers.id, total_invoiced.totalprice, paymentsummary.amount
        ";

        $creditResult = DB::select($creditQuery, [
            $invoice->updated_at,
            $invoice->updated_at,
            $invoice->customer_id
        ]);
    
        $invoice->newcredit = !empty($creditResult) ? round($creditResult[0]->credit, 2) : 0;
        $invoice->customer->groupcompany = DB::table('companies')
        ->where('companies.group_id',explode(',',$invoice->customer->group)[0])
        ->select('companies.*')
        ->first() ?? null;
        try{
            $pdf = Pdf::loadView('invoice_payments.print', array(
                'invoice' => $invoice
            ));

            if($function == 'download'){
                return $pdf->setPaper(array(0, 0, 300, $min), 'portrait')->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true])->download('download.pdf');
            }elseif($function == 'view'){
                return $pdf->setPaper(array(0, 0, 300, $min), 'portrait')->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true])->stream('view.pdf');
            }
        }
        catch(Exception $e){
            abort(404);
        }

    }

    private function upsertDetail($is_store, Request $req) {
        $input = Session::get('invoice_payment_data');
        if ($input == null) {
            return null;
        }

        $invoice = Invoice::where('id', $input['invoice_id'])->first();

        DB::beginTransaction();

        if ($is_store == true) {
            $invoicePayment = $this->invoicePaymentRepository->create($input);
        } else {
            $invoicePayment = $this->invoicePaymentRepository->update($input, $input['edit_id']);
        }

        // Create credit note in Xero if payment term is credit
        $xero_has_err = false;
        if ($invoice != null && $invoice->paymentterm == 2) {
            try {
                $redirect_uri = config('app.url') . '/invoicePayments';
                $xero = new XeroController($redirect_uri);
    
                // Get Xero's access token
                if ($req->has('code')) {
                    $res = $xero->getToken($req->code);
                    if (!$res->ok()) {
                        throw new Exception('Failed to get xero access token.');
                    }
                }
                // Xero auth
                $res = $xero->auth();
                if ($res !== true) {
                    return $res;
                }
                // Get contact
                $customer_name = Customer::where('id', $invoice->customer_id)->value('company');
                
                $res = $xero->getContact($customer_name); // Get contact
                $payload = $res->object();

                if (!$res->ok()) {
                    throw new Exception('Failed to get xero contact.');
                } elseif ($res->ok() && isset($payload->Contacts) && count($payload->Contacts) <= 0) { // Create contact in Xero
                    $res = $xero->createContact($customer_name);
                    if (!$res->ok()) {
                        throw new Exception('Failed to create contact for ' . $customer_name);
                    }
                    $payload = $res->object();
                }
                // Create credit note
                $items = [
                    'Quantity' => 1,
                    'UnitAmount' => $input['amount'], 
                    'Description' => $input['remark'] ?? $invoice->invoiceno
                ];
                $res = $xero->createCreditNote(false, $payload->Contacts[0], $items, 'IP' . ($is_store == true ? $invoicePayment->id : $input['edit_id']));
                if (!$res->ok()) {
                    throw new Exception('Failed to create credit note.');
                }
            } catch (\Throwable $th) {
                DB::rollback();
                report($th);

                $xero_has_err = true;
                Flash::error(__('invoice_payments.something_went_wrong'));

            }
        }
        
        if (!$xero_has_err) {
            if ($is_store == true) {
                Flash::success(__('invoice_payments.payment_saved_successfully'));
            } else {
                Flash::success(__('invoice_payments.payment_updated_successfully'));
            }

            DB::commit();
        }
        
        Session::forget('invoice_payment_data');
    }
}
