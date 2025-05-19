@php use Illuminate\Support\MessageBag;
/**
 * @var MessageBag $errors
 */
@endphp
@extends('layout.base_layout')

@section('content')

    @include('layout.sidebar_nav')

    <div class="col">

        <div class="border-bottom my-3">
            <h3 class="page_title">
                Create user
            </h3>
        </div>


        <form action="{{route('user_create')}}" method="POST">
            @csrf
            <div class="row m-0">

                <div class="col p-3 shadow me-3">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <div>
                                @foreach ($errors->all() as $error)
                                    <span>{{ $error }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif


                    <div class="form-group row mb-3">
                        <label class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10">
                            <input name="name" type="text" placeholder="Name" class="form-control" required autofocus>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-10">
                            <input name="email" type="text" placeholder="Email" class="form-control" required autofocus>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-sm-2 control-label">Password</label>
                        <div class="col-sm-10">
                            <input name="password" type="password" placeholder="Password. Min 6 symbols"
                                   minlength="6" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-sm-2 control-label">First Name</label>
                        <div class="col-sm-10">
                            <input name="first_name" type="text" placeholder="First Name" class="form-control" autofocus>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-sm-2 control-label">Last Name</label>
                        <div class="col-sm-10">
                            <input name="last_name" type="text" placeholder="Last Name" class="form-control" autofocus>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-sm-2 control-label">Projects</label>
                        <div class="col-sm-10">
                            <select name="projects[]" class="selectpicker col-12" multiple data-live-search="true" id="projects_select">
                                @foreach($projects as $project)
                                    <option value="{{$project->id}}">
                                        {{$project->title}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <select name="status" class="form-select border-secondary">
                                @foreach (\App\Enums\UserStatus::cases() as $option)
                                    <option value="{{$option->value}}" {{ ( $option == App\Enums\UserStatus::ACTIVE) ? 'selected' : '' }}>
                                        {{ucfirst(mb_strtolower($option->name))}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">

                        <button type="submit" class="btn btn-success px-5 mx-2">Create</button>

                        <a href="{{ url()->previous() }}" class="btn btn-outline-dark px-5">
                            <b>Cancel</b>
                        </a>
                    </div>

                </div>


                <div class="col p-3 shadow">

                    <h3>Permissions</h3>

                    <hr>

                    <table class="table table-striped">

                        <thead>
                        <tr class="table-primary">
                            <th scope="col">Entity</th>
                            <th scope="col">Add & Edit</th>
                            <th scope="col">Delete</th>
                        </tr>
                        </thead>

                        <tbody>

                        <tr>
                            <th scope="row">{{UserPermission::add_edit_projects->summary()}}</th>
                            <td><input name="add_edit_projects" class="form-check-input" type="checkbox"></td>
                            <td><input name="delete_projects" class="form-check-input" type="checkbox"></td>
                        </tr>

                        <tr>
                            <th scope="row">{{UserPermission::add_edit_repositories->summary()}}</th>
                            <td><input name="add_edit_repositories" class="form-check-input" type="checkbox"></td>
                            <td><input name="delete_repositories" class="form-check-input" type="checkbox"></td>
                        </tr>

                        <tr>
                            <th scope="row">{{UserPermission::add_edit_test_suites->summary()}}</th>
                            <td><input name="add_edit_test_suites" class="form-check-input" type="checkbox"></td>
                            <td><input name="delete_test_suites" class="form-check-input" type="checkbox"></td>
                        </tr>

                        <tr>
                            <th scope="row">{{UserPermission::add_edit_test_cases->summary()}}</th>
                            <td><input name="add_edit_test_cases" class="form-check-input" type="checkbox"></td>
                            <td><input name="delete_test_cases" class="form-check-input" type="checkbox"></td>
                        </tr>

                        <tr>
                            <th scope="row">{{UserPermission::add_edit_test_plans->summary()}}</th>
                            <td><input name="add_edit_test_plans" class="form-check-input" type="checkbox"></td>
                            <td><input name="delete_test_plans" class="form-check-input" type="checkbox"></td>
                        </tr>

                        <tr>
                            <th scope="row">{{UserPermission::add_edit_test_runs->summary()}}</th>
                            <td><input name="add_edit_test_runs" class="form-check-input" type="checkbox"></td>
                            <td><input name="delete_test_runs" class="form-check-input" type="checkbox"></td>
                        </tr>

                        <tr>
                            <th scope="row">{{UserPermission::add_edit_documents->summary()}}</th>
                            <td><input name="add_edit_documents" class="form-check-input" type="checkbox"></td>
                            <td><input name="delete_documents" class="form-check-input" type="checkbox"></td>
                        </tr>

                        <tr>
                            <th scope="row">{{UserPermission::manage_users->summary()}}</th>
                            <td colspan="2" style="padding-left: 20%;">
                                <input name="manage_users" class="form-check-input" type="checkbox">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">{{UserPermission::view_automation_runs->summary()}}</th>
                            <td colspan="2" style="padding-left: 20%;">
                                <input name="view_automation_runs" class="form-check-input" type="checkbox">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">{{UserPermission::manage_automation_runs->summary()}}</th>
                            <td colspan="2" style="padding-left: 20%;">
                                <input name="manage_automation_runs" class="form-check-input" type="checkbox">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">{{UserPermission::add_edit_product_versions->summary()}}</th>
                            <td colspan="2" style="padding-left: 20%;">
                                <input name="add_edit_product_versions" class="form-check-input" type="checkbox">
                            </td>
                        </tr>

                        </tbody>
                    </table>

                </div>

            </div>

        </form>

    </div>

@endsection

@section('footer')
    <script>
        $('body').on('click', 'th', function () {
            $(this).next().find('input[type=checkbox]').each(function () {
                this.checked = !this.checked;
            });
            $(this).next().next().find('input[type=checkbox]').each(function () {
                this.checked = !this.checked;
            });
        });
    </script>
@endsection
