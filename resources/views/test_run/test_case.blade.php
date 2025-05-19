<div id="test_case_block">
    <div class="border rounded py-1 my-2">

        <div class="position-static mx-2">
            <button type="button" class="btn btn-outline-success test_run_case_btn"
                    data-status="{{\App\Enums\TestRunCaseStatus::PASSED}}"
                    data-test_run_id="{{$testRun->id}}">
                Passed
            </button>

            <button type="button" class="btn btn-outline-danger test_run_case_btn"
                    data-status="{{\App\Enums\TestRunCaseStatus::FAILED}}"
                    data-test_run_id="{{$testRun->id}}">
                Failed
            </button>

            <button type="button" class="btn btn-outline-warning test_run_case_btn"
                    data-status="{{\App\Enums\TestRunCaseStatus::BLOCKED}}"
                    data-test_run_id="{{$testRun->id}}">
                <b>Blocked</b>
            </button>

            <button type="button" class="btn btn-outline-secondary test_run_case_btn"
                    data-status="{{\App\Enums\TestRunCaseStatus::NOT_TESTED}}"
                    data-test_run_id="{{$testRun->id}}">
                Not Tested
            </button>

            <div class="col-2 ms-5 d-inline-block">
                <input type="text" class="form-control" placeholder="Add Issue" name="issue" id="tc_issue_input"
                       data-filter="{{ Config::get('app.jira_host') }}/rest/api/2/issue/#QUERY#?fields=summary,priority,status,issuetype">
            </div>
            <button type="button" class="btn btn-primary float-end"
                    data-test_run_id="{{$testRun->id}}"
                    onclick="updateCaseStatus({{$testRun->id}}, {{$testCase->id}},
                    $('.test_run_case_btn[selected]').data().status,
                    $('#assignee_select').val(),
                    $('#assignee_select option:selected').prop('label'),
                    $('#tc_issues_input').val())">
                Save
            </button>
            <div class="col-3 float-end me-4">
                <select id="assignee_select" name="assignee" class="form-select border-secondary">
                    <option value="0">Not selected</option>
                    @foreach($assignees as $assignee)
                        <option value="{{$assignee->id}}"
                                @if(isset($testRun->data[$testCase->id]->a) && $assignee->id == $testRun->data[$testCase->id]->a)
                                    selected
                                @endif
                        >{{$assignee->name}}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group m-1 position-relative">
                <ul class="list-group list-group-flush" id="issues_list">
                </ul>
                <input type="hidden" name="issues" id="tc_issues_input"
                       value="{{ (isset($testRun->data[$testCase->id]) && isset($testRun->data[$testCase->id]->i)) ? $testRun->data[$testCase->id]->i : '' }}">
            </div>

        </div>

    </div>

    <div id="test_case_content">

        <div class="d-flex justify-content-between border-bottom mt-2 pb-2 mb-4">
            <div>
                <i class="bi {{\App\Enums\CasePriority::fromId($testCase->priority)->cls()}}"
                   title="{{ucfirst(mb_strtolower(App\Enums\CasePriority::from($testCase->priority)->name))}}"></i>

                <span class="fs-6 badge bg-secondary">{{$repository->prefix}}-{{$testCase->id}}</span>
                <span class="fs-5">
                    @if($testCase->automated)
                        <i class="bi bi-robot"></i>
                    @else
                        <i class="bi bi-person"></i>
                    @endif
                </span>
                <span class="fs-5">{{$testCase->title}}</span>
            </div>
        </div>

        <div class="p-4 pt-0 position-relative">
            @if(isset( $dependedTestCase ) )
                <p>
                    <b>Depended On:</b>
                    <i class="bi {{App\Enums\CasePriority::from($dependedTestCase->priority)->cls()}}"
                       title="{{ucfirst(mb_strtolower(App\Enums\CasePriority::from($dependedTestCase->priority)->name))}}"></i>

                    <span>
                    @if($dependedTestCase->automated)
                            <i class="bi bi-robot mx-1"></i>
                        @else
                            <i class="bi bi-person mx-1"></i>
                        @endif
                </span>

                    <u class="text-primary me-1">
                        <a target="_blank" href="{{route('test_case_show_page', $dependedTestCase->id)}}">
                            {{$repository->prefix}}-<span id="tce_case_id">{{$dependedTestCase->id}}</span>
                        </a>
                    </u>
                    {{$dependedTestCase->title}}
                </p>
            @endif
            @if(isset( $data->preconditions) && !empty($data->preconditions) )
                <strong class="fs-6 pb-3">Preconditions</strong>
                <div class="row mb-3 border p-3 rounded">

                    <div>
                        {!! $data->preconditions !!}
                    </div>

                </div>
            @endif

            @if(isset($data->steps) && !empty($data->steps))
                <strong class="fs-6 pb-3">Steps</strong>
                <div class="row mb-3 border p-3 rounded" id="steps_container">

                    <div class="row step pb-2 mb-2">
                        <div class="col-6">
                            <b>Action</b>
                        </div>
                        <div class="col-6">
                            <b>Expected result</b>
                        </div>
                    </div>

                    @foreach($data->steps as $id => $step)
                        <div class="row step border-top mb-2 pt-2" data-badge="{{$id+1}}">

                            <sapn class="step-number" data-tooltip="Click me to toggle step as failed or not">{{$id+1}}</sapn>
                            <div class="col-6">
                                <div>
                                    {!! $step->action !!}
                                </div>
                            </div>

                            <div class="col-6">
                                <div>
                                    {!! $step->result !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            @else
                <p>No additional details available.</p>
            @endif

        </div>

    </div>

    <div class="text-center mask d-none">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only"></span>
        </div>
    </div>

</div>

<script>
    setSelectedBtn({{ (isset($testRun->data[$testCase->id]) && isset($testRun->data[$testCase->id]->s)) ? $testRun->data[$testCase->id]->s : ''}});

    $('#tc_issue_input').autocomplete({
        filterMinChars: 5,
        filterDelay: 1000,
        filterAjaxParams: {
            headers: {
                'Authorization': 'Bearer {{ Config::get('app.jira_token') }}',
                'Content-Type': 'application/json'
            }
        },
        labelKey: 'summary',
        itemRenderer: function(dropdown, data) {
            var item = $('<a class="dropdown-item" href="#"></a>');;
            item.data(data);
            item.html('[<a href="#">' + data.key + '</a>] ' + data[this.labelKey]).appendTo(dropdown);
            return item;
        },
        preProcess: function(resp) {
            let res = [];
            if (resp instanceof Array)
                for (const item of resp) {
                    res.push({
                        key: item.key,
                        summary: item.fields.summary,
                        issuetype: resp.fields.issuetype.name,
                        status: resp.fields.status.name,
                        iconUrl: item.fields.issuetype.iconUrl
                    });
                }
            else if (typeof resp == 'object')
                res.push({
                    key: resp.key,
                    summary: resp.fields.summary,
                    issuetype: resp.fields.issuetype.name,
                    status: resp.fields.status.name,
                    iconUrl: resp.fields.issuetype.iconUrl
                });

            return res;
        },
        onPick(input, item) {
            var data = $(item).data();

            var value = $('#tc_issues_input').val();
            if (!value.includes(data.key)) {
                value = value + (value == '' ? '' : ',') + data.key;
                $('#tc_issues_input').val(value);

                $('#issues_list').issuelist("addItem", data);
                $('#issues_list').issuelist("refresh");
            }

            $(input).val(null);
        }
    });

    var issues = '"' + $('#tc_issues_input').val().replace(',', '","') + '"';
    $('#issues_list').issuelist({
        issueUrl: jiraUrl + '/browse/',
        prefetch: jiraUrl + '/rest/api/2/search?jql=issuekey in (' + issues + ')&fields=summary,priority,status,issuetype',
        preProcess: function(resp) {
            var res = [];
            if (resp instanceof Array)
                for (const item of resp) {
                    res.push({
                        key: item.key,
                        summary: item.fields.summary,
                        issuetype: item.fields.issuetype.name,
                        status: item.fields.status.name,
                        iconUrl: item.fields.issuetype.iconUrl
                    });
                };
            return res;
        },
        onBeforeFetch: function(el) {
            $(".mask").removeClass("d-none");
        },
        onAfterFetch: function(el) {
            $(".mask").addClass("d-none");
        },
        onBeforeDelete: function(delItem) {
            var values = $('#tc_issues_input').val().split(',');
            var index = values.indexOf(delItem.data().key);
            if (index >= 0)
                values.splice(index, 1);

            $('#tc_issues_input').val(values.join(','));
        }
        // issues:[
        //     {key: 'TMS-1', summary: 'Bug1'},
        //     {key: 'TMS-2', summary: 'Bug2', status: "Closed", iconUrl: jiraUrl + "/secure/viewavatar?size=xsmall&avatarId=10303&avatarType=issuetype"},
        // ]
    });


    $('#steps_container .step-number').click(function (e) {
        // console.log("After has been clicked..." + pseudoClick(this).after);
        // if ( pseudoClick(this).after==true) {
        //     let row = $(this);
        //     row.toggleClass("failed");
        // }

        selectFailedStep(this);
    });


    function selectFailedStep(stepNumber) {
        let step, row;
        console.log(typeof stepNumber)
        if (typeof stepNumber == 'object') {
            row = $(stepNumber).parent();
            step = row.data("badge");
        } else if (typeof stepNumber == 'number') {
            step = stepNumber;
            row = $('#steps_container .step').eq(step)
        } else
            return;

        if (row.hasClass('failed')) {
            $('#steps_container .step').removeClass('passed').removeClass('failed')
            resetAllBtns();
            setSelectedBtn(4);
        } else {
            $('#steps_container .step').slice(1,step).addClass('passed').removeClass('failed')
            $('#steps_container .step').slice(step).removeClass('passed').removeClass('failed')
            row.addClass('failed');
            resetAllBtns();
            setSelectedBtn(2);
        }
    }

    selectFailedStep({{$testRun->data[$testCase->id]->f ?? null}});

</script>
