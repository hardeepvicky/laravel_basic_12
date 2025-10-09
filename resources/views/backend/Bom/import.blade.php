@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start method="POST" />
    <div class="row">
        <div class="col-xxl-10">
            <div class="card">
                <div class="card-header card-no-border pb-0">

                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-xxl-6">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'file';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.file-field :id="$field" :name="$field" label="File (Excel)"/>
                                    </div>
                                </div>
                                <div class="col-md-4" style="padding-top:30px">
                                    <a target="_blank" href="{{ $sample }}">Sample Excel</a>
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
@endsection