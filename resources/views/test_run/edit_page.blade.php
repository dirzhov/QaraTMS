@extends('layout.base_layout')

@section('content')

    @include('layout.sidebar_nav')

    <div class="col">

        <div class="d-flex justify-content-between border-bottom my-3">

            <h3 class="page_title">
                Edit Test Run
                <i class="bi bi-arrow-right-short text-muted"></i>
                {{$testRun->title}}
            </h3>

            <div>
                @can('delete_test_runs')
                    <form method="POST" action="{{route("test_run_delete")}}">
                        @csrf
                        <input type="hidden" name="id" value="{{$testRun->id}}">
                        <input type="hidden" name="project_id" value="{{$testRun->project_id}}">

                        <button type="submit" class="btn btn-sm  btn-danger">
                            <i class="bi bi-trash3"></i>
                            Delete
                        </button>
                    </form>
                @endcan
            </div>

        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card p-4">
            <form method="POST" action="{{route('test_run_update')}}">
                @csrf

                <input type="hidden" name="id" value="{{$testRun->id}}">
                <input type="hidden" name="project_id" value="{{$testRun->project_id}}">
                <input type="hidden" name="is_automation" value="{{$testRun->is_automation}}">

                <div class="form-group row mb-3">
                    <label for="title" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="title" required maxlength="100" value="{{$testRun->title}}">
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="environment" class="col-sm-2 col-form-label">Environment</label>
                    <div class="col-sm-10">
                        <select id="environment" name="environment" class="selectpicker col-4">
                            @foreach (App\Enums\Environment::cases() as $option)
                                <option value="{{$option->value}}" data-content="{{ucfirst(mb_strtolower($option->name))}}" {{$option->value == $testRun->environment ? "selected" : ""}}></option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="os" class="col-sm-2 col-form-label">OS</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="os" required maxlength="32" value="{{$testRun->os}}">
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="os" class="col-sm-2 col-form-label">Browser</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="browser" required maxlength="32" value="{{$testRun->browser}}">
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="os" class="col-sm-2 col-form-label">Device</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="device" maxlength="32" value="{{$testRun->device}}">
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="run_parameters" class="col-sm-2 col-form-label">Run Parameters</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="run_parameters" maxlength="32" value="{{$testRun->run_parameters}}">
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="groups" class="col-sm-2 col-form-label">Groups</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="groups" maxlength="100" value="{{$testRun->groups}}">
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="priorities" class="col-sm-2 col-form-label">Priorities</label>
                    <select id="priorities" name="priorities[]" class="selectpicker col-4" multiple>
                        @foreach (App\Enums\CasePriority::cases() as $option)
                            <option value="{{$option->value}}" data-content="<i class='bi {{$option->cls()}} me-1'></i>{{ucfirst(mb_strtolower($option->name))}}"
                            {{is_array($testRun->priorities) && in_array($option->value, $testRun->priorities) ? ' selected' : ''}}></option>
                        @endforeach
                    </select>
                </div>


                <button type="submit" class="btn btn-warning px-5 me-2">
                    <b>Update</b>
                </button>

                <a href=" {{ url()->previous() }}" class="btn btn-outline-dark px-5">
                    <b>Cancel</b>
                </a>
            </form>
        </div>


    </div>

@endsection

