<!-- Code Field -->
<div class="form-group col-sm-6">
    {!! Form::label('code', __('products.code')) !!}<span class="asterisk"> *</span>
    {!! Form::text('code', null, ['class' => 'form-control', 'maxlength' => 255, 'autofocus']) !!} <!-- Removed duplicate maxlength -->
</div>

<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', __('products.name')) !!}<span class="asterisk"> *</span>
    {!! Form::text('name', null, ['class' => 'form-control', 'maxlength' => 255]) !!} <!-- Removed duplicate maxlength -->
</div>

<!-- Price Field -->
<div class="form-group col-sm-6">
    {!! Form::label('price', __('products.price')) !!}<span class="asterisk"> *</span>
    {!! Form::number('price', null, ['class' => 'form-control', 'step' => '0.01']) !!}
</div>

<!-- UOM Field -->
<div class="form-group col-sm-6">
    {!! Form::label('uom', __('products.uom')) !!}<span class="asterisk"> *</span>
    {!! Form::text('uom', null, ['class' => 'form-control', 'maxlength' => 255]) !!} <!-- Removed duplicate maxlength -->
</div>

<!-- SKU Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sku', __('products.sku')) !!}<span class="asterisk"> *</span>
    {!! Form::text('sku', null, ['class' => 'form-control', 'maxlength' => 500]) !!} <!-- Removed duplicate maxlength -->
</div>

<!-- Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('type', __('products.type')) !!}
    {{ Form::select('type', [
        0 => __('products.type_ice'),
    ], null, ['class' => 'form-control']) }}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', __('products.status')) !!}
    {{ Form::select('status', [
        1 => __('products.active'),
        0 => __('products.unactive'),
    ], null, ['class' => 'form-control']) }}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('products.save'), ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('products.cancel') }}</a>
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