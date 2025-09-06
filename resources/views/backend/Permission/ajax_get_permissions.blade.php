@extends($layout)

@section('content')
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
