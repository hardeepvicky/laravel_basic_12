@extends($layout)

@section('content')
    <?php
    $page_header_links = $empty_list = [];

    ?>

    @include($partial_path . '.page_header')

    <div class="card">
        <div class="card-body">
            <x-Form.start method="POST" action="{{ route($current_route_name) }}" />
                <div class="row mb-4">
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php
                        $field = 'model_class';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.drop-down :id="$field"  :name="$field" :label="$label" value="{{ $model_class ?? '' }}"  :list="$model_list" 
                            class="select2 cascade-drop-down" 
                            data-sr-cascade-target="#model_field"
                            data-sr-cascade-url="/change-history/ajax_get_model_fields/{v}" mandatory="true"
                            />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php
                        $field = 'model_field';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.drop-down :id="$field" :name="$field" label="Field"  value="{{ $model_field ?? '' }}" :list="$empty_list"
                            class="select2" mandatory="true"/>
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php
                        $field = 'model_id';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.text-field :id="$field"  :name="$field" :label="$label" value="{{ $model_id ?? '' }}"   mandatory="true"/>
                    </div>
                </div>
                <x-Backend.summary-search-form-footer :showPaginationDropDown="$false" />
            </form>
        </div>
    </div>

    <div class="m-1">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr class="border-bottom-primary">
                    <th>Sr.No.</th>
                    <th>Value</th>
                    <th>Type</th>
                    <th>Change At</th>
                    <th>Change By</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($change_history as $i => $history)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $history['current'] }}</td>
                    <td>{{ $history['type'] }}</td>
                    <td>{{ $history['created_at_user_tz'] }}</td>
                    <td>{{ get_value_from_list($userListCache, $history['created_by']) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        $(function(){

            $.events.execAtLast(function(){
                $('.cascade-drop-down').trigger("change", {
                    pageLoad : true
                });
            });
        });
    </script>
@endsection
