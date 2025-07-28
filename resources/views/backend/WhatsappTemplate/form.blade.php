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
                        <div class="row">
                            <div class="col-md-12 col-xxl-6">
                                <?php
                                $field = 'name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$model->{$field}"
                                    placeholder="Enter {{ $label }}" mandatory="true" />
                            </div>
                            <div class="col-md-12 col-xxl-6">
                                <?php
                                $field = 'language';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$model->{$field}"
                                    placeholder="Enter {{ $label }}" mandatory="true" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <table class="table table-sm table-striped table-bordered order-column template-table"
                            data-sr-table-t emplate-min-row="0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width : 80px;">
                                        <span class="sr-table-template-add">
                                            <i class="fas fa-plus-circle text-success icon"></i>
                                        </span>
                                    </th>
                                    <th style="width : 90%;">Default Header</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="sr-table-template-row hidden">
                                    <td>
                                        <div class="block">
                                            <div class="left-block">

                                            </div>
                                            <div class="right-block">
                                                <span class="sr-table-template-delete">
                                                    <i class=" fas fa-times-circle text-danger icon"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <x-inputs.text-field name="defaultHeaderValues[(sr-id)]" label=""/>
                                    </td>                                    
                                @php
                                    $defaultHeaderValues = old('defaultHeaderValues', $model->default_header_values ?? []);
                                    unset($defaultHeaderValues['(sr-id)']);
                                @endphp
                                @if (isset($defaultHeaderValues))
                                    @foreach ($defaultHeaderValues as $k => $arr)
                                        <tr class="" sr-id="{{ $arr['id'] }}"
                                            sr-counter="{{ $k }}">
                                            <td>
                                                <div class="block">
                                                    <div class="left-block">
                                                    </div>
                                                    <div class="right-block">
                                                        <span class="sr-table-template-delete">
                                                            <i class=" fas fa-times-circle text-danger icon"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php $value = $arr['default_header_values']; ?>
                                                <x-inputs.text-field name="defaultHeaderValues[{{ $k }}]"
                                                    label="" :value="$value"/>
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
                    <div class="form-group mb-3">
                        <table class="table table-sm table-striped table-bordered order-column template-table"
                            data-sr-table-t emplate-min-row="0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width : 80px;">
                                        <span class="sr-table-template-add">
                                            <i class="fas fa-plus-circle text-success icon"></i>
                                        </span>
                                    </th>
                                    <th style="width : 90%;">Default Body</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="sr-table-template-row hidden">
                                    <td>
                                        <div class="block">
                                            <div class="left-block">

                                            </div>
                                            <div class="right-block">
                                                <span class="sr-table-template-delete">
                                                    <i class=" fas fa-times-circle text-danger icon"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <x-inputs.text-field name="defaultBodyValues[(sr-id)]" label=""/>
                                    </td>                                    
                                @php
                                    $defaultBodyValues = old('defaultBodyValues', $model->default_body_values ?? []);
                                    unset($defaultBodyValues['(sr-id)']);
                                @endphp
                                @if (isset($defaultBodyValues))
                                    @foreach ($defaultBodyValues as $k => $arr)
                                        <tr class="" sr-id="{{ $arr['id'] }}"
                                            sr-counter="{{ $k }}">
                                            <td>
                                                <div class="block">
                                                    <div class="left-block">
                                                    </div>
                                                    <div class="right-block">
                                                        <span class="sr-table-template-delete">
                                                            <i class=" fas fa-times-circle text-danger icon"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php $value = $arr['default_body_values']; ?>
                                                <x-inputs.text-field name="defaultBodyValues[{{ $k }}]"
                                                    label="" :value="$value"/>
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
                    <div class="form-group mb-3">
                        <table class="table table-sm table-striped table-bordered order-column template-table"
                            data-sr-table-t emplate-min-row="0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width : 80px;">
                                        <span class="sr-table-template-add">
                                            <i class="fas fa-plus-circle text-success icon"></i>
                                        </span>
                                    </th>
                                    <th style="width : 90%;">Default Button</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="sr-table-template-row hidden">
                                    <td>
                                        <div class="block">
                                            <div class="left-block">

                                            </div>
                                            <div class="right-block">
                                                <span class="sr-table-template-delete">
                                                    <i class=" fas fa-times-circle text-danger icon"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <x-inputs.text-field name="defaultButtonValues[(sr-id)]" label=""/>
                                    </td>                                    
                                @php
                                    $defaultButtonValues = old('defaultButtonValues', $model->default_button_values ?? []);
                                    unset($defaultButtonValues['(sr-id)']);
                                @endphp
                                @if (isset($defaultButtonValues))
                                    @foreach ($defaultButtonValues as $k => $arr)
                                        <tr class="" sr-id="{{ $arr['id'] }}"
                                            sr-counter="{{ $k }}">
                                            <td>
                                                <div class="block">
                                                    <div class="left-block">
                                                    </div>
                                                    <div class="right-block">
                                                        <span class="sr-table-template-delete">
                                                            <i class=" fas fa-times-circle text-danger icon"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php $value = $arr['default_button_values']; ?>
                                                <x-inputs.text-field name="defaultButtonValues[{{ $k }}]"
                                                    label="" :value="$value"/>
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
        $(function() {
            $.events.execAtLast(function() {
                $(".template-table").srTableTemplate({
                    brace_type: "(",
                });
            });
        })
    </script>
@endsection
