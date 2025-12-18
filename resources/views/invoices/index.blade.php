@extends('layouts.app')

@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">{{ __('invoices.invoices') }}</li>
    </ol>
    <div class="container-fluid">
        <div class="animated fadeIn">
             @include('flash::message')
             <div class="row">
                 <div class="col-lg-12">
                     <div class="card">
                         <div class="card-header">
                             <i class="fa fa-align-justify"></i>
                             {{ __('invoices.invoices') }}
                             <a class="pull-right" href="{{ route('invoices.create') }}"><i class="fa fa-plus-square fa-lg"></i></a>
                             <a class="pull-right text-danger pr-2" id="massdelete" href="#" alt="Mass delete"><i class="fa fa-trash fa-lg"></i></a>
                             <a class="pull-right text-success pr-2" id="massactive" href="#" alt="Mass active"><i class="fa fa-check fa-lg"></i></a>
                             <!--<a class="pull-right pr-2" id="masssyncxero" href="#" alt="Mass Sync to Xero"><i class="fa fa-refresh fa-lg"></i></a>-->
                             <a class="pull-right text-primary pr-2" id="masssyncsql" href="#" alt="Mass Sync to SQL" data-toggle="tooltip" title="Sync to SQL">
                                <img src="{{ asset('images/icon/sql_icon.webp') }}" alt="SQL Sync" width="25" />
                             </a>
                         </div>
                         <div class="card-body">
                             @include('invoices.table')
                               <div class="pull-right mr-3">                               
                              </div>
                         </div>
                     </div>
                  </div>
             </div>
         </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Tooltip without delay
            $('[data-toggle="tooltip"]').tooltip({
                delay: { "show": 0, "hide": 0 }
            });
        });

        $(document).keyup(function(e) {
            if(e.altKey && e.keyCode == 78){
                $('.card .card-header a')[0].click();
            } 
        });
        
        $(document).on("click", "#masssave", function(e){
            var m = "";
            if(window.checkboxid.length == 0){
                noti('i','Info','Please select at least one row');
                return;
            }else if(window.checkboxid.length == 1){
                m = "Confirm to save 1 row"
            }else{
                m = "Confirm to save " + window.checkboxid.length + " rows!"
            }
            $.confirm({
                title: 'Save View',
                content: m,
                buttons: {
                    Yes: function() {
                        masssave(window.checkboxid);
                    },
                    No: function() {
                        return;
                    }
                }
            });

        });

        $(document).on("click", "#masssyncxero", function(e){
            var m = "";
            if(window.checkboxid.length == 0){
                noti('i','Info','Please select at least one row');
                return;
            }else if(window.checkboxid.length == 1){
                m = "Confirm to sync 1 row!"
            }else{
                m = "Confirm to sync " + window.checkboxid.length + " rows!"
            }
            $.confirm({
                title: 'Mass Sync to Xero',
                content: m,
                buttons: {
                    Yes: function() {
                        let url = "{{config('app.url')}}/invoices/sync-xero"
                        url = `${url}?ids=${window.checkboxid}`

                        window.location.href = url
                    },
                    No: function() {
                        return;
                    }
                }
            });
        });
        $(document).on("click", "#masssyncsql", function(e){
            var m = "";
            if(window.checkboxid.length == 0){
                noti('i','Info','Please select at least one row');
                return;
            } else if(window.checkboxid.length == 1){
                m = "Confirm to sync 1 row!";
            } else {
                m = "Confirm to sync " + window.checkboxid.length + " rows!";
            }

            $.confirm({
                title: 'Mass Sync SQL',
                content: m,
                buttons: {
                    Yes: function() {
                        // Prepare POST data
                        var data = {
                            invoices_id: window.checkboxid.join(','),
                            _token: "{{ csrf_token() }}"  // CSRF token for security
                        };

                        // Send AJAX POST request
                        $.ajax({
                            url: "{{ route('invoices.syncsqlrecord') }}",  // Adjust the URL to your route
                            type: "POST",
                            data: data,
                           success: function (response) {
                                toastr.success('Sync started successfully!', 'Success');

                                let message = '<div style="line-height:1.6">';
                                message += '<b>Sync Summary:</b><br>';
                                message += 'Success: ' + response.success_count + ' invoices<br>';
                                message += 'Failed: ' + response.fail_count + ' invoices<br>';
                                message += 'System will sync in background, please check the sync status later.<br><br>';

                                if (response.synced_invoices.length > 0) {
                                    message += '<b>Synced Invoices:</b><br>' +
                                        response.synced_invoices.join('<br>') + '<br><br>';
                                }

                                if (Object.keys(response.invalid_invoices).length > 0) {
                                    message += '<b>Invalid Invoices:</b><br>' +
                                        Object.entries(response.invalid_invoices)
                                            .map(([id, error]) => 'Invoice ' + id + ': ' + error)
                                            .join('<br>') + '<br><br>';
                                }

                                if (Object.keys(response.sync_failures).length > 0) {
                                    message += '<b>Sync Failures:</b><br>' +
                                        Object.entries(response.sync_failures)
                                            .map(([id, error]) => 'Invoice ' + id + ': ' + error)
                                            .join('<br>') + '<br><br>';
                                }

                                if (response.not_found.length > 0) {
                                    message += '<b>Not Found Invoices:</b><br>' +
                                        response.not_found.join('<br>') + '<br><br>';
                                }

                                message += '</div>';

                                $.confirm({
                                    title: 'Sync Results',
                                    content: message,
                                    type: 'blue',
                                    useBootstrap: false,   // ✅ important
                                    buttons: {
                                        Ok: function () {
                                            location.reload();
                                        }
                                    }
                                });
                            },
                            error: function(error) {
                                // Handle error
                                toastr.error('Failed to start sync. Please try again.', 'Error');
                            }
                        });
                    },
                    No: function() {
                        return;
                    }
                }
            });
        });
        function masssave(ids){
            ShowLoad();
            $.ajax({
                url: "{{config('app.url')}}/invoices/masssave",
                type:"POST",
                data:{
                ids: ids
                ,_token: "{{ csrf_token() }}"
                },
                success:function(response){
                    window.checkboxid = [];
                    $('.buttons-reload').click();
                    toastr.success('Please find Save View ID: '+response, 'Save Successfully', {showEasing: "swing", hideEasing: "linear", showMethod: "fadeIn", hideMethod: "fadeOut", positionClass: "toast-bottom-right", timeOut: 0, allowHtml: true });
                },
                error: function(error) {
                    noti('e','Please contact your administrator',error.responseJSON.message)
                    HideLoad();
                }
            });
        }
        
        $(document).on("click", "#massdelete", function(e){
            var m = "";
            if(window.checkboxid.length == 0){
                noti('i','Info','Please select at least one row');
                return;
            }else if(window.checkboxid.length == 1){
                m = "Confirm to delete 1 row!"
            }else{
                m = "Confirm to delete " + window.checkboxid.length + " rows!"
            }
            $.confirm({
                title: 'Mass Delete',
                content: m,
                buttons: {
                    Yes: function() {
                        massdelete(window.checkboxid);
                    },
                    No: function() {
                        return;
                    }
                }
            });
        });
        
        $(document).on("click", "#massactive", function(e){
            var m = "";
            if(window.checkboxid.length == 0){
                noti('i','Info','Please select at least one row');
                return;
            }else if(window.checkboxid.length == 1){
                m = "Confirm to update 1 row"
            }else{
                m = "Confirm to update " + window.checkboxid.length + " rows!"
            }
            $.confirm({
                title: 'Mass Update',
                content: m,
                buttons: {
                    Completed: function() {
                        massupdatestatus(window.checkboxid,1);
                    },
                    New: function() {
                        massupdatestatus(window.checkboxid,0);
                    },
                    somethingElse: {
                        text: 'Cancel',
                        btnClass: 'btn-gray',
                        keys: ['enter', 'shift']
                    }
                }
            });
            
        });
        function massdelete(ids){
            ShowLoad();
            $.ajax({
                url: "{{config('app.url')}}/invoices/massdestroy",
                type:"POST",
                data:{
                ids: ids
                ,_token: "{{ csrf_token() }}"
                },
                success:function(response){
                    window.checkboxid = [];
                    $('.buttons-reload').click();
                    noti('s','Delete Successfully',response+' row(s) had been deleted.')
                },
                error: function(error) {
                    noti('e','Please contact your administrator',error.responseJSON.message)
                    HideLoad();
                }
            });
        }
        function massupdatestatus(ids,status){
            ShowLoad();
            $.ajax({
                url: "{{config('app.url')}}/invoices/massupdatestatus",
                type:"POST",
                data:{
                ids: ids,
                status: status
                ,_token: "{{ csrf_token() }}"
                },
                success:function(response){
                    window.checkboxid = [];
                    $('.buttons-reload').click();
                    noti('s','Update Successfully',response+' row(s) had been updated.')
                },
                error: function(error) {
                    noti('e','Please contact your administrator',error.responseJSON.message)
                    HideLoad();
                }
            });
        }
    </script>
@endpush