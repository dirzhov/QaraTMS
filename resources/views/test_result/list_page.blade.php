@extends('layout.base_layout')

@section('head')
    <link href="{{asset('/css/bootstrap-table.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="/css/jquery.resizableColumns.min.css">
    <script src="{{asset('/js/table-ext/jquery.resizableColumns.min.js')}}"></script>

    <script src="https://cdn.jsdelivr.net/npm/ansi_up@4.0.4/ansi_up.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
    <style>
        /*@font-face {*/
        /*    font-family: 'DejaVu Sans Mono';*/
        /*    url('/css/fonts/dejavu-sans-mono.woff2') format('woff2'), !* Super Modern Browsers *!*/
        /*    url('/css/fonts/dejavu-sans-mono.woff') format('woff'), !* Pretty Modern Browsers *!*/
        /*    url('/css/fonts/dejavu-sans-mono.ttf') format('truetype'), !* Safari, Android, iOS *!*/
        /*    url('/css/fonts/dejavu-sans-mono.svg#dejavu_sansregular') format('svg'); !* Legacy iOS *!*/
        /*}*/
        @font-face {
            font-family: "DejaVu Sans Mono";
            font-style: normal;
            font-weight: normal;
            src: local(DejaVu Sans Mono), local(DejaVuSansMono),
            url('/css/fonts/DejaVuSansMono.woff') format("woff");
        }
        @font-face {
            font-family: "DejaVu Sans Mono";
            font-style: normal;
            font-weight: bold;
            src: local(DejaVu Sans Mono Bold), local(DejaVuSansMono-Bold),
            url('/css/fonts/DejaVuSansMono-Bold.woff') format("woff");
        }

        .console-log {
            background-color: #2B2B2B;
            color: #A9B7C6;
            padding: 1rem;
            font-family: 'DejaVu Sans Mono', sans-serif;
            font-size: 14px;
        }
        .code {
            font-family: "JetBrains Mono", monospace;
            font-size: 13px;
            font-optical-sizing: auto;
            font-weight: 200;
            font-style: normal;
        }
    </style>
@endsection

