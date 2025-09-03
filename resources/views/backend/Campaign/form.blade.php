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
                                $field = 'name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" layout="horizontal" mandatory="true" />
                                    
                                <?php
                                $field = 'start_date';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}" class="date-picker"
                                    placeholder="Enter {{ $label }}"  layout="horizontal" mandatory="true" />

                                <?php
                                $field = 'end_date';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}" class="date-picker" layout="horizontal"
                                    placeholder="Enter {{ $label }}" />

                                <?php
                                $field = 'description';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-area :id="$field" :name="$field" :label="$label"
                                    rows="3" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" class="auto-suggesstion-list"  layout="horizontal" />

                            <div class="row mb-3">
                                <?php
                                    $field = 'campaignFile';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    $value = implode(',', $model->campaignFile?->pluck('document_file_id')->toArray());
                                ?>
                                <div class="col-sm-3">
                                    <label class="form-label">
                                        {{ $label }}
                                    </label>
                                </div>
                                <div class="col-sm-9">
                                    <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                        :value="$value" multiple="multiple" />
                                    <span id="files-choose" class="btn btn-secondary">Choose Files</span>
                                </div>
                            </div>

                            <?php
                            $field = 'list_display_order';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field type="number" :name="$field" :label="$label"
                                value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}"
                                    layout="horizontal"
                                mandatory="true" autocomplete="off" />


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
            </div>

            <x-Backend.form-common-footer-buttons />
        </div>
    </div>
    </form>

    <script>
        $(function() {
            $("#campaignFile").browseGallery({
                opener: "#files-choose"
            });
        })
    </script>
@endsection
