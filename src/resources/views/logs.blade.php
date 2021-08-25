@extends('log-viewer::backpack._master')

@section('content')
    {!! $rows->render() !!}

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover pb-0 mb-0">
                <thead>
                <tr>
                    @foreach($headers as $key => $header)
                        <th class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                            @if ($key == 'date')
                                <span class="label label-info">{{ $header }}</span>
                            @else
                                <span class="level level-{{ $key }}">
                                {!! log_styler()->icon($key) . ' ' . $header !!}
                            </span>
                            @endif
                        </th>
                    @endforeach
                    <th class="text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @if ($rows->count() > 0)
                    @foreach($rows as $date => $row)
                        <tr>
                            @foreach($row as $key => $value)
                                <td class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                                    @if ($key == 'date')
                                        <span class="label label-primary">{{ $value }}</span>
                                    @elseif ($value == 0)
                                        <span class="level level-empty">{{ $value }}</span>
                                    @else
                                        <a href="{{ route('log-viewer::logs.filter', [$date, $key]) }}">
                                            <span class="level level-{{ $key }}">{{ $value }}</span>
                                        </a>
                                    @endif
                                </td>
                            @endforeach
                            <td class="text-right">
                                <a href="{{ route('log-viewer::logs.show', [$date]) }}" class="btn btn-sm btn-info">
                                    <i class="la la-search"></i>
                                </a>
                                <a href="{{ route('log-viewer::logs.download', [$date]) }}" class="btn btn-sm btn-success">
                                    <i class="la la-download"></i>
                                </a>
                                <button type="button" data-toggle="modal" data-target="#delete-log-modal" class="btn btn-sm btn-danger" data-log-date="{{ $date }}">
                                    <i class="la la-trash-o"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="11" class="text-center">
                            <span class="label label-default">Logs Empty</span>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>

    {!! $rows->render() !!}
@endsection

@section('modals')
    {{-- DELETE MODAL --}}
    <div class="modal fade" id="delete-log-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-danger" role="document">
            <form id="delete-log-form" action="{{ route('log-viewer::logs.delete') }}" method="POST">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="date" value="">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">DELETE LOG FILE</h4>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this log file ?</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary float-left" type="button" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger" type="button" data-loading-text="Loading&hellip;">DELETE FILE</button>
                    </div>
                </div>
                <!-- /.modal-content-->
            </form>
        </div>
        <!-- /.modal-dialog-->
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            var deleteLogModal = $('div#delete-log-modal'),
                deleteLogForm  = $('form#delete-log-form'),
                submitBtn      = deleteLogForm.find('button[type=submit]');

            $("a[href=#delete-log-modal]").on('click', function(event) {
                event.preventDefault();
                var date = $(this).data('log-date');
                deleteLogForm.find('input[name=date]').val(date);
                deleteLogModal.find('.modal-body p').html(
                    'Are you sure you want to <span class="label label-danger">DELETE</span> this log file <span class="label label-primary">' + date + '</span> ?'
                );

                deleteLogModal.modal('show');
            });

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
                            location.reload();
                        }
                        else {
                            alert('AJAX ERROR ! Check the console !');
                            console.error(data);
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

            deleteLogModal.on('hidden.bs.modal', function() {
                deleteLogForm.find('input[name=date]').val('');
                deleteLogModal.find('.modal-body p').html('');
            });
        });
    </script>
@endsection
