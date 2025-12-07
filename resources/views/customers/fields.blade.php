<!-- Code Field -->
<div class="form-group col-sm-6">
    {!! Form::label('code', __('customers.code')) !!}:<span class="asterisk"> *</span>
    {!! Form::text('code', null, ['class' => 'form-control', 'maxlength' => 255, 'autofocus']) !!} <!-- Removed duplicate maxlength -->
</div>

<!-- Company Field -->
<div class="form-group col-sm-6">
    {!! Form::label('company', __('customers.company')) !!}:<span class="asterisk"> *</span>
    {!! Form::text('company', null, ['class' => 'form-control', 'maxlength' => 255]) !!} <!-- Removed duplicate maxlength -->
</div>

<!-- Chinese Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('chinese_name', __('customers.chinese_name')) !!}:
    {!! Form::text('chinese_name', null, ['class' => 'form-control', 'maxlength' => 255]) !!} <!-- Removed duplicate maxlength -->
</div>

<!-- Paymentterm Field -->
<div class="form-group col-sm-6">
    {!! Form::label('paymentterm', __('customers.payment_term')) !!}:<span class="asterisk"> *</span>
    {{ Form::select('paymentterm', [
        1 => __('customers.payment_term_cash'),
        2 => __('customers.payment_term_credit_note'),
    ], null, ['class' => 'form-control']) }}
</div>

<!-- Group Field -->
<div class="form-group col-sm-6">
    {!! Form::label('group', __('customers.group')) !!}:
    {!! Form::select('group[]', $groups, explode(",", $customer->group ?? ""), ['class' => 'selectpicker form-control', 'multiple' => true]) !!}
    {{-- {!! Form::select('group[]', $groups, explode(",", $customer->group ?? ""), ['class' => 'selectpicker form-control', 'placeholder' => 'Select Group']) !!} --}}
</div>

<!-- Agent Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('agent_id', __('customers.agent')) !!}:
    {!! Form::select('agent_id', $agentItems, null, ['class' => 'form-control', 'placeholder' => 'Pick a Agent...']) !!}
</div>

<!-- Supervisor Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supervisor_id', __('customers.operation')) !!}:
    {!! Form::select('supervisor_id', $supervisorItems, null, ['class' => 'form-control', 'placeholder' => 'Pick a Operation...']) !!}
</div>

<!-- Phone Field -->
<div class="form-group col-sm-6">
    {!! Form::label('phone', __('customers.phone')) !!}:
    {!! Form::text('phone', null, ['class' => 'form-control', 'maxlength' => 20]) !!} <!-- Removed duplicate maxlength -->
</div>

<!-- Address Field -->
<div class="form-group col-sm-6">
    {!! Form::label('address', __('customers.address')) !!}:
    {!! Form::text('address', null, ['class' => 'form-control', 'maxlength' => 65535]) !!} <!-- Removed duplicate maxlength -->
</div>

<!-- Sst Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sst', __('customers.ssm')) !!}:
    {!! Form::text('sst', null, ['class' => 'form-control', 'maxlength' => 255]) !!} <!-- Removed duplicate maxlength -->
</div>

<!-- Tin Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tin', __('customers.tin')) !!}:
    {!! Form::text('tin', null, ['class' => 'form-control', 'maxlength' => 255]) !!} <!-- Removed duplicate maxlength -->
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', __('customers.status')) !!}:<span class="asterisk"> *</span>
    {{ Form::select('status', [
        1 => __('customers.active'),
        0 => __('customers.unactive'),
    ], null, ['class' => 'form-control']) }}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('customers.save'), ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('customers.index') }}" class="btn btn-secondary">{{ __('customers.cancel') }}</a>
</div>

@push('scripts')
    <script>
        $(document).keyup(function(e) {
            if (e.key === "Escape") {
                $('form a.btn-secondary')[0].click();
            }
        });
        $(document).ready(function () {
            HideLoad();
        });
    </script>
@endpush