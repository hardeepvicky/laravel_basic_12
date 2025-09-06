@extends($layout)

@section('content')
    <?php
    use App\Models\NotificationSetting;
    
    $page_header_links = [];
    ?>

    @include($partial_path . '.page_header')

    <x-backend.form-errors />

    <form action="{{ $form['url'] }}" method="POST">
        {!! csrf_field() !!}
        {{ method_field($form['method']) }}        
        <?php
        $base_field = 'on_dispatch_order_from_sales_lead';
        $label = NotificationSetting::LABELS[$base_field];
        ?>
        <div class="card border border-primary">
            <div class="card-header bg-transparent border-primary">
                <h4 class="card-title">{{ $label }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="form-group">
                            <?php
                            $field = 'type';
                            $label = "1st Condition";
                            $value = $records[$base_field][$field] ?? [];
                            ?>
                            <x-Inputs.drop-down :id="$field" name="data[{{ $base_field }}][{{ $field }}]" :label="$label"
                                :list="$type_list" :value="$value" class="select2" mandatory="true" />
                        </div>
                        <div class="form-group type-block type-block_{{ NotificationSetting::TYPE_ROLE }}">
                            <?php
                            $field = 'role_id_list';
                            $label = 'Roles';
                            $value = $records[$base_field][$field] ?? [];
                            ?>
                            <x-Inputs.drop-down :id="$field" name="data[{{ $base_field }}][{{ $field }}][]" :label="$label"
                                :list="$type_list" :value="$value" multiple="multiple" class="select2" mandatory="true" />
                        </div>
                        <div class="form-group type-block type-block_{{ NotificationSetting::TYPE_DEPARTMENT }}">
                            <?php
                            $field = 'dept_id_list';
                            $label = 'Department';
                            $value = $records[$base_field][$field] ?? [];
                            ?>
                            <x-Inputs.drop-down :id="$field" name="data[{{ $base_field }}][{{ $field }}][]" :label="$label"
                                :list="$dept_list" :value="$value" multiple="multiple" class="select2" mandatory="true" />
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-group">
                            <?php
                            $field = 'sub_type';
                            $label = "2nd Condition";
                            $value = $records[$base_field][$field] ?? [];
                            ?>
                            <x-Inputs.drop-down :id="$field" name="data[{{ $base_field }}][{{ $field }}]" :label="$label"
                                :list="$sub_type_list" :value="$value" class="select2" />
                        </div>
                        <div class="form-group sub_type-block sub_type-block_{{ NotificationSetting::SUB_TYPE_DESIGNATION }}">
                            <?php
                            $field = 'desg_id_list';
                            $label = 'Designations';
                            $value = $records[$base_field][$field] ?? [];
                            ?>
                            <x-Inputs.drop-down :id="$field" name="data[{{ $base_field }}][{{ $field }}][]" :label="$label"
                                :list="$desg_list" :value="$value" multiple="multiple" class="select2" />
                        </div>
                        <div class="form-group sub_type-block sub_type-block_{{ NotificationSetting::SUB_TYPE_USER }}">
                            <?php
                            $field = 'user_id_list';
                            $label = 'User';
                            $value = $records[$base_field][$field] ?? [];
                            ?>
                            <x-Inputs.drop-down :id="$field" name="data[{{ $base_field }}][{{ $field }}][]" :label="$label"
                                :list="$user_list" :value="$value" multiple="multiple" class="select2" />
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

    <script>
        $(function(){

            function show_hide_field() {

                var type = $("#type").val();

                var selector = ".type-block";
                $(selector).hide();
                $(selector).find("input,select").attr("disabled", true);

                console.log("hide", selector);

                var selector = ".type-block_any";

                if ($(selector).length > 0) {
                    console.log("show", selector);
                    $(selector).show();
                    $(selector).find("input,select").removeAttr("disabled");
                }

                if (type)
                {
                    var selector = ".type-block_" + type;

                    if ($(selector).length > 0) {
                        console.log("show", selector);
                        $(selector).show();
                        $(selector).find("input,select").removeAttr("disabled");
                    }
                }

                var sub_type = $("#sub_type").val();

                var selector = ".sub_type-block";
                $(selector).hide();
                $(selector).find("input,select").attr("disabled", true);

                console.log("hide", selector);

                var selector = ".sub_type-block_any";

                if ($(selector).length > 0) {
                    console.log("show", selector);
                    $(selector).show();
                    $(selector).find("input,select").removeAttr("disabled");
                }

                if (sub_type)
                {
                    var selector = ".sub_type-block_" + sub_type;

                    if ($(selector).length > 0) {
                        console.log("show", selector);
                        $(selector).show();
                        $(selector).find("input,select").removeAttr("disabled");
                    }
                }
            }

            $("#type, #sub_type").change(show_hide_field).trigger("change", {pageLoad : true});

        });
    </script>
@endsection
