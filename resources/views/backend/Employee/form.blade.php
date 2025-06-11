@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'Summary', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <form action="{{ $form['url'] }}" method="POST" class="" enctype="multipart/form-data">
        {!! csrf_field() !!}
        {{ method_field($form['method']) }}

        <div class="row">
            <div class="offset-md-2 col-md-8">
                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <h3>Basic</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field name="name" label="Name" 
                                        :value="$model->name"
                                        placeholder="Enter Name"                                     
                                        mandatory="true" 
                                    />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.drop-down name="department_id" label="Department" 
                                        class="select2" 
                                        :list="$department_list"
                                        :value="$model->department_id" 
                                        mandatory="true" 
                                        />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.drop-down name="designation_id" label="Designation" 
                                        :list="$designation_list"
                                        :value="$model->designation_id" 
                                        mandatory="true"  
                                        class="select2" 
                                        />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.drop-down name="current_state_id" label="Current State" 
                                        :list="$state_list"
                                        class="select2 cascade-drop-down" 
                                        mandatory="true" 
                                        :value="$model->current_state_id"
                                        data-sr-cascade-target="#current_city_id"
                                        data-sr-cascade-url="/city/ajax_get_list/{v}" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.drop-down id="current_city_id" name="current_city_id" label="Current City"
                                        class="select2" 
                                        mandatory="true" 
                                        :data-value="$model->current_city_id" 
                                        />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.drop-down name="permanent_state_id" label="Permanent State" 
                                        class="select2 cascade-drop-down" 
                                        :list="$state_list"
                                        :value="$model->permanent_state_id"
                                        mandatory="true" 
                                        data-sr-cascade-target="#permanent_city_id"
                                        data-sr-cascade-url="/city/ajax_get_list/{v}" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.drop-down id="permanent_city_id" name="permanent_city_id" label="Permanent City" 
                                        class="select2" 
                                        mandatory="true" 
                                        :data-value="$model->permanent_city_id" />
                                </div>
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3 checkbox-checked">
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
        $(document).ready(function() {            
            $.events.execAtLast(function(){            
                $(".cascade-drop-down").trigger("change", {pageLoad : true});
            });
        })
    </script>





@endsection