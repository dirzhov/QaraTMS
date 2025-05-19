<div id="test_case_editor">

    <div class="d-flex justify-content-between border-bottom mt-2 pb-2 mb-2">
        <div>
            <span class="fs-5">Edit Test Case</span>
        </div>

        <div>
            <button href="button" class="btn btn-outline-dark btn-sm" onclick="renderTestCase({{$testCase->id}})">
                <i class="bi bi-x-lg"></i> <b>Cancel</b>
            </button>
        </div>
    </div>

    <div id="test_case_content">
        <div class="p-4 pt-0">

            <div class="row mb-3">

                <div class="mb-3 p-0 form-floating">
                    <input name="title" id="tce_title_input" type="text" class="form-control border-secondary"
                           placeholder="title" value="{{$testCase->title}}">
                    <label for="title" class="form-label">Title</label>
                </div>

                <div class="mb-3 justify-content-start border p-3 bg-light">

                    <div class="d-flex mb-3">
                        <div class="col-2">
                            <label for="test_suite_id" class="form-label"><strong>Test Suite</strong></label>
                            <select name="suite_id" id="tce_test_suite_select" class="form-select border-secondary">

                                @foreach($repository->suites as $repoTestSuite)
                                    <option value="{{$repoTestSuite->id}}"
                                            @if($repoTestSuite->id == $testCase->suite_id)
                                                selected
                                            @endif>
                                        {{$repoTestSuite->title}}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col-2 mx-4">
                            <label class="form-label">
                                <b>Created by</b>
                            </label>
                            <div>
                                {{$creator->name}}
                            </div>
                        </div>
                        <div class="col-3 me-4">
                            <label class="form-label">
                                <b>Assignee</b>
                            </label>

                            <select id="tce_assignee_select" name="assignee" class="form-select border-secondary">
                                @foreach($assignees as $assignee)
                                    <option value="{{$assignee->id}}"
                                            @if($assignee->id == $testCase->assignee_id)
                                                selected
                                            @endif
                                    >
                                        {{$assignee->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2 me-4">
                            <div>
                            <label class="form-label">
                                <b>Priority</b>
                            </label>
                            </div>
                            <select id="tce_priority_select" name="priority" class="selectpicker col-12">
                                @foreach (App\Enums\CasePriority::cases() as $option)
                                    <option value="{{$option->value}}" data-content="<i class='bi {{$option->cls()}} me-1'></i>{{ucfirst(mb_strtolower($option->name))}}"
                                            {{ ( $option->value == $testCase->priority) ? 'selected' : '' }}></option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label"><b>Type</b> <i class="bi bi-person"></i> | <i class="bi bi-robot"></i></label>
                            <select name="automated" class="form-select border-secondary" id="tce_automated_select">
                                @foreach (App\Enums\TestCaseType::cases() as $option)
                                    <option value="{{$option->value}}" {{ ( $option->value == $testCase->automated) ? 'selected' : '' }}>
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
                                    <option value="{{$component->id}}"
                                            @if(!empty($testCase->components) && in_array($component->id, $testCase->components))
                                                selected
                                            @endif
                                    >{{$component->title}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">
                                <b>Severety</b>
                                <i class="bi bi-chevron-double-up text-danger"></i>|
                                <i class="bi bi-chevron-up text-danger"></i>|
                                <i class="bi bi-list text-info"></i>|
                                <i class="bi bi-chevron-down text-warning"></i>|
                                <i class="bi bi-chevron-double-down text-warning"></i>
                            </label>

                            <select id="tce_severity_select" name="severity" class="form-select border-secondary">
                                @foreach (App\Enums\CaseSeverity::cases() as $option)
                                    <option value="{{$option->value}}" {{ ( $option->value == $testCase->severity) ? 'selected' : '' }}>
                                        {{ucfirst(mb_strtolower($option->name))}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label"><b>Automation Status</b></label>
                            <select name="automated_status" class="form-select border-secondary" id="tce_automated_status_select">
                                @foreach (App\Enums\AutomationStatus::cases() as $option)
                                    <option value="{{$option->value}}" {{ ( $option->value == $testCase->automated_status) ? 'selected' : '' }}>
                                        {{str_replace("_", " ", ucfirst(mb_strtolower($option->name)))}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="collapse multi-collapse mb-3 mx-0 form-floating" id="tce_additional2">
                        <input name="script_name" id="tce_script_name_input" type="text" class="form-control border-secondary"
                               placeholder="package.class.method" autofocus value="{{$testCase->script_name}}">
                        <label for="script_name" class="form-label">Script Name</label>
                    </div>
                    <div class="collapse multi-collapse mb-3 mx-0 form-floating" id="tce_additional3">
                        <input name="requirements" id="tce_requirements_input" type="text" class="form-control border-secondary"
                               placeholder="http://jira.ticket" autofocus value="{{$testCase->requirements}}">
                        <label for="requirements" class="form-label">Requirements</label>
                    </div>

                </div>

                <input type="hidden" id="tce_case_id" value="{{$testCase->id}}">

                <div class="col p-0">
                    <label class="form-label"><b>Preconditions</b></label>
                    @if(isset($data->preconditions))
                        <textarea name="pre_conditions" class="editor_textarea form-control border-secondary"
                                  id="tce_preconditions_input" rows="3">{{ $data->preconditions }}</textarea>
                    @else
                        <textarea name="pre_conditions" class="editor_textarea form-control border-secondary"
                                  id="tce_preconditions_input" rows="3"></textarea>
                    @endif
                </div>

            </div>

            <div class="row" id="steps_container">
                <div class="p-0 mb-1">
                    <b class="fs-5">Steps</b>
                    <span class="text-muted" style="font-size: 12px">Action <i class="bi bi-arrow-right"></i> Expected Result</span>
                </div>

                @if(isset($data->steps))

                    @foreach($data->steps as $id => $step)

                        <div class="row m-0 mt-2 p-0 step">
                            <div class="col-auto p-0 d-flex flex-column align-items-center">
                                <span class="fs-5 step_number">{{$id+1}}</span>

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
                                <textarea class="editor_textarea form-control border-secondary step_action" rows="2">
                                    @if(isset($step->action))
                                        {!! $step->action !!}
                                    @endif
                                </textarea>
                            </div>
                            <div class="col p-0 test_case_step">
                                <textarea class="editor_textarea form-control border-secondary step_result" rows="2">
                                    @if(isset($step->result))
                                        {!! $step->result !!}
                                    @endif
                                </textarea>
                            </div>
                        </div>
                    @endforeach

                @else

                    <div class="row m-0 p-0 step">
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
                            <textarea class="editor_textarea form-control border-secondary step_action"
                                      rows="2"></textarea>
                        </div>
                        <div class="col p-0 test_case_step">
                            <textarea class="editor_textarea form-control border-secondary step_result"
                                      rows="2"></textarea>
                        </div>
                    </div>

                @endif

            </div>

        </div>
    </div>

    <div id="test_case_editor_footer" class="d-flex justify-content-end border-top pt-2">

        <button type="button" class="btn btn-primary px-5" onclick="addStep()">
            <i class="bi bi-plus-circle"></i>
            Add Step
        </button>

        <div class="col d-flex justify-content-end pe-3">
            <button id="tce_save_btn" type="button" class="btn btn-warning px-5 mx-3 me-3" onclick="updateTestCase()">
                <i class="bi bi-save"></i>
                Update Test Case
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

