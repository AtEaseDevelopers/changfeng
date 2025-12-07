<!-- Code Field -->
<div class="form-group col-sm-6">
    {!! Form::label('code', __('commission.commission_type')) !!}
    {{ Form::select('code', [
        'agent_commission_product_type' => __('commission.agent_commission'),
        'kelindan_commission_product_type' => __('commission.kelindan_commission'),
        'driver_commission_product_type' => __('commission.driver_commission'),
        'operation_commission_product_type' => __('commission.operation_commission')
    ], null, ['class' => 'form-control']) }}
</div>

<!-- Description Field -->
<div class="form-group col-sm-6">
    {!! Form::label('description', __('commission.product_type')) !!}
    {{ Form::select('description', [
        0 => __('commission.ice')
    ], null, ['class' => 'form-control']) }}
</div>

<!-- Value Field -->
<div class="form-group col-sm-6">
    {!! Form::label('value', __('commission.value')) !!}<span class="asterisk"> *</span>
    {!! Form::text('value', null, ['class' => 'form-control']) !!}
</div>

<!-- Sequence Field -->
<div class="form-group col-sm-6">
    {!! Form::hidden('sequence', '2', ['class' => 'form-control']) !!}
</div>

{{-- <!-- Str Udf1 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('STR_UDF1', 'String UDF1:') !!}
    {!! Form::text('STR_UDF1', null, ['class' => 'form-control']) !!}
</div>

<!-- Str Udf2 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('STR_UDF2', 'String UDF2:') !!}
    {!! Form::text('STR_UDF2', null, ['class' => 'form-control']) !!}
</div>

<!-- Str Udf3 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('STR_UDF3', 'String UDF3:') !!}
    {!! Form::text('STR_UDF3', null, ['class' => 'form-control']) !!}
</div>

<!-- Int Udf1 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('INT_UDF1', 'Integer UDF1:') !!}
    {!! Form::number('INT_UDF1', null, ['class' => 'form-control']) !!}
</div>

<!-- Int Udf2 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('INT_UDF2', 'Integer UDF2:') !!}
    {!! Form::number('INT_UDF2', null, ['class' => 'form-control']) !!}
</div>

<!-- Int Udf3 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('INT_UDF3', 'Integer UDF3:') !!}
    {!! Form::number('INT_UDF3', null, ['class' => 'form-control']) !!}
</div> --}}

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('commission.save'), ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('commission_group.index') }}" class="btn btn-secondary">{{ __('commission.cancel') }}</a>
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