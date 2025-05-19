@extends('layout.base_layout')

@section('head')
    <script src="{{asset('/js/chart.umd.min.js')}}"></script>
    <script src="{{asset('/js/chartjs-plugin-datalabels@2.js')}}"></script>
    <script>
        Chart.register(ChartDataLabels);
        Chart.defaults.set('plugins.datalabels', {
            color: '#FE777B'
        });
    </script>
@endsection

@section('content')
    <span id="total-chart-stat" class="px-0 mb-2">@include('test_result.chart')</span>
    <div class="row">
        <div class="col-4" style="width:500px;height:100%">
            <canvas id="groupsChart" style="width:100%;max-width:500px; height:100%;"></canvas>
        </div>
        <div class="col-8 ms-4">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-charts-tab" data-bs-toggle="tab" data-bs-target="#nav-charts" type="button" role="tab" aria-controls="nav-charts" aria-selected="true">Fails Charts</button>
                    <button class="nav-link" id="nav-all-app-defects-tab" data-bs-toggle="tab" data-bs-target="#nav-all-app-defects" type="button" role="tab" aria-controls="nav-all-app-defects" aria-selected="false">App Defects</button>
                    <button class="nav-link" id="nav-test-defects-tab" data-bs-toggle="tab" data-bs-target="#nav-test-defects" type="button" role="tab" aria-controls="nav-test-defects" aria-selected="false">Test Defects</button>
                    <button class="nav-link" id="nav-group-defects-tab" data-bs-toggle="tab" data-bs-target="#nav-group-defects" type="button" role="tab" aria-controls="nav-group-defects" aria-selected="false">Defects in Group</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-charts" role="tabpanel" aria-labelledby="nav-charts-tab">
                    @include('statistics.charts')
                </div>
                <div class="tab-pane fade" id="nav-all-app-defects" role="tabpanel" aria-labelledby="nav-all-app-defects-tab">
                    @include('statistics.all_app_defects')
                </div>
                <div class="tab-pane fade" id="nav-test-defects" role="tabpanel" aria-labelledby="nav-test-defects-tab">

                </div>
                <div class="tab-pane fade" id="nav-group-defects" role="tabpanel" aria-labelledby="nav-group-defects-tab">
                    @include('statistics.defects_in_group')
                </div>
            </div>

        </div>
    </div>


@endsection