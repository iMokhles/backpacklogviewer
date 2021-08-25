@extends('log-viewer::backpack._master')

@section('content')
    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-header"><i class="la la-align-justify"></i> Levels</div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($log->menu() as $levelKey => $item)
                            @if ($item['count'] === 0)
                                <li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center" disabled>
                                    {{ $item['name'] }} <span class="badge badge-primary badge-pill">{{ $item['count'] }}</span>
                                </li>
                            @else
                                <a href="{{ $item['url'] }}"
                                   class="list-group-item @if($levelKey === 'all') active @endif d-flex list-group-item-action justify-content-between align-items-center"
                                   style="background-color: {{log_styler()->color($levelKey)}}; color: #fff"
                                >
                                    {{ $item['name'] }} <span class="badge badge-default badge-pill">{{ $item['count'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Log info
                    <div class="btn-group btn-group-sm float-right" role="group" aria-label="actions">
                        <a class="btn btn-success" href="{{ route('log-viewer::logs.download', [$log->date]) }}"><i class="la la-download"></i> DOWNLOAD</a>
                        <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#delete-log-modal"><i class="la la-trash-o"></i> DELETE</button>
                    </div>
                </div>
                <div class="card-body">
                    <strong>File path:</strong> {{ $log->getPath() }}
                    <br />
                    <br />
                    <table class="table table-hover pb-0 mb-0">
                        <thead>
                        <tr>
                            <th>Log entries:</th>
                            <th>Size:</th>
                            <th>Created at:</th>
                            <th>Updated at:</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ $entries->total() }}</td>
                            <td>{{ $log->size() }}</td>
                            <td>{{ $log->createdAt() }}</td>
                            <td>{{ $log->updatedAt() }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <form action="{{ route('log-viewer::logs.search', [$log->date, $level]) }}" method="GET">
                        <div class=form-group">
                            <div class="input-group">
                                <input class="form-control" id="query" name="query" value="{!! request('query') !!}" placeholder="Type here to search">
                                <span class="input-group-append">
                                    @if (request()->has('query'))
                                        <a href="{{ route('log-viewer::logs.show', [$log->date]) }}" class="btn btn-danger"><span class="la la-remove"></span></a>
                                    @endif
                                    <button id="search-btn" class="btn btn-primary"><span class="la la-search"></span></button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-hover pb-0 mb-0">
                        <thead>
                        <tr>
                            <th>ENV</th>
                            <th style="width: 120px;">Level</th>
                            <th style="width: 65px;">Time</th>
                            <th>Header</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($entries as $key => $entry)
                            <tr>
                                <td>
                                    <span class="label label-env">{{ $entry->env }}</span>
                                </td>
                                <td>
                                        <span class="level level-{{ $entry->level }}">
                                            {!! $entry->level() !!}
                                        </span>
                                </td>
                                <td>
                                        <span class="label label-default">
                                            {{ $entry->datetime->format('H:i:s') }}
                                        </span>
                                </td>
                                <td>
                                    <p>{{ $entry->header }}</p>
                                </td>
                                <td class="text-right">
                                    @if ($entry->hasStack())
                                        <a class="btn btn-xs btn-default" role="button" data-toggle="collapse" href="#log-stack-{{ $key }}" aria-expanded="false" aria-controls="log-stack-{{ $key }}">
                                            <i class="la la-toggle-on"></i> Stack
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @if ($entry->hasStack())
                                <tr>
                                    <td colspan="5" class="stack">
                                        <div class="stack-content collapse" id="log-stack-{{ $key }}">
                                            {!! $entry->stack() !!}
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <span class="label label-default">Logs Empty</span>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($entries->hasPages())
                    <div class="card-footer">
                        {!! $entries->appends(compact('query'))->render() !!}

                        <span class="label label-info float-right">
                            Page {!! $entries->currentPage() !!} of {!! $entries->lastPage() !!}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('modals')
    {{-- DELETE MODAL --}}
    <div class="modal fade" id="delete-log-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-danger">
            <form id="delete-log-form" action="{{ route('log-viewer::logs.delete') }}" method="POST">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="date" value="{{ $log->date }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">DELETE LOG FILE</h4>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to <span class="label label-danger">DELETE</span> this log file <span class="label label-primary">{{ $log->date }}</span> ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-default pull-left" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger" data-loading-text="Loading&hellip;">DELETE FILE</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            var deleteLogModal = $('div#delete-log-modal'),
                deleteLogForm  = $('form#delete-log-form'),
                submitBtn      = deleteLogForm.find('button[type=submit]');

            deleteLogForm.on('submit', function(event) {
                event.preventDefault();
                submitBtn.button('loading');

                $.ajax({
                    url:      $(this).attr('action'),
                    type:     $(this).attr('method'),
                    dataType: 'json',
                    data:     $(this).serialize(),
                    success: function(data) {
                        submitBtn.button('reset');
                        if (data.result === 'success') {
                            deleteLogModal.modal('hide');
                            location.replace("{{ route('log-viewer::logs.list') }}");
                        }
                        else {
                            alert('OOPS ! This is a lack of coffee exception !')
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        alert('AJAX ERROR ! Check the console !');
                        console.error(errorThrown);
                        submitBtn.button('reset');
                    }
                });

                return false;
            });

            @unless (empty(log_styler()->toHighlight()))
            $('.stack-content').each(function() {
                var $this = $(this);
                var html = $this.html().trim()
                    .replace(/({!! join('|', log_styler()->toHighlight()) !!})/gm, '<strong>$1</strong>');

                $this.html(html);
            });
            @endunless
        });
    </script>
@endsection
