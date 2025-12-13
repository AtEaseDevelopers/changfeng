<!-- Code Field -->
<div class="form-group">
    {!! Form::label('code', __('products.code')) !!}:
    <p>{{ $product->code }}</p>
</div>

<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', __('products.name')) !!}:
    <p>{{ $product->name }}</p>
</div>

<!-- Price Field -->
<div class="form-group">
    {!! Form::label('price', __('products.price')) !!}:
    <p>{{ number_format($product->price, 2) }}</p>
</div>

<!-- UOM Field -->
<div class="form-group">
    {!! Form::label('uom', __('products.uom')) !!}:
    <p>{{ (!empty($product->uom)) ? $product->uom : '-' }}</p>
</div>

<!-- SKU Field -->
<div class="form-group">
    {!! Form::label('sku', __('products.sku')) !!}:
    <p>{{ (!empty($product->sku)) ? $product->sku : '-' }}</p>
</div>

<!-- Type Field -->
<div class="form-group">
    {!! Form::label('type', __('products.type')) !!}:
    @switch($product->type)
        @case(1)
            <p>{{ __('products.type_coffee') }}</p>
            @break
        @case(2)
            <p>{{ __('products.type_tea') }}</p>
            @break
        @case(3)
            <p>{{ __('products.type_cocoa') }}</p>
            @break
        @case(4)
            <p>{{ __('products.type_ice') }}</p>
            @break
        @default
            <p>{{ __('products.type_other') }}</p>
    @endswitch
    <!--<p>{{ $product->status == 1 ? "Ice" : "Other" }}</p>-->
</div>

<!-- Status Field -->
<div class="form-group">
    {!! Form::label('status', __('products.status')) !!}:
    <p>{{ $product->status == 1 ? __('products.active') : __('products.unactive') }}</p>
</div>

@push('scripts')
    <script>
        $(document).keyup(function(e) {
            if (e.key === "Escape") {
                $('.card .card-header a')[0].click();
            }
        });
        $(document).ready(function () {
            HideLoad();
        });
    </script>
@endpush