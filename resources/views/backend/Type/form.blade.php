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
                            <div class="form-group mb-3">
                                <x-Inputs.drop-down name="type" label="Type" :list="$type_list" :value="$model->type"
                                    class="select2 cascade-drop-down" data-sr-cascade-target="#code"
                                    data-sr-cascade-url="/type/ajax_get_code_list/{v}" mandatory="true" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php $list = []; ?>
                                <x-Inputs.drop-down id="code" name="code" label="Code" :list="$list"
                                    :value="$model->code" class="select2" mandatory="true" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <x-Inputs.text-field name="name" label="Name" :value="$model->name"
                                    placeholder="Enter Name" autocomplete="off" mandatory="true" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <x-Inputs.text-field name="short_name" label="Short Name" :value="$model->short_name"
                                    placeholder="Enter Short Name" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <x-Inputs.text-field name="list_display_order" label="Display Order For Drop Downs"
                                    class="validate-int" :value="$model->list_display_order" placeholder="Enter 1" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <x-Inputs.checkbox name="is_active" label="Active" :value="$model->is_active" />
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
                $(".cascade-drop-down").trigger("change", {
                    pageLoad: true
                });
            });
        })
    </script>
@endsection
