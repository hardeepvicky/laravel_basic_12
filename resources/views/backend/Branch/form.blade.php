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
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'company_id';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                            :list="$company_list" value="{{ $model->{$field} }}"
                                            class="select2 " 
                                            mandatory="true" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'type';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                            :list="$type_list" value="{{ $model->{$field} }}"
                                            class="select2 " 
                                            mandatory="true" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'name';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                            placeholder="Enter {{ $label }}" mandatory="true" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'mobile';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.text-field :name="$field" :label="$label"
                                            value="{{ $model->{$field} }}" class="validate-mobile"
                                            placeholder="Enter {{ $label }}" mandatory="true" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'email';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.text-field :name="$field" :label="$label"
                                            value="{{ $model->{$field} }}" class="validate-email"
                                            placeholder="Enter {{ $label }}" mandatory="true" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'phone_no';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.text-field :name="$field" :label="$label"
                                            value="{{ $model->{$field} }}" class="validate-phone"
                                            placeholder="Enter {{ $label }}"  />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'gst_no';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.text-field :name="$field" :label="$label"
                                            value="{{ $model->{$field} }}" 
                                            placeholder="Enter {{ $label }}"  />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'state_id';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                            :list="$state_list" value="{{ $model->{$field} }}"
                                            class="select2 cascade-drop-down" data-sr-cascade-target="#district_id"
                                            data-sr-cascade-url="/district/ajax_get_list/{v}" mandatory="true" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'district_id';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                            :list="$empty_list" value="{{ $model->{$field} }}"
                                            class="select2 cascade-drop-down" data-sr-cascade-target="#city_id"
                                            data-sr-cascade-url="/city/ajax_get_list/0/{v}" mandatory="true" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'city_id';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                            :list="$empty_list" value="{{ $model->{$field} }}" class="select2"
                                            mandatory="true" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <?php
                                $field = 'addr1';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}"  mandatory="true"/>
                            </div>

                            <div class="form-group mb-3">
                                <?php
                                $field = 'addr2';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" mandatory="true"/>
                            </div>

                            <div class="form-group mb-3">
                                <div class="textarea-wrapper">
                                    <?php
                                    $field = 'description';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.text-area :id="$field" :name="$field" :label="$label"
                                        rows="3" value="{{ $model->{$field} }}"
                                        placeholder="Enter {{ $label }}" class="auto-suggesstion-list" />
                                    <div id="{{ $field . '-suggestion-box' }}" class="suggestion-box hidden"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'list_display_order';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.text-field type="number" :name="$field" :label="$label"
                                            value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}"
                                            mandatory="true" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-xl-6" style="padding-top: 30px">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'is_active';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.checkbox :name="$field" :label="$label"
                                            value="{{ $model->{$field} }}" />
                                    </div>
                                </div>
                            </div>
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
            suggestion_list($("#description"), $("#description-suggestion-box"), [], "/tags/ajax_get_list");

            $.events.execAtLast(function() {

                $("#state_id").trigger("change", {
                    pageLoad: true
                });
            });
        })
    </script>
@endsection
