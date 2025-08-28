@extends($layout)

@section('content')
    <div class="card">
        <div class="card-header card-no-border p-1">
            <div class="d-md-flex d-sm-block align-items-center">
                <div class="flex-grow-1">
                    <form method="GET" class="summary_search" action="{{ route($current_route_name) }}"
                        data-load_html_at="#{{ $index_table_id }}">
                        <input type="hidden" name="is_sort_clear" value="1" />
                        <input type="hidden" name="value" value="{{ $selected_value ?? '' }}" />
                        <div class="row m-1">
                            
                            <div class="col-md-12 col-lg-6 col-xxxl-3 mb-2 select2-small">
                                <?php
                                $field = 'type';
                                $label = "Type";
                                ?>
                                <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]"
                                    :list="$type_list" class="select2 form-select-sm" />
                            </div>
                            <div class="col-md-6 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'name';
                                $label = "Name";
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]"  class="form-control-sm" />
                            </div>
                            <div class="col-md-6 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'mobile';
                                $label = "Mobile";
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]"  class="form-control-sm" />
                            </div>
                            <div class="col-md-6 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'mobile2';
                                $label = "Mobile2";
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]"  class="form-control-sm"/>
                            </div>
                            <div class="col-md-6 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'email';
                                $label = "Email";
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]"  class="form-control-sm"/>
                            </div>
                            
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <label class="form-label">Sort</label>
                                <div class="d-flex">
                                    <?php
                                    $field = 'sort_by';
                                    $label = "Sort By";
                                    ?>
                                    <x-Inputs.drop-down :name="$field" :label="$false" :value="$sort_by"
                                        :list="$sort_by_list" class="input-inline form-select-sm" />
                                    <?php
                                    $field = 'sort_dir';
                                    $label = "Sort Direction";
                                    ?>
                                    <x-Inputs.drop-down :name="$field" :label="$false" :value="$sort_dir"
                                        :list="$sort_dir_list" class="input-inline form-select-sm" :pleaseSelect="$false" />
                                </div>
                            </div>
                        </div>
                        <x-Backend.summary-search-form-footer :showPaginationDropDown="$false" />
                    </form>

                </div>
                <div class="text-end">
                    <div class="d-inline-flex m-1">
                        @foreach($type_list as $route_prefix => $label)
                        <a target="_blank" href="{{ route($route_prefix . ".create") }}" class="btn btn-outline-secondary ml-2" >
                            Add {{ $label }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body file-manager">
            <div id="{{ $index_table_id }}" class="index_table">
                @include($viewPrefix . '.chooser_records')
            </div>
        </div>
    </div>

    <script>
        $(function(){
            @if(!$multiple)
            $("input.contact").change(function()
            {
                if (this.checked)
                {
                    $("input.contact").prop("checked", false);
                    $(this).prop("checked", true);
                }
            });
            @endif
        });
    </script>
@endsection
