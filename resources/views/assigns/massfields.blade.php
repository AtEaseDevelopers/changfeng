<!-- Driver Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('driver_id', __('assign.driver')) !!}<span class="asterisk"> *</span>
    {!! Form::select('driver_id', $driverItems, null, [
        'class' => 'form-control',
        'placeholder' => __('assign.placeholder_pick_driver'),
        'autofocus',
        'required' => true
    ]) !!}
</div>

<!-- Customer Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('group', __('assign.group')) !!}<span class="asterisk"> *</span>
    {!! Form::select('group', $groups, null, [
        'class' => 'form-control',
        'placeholder' => __('assign.placeholder_pick_group'),
        'required' => true
    ]) !!}
</div>

<div class="form-group col-sm-6" id="sequence_details">
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('assign.save'), ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('assigns.index') }}" class="btn btn-secondary">{{ __('assign.cancel') }}</a>
</div>

@push('scripts')
    <script>
        $(document).keyup(function(e) {
            if (e.key === "Escape") {
                $('form a.btn-secondary')[0].click();
            }
        });
        $(document).ready(function() {
            insert();
            HideLoad();
        });

        $('#group').on('change', function() {
            insert();
        });

        function insert() {
            var group_id = $('#group').val();
            if (group_id == null || group_id == '') {
                return;
            }
            ShowLoad();
            $('#sequence_details').html('');
            $.ajax({
                type: 'POST',
                url: '{{ route('assigns.customerfindgroup') }}',
                dataType: 'json',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'group_id': group_id
                },
                success: function(response) {
                    if (response.status) {
                        var result = `
                            <table class="table table-striped table-bordered dataTable" width="100%" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <th>
                                            {{ __('assign.company') }}
                                        </th>
                                        <th>
                                            {{ __('assign.sequence') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        $.each(response.data, function(v, k) {
                            result = result + generateTr(k['id'], k['company']);
                        });
                        result = result + `
                            </tbody>
                        </table>
                        `;
                        $('#sequence_details').html(result);
                    } else {
                        noti('w', 'Warning', response.message);
                    }
                    HideLoad();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    noti('e', 'Server Error', errorThrown);
                    HideLoad();
                }
            });
        }

        function generateTr(customer_id, customer_name) {
            return `
            <tr role="row" class="odd">
                <td>
                    <select class="form-control" name="customer[]">
                        <option selected="selected" value="` + customer_id + `">` + customer_name + `</option>
                    </select>
                </td>
                <td>
                    <input class="form-control w-100" type="number" name="sequence[]" step="1" min="0" required value="2">
                </td>
            </tr>
            `;
        }
    </script>
@endpush