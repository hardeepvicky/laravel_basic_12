@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />

    <div class="row">
        <div class="col-md-12 offset-lg-1 col-lg-10">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                    <h3>Basic</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <?php
                            $field = 'is_link';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.checkbox :id="$field" :name="$field" :label="$label" value="{{ $model->{$field}  }}"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'department_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$dept_list" value="{{ $model->{$field}  }}" class="select2" pleaseSelect="For All"/>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'parent_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$tree_list" value="{{ $model->{$field}  }}" class="select2" pleaseSelect="Root"/>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field}  }}"
                                    placeholder="Enter {{ $label }}" mandatory="true" autocomplete="off" />
                            </div>                            
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'css_class';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field}  }}"
                                    placeholder="Enter {{ $label }}" />
                            </div>                            
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'icon_css_class';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field}  }}"
                                    placeholder="Enter {{ $label }}" />
                            </div>                            
                        </div>
                        <div class="col-md-6 col-lg-4 is_link-block is_link-block-1">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'route_name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$route_list" value="{{ $model->{$field}  }}" class="select2" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'menu_display_order';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field type="number" :name="$field" :label="$label" value="{{ $model->{$field}  }}"
                                    placeholder="Enter {{ $label }}" mandatory="true" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'is_active';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.checkbox :name="$field" :label="$label" value="{{ $model->{$field}  }}"/>
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
            $.events.execAtLast(function() {
               
                
                function show_hide_field() {

                    var is_link = $("#is_link").prop("checked");
                    is_link = is_link ? 1 : 0;

                    var selector = ".is_link-block";
                    $(selector).hide();
                    $(selector).find("input,select").attr("disabled", true);

                    console.log("hide", selector);

                    var selector = ".is_link-block-any, .is_link-block-" + is_link;

                    if ($(selector).length > 0) {
                        console.log("show", selector);
                        $(selector).show();
                        $(selector).find("input,select").removeAttr("disabled");
                    }
                }


                $("#is_link").change(show_hide_field);

                show_hide_field();
            });
        })
    </script>
@endsection
