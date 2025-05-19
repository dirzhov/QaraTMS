@extends('layout.base_layout')

@section('content')

    @include('layout.sidebar_nav')

    <div class="col">
        <div class="d-flex justify-content-between border-bottom my-3">

            <h3 class="page_title"> Edit Project
                <i class="bi bi-arrow-right-short text-muted"></i>
                {{$project->title}}
            </h3>

            <div>
                @can(App\Enums\UserPermission::delete_projects)
                    <form method="POST" action={{route("project_delete")}}>
                        @csrf
                        <input type="hidden" name="id" value="{{$project->id}}">

                        <button type="submit" class="btn btn-sm btn-danger">
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

        <div class="base_block shadow p-4">
            <form action="{{route('project_update')}}" method="POST">

                @csrf
                <input type="hidden" name="id" value="{{$project->id}}">

                <div class="mb-3">
                    <label for="title" class="form-label">Project Name</label>
                    <input type="text" class="form-control" name="title" required value="{{$project->title}}"
                           maxlength="100">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" name="description"
                              maxlength="255"> {{$project->description}} </textarea>
                </div>

                @can(UserPermission::add_edit_product_versions)
                <div class="mb-3 row">
                    <div class="col-6">
                        <div class="row mb-2">
                            <div class="col-8">
                                <input type="text" class="form-control" id="version_title" maxlength="100">
                            </div>
                            <div class="col-4">
                                <span id="save_version" class="btn btn-success btn-md me-2">
                                    <i class="bi bi-floppy"></i>
                                </span>
                                <span id="deactivate_version" class="btn btn-danger btn-md">
                                    <i class="bi bi-trash3"></i>
                                </span>
                            </div>
                        </div>
                    <select class="form-select" id="product_versions" size="8">
                        <option value="0" selected>not selected</option>
                        @foreach($versions as $version)
                            <option value="{{$version->id}}" {{$version->status == 0 ? 'class=deactivated':''}}>{{$version->name}}</option>
                        @endforeach
                    </select>
                    </div>
                    <div class="col-6">
                    </div>
                </div>
                @endcan

                <div>
                    <button type="submit" class="btn btn-warning px-5 me-2">
                        <b>Update</b>
                    </button>

                    <a href="{{ url()->previous() }}" class="btn btn-outline-dark px-5">
                        <b>Cancel</b>
                    </a>
                </div>

            </form>
        </div>
    </div>

    <script>
        $(function() {
            var version_id = 0;
            const input = '#version_title';

            $('#product_versions option').on( "dblclick", function(e) {
                var text = $(e.target).text(), val = $(e.target).val();
                if (parseInt(val) != 0) {
                    $(input).val(text);
                    version_id = val;
                }else {
                    $(input).val("");
                    version_id = 0;
                }
            } );

            $('#save_version').on( "click", function() {
                if (version_id > 0)
                    $.ajax({
                        type: "PUT",
                        url: `/product_version/${version_id}`,
                        data: {
                            _token: csrf_token,
                            'name': $(input).val(),
                        },
                        success: function (data) {
                            $(`#product_versions option[value="${version_id}"]`).text($(input).val());
                            infoToast(data.message);
                        },
                        error: (resp) => errorToast(resp.responseJSON.message)
                    });
                else
                    $.ajax({
                        type: "POST",
                        url: "/project/{{$project->id}}/product_version",
                        data: {
                            _token: csrf_token,
                            'name': $(input).val(),
                        },
                        success: function (data) {
                            $('#product_versions').append(`<option value="${data.data.id}">${data.data.name}</option>`);
                            version_id = data.data.id;
                            infoToast(data.message);
                        },
                        error: (resp) => errorToast(resp.responseJSON.message)
                    });
            });

            $('#deactivate_version').on( "click", function() {
                if (version_id > 0)
                    $.ajax({
                        type: "DELETE",
                        data: { _token: csrf_token },
                        url: `/product_version/${version_id}`,
                        success: function (data) {
                            $(`#product_versions option[value=${version_id}]`).addClass("deactivated");
                            infoToast(data.message);
                        },
                        error: (resp) => errorToast(resp.responseJSON.message)
                    });
            });

        });

    </script>
@endsection

