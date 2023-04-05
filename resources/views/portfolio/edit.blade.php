@extends('adminlte::page')

@section('css')
<style>
    #file-wrapper {
        background-color: lightgrey;
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
                <h3 class="card-title">Edit Portfolio</h3>
            </div>
            <div class="col-auto">
                <a href="{{route('admin.portfolios.index')}}" class="btn btn-warning btn-sm ">Back</a>
            </div>
        </div>
    </div>
    <!-- /.card-header -->

    <div class="card-body">
        @include('modules::components.alert')
        <form action="{{route('admin.portfolios.update', [ 'portfolio' => $portfolio ] )}}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" class="form-control" required
                    placeholder="Please enter your title..." value="{{ old('title', $portfolio->title) }}">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="5" class="form-control" required
                    placeholder="Please enter your description...">{{ old('description', $portfolio->description) }}</textarea>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-light mb-3" data-toggle="modal"
                    data-target="#file-manager">
                    <i class="fas fa-folder-open"></i> Browse
                </button>
                <div id="file-wrapper" class="rounded d-flex flex-wrap mb-3">
                    @foreach ($images as $image)
                        <div class="col-auto py-2 file-holder">
                            <div style="background-image:url('{{ $image->file_path }}')" class="preview shadow-sm rounded">
                                <span class="remove-file-btn btn btn-light btn-sm shadow-sm">
                                    <i class="fas fa-times"></i>
                                </span>
                                <input type="hidden" name="files[]" value="{{ optional($image->files()->first())->id }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success">Update</button>
            </div>
        </form>
    </div>
</div>

@include('modules::modals.file-manager')

@endsection


@section('js')
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
                            <input type="hidden" name="files[]" value="' + file.id + '"> \
                        </div></div>';

            $('#file-wrapper').append(html)
            $('#file-manager').modal('hide')
        }
    });
    $('#file-manager').on('show.bs.modal', function (e) {
        $('#file-manager').attr('src', $('#file-manager').attr('src'));
    })
    $('#file-wrapper').on('click', '.remove-file-btn', function() {
        $(this).closest('.file-holder').fadeOut(300, function() { $(this).remove(); })
    })
</script>
@endsection