@section('content')

    @include('layout.sidebar_nav')


    <div class="col">

        <div class="border-bottom my-3 clearfix">
            <h4 class="page_title">
                <span class="float-left"><span class="text-secondary">Automation Test Run:</span> {{$testRun->title}}
                    <span id="total-chart" class="d-inline-block" style="width: 180px;">@include('test_result.chart')</span>
                    <a class="btn btn-link ms-2 p-0 text-decoration-none text-start align-text-bottom"
                       href="/test_run/{{$testRun->id}}/statistics" target="_blank">Statistic &rArr;</a></span>
                <div class="buttons-toolbar float-end"></div>
            </h4>

        </div>

        <div class="row row-cols-1 row-cols-md-1 g-3">
            <table
                    class="table-dark table-fixed table-sm"
                    id="table"
                    data-height="828"
                    data-unique-id="id"
                    data-buttons-toolbar=".buttons-toolbar"
                    data-show-toggle="true"
                    data-show-columns="true"
                    data-show-refresh="true"
                    data-show-search-clear-button="true"
                    data-filter-control="true"
                    data-pagination="true"
                    data-page-size="18"
                    data-page-list="[18, 30, 150, all]"
                    data-page-number="1"
                    data-side-pagination="server"
                    data-url="{{Config::get('app.url')}}/api/test-run/{{$testRun->id}}/test-results"
                    data-test-run-id="{{$testRun->id}}"
                    data-ajax-options="ajaxOptions"
                    data-resizable="true"
                    data-show-footer="true"
                    data-detail-view="true"
                    data-detail-formatter="detailFormatter"
                    data-editable-url="/my/editable/update/path"
            >
                <thead>
                <tr>
                    <th data-field="id" data-width="60">
                        Id
                    </th>
                    <th data-field="priority" title="Priority" data-formatter="priorityFormatter" data-sortable="true"
                        data-filter-control="select" data-filter-data="var:casePriority"
                        data-show-filter-control-switch="true" data-width="60">
                        Pri
                    </th>
                    <th data-field="test_case_id" data-width="60">
                        TC Id
                    </th>
                    <th data-field="tc_name" data-sortable="true" data-formatter="caseFormatter"
                        data-width="30" data-width-unit="%" data-footer-formatter="idFormatter">
                        Test Case
                    </th>
                    <th data-field="test_run_id" data-visible="false">
                        Test Run
                    </th>
                    <th data-field="status" data-formatter="caseStatusFormatter" data-sortable="true"
                        data-filter-control="select" data-filter-data="var:caseStatus"
                        data-width="90" data-footer-formatter="failedPassedFormatter">
                        Status
                    </th>
                    <th data-field="failed_step" title="Failed Step" data-visible="false" data-width="60">
                        F. Step
                    </th>
                    <th data-field="error_message" data-formatter="errorMessageFormatter">
                        Error Message
                    </th>
                    <th data-field="start_time" data-sortable="true" data-formatter="startTimeFormatter" data-width="130">
                        Start Time
                    </th>
                    <th data-field="execution_time" data-sortable="true" title="Execution Time"
                        data-footer-formatter="totalTimeFormatter" data-visible="false" data-width="100">
                        Exec. Time
                    </th>
                    <th data-field="reviewer_id" data-sortable="true" title="Reviewer"
                        data-formatter="reviewerFormatter" data-width="100">
                        Reviewer
                    </th>
                    <th data-field="review_status" data-sortable="true" title="Review Status"
                        data-formatter="reviewFormatter" data-width="130" class="fs-s">
                        Review
                    </th>
                    <th data-field="issues" data-formatter="issuesFormatter" class="fs-s"
                        data-footer-formatter="totalUniqueIssuesFormatter" data-width="170">
                        Issues
                    </th>
                    <th data-field="script_name" data-visible="false">
                        Script Name
                    </th>
                </tr>
                </thead>
            </table>
        </div>

    </div>

    <script>

        window.idFormatter = (rows) => {
            var p = new Array(5).fill(0)
            rows.forEach((row) => p[row.priority-1]++)
            return `Total:  ` +
                '<i class="me-1 bi {{\App\Enums\CasePriority::HIGHEST->cls()}}"></i>' + p[0] +
                ' <i class="me-1 bi {{\App\Enums\CasePriority::HIGH->cls()}}"></i>' + p[1] +
                ' <i class="me-1 bi {{\App\Enums\CasePriority::MEDIUM->cls()}}"></i>' + p[2] +
                ' <i class="me-1 bi {{\App\Enums\CasePriority::LOW->cls()}}"></i>' + p[3] +
                ' <i class="me-1 bi {{\App\Enums\CasePriority::LOWEST->cls()}}"></i>' + p[4]
        }

        window.failedPassedFormatter = (rows) => {
            var skipped = rows.reduce((total, r) => total + (r.status==3?1:0), 0);
            var failed = rows.reduce((total, r) => total + (r.status==2?1:0), 0);
            var passed = rows.reduce((total, r) => total + (r.status==1?1:0), 0);
            return `<span title="Passed/Failed/Skipped"><span class="text-success">${passed}</span> / <span class="text-danger">${failed}</span> / ${skipped}</span`;
        }

        window.totalTimeFormatter = (rows) => {
            var msec = rows.reduce((total, r) => total + r.execution_time, 0)
            if (msec < 1000)
                return rows.reduce((total, r) => total + r.execution_time, 0) + 'ms';
            return dayjs.duration(msec).format('HH:mm:ss')
        }

        window.totalUniqueIssuesFormatter = (rows) => {
            var allUniqueIssues = rows.reduce((total, r) => {
                if (r.issues) {
                    return r.issues.split(',').reduce((total2, i) => {(total2.indexOf(i)===-1) ? total2.push(i) : total2; return total2}, total)
                } else return total;
            }, new Array());
            return '<div class="show-all-issues" title="' + allUniqueIssues.join(',') + '">' + allUniqueIssues.length + '</div>';
        }

        window.detailFormatter = (index, row) => {
            return `<p>` +
                (row.error_message ? `<b>${escapeHTML(row.error_message)}:</b>` : '') +
                (row.full_error ? `<div class="position-relative"><pre>${escapeHTML(row.full_error)}:</pre>
                <button class="btn py-0 px-1 position-absolute" style="top:10px;right:10px;" type="button" title="Copy" onclick="copyToClipboard(${row.id})">
                    <i class="bi bi-copy"></i>
                </button></div>` : '') + `</p>`+
                (row.has_screenshot ? `<button type="button" class="btn btn-link px-3 py-0 text-decoration-none text-start" data-bs-toggle="modal" data-bs-target="#screenshotModal"
                        data-bs-url="{{$host}}/api/screenshot/${row.id}">screenshot</button>` : '') +
                `<button type="button" class="btn btn-link p-0 text-decoration-none text-start" data-bs-toggle="modal" data-bs-target="#testlogModal"
                        data-bs-url="{{$host}}/api/testlog/${row.id}">log</button>`;
        }

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

        window.priorityFormatter = (value, row) => {
            return renderPriority(value, true)
        }

        var caseStatus = {
            1: "Passed",
            2: "Failed",
            3: "Skipped"
        }
        var casePriority = {
            1: "P1",
            2: "P2",
            3: "P3",
            4: "P4",
            5: "P5"
        }
        var projectUsers={
        @foreach($projectActiveUsers as $reviewer)
        {{$reviewer->id}}:{
            "name": "{{$reviewer->name}}"
        },
        @endforeach
        }
        var reviewStatus={
            @foreach(App\Enums\TestResultReviewStatus::cases() as $status)
                    {{$status->value}}:{
                "title": "{{ucwords(str_replace('_', ' ',mb_strtolower($status->name)))}}",
                "cls": "{{$status->cls()}}"
            },
            @endforeach
        }

        window.caseStatusFormatter = (value, row) => {
            return caseStatus[value] || '-';
        }
        window.caseFormatter = (value, row) => {
            return `<button type="button" class="btn btn-link p-0 text-decoration-none text-start" data-bs-toggle="modal"
                    data-bs-target="#test_case_overlay" data-bs-test_result_id="${row.id}" title="${value}">${value}
                    <span class="text-muted">${row.tc_params ? '['+row.tc_params+']' : ''}</span></button>`
        }
        window.errorMessageFormatter = (value, row) => {
            if (!value) return "";
            return (value.length > 60) ? (`<span title="${value.replace(/"/g, "''").substring(0, 1023)}">` + value.substring(0, 59) + '..</span>') : value;
        }
        window.startTimeFormatter = (value, row) => {
            return dayjs(value).format('DD/MM HH:mm:ss')
        }
        window.issuesFormatter= (value, row) => {
            if (!value && row.status != 1)
                return `<i class="bi bi-plus-square cursor-pointer" data-bs-toggle="modal" data-bs-target="#reviewModal"
                    data-bs-test_result_id="${row.id}"></i>`;
            if (!value) return;

            var issues = value.split(',')
            return `<i class="bi bi-plus-square cursor-pointer" data-bs-toggle="modal" data-bs-target="#reviewModal"
                    data-bs-test_result_id="${row.id}"></i>&nbsp;`
                + issues.reduce((accumulator, currentValue) => accumulator + (accumulator==""?'':', ')
                + `<a href="${jiraUrl}/browse/${currentValue}" target=_blank>${currentValue}</a>`, "");
        }
        window.reviewerFormatter = (value, row) => {
            if (value && projectUsers[value])
                if (row.review_status != null)
                    return `<i class='bi ${reviewStatus[row.review_status].cls} me-1'></i>` + projectUsers[value].name;
                else
                    return projectUsers[value].name;
            return;
        }
        window.reviewFormatter = (value, row) => {
            if (!value) return;

            var status = reviewStatus[value];
            return `<i class='bi ${status.cls} me-1'></i>${status.title}`;
        }

        function copyToClipboard(id) {
            let row = $('#table').bootstrapTable('getRowByUniqueId', id)
            navigator.clipboard.writeText(row.full_error).then(infoToast("Stacktrace copied"))
        }

        let rows = []
        $(function() {
            $('#table').bootstrapTable({
                rowStyle :  function(row, index) {
                    if (row.status == 1)
                        return {
                            classes: 'table-success',
                            //css: {"color": "blue"}
                        };
                    else if (row.status == 2)
                        return {
                            classes: 'table-danger'
                        }
                    else if (row.status == 3)
                        return {
                            classes: 'table-secondary'
                        }
                }

            });
            $('#table').on('click-row.bs.table', function (e, row, $element) {
                var index = $element.data('index');
                $('#table tbody').find('tr').removeClass('active');
                $('#table tbody').find('tr').eq(index).addClass('active');
            });
            $('#table').on('refresh.bs.table', function (params) {
                var test_run_id = $('#table').data('test-run-id');
                console.log(test_run_id)
                $("#total-chart").load(`/atrchart/${test_run_id}`, function () {
                });
            });

            $('#table').on('expand-row.bs.table', function (e, row, $detail) {
                // var index = $element.data('index');
                // alert('Row Index is: ' + index.toString());
            });

        })
    </script>
@endsection

@section('footer')

    @include('test_result.test_result_review_modal')
    @include('test_result.test_case_result_modal')
    @include('test_result.screenshot_preview_modal')
    @include('test_result.testlog_preview_modal')

    <script src="{{asset('/js/bootstrap-table.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <script src="{{asset('/js/table-ext/bootstrap-table-resizable.min.js')}}"></script>
    <script src="{{asset('/js/table-ext/bootstrap-table-filter-control.js')}}"></script>
    <script src="{{asset('/js/bootstrap-autocomplete.js')}}"></script>
    <script src="{{asset('/js/issue-list.js')}}"></script>

@endsection