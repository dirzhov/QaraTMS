<div id="test_case_editor">

    <div class="d-flex justify-content-between border-bottom mt-2 pb-2 mb-2">
        <div>
            <span class="fs-5">Create Test Case</span>
        </div>

        <div>
            <button href="button" class="btn btn-outline-dark btn-sm" onclick="closeTestCaseEditor()">
                <i class="bi bi-x-lg"></i> <b>Cancel</b>
            </button>
        </div>
    </div>

    <div id="test_case_content">
        <div class="p-4 pt-0">

            <div class="row mb-3">

                <div class="mb-3 p-0 form-floating">
                    <input name="title" id="tce_title_input" type="text" class="form-control border-secondary"
                           placeholder="title" autofocus>
                    <label for="title" class="form-label">Title</label>
                </div>

                <div class="mb-3 justify-content-start border p-3 bg-light">

                    <div class="d-flex mb-1">
                        <div class="col">
                            <label for="test_suite_id" class="form-label"><strong>Test Suite</strong></label>
                            <select name="suite_id" id="tce_test_suite_select" class="selectpicker">

                                @foreach($repository->suites as $repoTestSuite)
                                    <option value="{{$repoTestSuite->id}}"
                                            @if($repoTestSuite->id == $parentTestSuite->id)
                                                selected
                                            @endif
                                    >
                                        {{$repoTestSuite->title}}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col mx-4">
                            <label class="form-label">
                                <b>Assignee</b>
                            </label>

                            <select id="tce_assignee_select" name="assignee" class="selectpicker">
                                @foreach($assignees as $assignee)
                                    <option value="{{$assignee->id}}">
                                        {{$assignee->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col me-4">
                            <label class="form-label">
                                <b>Priority</b>
                            </label>

                            <select id="tce_priority_select" name="priority" class="selectpicker">
                                @foreach (App\Enums\CasePriority::cases() as $option)
                                    <option value="{{$option->value}}" data-content="<i class='bi {{$option->cls()}} me-1'></i>{{ucfirst(mb_strtolower($option->name))}}"></option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col">
                            <label class="form-label" for="automated"><b>Type</b> <i class="bi bi-person"></i> | <i
                                        class="bi bi-robot"></i></label>
                            <select name="automated" id="tce_automated_select" class="selectpicker col-12">
                                @foreach (App\Enums\TestCaseType::cases() as $option)
                                    <option value="{{$option->value}}" {{ ($option == App\Enums\TestCaseType::MANUAL) ? 'selected' : '' }}>
                                        {{ucfirst(mb_strtolower($option->name))}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <p class="mb-0">
                        <button type="button" class="btn btn-link expander" data-bs-toggle="collapse" data-bs-target=".multi-collapse"
                                aria-expanded="false" aria-controls="tce_additional tce_additional2 tce_additional3">Show additional</button>
                    </p>
                    <div class="collapse multi-collapse row mb-3" id="tce_additional">
                        <div class="col-6">
                            <label class="form-label d-block" for="components"><b>Components</b></label>
                            <select name="components" class="selectpicker col-12" multiple data-live-search="true" id="tce_components_select">
                                @foreach($components as $component)
                                    <option value="{{$component->id}}">
                                        {{$component->title}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label class="form-label">
                                <b>Severety</b>
                            </label>

                            <select id="tce_severity_select" name="severity" class="selectpicker col-12">
                                @foreach (App\Enums\CaseSeverity::cases() as $option)
                                    <option value="{{$option->value}}" data-content="<i class='bi {{$option->cls()}} me-1'></i>{{ucfirst(mb_strtolower($option->name))}}"></option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label class="form-label"><b>Automation Status</b></label>
                            <select id="tce_automated_status_select" name="automated_status" class="selectpicker col-12">
                                @foreach (App\Enums\AutomationStatus::cases() as $option)
                                    <option value="{{$option->value}}" {{ ( $option == App\Enums\AutomationStatus::NOT_AUTOMATED) ? 'selected' : '' }}>
                                        {{str_replace("_", " ", ucfirst(mb_strtolower($option->name)))}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="collapse multi-collapse mb-3 mx-0 form-floating" id="tce_additional2">
                        <input name="script_name" id="tce_script_name_input" type="text" class="form-control border-secondary" placeholder="package.class.method" autofocus>
                        <label for="script_name" class="form-label">Script Name</label>
                    </div>
                    <div class="collapse multi-collapse mb-3 mx-0 form-floating" id="tce_additional3">
                        <input name="requirements" id="tce_requirements_input" type="text" class="form-control border-secondary" placeholder="http://jira.ticket" autofocus>
                        <label for="requirements" class="form-label">Requirements</label>
                    </div>
                </div>

                <div class="col p-0">
                    <label class="form-label"><b>Preconditions</b></label>
                    <textarea name="pre_conditions" class="editor_textarea form-control border-secondary"
                              id="tce_preconditions_input" rows="3"></textarea>
                </div>

            </div>

            <div class="row" id="steps_container">
                <div class="p-0 mb-1">
                    <b class="fs-5">Steps</b>
                    <span class="text-muted" style="font-size: 12px">Action <i class="bi bi-arrow-right"></i> Expected Result</span>
                </div>

                <div class="row m-0 p-0 mt-2 step">
                    <div class="col-auto p-0 d-flex flex-column align-items-center">
                        <span class="fs-5 step_number">1</span>

                        <button type="button" class="btn btn-outline btn-sm step_delete_btn px-1 py-0"
                                onclick="stepUp(this)">
                            <i class="bi bi-arrow-up-circle"></i>
                        </button>

                        <button type="button" class="btn btn-outline-danger btn-sm step_delete_btn px-1 py-0"
                                onclick="removeStep(this)">
                            <i class="bi bi-x-circle"></i>
                        </button>

                        <button type="button" class="btn btn-outline btn-sm step_delete_btn px-1 py-0"
                                onclick="stepDown(this)">
                            <i class="bi bi-arrow-down-circle"></i>
                        </button>
                    </div>

                    <div class="col p-0 px-1 test_case_step">
                        <textarea class="editor_textarea form-control border-secondary step_action" rows="2"></textarea>
                    </div>
                    <div class="col p-0 test_case_step">
                        <textarea class="editor_textarea form-control border-secondary step_result" rows="2"></textarea>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <div id="test_case_editor_footer" class="col-6 d-flex justify-content-between border-top pt-2">
        <div class="col">
            <button type="button" class="btn btn-primary" onclick="addStep()">
                <i class="bi bi-plus-circle"></i>
                Add Step
            </button>
        </div>

        <div class="col d-flex justify-content-end pe-3">

            <button id="tce_save_btn" type="button" class="btn btn-success me-3" onclick="createTestCase()">
                Create
            </button>

            <button id="tce_save_btn" type="button" class="btn btn-success me-3" onclick="createTestCase(true)">
                Create and add another
            </button>
        </div>
    </div>

</div>
<script>
    $("#tce_automated_select").on("change", function() {
        console.log("triggered");
        if ($(this).val() == 1)
            $("#tce_automated_status_select").val(2);
        else
            $("#tce_automated_status_select").val(1);
        $('.selectpicker').selectpicker('refresh')
    });

    $("#tce_automated_status_select").on("change", function() {
        if ($(this).val() == 2)
            $("#tce_automated_select").val(1);
        else
            $("#tce_automated_select").val(0);
        $('.selectpicker').selectpicker('refresh')
    });

    $('.selectpicker').selectpicker();
</script>
