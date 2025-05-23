<div class="tree_suite">

    @foreach($suites as $testSuite)

        {{--   SHOW CHILD SUITE TITLE WITH FULL PATH --}}
        <div style="background: #7c879138; padding-left: 5px; padding-bottom: 5px; border: 1px solid lightgray; border-radius: 3px">
            <i class="bi bi-folder2 fs-5"></i>

            <span class="text-muted" style="font-size: 14px">
                @foreach($testSuite->ancestors()->get()->reverse() as $parent)
                    {{$parent->title}}
                    <i class="bi bi-arrow-right-short"></i>
                @endforeach
            </span>
            <span>{{$testSuite->title}}</span>
        </div>


        <div class="tree_suite_test_cases">
            @foreach($testSuite->testCases->sortBy('order') as $testCase)

                @if( in_array($testCase->id, $testCasesIds)   )

                    <div class="tree_test_case tree_test_case_content py-1 ps-1"
                         onclick="loadTestCase({{$testRun->id}}, {{$testCase->id}})">

                        <div class='d-flex justify-content-between'>

                            <div class="mt-1">
                                <span>@if($testCase->automated)
                                        <i class="bi bi-robot"></i>
                                    @else
                                        <i class="bi bi-person"></i>
                                    @endif </span>

                                @if ($testCase->priority == 1)
                                <i class="bi bi-chevron-double-up text-danger"></i>
                                @elseif ($testCase->priority == 2)
                                <i class="bi bi-chevron-up text-danger"></i>
                                @elseif ($testCase->priority == 3)
                                <i class="bi bi-list text-info"></i>
                                @elseif ($testCase->priority == 4)
                                <i class="bi bi-chevron-down text-warning"></i>
                                @elseif ($testCase->priority == 5)
                                <i class="bi bi-chevron-double-down text-warning"></i>
                                @endif

                                <span class="text-muted ps-1 pe-3 ">{{$repository->prefix}}-{{$testCase->id}}</span>
                                <span>{{$testCase->title}}</span>
                            </div>


                            <div class="result_badge px-2 pt-1 text-end" data-test_case_id="{{$testCase->id}}">
                                @if(isset($results[$testCase->id]))
                                    @if($results[$testCase->id]->s == \App\Enums\TestRunCaseStatus::NOT_TESTED)
                                        <span class="badge bg-secondary status float-end">Not Tested</span>
                                    @elseif($results[$testCase->id]->s == \App\Enums\TestRunCaseStatus::PASSED)
                                        <span class="badge bg-success status float-end">Passed</span>
                                    @elseif($results[$testCase->id]->s == \App\Enums\TestRunCaseStatus::FAILED)
                                        <span class="badge bg-danger status float-end">Failed</span>
                                    @elseif($results[$testCase->id]->s == \App\Enums\TestRunCaseStatus::BLOCKED)
                                        <span class="badge bg-warning status float-end">Blocked</span>
                                    @endif

                                @else
                                    <span class="badge bg-secondary status float-end">Not Tested</span>
                                @endif
                                @if (isset($results[$testCase->id]->a))
                                    <small class="me-1 text-secondary">{{$users[$results[$testCase->id]->a]->name}}</small>
                                @endif
                            </div>
                        </div>

                    </div>

                @endif
            @endforeach
        </div>

    @endforeach

</div>
