@extends($layout)

@section('content')
    <?php
    $page_header_links = [];
    ?>

    @include($partial_path . '.page_header')


    <div class="card">
        <div class="card-body">
            <form method="GET" class="summary_search">
                <input type="hidden" name="is_sort_clear" value="1" />
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-4 col-xxxl-3 mb-2">
                        <label class="form-label">
                            Sections
                        </label>
                        <select name="section_list_key" class="form-select select2">
                            <option value="">Please Select</option>
                            @foreach ($section_list as $t => $k)
                                @php
                                    $attr = $t == $search['section_list_key'] ? 'selected="selected"' : '';
                                @endphp
                                <option value="{{ $t }}" {!! $attr !!}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>                    
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <x-Inputs.drop-down name="role_id" label="Role" :list="$role_list" :value="$search['role_id']" class="select2" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <x-Inputs.drop-down name="type" label="Action Type" :list="$type_list" :value="$search['type']" class="select2" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <x-inputs.text-field id="from_date" name="from_date" class="form-control date-picker"
                            label="From Date" :value="$search['from_date']" autocomplete="off" data-date-end="input#to_date" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <x-inputs.text-field id="to_date" name="to_date" class="form-control date-picker"
                            label="To Date" :value="$search['to_date']" autocomplete="off"
                            data-date-start="input#from_date" />
                    </div>
                </div>
                <x-Backend.summary-search-form-footer :selectedPaginationLimit="$pagination_limit" />
            </form>
        </div>
    </div>

    <div id="index_table">
        @include($viewPrefix . '.permission_change_logs_index_table')
    </div>
@endsection
