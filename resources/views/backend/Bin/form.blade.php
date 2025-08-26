@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />

    <div class="row">
        <div class="col-xxl-10">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-xxl-6">
                            <?php
                            $field = 'branch_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$branch_list" 
                                :value="$model->{$field}"
                                class="select2 " 
                                layout="horizontal"
                                mandatory="true" />

                            <?php
                            $field = 'code';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :id="$field"  :name="$field" :label="$label"
                                :value="$model->{$field}" 
                                layout="horizontal"
                                disabled="true"/>

                            <?php
                            $field = 'name';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$label" 
                                :value="$model->{$field}"
                                layout="horizontal"
                                placeholder="Enter {{ $label }}" 
                                mandatory="true" 
                            />

                            <?php
                            $field = 'remarks';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}"
                                rows="3" 
                                layout="horizontal"
                                placeholder="Enter {{ $label }}" 
                                />
                            
                            <?php
                            $field = 'list_display_order';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field type="number" :name="$field" :label="$label"
                                :value="$model->{$field}" 
                                layout="horizontal"
                                placeholder="Enter {{ $label }}"
                                mandatory="true" 
                                autocomplete="off" 
                            />

                            <?php
                            $field = 'is_active';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.checkbox :name="$field" :label="$label" :value="$model->{$field}" />
                        </div>
                    </div>
                </div>
            </div>

            <x-Backend.form-common-footer-buttons />
        </div>
    </div>
    </form>

    <script>
        $(function(){
            $("#branch_id").change(function(){
                var text = $(this).find("option:selected").text();

                var code = $("#code").val();

                $("#name").val(text + " " + code);
            }).trigger("change", {pageLoad : true});
        });
    </script>
@endsection
