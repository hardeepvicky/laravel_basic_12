@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];

    $disabled = isset($screen) && $screen == "edit" ? true : false;
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />

    <div class="row">
        <div class="col-xxxl-12">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-xxl-6">
                            <?php
                            $field = 'order_no';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}" placeholder="Enter {{ $label }}"
                                mandatory="true" disabled="true"/>

                            <?php
                            $field = 'dept_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}"
                                :list="$dept_list"
                                class="select2 cascade-drop-down"
                                data-sr-cascade-target="#sub_dept_id"
                                data-sr-cascade-url="/sub-department/ajax_get_list/{v}" mandatory="true"
                            />

                            <?php
                            $field = 'sub_dept_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}"
                                :list="$empty_list"
                                class="select2"
                            />

                            <?php
                            $field = 'item_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}"
                                :list="$item_list"
                                class="select2"
                                class="select2 cascade-drop-down"
                                data-sr-cascade-target="#uom_id"
                                data-sr-cascade-url="/item/ajax_get_child_uom_list/{v}"
                                mandatory="true"
                            />

                            <?php
                            $field = 'part_no';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}"
                                layout="horizontal" 
                                placeholder="Enter {{ $label }}"
                            />

                            <?php
                            $field = 'qty';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>

                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    @if (isset($label) && $label)
                                        <label class="form-label">
                                            {{ $label }}
                                            <span class="mandatory">*</span>
                                        </label>
                                    @endif
                                </div>                                
                                <div class="col-sm-8">
                                    <div class="d-flex align-items-start">                                        
                                        <div class="flex-grow-1">
                                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                                :value="$model->{$field}" 
                                                class="validate-float"                                                
                                                placeholder="Enter {{ $label }}"
                                                mandatory="true" 
                                            />                                            
                                        </div>
                                        <div class="flex-grow-1 p-1">
                                            <?php
                                                $field = 'uom_id';
                                            ?>
                                            <x-Inputs.drop-down field layout="horizontal" :id="$field" :name="$field" :label="$false"
                                                :value="$model->{$field}"
                                                :list="$empty_list"
                                                class="select2"
                                                mandatory="true"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            $field = 'remarks';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}"
                                rows="3"
                                placeholder="Enter {{ $label }}" />
                        </div>
                    </div>
                </div>
            </div>

            <x-Backend.form-common-footer-buttons />
        </div>
    </div>
    </form>

    <script>
        $(function() {
            $.events.execAtLast(function() {
                $("#dept_id, #item_id").trigger("change", {
                    pageLoad: true
                });
            });
        });
    </script>
@endsection
