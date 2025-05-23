@extends('layout.base_layout')

@section('head')
    <link href="{{asset('editor/summernote-repo.css')}}" rel="stylesheet">
    <script src="{{asset('editor/summernote-lite.min.js')}}"></script>
@endsection

@section('content')

    @include('layout.sidebar_nav')

    <div class="col">

        <div class="border-bottom my-3 d-flex justify-content-between">

            <h3 class="page_title">
                <i class="bi {{App\Enums\CasePriority::from($testCase->priority)->cls()}}"
                   title="{{ucfirst(mb_strtolower(App\Enums\CasePriority::from($testCase->priority)->name))}}"></i>

                <span>
                    @if($testCase->automated)
                        <i class="bi bi-robot mx-1"></i>
                    @else
                        <i class="bi bi-person mx-1"></i>
                    @endif
                </span>

                <span class="text-primary">
                     {{$repository->prefix}}-<span id="tce_case_id">{{$testCase->id}}</span>
                </span>
                {{$testCase->title}}

            </h3>

            <button type="button" class="btn btn-outline-dark btn-sm mb-2"
                    onclick="renderTestCaseEditForm({{$testCase->id}})">
                <i class="bi bi-pencil px-1"></i>
            </button>

        </div>

        <div id ="test_case_area" class="pb-5">
            <div id="test_case_editor">

                <div id="test_case_content" class="position-relative ">
                    <div class="p-4 pt-0">
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
                            <div class="row mb-3 border p-3 rounded">

                                <div>
                                    {!! $data->preconditions !!}
                                </div>

                            </div>
                        @endif

                        @if(isset($data->steps) && !empty($data->steps))
                            <strong class="fs-5 pb-3">Steps</strong>
                            <div class="row mb-3 border p-3 mt-1 rounded" id="steps_container">


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

                                        <sapn class="step-number">{{$id+1}}</sapn>
                                        <div class="col-6">
                                            <div>
                                                @if(isset($step->action))
                                                    {!! $step->action !!}
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div>
                                                @if(isset($step->action))
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
        </div>
    </div>
@endsection

@section('footer')
    <script src="{{asset('/js/repo/case_editor.js')}}"></script>
    <script src="{{asset('/js/repo/case_crud.js')}}"></script>
@endsection
