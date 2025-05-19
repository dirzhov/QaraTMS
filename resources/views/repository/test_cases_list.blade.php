@php use App\Enums\CasePriority;use App\Models\Repository;use App\Models\TestCase;
/**
 * @var TestCase[] $testCases
 * @var Repository $repository
 */
@endphp
@foreach($testCases as $testCase)

    <div id="{{$testCase->id}}" class="test_case border-bottom d-flex ps-1  justify-content-between"
         data-case_id="{{$testCase->id}}">

        <div class="d-flex justify-content-start test_case_clickable_area"
             onclick="renderTestCase('{{$testCase->id}}')">
            <div class="me-1 test_case_info">

                <i class="bi {{App\Enums\CasePriority::from($testCase->priority)->cls()}}"
                   title="{{ucfirst(mb_strtolower(App\Enums\CasePriority::from($testCase->priority)->name))}}"></i>

                <span>
                    @if($testCase->automated)
                        <i class="bi bi-robot mx-1"></i>
                    @else
                        <i class="bi bi-person mx-1"></i>
                    @endif
                </span>

                <u class="text-primary under">
                    <a href="{{route('test_case_show_page', $testCase->id)}}" target="_blank">{{$repository->prefix}}
                        -{{$testCase->id}}
                    </a>

{{--                    <button type="button" class="btn btn-outline-dark" onclick="renderTestCaseOverlay('{{$testCase->id}}')">--}}
{{--                        {{$repository->prefix}}-<span id="tce_case_id">{{$testCase->id}}</span>--}}
{{--                    </button>--}}
                </u>
            </div>

            <div class="test_case_title">
                <span>{{$testCase->title}}</span>
            </div>
        </div>

        <div class="test_case_controls">
            @can('add_edit_test_cases')
                <button class="btn py-0 px-1" type="button" title="Edit"
                            onclick="renderTestCaseEditForm('{{$testCase->id}}')">
                    <i class="bi bi-pencil"></i>
                </button>
            @endcan

            @can('add_edit_test_cases')
                <button class="btn py-0 px-1" type="button" title="Clone"
                        onclick="cloneAndEditTestCase('{{$testCase->id}}')">
                    <i class="bi bi-copy"></i>
                </button>
            @endcan

            @can('delete_test_cases')
                <button class="btn py-0 px-1" type="button" title="Delete" onclick="deleteTestCase({{$testCase->id}})">
                    <i class="bi bi-trash3"></i>
                </button>
            @endcan
        </div>

    </div>

@endforeach
