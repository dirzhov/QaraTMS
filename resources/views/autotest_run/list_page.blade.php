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
                    data-unique-id="id"
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
                    <th data-field="job_status" data-formatter="runFormatter" data-width="80">
                        Run
                    </th>
                    <th data-field="title" data-sortable="true" data-formatter="titleFormatter" data-width="30" data-width-unit="%">
                        Title
                    </th>
                    <th data-field="version" data-sortable="true" data-width="100">
                        Version
                    </th>
                    <th data-field="created_at" data-width="160" data-sortable="true">
                        Start time
                    </th>
                    <th data-field="groups">
                        Groups
                    </th>
                    <th data-field="priorities" data-formatter="priorityFormatter" data-visible="false">
                        Priorities
                    </th>
                    <th data-field="environment" data-sortable="true" data-width="10" data-width-unit="%">
                        Environment
                    </th>
                    <th data-field="os" data-formatter="osFormatter" data-sortable="true" data-width="100" data-visible="true">
                        OS
                    </th>
                    <th data-field="device" data-formatter="deviceFormatter" data-sortable="true" data-width="100" data-visible="false">
                        Device
                    </th>
                    <th data-field="browser" data-formatter="browserFormatter" data-sortable="true" data-width="100" data-visible="false">
                        Browser
                    </th>
                    <th data-field="run_parameters" data-sortable="true" data-width="120" data-visible="false">
                        Run Parameters
                    </th>
                    <th data-field="creator" data-width="120" data-visible="false">
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
        const table = $('#table');

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

        function escapeString(str) {
            return str
                .replace(/\\/g, '\\\\')   // Escape backslashes
                .replace(/"/g, '\\"')     // Escape double quotes
                .replace(/\n/g, '\\n')    // Escape newlines
                .replace(/\r/g, '\\r')    // Escape carriage returns
                .replace(/\t/g, '\\t');   // Escape tabs
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

        window.priorityFormatter = (value, row) => {
            var result = '';
            if (value != null && value.length) {
                for (var p of value.split(',')) {
                    if (result != '') result += ', ';
                    result += renderPriority(p, true);
                }
            }
            return result;
        }

        const browserMap = {
            'chrome':'browser-chrome',
            'chromium':'browser-chrome',
            'edge':'browser-edge',
            'firefox':'browser-firefox',
            'safari':'browser-safari',
            'webkit':'browser-safari'
        }
        const osMap = {
            'windows':'windows',
            'win':'windows',
            'win10':'windows',
            'win11':'windows',
            'ubuntu':'ubuntu',
            'linux':'tencent-qq',
            'ios':'apple',
            'macos':'apple',
            'android':'android2'
        }
        const deviceMap = {
            'tablet':'tablet',
            'mobile':'phone',
            'desktop':'pc-display',
            'cloud':'cloud-upload',
        }

        const commonFormatter = (value, map) => {
            if (map[value] !== undefined)
                return `<i class="bi bi-${map[value]} text-primary me-1"></i>${value}`;
            else
                return value;
        }
        window.browserFormatter = (value) => {
            return commonFormatter(value, browserMap)
        }

        window.osFormatter = (value) => {
            return commonFormatter(value, osMap)
        }

        window.deviceFormatter = (value) => {
            return commonFormatter(value, deviceMap)
        }

        window.runFormatter = (value, row) => {
            if (value == 0)
                return `<div class="text-center">
<span class="btn btn-sm btn-outline-success lh-1" onclick="startJob(${row.id})" title="Click to start">
<i class="bi bi-play-btn fs-5"></i>
</span></div>`
            else if (value == 1)
                return `<div class="text-center">
<span class="btn btn-sm btn-outline-primary lh-1" onclick="stopJob(${row.id})" title="Running. Click to stop">
<span class="run_spinner spinner-grow spinner-grow-sm" role="status"></span>
<i class="bi bi-stop-btn fs-5"></i></span></div>`
            else if (value == 2)
                return `<div class="text-center">
<span class="btn btn-sm btn-outline-secondary lh-1" onclick="resetJob(${row.id})" title="Aborted! Click to ReInit">
<i class="bi bi-stop-btn fs-5"></i></span></div>`
            else if (value == 3)
                return `<div class="text-center">
<span class="text-success lh-1" title="Finished">
<i class="bi bi-check-circle-fill fs-5"></i></span></div>`
            else if (value == 4)
                return `<div class="text-center">
<span class="text-danger lh-1" title="Failed">
<i class="bi bi-exclamation-circle-fill fs-5"></i></span></div>`
        }

        $(function() {
            table.bootstrapTable({
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

        function startJob(id) {
            const row = table.bootstrapTable('getRowByUniqueId', id);
            console.log(row);

            table.bootstrapTable('updateCellByUniqueId', {
                id: id,
                field: 'job_status',
                value: 1,
                reinit: false
            })
        }

        function stopJob(id) {
            const row = table.bootstrapTable('getRowByUniqueId', id);
            console.log(row);

            table.bootstrapTable('updateCellByUniqueId', {
                id: id,
                field: 'job_status',
                value: 2,
                reinit: false
            })
        }

        function resetJob(id) {
            const row = table.bootstrapTable('getRowByUniqueId', id);
            table.bootstrapTable('updateCellByUniqueId', {
                id: id,
                field: 'job_status',
                value: 0,
                reinit: false
            })
        }
    </script>

@endsection

@section('footer')
    <script src="{{asset('/js/bootstrap-table.min.js')}}"></script>
    <script src="{{asset('/js/table-ext/bootstrap-table-custom-view.min.js')}}"></script>
    <script src="{{asset('/js/table-ext/bootstrap-table-resizable.min.js')}}"></script>
@endsection