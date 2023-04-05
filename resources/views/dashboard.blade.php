@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col">

            </div>
            <div class="col">

            </div>
            <div class="col align-self-end">
                <select  class="form-control" name="statistics_type" onchange="update_stat(this.value)">
                    <option value="overall" {{ $statisticType == 'overall'?'selected':''}}>Overall Statistics</option>
                    <option value="daily" {{ $statisticType == 'daily'?'selected':''}}>Today's Statistics</option>
                    <option value="weekly" {{ $statisticType == 'weekly'?'selected':''}}>Weekly's Statistics</option>
                    <option value="monthly" {{ $statisticType == 'monthly'?'selected':''}}>Monthly's Statistics</option>
                    <option value="yearly" {{ $statisticType == 'yearly'?'selected':''}}>Anually's Statistics</option>
                </select>
            </div>
        </div>
        <div class="row">
            @foreach(collect($statistic)->sortBy('sequence') as $data)
            <div class="col-md-12 col-lg-3 mb-4" >
                <div class="small-box h-100 shadow {{$data['class']}}">
                    <div class="inner p-4">
                        @if(isset($data['prefix']))
                        <h4>{{$data['prefix']}}</h4>
                        @else
                        <h4 style="opacity:0"> - </h4>
                        @endif
                        <h3 class="{{(isset($data['decimal']) ? 'count-decimal':'count-number')}}" data-value="{{$data['value']}}"> - </h3>
                        <p>{{$data['label']}}</p>
                        </div>
                        <div class="icon">
                            <i class="{{$data['icon']}}" style="font-size:100px;top:35px;"></i>
                        </div>
                        @if (($data['more_info'] ?? true) !== false)
                            <a href="{{$data['route']}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        @endif
                    </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@stop

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
<script>
        function update_stat(type) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.dashboard.filter')}}',
                data: {
                    statistics_type: type
                },
                beforeSend: function () {
                    // $('#loading').show()
                },
                success: function (data) {
                    // insert_param('statistics_type',type);
                    // $('#order_stats').html(data.view)
                    window.location.reload();
                },
                complete: function () {
                    // $('#loading').hide()
                }
            });
        }

    // $( document ).ready(function() {
    //     console.log( "ready!" );

    // });

</script>
    {{-- <script> console.log('Hi!'); </script> --}}
@stop
