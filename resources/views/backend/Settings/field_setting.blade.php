@extends($layout)

@section('content')

@include($partial_path . '.page_header')

<div class="card">
    <div class="card-body">
        <div style="width : 500px; margin: auto;">
            <x-Inputs.drop-down id="type" name="type" label="Type" :value="$search['type']" :list="$model_list"
                class="select2" />
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if(isset($form))
        <x-Form.start id="setting" method="{{ $form['method'] }}" class="confirm-submit"
            data-msg="Please check everything carefully" />

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-primary active" id="tab_field_labels" data-bs-toggle="tab"
                    href="#tab_field_labels_block" role="tab" aria-controls="tab_field_labels_block"
                    aria-selected="true">Field Labels</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-primary" id="tab_other" data-bs-toggle="tab" href="#tab_other_block" role="tab"
                    aria-controls="tab_other_block" aria-selected="true">Other</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active show fade" id="tab_field_labels_block" role="tabpanel"
                aria-labelledby="tab_field_labels">
                <table class="table table-sm summary-table table-bordered m-2">
                    <thead>
                        <tr>
                            <th class="text-center">Sr. No.</th>
                            <th>Database Field</th>
                            <th>Default Label</th>
                            <th>Custom Label</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 0; ?>
                        @foreach ($fields as $field => $default_label)
                        <?php $i++; ?>
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $field }}</td>
                            <td>{{ $default_label }}</td>
                            <td>
                                <?php
                                $value = $model->field_labels[$field] ?? "";
                                ?>
                                <x-Inputs.text-field name="field_labels[{{ $field }}]" label="" :value="$value"
                                    placeholder="Enter Label" />
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="tab_other_block" role="tabpanel" aria-labelledby="tab_other">
            Other
        </div>

        <div class="form-buttons mb-4">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        @endif
    </div>
</div>


<script>
$(function() {
    $.events.execAtLast(function() {
        $("#type").change(function() {
            var v = $(this).val();

            if (v) {
                if ($("form#setting").length > 0) {
                    confirmDialog("This Will loose your data", function() {
                        window.location.href = '{{ route($current_route_name) }}/' + v;
                    });
                } else {
                    window.location.href = '{{ route($current_route_name) }}/' + v;
                }
            }
        })
    });
})
</script>

@endsection