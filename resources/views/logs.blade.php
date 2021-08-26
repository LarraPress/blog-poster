@extends('blog-poster::components.layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6>Job Logs</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        @if($logs->count() > 0)
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Job Name</th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Scraped Posts Count</th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Source URL</th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Log</th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created At</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($logs->items() as $key => $log)
                                    <tr>
                                        <td>{{ $log->job->name }}</td>
                                        <td>{{ $log->status }}</td>
                                        <td>{{ $log->scraped_posts_count }}</td>
                                        <td>{{ $log->source_url }}</td>
                                        <td><span class="log-col">{{ $log->log }}</span></td>
                                        <td>{{ $log->created_at }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            {!! $logs->links('blog-poster::components._paginator') !!}
                        @else
                            <p class="text-center">There is no log.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
