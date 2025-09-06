@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />

    <div class="card">
        <div class="card-header card-no-border pb-0">
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 col-xxl-6">
                    <x-Inputs.drop-down name="type" label="Type" :list="$type_list" :value="$model->type"
                        class="select2 cascade-drop-down" 
                        layout="horizontal"
                        data-sr-cascade-target="#code"
                        data-sr-cascade-url="/type/ajax_get_code_list/{v}" 
                        mandatory="true" />
                        
                    <x-Inputs.drop-down id="code" name="code" label="Code" 
                        :list="$empty_list"                            
                        :value="$model->code" 
                        layout="horizontal"
                        class="select2"/>

                    <x-Inputs.text-field name="name" label="Name" :value="$model->name"
                        layout="horizontal"
                        placeholder="Enter Name" 
                        autocomplete="off" 
                        mandatory="true" />

                    <x-Inputs.text-field name="short_name" label="Short Name" :value="$model->short_name"
                        layout="horizontal"
                        placeholder="Enter Short Name" 
                        autocomplete="off" />

                    <x-Inputs.text-field name="list_display_order" label="Display Order For Drop Downs"
                        :value="$model->list_display_order" 
                        class="validate-int" 
                        layout="horizontal"
                        placeholder="Enter 1" autocomplete="off" />

                    <div class="row">
                        <div class="col-sm-3">
                        </div>
                        <div class="col-sm-9">
                            <x-Inputs.checkbox name="is_active" label="Active" :value="$model->is_active" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <x-Backend.form-common-footer-buttons />
    </div>
            
    </form>

    <script>
        $(function() {
            $.events.execAtLast(function() {
                $(".cascade-drop-down").trigger("sr-cascade.change", {
                    pageLoad: true
                });
            });
        })
    </script>
@endsection
