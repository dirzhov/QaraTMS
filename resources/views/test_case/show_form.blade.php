<div id="test_case_editor">

    <div class="tc-header d-flex justify-content-between border-bottom mt-2 pb-2 mb-2">


        <div style="min-width: 140px">

            <i class="bi {{App\Enums\CasePriority::from($testCase->priority)->cls()}}"
               title="{{ucfirst(mb_strtolower(App\Enums\CasePriority::from($testCase->priority)->name))}}"></i>

            <span>
            @if($testCase->automated)
                    <i class="bi bi-robot mx-1"></i>
                @else
                    <i class="bi bi-person mx-1"></i>
                @endif
            </span>

            <u class="text-primary">
                <a target="_blank" href="{{route('test_case_show_page', $testCase->id)}}">
                    {{$repository->prefix}}-<span id="tce_case_id">{{$testCase->id}}</span>
                </a>
            </u>
        </div>

        <input type="hidden" id="tce_suite_id" value="{{$testCase->suite_id}}">

        <div class="test_case_title">
            <b>{{$testCase->title}}</b>
        </div>

        <div style="min-width: 105px" class="justify-content-end">

            @can(App\Enums\UserPermission::add_edit_test_cases)
                <button type="button" class="btn btn-outline-dark btn-sm"
                        onclick="renderTestCaseEditForm({{$testCase->id}})">
                    <i class="bi bi-pencil"></i>
                </button>
            @endcan

            <button href="button" class="btn btn-outline-dark btn-sm" onclick="renderTestCaseOverlay({{$testCase->id}})">
                <i class="bi bi-arrows-angle-expand"></i>
            </button>

            <button href="button" class="btn btn-outline-dark btn-sm" onclick="closeTestCaseEditor()">
                <i class="bi bi-x-lg"></i>
            </button>

        </div>

    </div>

    <div id="test_case_content">
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
                <strong class="fs-5 pb-3">Preconditions</strong>

                <div class="row mt-1 mb-3 border p-3 rounded">
                    <div>
                        {!! $data->preconditions !!}
                    </div>
                </div>
            @endif

            @if(isset($data->steps) && !empty($data->steps))
                <strong class="fs-5 pb-3">Steps</strong>

                <div class="row mt-1 mb-3 border p-3 rounded" id="steps_container">

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

                            <span class="step-number">{{$id+1}}</span>
                            <div class="col-6">
                                <div>
                                    @if(isset($step->action))
                                        {!! $step->action !!}
                                    @endif
                                </div>
                            </div>

                            <div class="col-6">
                                <div>
                                    @if(isset($step->result))
                                        {!! $step->result !!}
                                    @endif
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


</div>
