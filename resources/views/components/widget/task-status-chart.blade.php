<div id="task-status-chart" class="chart-block h-300"></div>

<script>
    $(function() {
        var records = JSON.parse('<?= json_encode($records) ?>');
        $.events.execAtLast(function() {

            am5.ready(function() {

                var root = am5.Root.new("task-status-chart");

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
                        valueField: "value",
                        categoryField: "label",
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
