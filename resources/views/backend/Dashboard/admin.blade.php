@extends($layout)

@section('content')
    <div class="container-fluid">
        @include($partial_path . '.page_header')

        <div class="row general-widget">
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <h3>Tasks</h3>
                        <p class="mb-0 mt-1">Data Since {{ if_date($last_year_date) }}</p>
                        <div class="card-header-right">
                            <ul class="list-unstyled card-option" style="width: 230px;">
                                <li><i class="fa-solid fa-maximize full-card"></i></li>
                                <li><i class="fa-solid fa-minus minimize-card"></i></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                @foreach ($task_figures as $task_figure => $arr)
                                    <div class="card small-widget bg-light-primary">
                                        <div class="card-body primary">
                                            <span class="f-light">
                                                {{ $arr['label'] }}
                                            </span>
                                            <div class="d-flex align-items-end gap-1">
                                                <h4>
                                                    {{ $arr['value'] }}
                                                </h4>
                                            </div>
                                            <div class="bg-gradient">
                                                <svg class="stroke-icon svg-fill">
                                                    <use href="/assets/svg/icon-sprite.svg#new-order"></use>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-lg-6">
                                <div id="task-chart" class="chart-block h-300"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        $(function() {
            var task_figures = JSON.parse('<?= json_encode(array_values($task_figures)); ?>');
            $.events.execAtLast(function() {

                am5.ready(function() {

                    var root = am5.Root.new("task-chart");

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

                    console.log(task_figures);

                    series.data.setAll(task_figures);

                    series.appear(1000, 100);

                }); // end am5.ready()
            })
        })
    </script>
@endsection
