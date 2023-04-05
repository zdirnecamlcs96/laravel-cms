@extends('adminlte::page')
@section('plugins.Select2', true)

@section('css')
<!-- Custom CSS -->
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-auto">
                <h3 class="card-title">Order {{$order->order_no ?? ''}}</h3>
            </div>
            <div class="col-auto">
                <a href="{{route('admin.orders.index')}}" class="btn btn-warning btn-sm ">Back</a>
            </div>

            <div class="col-9 text-right">
                <button id="send_email" class="action-btn btn btn-primary btn-sm ">Send Email</button>
            </div>
            <div class="col-1 text-right">
                <button id="print_receipt" class="action-btn btn btn-primary btn-sm ">Print Rceipt</button>
            </div>
        </div>


    </div>
    <!-- /.card-header -->

    <div class="card-body">
        @include('modules::components.alert')
        {{Form::model($order,['route'=>['admin.orders.update',$order->id]] )}}
        @csrf
        @method('PUT')

        @include('modules::orders.field',['mode' => 'edit'])

        {{Form::close()}}
    </div>
</div>

<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <form action="{{route('admin.orders.change_tracking_info',$order)}}" method="POST">
        @csrf
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Tracking Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group col-12">
                                <label>Tracking Code </label>
                                <input type="text" class="form-control" name="tracking_no" value="{{$order->tracking_no}}" required />
                            </div>
                            <div class="form-group col-12">
                                <label>Tracking URL</label>
                                <input type="text" class="form-control" name="tracking_url" value="{{$order->tracking_url}}" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection


@section('js')
<script>
    $(document).on("click","#print_receipt",function(){
        var url = "{{route('admin.orders.download_receipt',':id')}}";
        url = url.replace(':id', "{{$order->id}}");
        var w = window.open(url);
        w.window.print();
    })

    $(document).on("click","#send_email",function(){
        var url = "{{route('admin.orders.send_email',':id')}}";
        url = url.replace(':id', "{{$order->id}}");

        Swal.fire({
            title: 'Sending ...',
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false
        })

        $.ajax({
            type: "get",
            url: url,

            success: function(response){
                swal.fire('Successfuly sent')
            },

            error: function(response){
                if(response.status == 400){
                    $('#error_description').removeAttr('hidden',true);
                    $('#error_description').text(response)
                }else{
                    var error_messae = "";

                    $.each(response.responseJSON.errors, function( key, value ) {
                        error_messae = error_messae + key + " : " + value + "<br/>"
                    });

                    swal.fire('Alert',error_messae,'error')
                }

            }
        });
    })

    $(document).on("change","#status",function(){
    var status = $(this).val();
    var order_id = "{{$order->id}}";
    var token = "{{csrf_token()}}";
    var url = "{{route('admin.orders.change_status')}}";

    Swal.fire({
        title: 'Changing ...',
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false
    })

    $.ajax({
            type: "POST",
            url: url,
            data: {
                _token: token,
                status: status,
                order_id: order_id
            },

            success: function(response){
                swal.fire(response)
            },

            error: function(response){
                if(response.status == 400){
                    $('#error_description').removeAttr('hidden',true);
                    $('#error_description').text(response)
                }else{
                    var error_messae = "";

                    $.each(response.responseJSON.errors, function( key, value ) {
                        error_messae = error_messae + key + " : " + value + "<br/>"
                    });

                    swal.fire('Alert',error_messae,'error')
                }
            }
        });
    })
</script>
@endsection