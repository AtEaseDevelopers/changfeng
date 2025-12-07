<!-- Product Id Field -->
<div class="form-group col-sm-6"><span class="asterisk"> *</span>
    {!! Form::label('product_id', __('special_prices.product')) !!}
    {!! Form::select('product_id', $productItems, null, ['class' => 'form-control', 'placeholder' => 'Pick a Product...','autofocus']) !!}
</div>

<!-- Customer Id Field -->
<div class="form-group col-sm-6"><span class="asterisk"> *</span>
    {!! Form::label('customer_id', __('special_prices.customer')) !!}
    {!! Form::select('customer_id', $customerItems, null, ['class' => 'form-control', 'placeholder' => 'Pick a Customer...']) !!}
</div>

<!-- Price Field -->
<div class="form-group col-sm-6">
    {!! Form::label('price', __('special_prices.price')) !!}
    {!! Form::number('price', null, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', __('special_prices.status')) !!}
    {{ Form::select('status', [
        1 => __('special_prices.active'),
        0 => __('special_prices.unactive'),
    ], null, ['class' => 'form-control']) }}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('special_prices.save'), ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('specialPrices.index') }}" class="btn btn-secondary">{{ __('special_prices.cancel') }}</a>
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


