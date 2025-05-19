@extends('layout.base_layout')

@section('head')
    <link href="{{asset('/css/bootstrap-table.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="/css/jquery.resizableColumns.min.css">
    <script src="{{asset('/js/table-ext/jquery.resizableColumns.min.js')}}"></script>
@endsection

@section('content')

    @include('layout.sidebar_nav')

    <div class="col">

        <div class="border-bottom my-3">
            <h3 class="page_title">
                Automation Test Runs

                @can(App\Enums\UserPermission::add_edit_test_runs)
                    <a class="mx-3" href="{{route("test_run_create_page", [$project->id, 1])}}">
                        <button type="button" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i> New Test Run
                        </button>
                    </a>
                @endcan
                <div class="buttons-toolbar float-end"></div>
            </h3>
        </div>

        <div class="row row-cols-1 row-cols-md-1 g-3">
            <table
                    id="table"
                    data-show-toggle="true"
                    data-show-columns="true"
                    data-show-refresh="true"
                    data-buttons-toolbar=".buttons-toolbar"
                    data-pagination="true"
                    data-page-size="15"
                    data-page-list="[15, 50, 100]"
                    data-page-number="1"
                    data-side-pagination="server"
                    data-url="{{Config::get('app.url')}}/api/project/{{$project->id}}/autotest-runs"
                    data-ajax-options="ajaxOptions"
                    data-total-rows="15"
                    data-show-custom-view="true"
                    data-custom-view="customViewFormatter"
                    data-show-custom-view-button="true"
                    data-resizable="true"
            >
                <thead>
                <tr>
                    <th data-field="id" data-width="50">
                        Id
                    </th>
                    <th data-field="title" data-sortable="true" data-formatter="titleFormatter" data-width="30" data-width-unit="%">
                        Title
                    </th>
                    <th data-field="created_at" data-width="160" data-sortable="true">
                        Start time
                    </th>
                    <th data-field="groups">
                        Groups
                    </th>
                    <th data-field="environment" data-sortable="true" data-width="10" data-width-unit="%">
                        Environment
                    </th>
                    <th data-field="creator">
                        Creator
                    </th>
                    <th data-field="status" data-formatter="statusFormatter">
                        Status
                    </th>
                </tr>
                </thead>
            </table>
        </div>

    </div>


    <template id="profileTemplate">
        <div class="col-4 mb-3">
            <div class="card base_block shadow h-100 rounded border">

                <div class="card-body d-flex justify-content-between ">
                    <div>
                        <a class="fs-4" href="%TEST_RUN_URL%">
                            <i class="bi bi-play-circle"></i> %TITLE%
                        </a>
                    </div>

                    <div style="min-width: 26%">
                        <div class="text-muted"
                              title="created at">%CREATED_AT% </div>
                        <small class="text-muted float-end"
                              title="created by">%CREATED_BY% </small>
                    </div>
                </div>

                <div class="border-top p-2">

                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: %STATUS_PASSED_PERC%%"
                             title="Passed">
                            %STATUS_PASSED_COUNT%
                        </div>

                        <div class="progress-bar bg-danger" role="progressbar" style="width: %STATUS_FAILED_PERC%%"
                             title="Failed">
                            %STATUS_FAILED_COUNT%
                        </div>

                        <div class="progress-bar bg-warning" role="progressbar" style="width: %STATUS_BLOCKED_PERC%%"
                             title="Blocked">
                            %STATUS_BLOCKED_COUNT%
                        </div>

                        <div class="progress-bar bg-secondary" role="progressbar"
                             style="width: %STATUS_NOT_TESTED_PERC%%" title="Not Tested">
                            %STATUS_NOT_TESTED_COUNT%
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </template>

    <script>
        window.ajaxOptions = {
            beforeSend (xhr) {
                xhr.setRequestHeader('custom-auth-token', 'custom-auth-token')
            },
            converters: {
                "text json": function(data) {
                    // console.log(data);
                    return JSON.parse(data).data;
                }

            }
        }
        window.statusFormatter = (value, row) => {

            return `<div class="progress">
                <div class="progress-bar bg-success" role="progressbar" style="width: ${value.passed[1]}%" title="Passed">
                    ${value.passed[0]}
                </div>

            <div class="progress-bar bg-danger" role="progressbar" style="width: ${value.failed[1]}%"
                 title="Failed">
                ${value.failed[0]}
            </div>

            <div class="progress-bar bg-warning" role="progressbar" style="width: ${value.blocked[1]}%"
                 title="Blocked">
                ${value.blocked[0]}
            </div>

            <div class="progress-bar bg-secondary" role="progressbar"
                 style="width: ${value.not_tested[1]}%" title="Not Tested">
                ${value.not_tested[0]}
            </div>
        </div>`
        }
        window.titleFormatter = (value, row) => {
            return `<a href="${row.url}">${value}</a>`
        }

        $(function() {
            $('#table').bootstrapTable({
                {{--data: [--}}
                {{--        @foreach($testRuns as $testRun)--}}
                {{--    {--}}
                {{--        id: '{{$testRun->id}}',--}}
                {{--        title: '{{$testRun->title}}',--}}
                {{--        created_at: '{{$testRun->created_at->format('d-m-Y H:i')}}',--}}
                {{--        creator: '{{$testRun->creator}}',--}}
                {{--        groups: '{{$testRun->groups}}',--}}
                {{--        env: '{{ucfirst(mb_strtolower(\App\Enums\Environment::from($testRun->environment)->name))}}',--}}
                {{--        url: '{{route('test_run_show_page', [$project->id, $testRun->id])}}',--}}
                {{--        status: <?= json_encode($testRun->getChartData()) ?>--}}
                {{--    },--}}
                {{--        @endforeach--}}
                {{--]--}}
            })
        })

        window.customViewFormatter = data => {
            const template = $('#profileTemplate').html()
            let view = ''

            $.each(data, function (i, row) {
                view += template.replace('%TITLE%', row.title)
                    .replace('%TEST_RUN_ID%', row.id)
                    .replace('%TEST_RUN_URL%', row.url)

                    .replace('%ENV%', row.env)
                    .replace('%CREATED_AT%', row.created_at)
                    .replace('%CREATED_BY%', row.creator)
                    .replace('%GROUPS%', row.groups)

                    .replace('%STATUS_PASSED_PERC%', row.status.passed[1])
                    .replace('%STATUS_PASSED_COUNT%', row.status.passed[0])

                    .replace('%STATUS_FAILED_PERC%', row.status.failed[1])
                    .replace('%STATUS_FAILED_COUNT%', row.status.failed[0])

                    .replace('%STATUS_BLOCKED_PERC%', row.status.blocked[1])
                    .replace('%STATUS_BLOCKED_COUNT%', row.status.blocked[0])

                    .replace('%STATUS_NOT_TESTED_PERC%', row.status.not_tested[1])
                    .replace('%STATUS_NOT_TESTED_COUNT%', row.status.not_tested[0])
            })

            return `<div class="row mx-0">${view}</div>`
        }
    </script>

@endsection

@section('footer')
    <script src="{{asset('/js/bootstrap-table.min.js')}}"></script>
    <script src="{{asset('/js/table-ext/bootstrap-table-custom-view.min.js')}}"></script>
    <script src="{{asset('/js/table-ext/bootstrap-table-resizable.min.js')}}"></script>
@endsection