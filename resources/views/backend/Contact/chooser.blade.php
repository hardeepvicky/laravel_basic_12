@extends($layout)

@section('content')
    <div class="card">
        <div class="card-header card-no-border p-1">
            <div class="hidden">
                <div>
                    <form method="GET" class="summary_search" action="{{ route($current_route_name) }}"
                        data-load_html_at="#{{ $index_table_id }}">
                        <input type="hidden" name="is_sort_clear" value="1" />
                        <input type="hidden" name="value" value="{{ $selected_value ?? '' }}" />
                        <?php 
                            $field = "type";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                        
                        <?php 
                            $field = "name";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                        
                        <?php 
                            $field = "state_id";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                        
                        <?php 
                            $field = "city_id";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                        
                        <?php 
                            $field = "mobile";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                        
                        <?php 
                            $field = "mobile2";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                        
                        <?php 
                            $field = "email";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                    </form>
                </div>
            </div>

            <div class="d-inline-flex m-1">
                @foreach($type_list as $route_prefix => $label)
                <a target="_blank" href="{{ route($route_prefix . ".create", ['refer_from' => 'contact_chooser']) }}" class="btn btn-outline-secondary ml-2" >
                    Add {{ $label }}
                </a>
                @endforeach
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

            $('.modal-select2').select2({
                placeHolder: "All",
                theme: "bootstrap-5",
                dropdownParent: $("#" + '<?= $modal_id ?>')
            });
        });
    </script>
@endsection
