@extends($layout)

@section('content')
<<<<<<< HEAD
<div class="row">
    <div class="col-sm-6">
        <label class="pointer">
            <input type="checkbox" id="select-all">
            <b>Select All</b>
        </label>
    </div>
    <div class="col-sm-6 text-right">
        <button type="submit" class="btn btn-primary w-md">Submit</button>
    </div>
</div>

<table class="table table-striped table-bordered table-hover mt-2 mb-2">
    <thead>
        <tr>
            <th class="text-center">#</th>
            <th>Section</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @php
            $i = 0;
        @endphp
        @foreach($sections as $section_name => $actions)
            @php
                $section_name = trim($section_name);
                $section_name_str = str_replace(" ", "_", $section_name);
                
                $a = 0;
            @endphp
            
            @foreach($actions as $action_name => $action_arr)
                @php
                    $a++;
                    $i++;
                    $tr_cls = $a == 1 ? "first-row" : "";
                @endphp
                <tr class="{{ $tr_cls }}">
                    <td class="text-center">{{ $i }}</td>
                    <td>
                        <?php if ($a == 1): ?>
                            <label class="pointer">
                                <input type="checkbox"                                     
                                    class="chk-select-all aco_section" 
                                    data-sr-chkselect-children="input.section-<?= $section_name_str ?>">
                                <b><?= $section_name ?></b>
                            </label>
                        <?php else: ?>
                            <?= $section_name ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <label class="pointer">
                            <input type="checkbox"     
                                <?= $action_arr['is_checked'] ? "checked" : "" ?>
                                class="section-<?= $section_name_str ?> aco_action" 
                                name="data[<?= $section_name ?>][]" 
                                value="<?= $action_name ?>"
                                />
                            <?= $action_name ?>
                        </label>
                    </td>                            
                </tr>
            @endforeach    
        @endforeach    
    </tbody>
</table>

<div class="row justify-content-center mt-2">
    <div class="col-sm-3">
        <div>
            <button type="submit" class="btn btn-primary w-md">Submit</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function()
    {
        $("#select-all").change(function()
        {
            $("input.aco_section").prop("checked", $(this).prop("checked"));
            $("input.aco_section").trigger("sr-chkselect.childcheck");
        });
    });
</script>

@endsection
=======
    <link rel="stylesheet" type="text/css"
        href="{{ get_asset_url('assets/libs/jstree/dist/themes/default/style.min.css') }}" />

    <div id="hidden_routes">

    </div>

    <div id="jstree">
    </div>

    <div class="row justify-content-center mt-2">
        <div class="col-sm-3">
            <div>
                <button type="submit" class="btn btn-primary w-md">Submit</button>
            </div>
        </div>
    </div>

    <script src=" {{ get_asset_url('assets/libs/jstree/dist/jstree.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var sections = JSON.parse('<?= json_encode($sections) ?>');

            $("#jstree").jstree({
                'core': {
                    "check_callback": true,
                    'data': sections
                },
                "plugins": ["checkbox"]

            }).bind("ready.jstree", function(event, data) {

                $(this).jstree("open_all");

            }).on('changed.jstree', function(e, data) {

                var routes = [];

                var i, j, r = [];
                for (i = 0, j = data.selected.length; i < j; i++) {
                    var node = data.instance.get_node(data.selected[i], true);

                    if (node.length > 0)
                    {
                        var r = node.data("routes");

                        if (r)
                        {
                            routes.push(r);
                        }
                    }
                }

                var html = "";

                routes.forEach(function(route_name)
                {
                    html += '<input type="hidden" name="route_names[]" value="' + route_name + '" />';
                });

                console.log(routes);

                $("#hidden_routes").html(html);
            })
        });
    </script>
@endsection
>>>>>>> base_v3
