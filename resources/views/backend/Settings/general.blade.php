@extends($layout)

@section('content')
    <?php
    
    use App\Models\Setting;
    
    $page_header_links = [];
    $false = false;
    
    function get_value($record_list, $field)
    {
        return isset($record_list[$field]) ? $record_list[$field] : '';
    }
    ?>

    @include($partial_path . '.page_header')

    <x-backend.form-errors />

    <form action="{{ $form['url'] }}" method="POST">
        {!! csrf_field() !!}
        {{ method_field($form['method']) }}
        <div class="row">
            <div class="col-lg-6 col-sm-12">
                <div class="card border border-primary">
                    <div class="card-header bg-transparent border-primary">
                        <h4 class="card-title">Basic Info</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <?php $field = 'company_name';
                            $value = get_value($record_list, $field);
                            $label = Setting::LABELS[$field]; ?>
                            <x-inputs.text-field name="data[text][{{ $field }}]" :label="$label"
                                :value="$value" />
                        </div>
                        <div class="form-group mb-3">
                            <?php $field = 'company_tag_line';
                            $value = get_value($record_list, $field);
                            $label = Setting::LABELS[$field]; ?>
                            <x-inputs.text-field name="data[text][{{ $field }}]" :label="$label"
                                :value="$value" />
                        </div>
                        <div class="form-group mb-3">
                            <?php $field = 'company_email';
                            $value = get_value($record_list, $field);
                            $label = Setting::LABELS[$field]; ?>
                            <x-inputs.text-field name="data[text][{{ $field }}]" :label="$label"
                                :value="$value" />
                        </div>
                        <div class="form-group mb-3">
                            <?php $field = 'company_address';
                            $value = get_value($record_list, $field);
                            $label = Setting::LABELS[$field]; ?>
                            <x-inputs.text-field name="data[text][{{ $field }}]" :label="$label"
                                :value="$value" />
                        </div>
                        <div class="form-group mb-3">
                            <?php $field = 'company_gst_no';
                            $value = get_value($record_list, $field);
                            $label = Setting::LABELS[$field]; ?>
                            <x-inputs.text-field name="data[text][{{ $field }}]" :label="$label"
                                :value="$value" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-sm-12">
                <div class="card border border-primary">
                    <div class="card-header bg-transparent border-primary">
                        <h4 class="card-title">Other</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <?php $field = 'export_csv_max_record_count';
                            $value = get_value($record_list, $field);
                            $label = Setting::LABELS[$field]; ?>
                            <x-inputs.text-field name="data[text][{{ $field }}]" :label="$label" :value="$value"
                                class="form-control validate-int" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-sm-12">
                <div class="card border border-primary">
                    <div class="card-header bg-transparent border-primary">
                        <h4 class="card-title">Item</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <?php $field = 'item_sku_pattern';
                            $value = get_value($record_list, $field);
                            $label = Setting::LABELS[$field]; ?>
                            <x-inputs.text-field name="data[text][{{ $field }}]" :label="$label" :value="$value"/>
                            <div class="help-block">
                                {category}-{brand}-{color}-{size}-{specification}
                                <ul class="b-t-dark">
                                    <li>Category : {category}</li>
                                    <li>Color : {color}</li>
                                    <li>Brand : {brand}</li>
                                    <li>Size : {size}</li>
                                    <li>Specification : {specification}</li>                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
        </div>
    </form>
@endsection
