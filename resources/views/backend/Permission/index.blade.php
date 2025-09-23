@extends($layout)

@section('content')
<<<<<<< HEAD

<?php
    $page_header_links = [
        ["title" => "Assign", "url" => route($routePrefix . ".assign")]
    ];
?>

@include($partial_path . ".page_header")

<div class="card">
    <div class="card-body new-btn-showcase">
        <form method="GET" action="{{ route($routePrefix . '.index') }}">
            <div class="row mb-4">
                <div class="col-md-3">
                    <x-Inputs.drop-down name="section_name" 
                        label="Sections" 
                        :list="$section_list" 
                        :value="$search['section_name']" 
                        class="select2" 
                        />
                </div>
                <div class="col-md-3">
                    <x-Inputs.text-field name="action_name" :value="$search['action_name']" label="Action" />
                </div>
                <div class="col-md-3">
                    <x-Inputs.drop-down name="role_id" 
                        label="Roles" 
                        :list="$role_list"
                        :value="$search['role_id']" 
                        class="select2" 
                        />
=======
    <?php
    $page_header_links = [['title' => 'Assign', 'url' => route($routePrefix . '.assign')]];
    ?>

    @include($partial_path . '.page_header')

    <div class="card">
        <div class="card-body new-btn-showcase">
            <x-Form.start action="{{ route($routePrefix . '.index') }}" class="form-get-encode-before-submit" />
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
                    <x-Inputs.drop-down name="role_id" label="Roles" :list="$role_list" :value="$search['role_id']" class="select2" />
>>>>>>> base_v3
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-sm-3">
                    <div>
                        <button type="submit" class="btn btn-primary w-md">Search</button>
                        <span class="btn btn-light text-dark w-md clear_form_search_conditions">Clear</span>
                    </div>
                </div>
            </div>
<<<<<<< HEAD
        </form>
    </div>
</div>

@if(isset($records))
<div class="card">
    <div class="card-body">
        <table class="table table-striped table-bordered table-hover mb-0 sr-data-table">
            <thead>
                <tr>
                    <th class="text-center" data-sr-data-table-search-clear="1">#</th>
                    <th data-sr-data-table-search="1">Section</th>
                    <th data-sr-data-table-search="1">Action</th>
                    <th data-sr-data-table-search="1">Role</th>                    
                    <th style="width: 8%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $i => $record)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $record['section'] ?? "" }}</td>
                    <td>{{ $record['action'] ?? "" }}</td>
                    <td>{{ $record['role']['name'] }}</td>                    
                    <td>
                        @if ($record['can_be_delete'])
                        <button class="btn btn-danger delete-btn" data-id="{{ $record['id'] }}">
                            <i class="bx bx-trash label-icon"></i>
                        </button>
                        <div class="spinner-border text-primary m-1" style="vertical-align:middle; display:none">
                            <span class="sr-only">Loading...</span>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<script type="text/javascript">
    $(document).ready(function() {
        $("button.delete-btn").click(function() {
            var _tr = $(this).closest("tr");
            
            $(this).parent().find(".spinner-border").show();

            var id = $(this).data("id");

            var url = "{{ route($routePrefix . '.ajax_delete') }}";

            var requestJson = {
                id: id
            };

            $.post(url, requestJson, function(response) {
                ajaxHandleResponse(url, response, function(responseJson) {
                    _tr.fadeOut();                   
                });
            });

            return false;
        });
    });
</script>

@endsection
=======
            </form>
        </div>
    </div>

    @if (isset($records))
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-bordered table-hover mb-0 sr-data-table">
                    <thead>
                        <tr>
                            <th class="text-center" data-sr-data-table-search-clear="1">#</th>
                            <th data-sr-data-table-search="1">Section</th>
                            <th data-sr-data-table-search="1">Role</th>
                            <th data-sr-data-table-search="1">Routes</th>
                            <th style="width: 8%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $i => $record)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $record['section'] }}</td>
                                <td>{{ $record['role'] }}</td>
                                <td>                                    
                                    {!! implode("<br/>", $record['route_list']) !!}
                                </td>
                                <td>
                                    @if ($record['can_be_delete'])
                                        <button class="btn btn-danger delete-btn" data-id="{{ implode(",", array_keys($record['route_list'])) }}">
                                            <i class="<?= ICON_SUMMARY_DELETE ?>"></i>
                                        </button>
                                        <div class="spinner-border text-primary m-1"
                                            style="vertical-align:middle; display:none">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <script type="text/javascript">
        $(document).ready(function() {
            $("button.delete-btn").click(function() {
                var _tr = $(this).closest("tr");

                $(this).parent().find(".spinner-border").show();

                var id = $(this).data("id");

                var url = "{{ route($routePrefix . '.ajax_delete') }}";

                var requestJson = {
                    id: id
                };

                $.post(url, requestJson, function(response) {
                    ajaxHandleResponse(url, response, function(responseJson) {
                        _tr.fadeOut();
                    });
                });

                return false;
            });

            $("form.form-get-encode-before-submit").submit(function(e) {
                e.preventDefault();

                var data = $(this).serializeArray();

                var method = $(this).attr("method");
                var action = $(this).attr("action");

                add_form_to_body_and_submit(method, action, data);

                return false;
            });
        });
    </script>
@endsection
>>>>>>> base_v3
