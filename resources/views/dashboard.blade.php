@extends('blog-poster::components.layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6>Jobs</h6>
                        </div>
                        <div class="col-lg-6 col-5 my-auto text-end">
                            <div class="dropdown float-lg-end pe-4">
                                <a class="btn bg-gradient-dark mb-0" href="{{ route('blog-poster.jobs.create') }}"><i class="fas fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Add New Job</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        @if($jobs->count() > 0)
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Icon</th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Name</th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Source</th>

                                    @if(! is_null(config('blog-poster.category')))
                                        <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Category</th>
                                    @endif

                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Daily Limit</th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Is Draft</th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created At</th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Updated At</th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($jobs as $key => $job)
                                    <tr>
                                        <td>{{ $job->id }}</td>
                                        <td><img src="{{ $job->icon }}" width="30" style="border-radius: 100%"></td>
                                        <td>{{ $job->name }}</td>
                                        <td>{{ $job->source }}</td>

                                        @if(! is_null(config('blog-poster.category')))
                                            <td>{{ $job->category_id }}</td>
                                        @endif

                                        <td>{{ $job->daily_limit }}</td>
                                        <td>{{ $job->is_draft }}</td>
                                        <td>{{ $job->created_at }}</td>
                                        <td>{{ $job->updated_at }}</td>
                                        <td>
                                            <a href="{{ route('blog-poster.jobs.edit', ['id' => $job->id]) }}" class="btn bg-gradient-info mb-0">
                                                Edit
                                            </a>
                                            <a href="{{ route('blog-poster.jobs.copy', ['id' => $job->id]) }}" class="btn bg-gradient-info mb-0">
                                                Copy
                                            </a>
                                            <a href="{{ route('blog-poster.jobs.delete', ['id' => $job->id]) }}" class="action-delete-job btn bg-gradient-danger mb-0">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-center">There is no created job. Create your first one by clicking on <b>ADD NEW JOB</b> button.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(".action-delete-job").on('click', function (e){
            e.preventDefault();

            if(! window.confirm('Are you sure?'))
                return false;

            const job_row = $(this).closest('tr');
            const action_url = $(this).attr('href');

            $.ajax({
                url: action_url,
                type: 'delete',
                data:{
                    _token: $('meta[name=csrf]').attr('content')
                }
            }).done(function (){
                job_row.remove()
            })
        })
    </script>
@endsection
