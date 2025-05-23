@php use App\Models\Project;use App\Models\Repository;
/**
 * @var Repository[] $repositories
 * @var Project $project
 */
@endphp
@extends('layout.base_layout')

@section('content')

    @include('layout.sidebar_nav')


    <div class="col">

        <div class="border-bottom my-3">
            <h3 class="page_title">
                Repositories

                @can(App\Enums\UserPermission::add_edit_repositories)
                    <a class="mx-3" href="{{route("repository_create_page", $project->id)}}">
                        <button type="button" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i> Add New
                        </button>
                    </a>
                @endcan
            </h3>
        </div>


        <div class="row row-cols-3 g-3">
            @foreach($repositories as $repository)

                <div class="col">
                    <div class="card base_block border h-100 shadow-sm rounded">

                        <div class="card-body">
                            <div>
                                <i class="bi bi-stack"></i>
                                <a class="fs-4"
                                   href="{{ route('repository_show_page', [$project->id, $repository->id]) }}">{{$repository->title}}</a>
                            </div>

                            @if($repository->description)
                                <div class="card-text text-muted">
                                    <span> {{$repository->description}} </span>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end border-top p-2">
                            <span class="text-muted">
                                <b>{{ $repository->suitesCount() }}</b> Test Suites
                                 | <b>{{ $repository->casesCount() }}</b> Test Cases
                                  | <b>{{ $repository->automatedCasesCount() }}</b> Automated
                             </span>
                        </div>

                    </div>
                </div>

            @endforeach
        </div>

    </div>

@endsection


