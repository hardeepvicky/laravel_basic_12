<div class="card general-widget">
    <div class="card-header card-no-border pb-0">
        <h3>Stock & Inventory</h3>
        <div class="card-header-right">
            <ul class="list-unstyled card-option" style="width: 230px;">
                <li><i class="fa-solid fa-maximize full-card"></i></li>
                <li><i class="fa-solid fa-minus minimize-card"></i></li>
            </ul>
        </div>
    </div>

    <div class="card-body">
        <div wire:loading class="text-center text-lg p-8">
            <i class="fas fa-spinner fa-spin"></i> Loading data...
        </div>
        @if($data)
            <div wire:loading.remove>
                <div class="row">
                    <div class="col-md-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3>Product Stock</h3>
                            </div>
                            <div class="card-body">
                                <div id="stock-chart" class="chart-block h-300"></div>
                                <script>
                                    $(function() {
                                        var records = JSON.parse('<?= json_encode($data['stocks']) ?>');
                                        $.events.execAtLast(function() {

                                            am5.ready(function() {

                                                var root = am5.Root.new("stock-chart");

                                                root.setThemes([
                                                    am5themes_Animated.new(root)
                                                ]);

                                                var chart = root.container.children.push(
                                                    am5percent.PieChart.new(root, {
                                                        endAngle: 270
                                                    })
                                                );

                                                var series = chart.series.push(
                                                    am5percent.PieSeries.new(root, {
                                                        valueField: "sum_qty",
                                                        categoryField: "name",
                                                        endAngle: 270
                                                    })
                                                );

                                                series.states.create("hidden", {
                                                    endAngle: -90
                                                });

                                                series.data.setAll(records);

                                                series.appear(1000, 100);

                                            }); // end am5.ready()
                                        })
                                    })
                                </script>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3>Item Inventory Unit Wise</h3>
                            </div>
                            <div class="card-body">
                                @php $i = 0; @endphp
                                @foreach($data['inventory'] as $unit => $records)
                                @php $i++; @endphp
                                <div id="inventory-chart-{{ $i }}" class="chart-block h-300"></div>
                                <script>
                                    $(function() {
                                        var records = JSON.parse('<?= json_encode($records) ?>');
                                        $.events.execAtLast(function() {

                                            am5.ready(function() {

                                                var root = am5.Root.new("inventory-chart-{{ $i }}");

                                                root.setThemes([
                                                    am5themes_Animated.new(root)
                                                ]);

                                                var chart = root.container.children.push(
                                                    am5percent.PieChart.new(root, {
                                                        endAngle: 270
                                                    })
                                                );

                                                var series = chart.series.push(
                                                    am5percent.PieSeries.new(root, {
                                                        valueField: "sum_qty",
                                                        categoryField: "name",
                                                        endAngle: 270
                                                    })
                                                );

                                                series.states.create("hidden", {
                                                    endAngle: -90
                                                });

                                                series.data.setAll(records);

                                                series.appear(1000, 100);

                                            }); // end am5.ready()
                                        })
                                    })
                                </script>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>