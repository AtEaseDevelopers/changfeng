@extends('layouts.app')

@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">{{ __('inventory_balances.inventory_balances') }}</li>
    </ol>
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('flash::message')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i>
                            {{ __('inventory_balances.inventory_balances') }}
                            <button class="border-0 bg-transparent pull-right text-danger" data-toggle="modal" data-target="#stockout"><i class="fa fa-cart-arrow-down fa-lg"></i></button>
                            <button class="border-0 bg-transparent pull-right text-success pr-2" data-toggle="modal" data-target="#stockin"><i class="fa fa-cart-plus fa-lg"></i></button>
                        </div>
                        <div class="card-body">
                            @include('inventory_balances.table')
                            <div class="pull-right mr-3">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="stockin" class="modal fade">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ __('inventory_balances.stock_in') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body text-center">
                    {!! Form::open(['route' => 'inventoryBalances.stockin', 'enctype' => 'multipart/form-data']) !!}
                    <!-- Custom Dropdown with Checkboxes for Lorries -->
                    <div class="form-group">
                        <label for="lorry_id" class="col-form-label">{{ __('inventory_balances.lorry') }}:</label>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-block dropdown-toggle" type="button" id="dropdownLorryStockIn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('inventory_balances.select_lorry') }}
                            </button>
                            <div class="dropdown-menu p-3" aria-labelledby="dropdownLorryStockIn" style="width: 100%; max-height: 300px; overflow-y: auto;">
                                <!-- Live search input -->
                                <input type="text" class="form-control mb-3" id="lorrySearchStockIn" placeholder="Search Lorries...">
                                <!-- Lorries checkboxes -->
                                <div id="lorryListStockIn">
                                    @foreach($lorryItems as $lorryId => $lorryName)
                                        <div class="form-check ml-3">
                                            <input class="form-check-input lorry-checkbox-stockin" type="checkbox" name="lorry_id[]" value="{{ $lorryId }}" id="lorry_stockin_{{ $lorryId }}">
                                            <label class="form-check-label" for="lorry_stockin_{{ $lorryId }}">
                                                {{ $lorryName }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="product_id" class="col-form-label">{{ __('inventory_balances.product') }}:</label>
                        {{ Form::select('product_id', $productItems, null, ['class' => 'form-control', 'placeholder' => 'Pick a Product...']) }}
                    </div>
                    <div class="form-group">
                        <label for="quantity" class="col-form-label">{{ __('inventory_balances.transfer_quantity') }}:</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">+</span>
                            </div>
                            <input type="number" min="0" class="form-control" placeholder="Transfer Quantity" name="quantity">
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary rounded-0 mt-2" data-dismiss="modal">{{ __('inventory_balances.cancel') }}</button>
                    <button type="submit" name="button" class="btn btn-primary rounded-0 mt-2">{{ __('inventory_balances.update') }}</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <div id="stockout" class="modal fade">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ __('inventory_balances.stock_out') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body text-center">
                    {!! Form::open(['route' => 'inventoryBalances.stockout', 'enctype' => 'multipart/form-data']) !!}
                    <div class="form-group">
                        <label for="lorry_id" class="col-form-label">{{ __('inventory_balances.lorry') }}:</label>
                        {{ Form::select('lorry_id', $lorryItems, null, ['class' => 'form-control', 'placeholder' => 'Pick a Lorry...']) }}
                    </div>
                    <div class="form-group">
                        <label for="product_id" class="col-form-label">{{ __('inventory_balances.product') }}:</label>
                        {{ Form::select('product_id', $productItems, null, ['class' => 'form-control', 'placeholder' => 'Pick a Product...', 'id' => 'product_id']) }}
                    </div>
                    <div class="form-group">
                        <label for="quantity" class="col-form-label">{{ __('inventory_balances.transfer_quantity') }}:</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">-</span>
                            </div>
                            <input type="number" min="0" class="form-control" placeholder="Transfer Quantity" name="quantity" id="quantity">
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary rounded-0 mt-2" data-dismiss="modal">{{ __('inventory_balances.cancel') }}</button>
                    <button type="submit" name="button" class="btn btn-primary rounded-0 mt-2">{{ __('inventory_balances.update') }}</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        .dropdown-menu {
            border-radius: 0.25rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .dropdown-menu .form-check {
            padding: 0.25rem 0;
        }

        .dropdown-toggle::after {
            margin-left: 10px;
        }

        .dropdown-menu::-webkit-scrollbar {
            width: 8px;
        }

        .dropdown-menu::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .dropdown-menu::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .dropdown-menu::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .btn-outline-primary {
            border-color: #007bff;
            color: #007bff;
        }

        .btn-outline-primary:hover {
            background-color: #007bff;
            color: #fff;
        }
    </style>

    <script>
        $(document).ready(function () {
            // Implement live search functionality
            $('#lorrySearchStockIn').on('keyup', function () {
                var searchTerm = $(this).val().toLowerCase();
                $('#lorryListStockIn .form-check').each(function () {
                    var lorryName = $(this).find('label').text().toLowerCase();
                    if (lorryName.includes(searchTerm)) {
                        $(this).show(); // Show matching items
                    } else {
                        $(this).hide(); // Hide non-matching items
                    }
                });
            });
        });

        $(document).ready(function () {
            // When a checkbox is selected or deselected in the Stock In modal
            $('.lorry-checkbox-stockin').change(function () {
                var selected = [];
                // Collect all selected lorries
                $('.lorry-checkbox-stockin:checked').each(function () {
                    selected.push($(this).next('label').text());
                });

                // Update the dropdown button text with the selected lorries or default text
                var button = $('#dropdownLorryStockIn');
                if (selected.length > 0) {
                    button.text(selected.join(', '));
                } else {
                    button.text('Select Lorries');
                }
            });
        });
    </script>

    <script>
        $(document).keyup(function(e) {
            if(e.altKey && e.keyCode == 78){
                $('.card .card-header a')[0].click();
            }
        });

        $('#stockout').find('#lorry_id').change(function(){
            getstock();
        });

        $('#stockout').find('#product_id').change(function(){
            getstock();
        });

        function getstock() {
            var lorry_id = $('#stockout').find('select[name="lorry_id"]').val();
            var product_id = $('#stockout').find('select[name="product_id"]').val();

            console.log('Lorry ID: ', lorry_id); // Debugging
            console.log('Product ID: ', product_id); // Debugging

            if (lorry_id != '' && product_id != '') {
                ShowLoad();
                $.ajax({
                    url: '{{ ENV("APP_URL") }}' + '/inventoryBalances/getstock/' + lorry_id + '/' + product_id,
                    type: 'GET',
                    success: function(data) {
                        if (data.status) {
                            $('#stockout').find('#quantity').prop('disabled', false);
                            $('#stockout').find('#quantity').val(data.quantity);
                        } else {
                            $('#stockout').find('#quantity').prop('disabled', true);
                            $('#stockout').find('#quantity').val(data.quantity);
                            noti('e', 'Warning', data.message);
                        }
                        HideLoad();
                    },
                    error: function(error) {
                        noti('e', 'Please contact your administrator', error.responseJSON.message);
                        HideLoad();
                    }
                });
            }
        }

    </script>
@endpush
