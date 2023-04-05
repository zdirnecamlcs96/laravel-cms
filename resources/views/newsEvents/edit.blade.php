@extends('adminlte::page')

@section('css')
<!-- Custom CSS -->
<link rel="stylesheet" href="{{asset( config('cms.asset_url') .'/css/banner.css?' . env('APP_VERSION'))}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" integrity="sha512-xmGTNt20S0t62wHLmQec2DauG9T+owP9e6VU8GigI0anN7OXLip9i7IwEhelasml2osdxX71XcYm6BQunTQeQg==" crossorigin="anonymous" />

<style>
    #file-wrapper {
        background-color: lightgrey;
    }

    .bootstrap-tagsinput {
        display: block !important;
    }

    .bootstrap-tagsinput .tag {
        margin-right: 2px;
        color: rgb(255, 255, 255);
        background: #C5353A;
        padding: 3px;
        border-radius: 10%;
    }

    .preview {
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        width: 8rem;
        height: 8rem;
        position: relative;
    }

    .preview .remove-file-btn {
        position: absolute;
        top: .5rem;
        right: .5rem;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <div class="col-auto p-0">
                <h3 class="card-title">Edit Admin</h3>
            </div>
            <div class="col-auto">
                <a href="{{route('admin.newsEvents.index')}}" class="btn btn-warning btn-sm ">Back</a>
            </div>
        </div>
    </div>
    <!-- /.card-header -->

    <div class="card-body">
        @include('modules::components.alert')
        {{Form::model($newsEvent,['route'=>['admin.newsEvents.update',$newsEvent->id]] )}}
        @csrf
        @method('PUT')

        @include('modules::newsEvents.field',['mode' => 'edit'])

        <div class="form-group">
            <button type="submit" class="btn btn-success">Update</button>
        </div>
        {{Form::close()}}
    </div>
</div>

@include('modules::modals.file-manager')

@endsection


@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js" integrity="sha512-VvWznBcyBJK71YKEKDMpZ0pCVxjNuKwApp4zLF3ul+CiflQi6aIJR+aZCP/qWsoFBA28avL5T5HA+RE+zrGQYg==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput-angular.min.js" integrity="sha512-KT0oYlhnDf0XQfjuCS/QIw4sjTHdkefv8rOJY5HHdNEZ6AmOh1DW/ZdSqpipe+2AEXym5D0khNu95Mtmw9VNKg==" crossorigin="anonymous"></script>

<script>
    window.addEventListener('message', function(e) {
        let data = e.data
        if (typeof data == "string" && data.startsWith("file")) {
            let file = JSON.parse(data.split('|')[1])
            let html = '<div class="col-auto py-2 file-holder">\
                            <div style="background-image:url(\'' + file.url + '\')" class="preview shadow-sm rounded"> \
                            <span class="remove-file-btn btn btn-light btn-sm shadow-sm"> \
                                <i class="fas fa-times"></i> \
                            </span> \
                            <input type="hidden" name="'+file.action+'" value="' + file.id + '"> \
                        </div></div>';

        if(file.action == "thumbnail"){
            $("#thumbnail-file-wrapper").empty();
            $('#thumbnail-file-wrapper').append(html)
        }else{
            $("#banner-file-wrapper").empty();
            $('#banner-file-wrapper').append(html)
        }        
            $('#file-manager').modal('hide')
        }
    });

    $('.brwose_btn').on('click', function (e) {
        $('#file-manager').find('iframe').attr('src', "{{ route('admin.file.manager') }}?action="+$(this).attr('data-type'));
    })

    $('#thumbnail-file-wrapper').on('click', '.remove-file-btn', function() {
        $(this).closest('.file-holder').fadeOut(300, function() { $(this).remove(); })
    })

    
    $('#banner-file-wrapper').on('click', '.remove-file-btn', function() {
        $(this).closest('.file-holder').fadeOut(300, function() { $(this).remove(); })
    })

    createEditor(document.querySelector( '#editor' ))

</script>
@endsection