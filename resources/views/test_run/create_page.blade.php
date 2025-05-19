@extends('layout.base_layout')

@section('content')

    @include('layout.sidebar_nav')

    <div class="col">

        <div class="border-bottom my-3">
            <h3 class="page_title">
                @if ($is_automation)
                Add Automation Test Run
                @else
                Add Test Run
                @endif
            </h3>
        </div>

        <div class="row m-0">

            <div class="col base_block p-3 shadow" style="margin-right: 10px;">

                <form action="{{route('test_run_create')}}" method="POST">
                    @csrf
                    <input type="hidden" name="project_id" value="{{$project->id}}">
                    <input type="hidden" name="is_automation" value="{{$is_automation}}">

                    <div class="form-group row mb-3">
                        <label for="title" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="title" required maxlength="100" value="{{$is_automation ? 'Automation Test Run' : 'Test Run'}}">
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="test_plan_id" class="col-sm-2 col-form-label">Select Test Plan</label>
                        <div class="col-sm-10">
                            <select name="test_plan_id" class="form-select" required>
                                <option disabled selected value> -----</option>

                                @foreach($testPlans as $testPlan)
                                    <option value="{{$testPlan->id}}">{{$testPlan->title}}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="environment" class="col-sm-2 col-form-label">Environment</label>
                        <div class="col-sm-10">
                            <select id="environment" name="environment" class="selectpicker col-4">
                                @foreach (App\Enums\Environment::cases() as $option)
                                    <option value="{{$option->value}}" data-content="{{ucfirst(mb_strtolower($option->name))}}"></option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="os" class="col-sm-2 col-form-label">OS</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="os" required maxlength="32" value="">
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="os" class="col-sm-2 col-form-label">Browser</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="browser" required maxlength="32" value="">
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="os" class="col-sm-2 col-form-label">Device</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="device" maxlength="32" value="">
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="run_parameters" class="col-sm-2 col-form-label">Run Parameters</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="run_parameters" maxlength="32" value="">
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="groups" class="col-sm-2 col-form-label">Groups</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="groups" maxlength="100" value="">
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="priorities" class="col-sm-2 col-form-label">Priorities</label>
                        <select id="priorities" name="priorities[]" class="selectpicker col-4" multiple>
                            @foreach (App\Enums\CasePriority::cases() as $option)
                                <option value="{{$option->value}}" data-content="<i class='bi {{$option->cls()}} me-1'></i>{{ucfirst(mb_strtolower($option->name))}}"></option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success w-100"><b>Save</b></button>
                </form>

            </div>

        </div>


    </div>

@endsection


