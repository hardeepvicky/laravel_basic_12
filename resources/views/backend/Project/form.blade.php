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
                            <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                placeholder="Enter {{ $label }}"
                                layout="horizontal"
                                mandatory="true" />
                          
                            <?php
                                $field = 'description';
                                $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <label class="form-label">
                                        {{ $label }}
                                    </label>
                                </div>
                                <div class="col-sm-9">
                                    <div class="textarea-wrapper">
                                        <x-Inputs.text-area :id="$field" :name="$field" :label="$false"
                                            rows="3" :value="$model->{$field}"
                                            placeholder="Enter {{ $label }}" class="auto-suggesstion-list" />
                                        <div id="{{ $field . '-suggestion-box' }}" class="suggestion-box hidden"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'projectFile';
                                $label = "Files";
                                $value = implode(',', $model->projectFile?->pluck('document_file_id')->toArray() ?? []);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                    :value="$value" multiple="multiple" />
                                <span id="files-choose" class="btn btn-secondary">Choose Files</span>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h4 class="b-b-info">
                                        Targets
                                    </h4>
                                    <table class="table table-bordered order-column template-table"
                                        data-sr-table-template-min-row="0">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width : 50px;">
                                                    <span class="sr-table-template-add">
                                                        <i class="fas fa-plus-circle text-success icon"></i>
                                                    </span>
                                                </th>
                                                <th style="width:60%">
                                                    Target
                                                </th>
                                                <th style="width:20%">
                                                    {{ get_value_from_list($target_field_labels, 'end_date') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="sr-table-template-row hidden">
                                                <td>
                                                    <div class="block">
                                                        <div class="left-block">
                                                            <div class="sr-table-template-move-up">
                                                                <i class="fas fa-arrow-circle-up icon"></i>
                                                            </div>
                                                            <div class="sr-table-template-move-down">
                                                                <i class="fas fa-arrow-circle-down icon"></i>
                                                            </div>
                                                        </div>
                                                        <div class="right-block">
                                                            <span class="sr-table-template-delete">
                                                                <i class=" fas fa-times-circle text-danger icon"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <x-inputs.text-field type="hidden" name="project_target[(sr-id)][id]" label="" />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'name';
                                                    ?>
                                                    <x-inputs.text-field name="project_target[(sr-id)][{{ $field }}]" :label="$false" class="will-required" />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'end_date';
                                                    ?>
                                                    <x-inputs.text-field name="project_target[(sr-id)][{{ $field }}]" :label="$false"  class="will-date-picker"/>
                                                </td>
                                            </tr>
                                            @php
                                                $project_target = old('project_target',$model->projectTarget?->toArray() ?? []);
                                                unset($project_target['(sr-id)']);
                                                // d($project_target, true);
                                            @endphp
                                            @if (isset($project_target))
                                                @foreach ($project_target as $k => $child_arr)
                                                    <tr class="" sr-id="{{ $child_arr['id'] }}" sr-counter="{{ $k }}">
                                                        <td>
                                                            <div class="block">
                                                                <div class="left-block">
                                                                    <div class="sr-table-template-move-up">
                                                                        <i class="fas fa-arrow-circle-up icon"></i>
                                                                    </div>
                                                                    <div class="sr-table-template-move-down">
                                                                        <i class="fas fa-arrow-circle-down icon"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="right-block">
                                                                    <span class="sr-table-template-delete">
                                                                        <i
                                                                            class=" fas fa-times-circle text-danger icon"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="project_target[{{ $k }}][id]" value="{{ $child_arr['id'] ?? '' }}" />
                                                        </td>                                                        
                                                        <td>
                                                            <?php
                                                            $field = 'name';
                                                            $value = $child_arr[$field] ?? '';
                                                            ?>
                                                            <x-inputs.text-field name="project_target[{{ $k }}][{{ $field }}]" :label="$false" :value="$value" class="will-required" />
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $field = 'end_date';
                                                            $value = $child_arr[$field] ?? '';
                                                            ?>
                                                            <x-inputs.text-field name="project_target[{{ $k }}][{{ $field }}]" :label="$false" :value="$value" class="date-picker"/>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th class="text-center" style="width : 80px;">
                                                    <span class="sr-table-template-add">
                                                        <i class="fas fa-plus-circle text-success icon"></i>
                                                    </span>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <?php
                                $field = 'list_display_order';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field type="number" layout="horizontal" :name="$field" :label="$label"
                                    :value="$model->{$field}" placeholder="Enter {{ $label }}"
                                    mandatory="true" autocomplete="off" />
                                    
                            <div class="row">
                                <div class="col-sm-3">
                                    
                                </div>
                                <div class="col-sm-9" style="padding-top: 30px">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'is_active';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.checkbox :name="$field" :label="$label"
                                            :value="$model->{$field}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(request()->has('refer_from'))
            <div class="form-buttons mb-4">
                <button type="submit" class="btn btn-primary" name="save_and_close" value="1">Submit & Close</button>
            </div>
            @else
                <x-Backend.form-common-footer-buttons />
            @endif
        </div>
    </div>
    </form>

    <script>
        $(function() {
            suggestion_list($("#description"), $("#description-suggestion-box"), [], "/tags/ajax_get_list");

            $(".template-table").srTableTemplate({
                brace_type: "(",
                afterRowAdd(_table, last_id, _tr) {
                    _tr.find(".will-select2").select2({
                        placeHolder: "Please Select",
                        theme: "bootstrap-5",
                    });

                    _tr.find(".will-required").attr("required", true);

                    _tr.find(".will-date-picker").flatpickr({
                        dateFormat: constants.dateFormat
                    });
                }
            });

            $("#projectFile").browseGallery({
                opener: "#files-choose"
            });

            $.events.execAtLast(function() {
            });
        })
    </script>
@endsection
