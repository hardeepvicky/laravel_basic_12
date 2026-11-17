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
                    <div class="form-group mb-3">
                        <x-Inputs.text-field name="name" label="Name" :value="$model->name"
                            placeholder="Enter Name" autocomplete="off" mandatory="true" />
                    </div>                   
                    <div class="form-group mb-3">
                        <?php $field = "document_file_id"; ?>
                        <span id="choose-image" class="btn btn-secondary">Choose Image</span>
                        <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$model->{$field}"/>
                    </div>
                    <div class="form-group mb-3">
                        <x-Inputs.text-field name="list_display_order" label="Display Order For Drop Downs"
                            class="validate-int" :value="$model->list_display_order" placeholder="Enter 1" autocomplete="off" />
                    </div>
                    <div class="form-group mb-3">
                        <x-Inputs.checkbox name="is_active" label="Active" :value="$model->is_active" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xxl-8">
                    <x-Backend.form-common-footer-buttons />
                </div>
            </div>
        </div>
        
    </div>
            
    </form>

    <script>
        $(function(){
            $("#document_file_id").browseGallery({
                opener: "#choose-image",
                postfix: "choose-image",
                type: "image",
                index_table_id: "document_file_image"
            });
        });
    </script>
@endsection
