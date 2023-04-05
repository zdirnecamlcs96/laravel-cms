@extends('adminlte::page')

@section('plugins.Dropzone', true)
@section('plugins.FancyBox', true)
@section('plugins.Bootbox', true)
@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('css')
<!-- Custom CSS -->

<link rel="stylesheet" href="{{asset( config('cms.asset_url') .'/css/banner.css?' . env('APP_VERSION'))}}">
<style>
    .preview {
        height: 3rem;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">File (Maximum image size cannot exceed more than 2 MB)</h3>
    </div>
    <!-- /.card-header -->

    <div class="card-body">
        @include('modules::file.file_manager', compact('files'))
    </div>
</div>
@endsection

@section('js')
<script>
    $('.table').on('click', '.btn-select', function() {

        let obj = {
            id: $(this).data('id'),
            url: $(this).data('path'),
            action: (new URL(document.location))?.searchParams?.get('action')
        }
        window.parent.postMessage('file|' + JSON.stringify(obj), "*");
        Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: 'Selected',
                showConfirmButton: false,
                timer: 1500
        })
    })
</script>
<script>
    Dropzone.autoDiscover = false;

    $(document).ready(function(){

        $('.datatable').DataTable({
            dom: "<'row'<'col-sm-6'l> <'col-sm-6'f>>" +
                "<'row'<'col-sm-12' <'table-responsive' tr>>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        })

        @if(count($errors->formerror->all()) > 0)
            bootbox.alert({
                message: "<b>Please resolve the following errors:</b></br>"
                    @foreach($errors->formerror->all() as $error)
                        + "</br> {{ $error }}"
                    @endforeach
            });
        @endif

        var myDropzone = new Dropzone(".record_dropzone", {
            url: $('#dropzone-form').data('action'),
            uploadMultiple: true,
            parallelUploads: 10,
            acceptedFiles: ".jpeg,.jpg,.png",
            maxFilesize: 2,
            autoProcessQueue: true,
            error: function(file, response) {
                if ($.type(response) === "string") {
                    var message = response;
                } else {
                    var message = response.message;
                }
                $('.dropzone').append(message);
            },
            paramName: "file",
        });

         myDropzone.on("queuecomplete", function() {
            // console.log(progress)
            // if(progress){
            //     window.location.reload();
            // }
        });

        myDropzone.on('success',file => {
            // console.log(file,'this is file');
            window.location.reload();
        })


    })

	$('body').on('click', '.record-delete', function(e) {
        e.preventDefault()
        var csrfField = document.createElement("input");
        csrfField.setAttribute("type", "hidden");
        csrfField.setAttribute("name", "_token");
		csrfField.setAttribute("value", "{{ csrf_token() }}");
		bootbox.confirm({
			message: "<form class='bootbox-form' id='file-delete' method='POST'>\
            <input name='_method' type='hidden' value='DELETE'>\
            <p>Are you sure to remove this file?</p>\
            </form>",
			buttons: {
				confirm: {
					label: 'Yes',
					className: 'btn-success'
				},
				cancel: {
					label: 'No',
					className: 'btn-danger'
				}
			},
			callback: function (result) {
				if (result) {
                    var url = $(e.currentTarget).data('action-url');

                    $('#file-delete').attr('action', url);
                    $('#file-delete').append(csrfField);
                    $('#file-delete').submit();
				}
			}
		});
    })

    $('.record-modify').on('click', function(e) {
        e.preventDefault()
        var csrfField = document.createElement("input");
        csrfField.setAttribute("type", "hidden");
        csrfField.setAttribute("name", "_token");
		csrfField.setAttribute("value", "{{ csrf_token() }}");
        var seq = $(this).data('seq');
        var title = $(this).data('title');
        var desc = $(this).data('desc');
        var link = $(this).data('link');
        var banner_id = $(this).data('id');

		bootbox.confirm("<form class='bootbox-form' id='seq-edit' method='POST'>\
            <input name='_method' type='hidden' value='PATCH'>\
            <input name='banner_id' type='hidden' value=\"" + banner_id + "\">\
			<label>Sequence</label>\
            <input class='bootbox-input bootbox-input-number form-control' type='number' name='seq' placeholder='Sequence' value=\"" + seq + "\"><br>\
            <br>\
        </form>", function(result) {
            if(result) {
				var url = $(e.currentTarget).data('action-url');

                $('#seq-edit').attr('action', url);
                $('#seq-edit').append(csrfField);
				$('#seq-edit').submit();
            }
        });
	})
</script>
@endsection
