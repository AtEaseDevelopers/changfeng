<!-- Invoiceno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('invoiceno', __('invoices.invoice_no')) !!}<span class="asterisk"> *</span>
    {!! Form::text('invoiceno', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255,'autofocus', 'placeholder' => 'SYSTEM GENERATED IF BLANK']) !!}
</div>

<!-- Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('date', __('invoices.date')) !!}<span class="asterisk"> *</span>
    {!! Form::text('date', null, ['class' => 'form-control','id'=>'date']) !!}
</div>

@push('scripts')
   <script type="text/javascript">
           $('#date').datetimepicker({
               format: 'DD-MM-YYYY',
               useCurrent: true,
               icons: {
                   up: "icon-arrow-up-circle icons font-2xl",
                   down: "icon-arrow-down-circle icons font-2xl"
               },
               sideBySide: true
           })
       </script>
@endpush


<!-- Customer Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('customer_id', __('invoices.customer')) !!}<span class="asterisk"> *</span>
    {!! Form::select('customer_id', $customerItems, null, ['class' => 'form-control select2-customer', 'placeholder' => 'Pick a Customer...']) !!}
</div>


<!-- Driver Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('driver_id', __('invoices.driver')) !!}
    {!! Form::select('driver_id', $driverItems, null, ['class' => 'form-control', 'placeholder' => 'Pick a Driver...']) !!}
</div>


<!-- Kelindan Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('kelindan_id', __('invoices.kelindan')) !!}
    {!! Form::select('kelindan_id', $kelindanItems, null, ['class' => 'form-control', 'placeholder' => 'Pick a Kelindan...']) !!}
</div>


<!-- Agent Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('agent_id',  __('invoices.agent'))  !!}
    {!! Form::select('agent_id', $agentItems, null, ['class' => 'form-control', 'placeholder' => 'Pick a Agent...']) !!}
</div>


<!-- Supervisor Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supervisor_id',  __('invoices.supervisor'))  !!}
    {!! Form::select('supervisor_id', $supervisorItems, null, ['class' => 'form-control', 'placeholder' => 'Pick a Supervisor...']) !!}
</div>


<!-- Paymentterm Field -->
<div class="form-group col-sm-6">
    {!! Form::label('paymentterm',  __('invoices.payment_term'))  !!}
    {{ Form::select('paymentterm', array(1 => 'Cash' , 2 => 'Credit',3 => 'Online BankIn' , 4 => 'E-wallet', 5 => 'Cheque'), null, ['class' => 'form-control']) }}
</div>

<!-- ChequeNo Field -->
<div class="form-group col-sm-6" id='cheque-container' style='display:none;'>
    {!! Form::label('chequeno', __('invoices.cheque_no')) !!}
    {!! Form::text('chequeno', null, ['class' => 'form-control','maxlength' => 20,'maxlength' => 20]) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status',  __('invoices.status'))  !!}<span class="asterisk"> *</span>
    {{ Form::select('status', array(0 => 'New' , 1 => 'Completed'), null, ['class' => 'form-control']) }}
</div>

<!-- Remark Field -->
<div class="form-group col-sm-6">
    {!! Form::label('remark',  __('invoices.remark'))  !!}
    {!! Form::text('remark', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<input type="text" class="d-none" name="method" id="method" value="2">

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::button( __('invoices.save_&_exit') , ['class' => 'btn btn-primary','id' => 'save_exit']) !!}
    {!! Form::button (__('invoices.save_&_continue') , ['class' => 'btn btn-primary','id' => 'save_continue']) !!}
    <a href="{{ route('invoices.index') }}" class="btn btn-secondary"> {{__('invoices.cancel') }}</a>
</div>

@push('scripts')
    <script>
        $(document).keyup(function(e) {
            if (e.key === "Escape") {
                $('form a.btn-secondary')[0].click();
            }
        });
        $(document).ready(function () {
            $('.select2-customer').select2({
                placeholder: "Search for a customer...",
                allowClear: true,
                width: '100%'
            });
            HideLoad();
        });
        $("#save_exit").click(function(){
            $('#method').val(1);
            $('form').submit();
        });
        $("#save_continue").click(function(){
            $('#method').val(2);
            $('form').submit();
        });
        
        $("#customer_id").change(function(){
            ShowLoad();
            var url = '{{ config("app.url") }}/invoices/customer/'+$('#customer_id').val();
            $.get(url, function(data, status){
                if(status == 'success'){
                    if(data.status){
                        $('#agent_id').val(data.data.agent_id);
                        $('#supervisor_id').val(data.data.supervisor_id);
                    }else{
                        noti('e','Please contact your administrator',data.message);
                    }
                    HideLoad();
                }else{
                    noti('e','Please contact your administrator','');
                    HideLoad();
                }
            });
        });
        $('#paymentterm').change(function(){
      
            if($(this).val() == "5")
            {
                $('#cheque-container').show();
            }
            else
            {
                $('#cheque-container').hide();
            }
        });
    </script>
@endpush
