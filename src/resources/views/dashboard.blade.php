@extends('log-viewer::backpack._master')

@section('content')
    <div class="row justify-content-center">
        @if($percents)
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body p-10">
                        <canvas id="stats-doughnut-chart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body p-10">
                        <div class="row">
                            @foreach($percents as $level => $item)
                                <div class="col-md-4">
                                    <div class="info-box level level-{{ $level }} {{ $item['count'] === 0 ? 'level-empty' : '' }}">
                                <span class="info-box-icon">
                                    {!! log_styler()->icon($level) !!}
                                </span>

                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ $item['name'] }}</span>
                                            <span class="info-box-number">
                                        {{ $item['count'] }} entries - {!! $item['percent'] !!} %
                                    </span>
                                            <div class="progress">
                                                <div class="progress-bar" style="width: {{ $item['percent'] }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-offset-md-3 col-md-6">
                <div class="jumbotron jumbotron-fluid">
                    <div class="container">
                        <p class="display-4 text-center">No logs available</p>
                    </div>
                </div>
            </div>

        @endif
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            new Chart($('canvas#stats-doughnut-chart'), {
                type: 'doughnut',
                data: {!! $chartData !!},
                options: {
                    legend: {
                        position: 'bottom'
                    }
                }
            });
        });
    </script>
@endsection
