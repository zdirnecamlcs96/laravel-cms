@extends('adminlte::page')

@section('plugins.FancyBox', true)
@section('plugins.Bootbox', true)

@section('css')
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{asset( config('cms.asset_url') .'/css/banner.css?' . env('APP_VERSION'))}}">

    <style>
        #pageloader
        {
        background: rgba( 255, 255, 255, 0.8 );
        display: none;
        height: 100%;
        position: fixed;
        width: 100%;
        z-index: 9999;
        }

        #pageloader img
        {
        left: 50%;
        margin-left: -32px;
        margin-top: -32px;
        position: absolute;
        top: 50%;
        }
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
        <h3 class="card-title">Banner</h3>
    </div>
    <!-- /.card-header -->

    <div class="card-body">
        <p>
            Last modified: {{ $lastModified?->updated_at->format('d/m/Y H:i:s') }} ({{ $lastModified?->causer?->name ?? '-' }})
        </p>
        <div id="enquiry-table_wrapper">
            @can('banner_create')
                <div class="form-group">
                    <button class="btn btn-light mb-3 brwose_btn" data-type="banner">
                        <i class="fas fa-folder-open"></i> Browse
                    </button>
                    <form action="{{ route('admin.banners.store') }}" method="POST">
                        @csrf
                        <div id="file-wrapper" class="rounded d-flex flex-wrap mb-3"></div>
                        <div class="form-group">
                            <button class="btn btn-success">Update</button>
                        </div>
                    </form>
                </div>
            @endcan

            <div class="form-row row-cols-2 row-cols-md-5">
                @forelse ($banners as $banner)
                    <div class="col same-height">
                        <div class="record-item">
                            <img data-fancybox="gallery" href="{{ asset($banner->file_path) }}" src="{{ asset($banner->file_path) }}" class="record-image" />
                            <div class="overlay">
                                @can('banner_update')
                                    <div class="control record-modify" data-action-url="{{ route('admin.banners.update', $banner->id) }}" data-seq="{{$banner->sequence}}" data-title="{{$banner->title}}" data-desc="{{$banner->desc}}" data-link="{{$banner->link}}"
                                        data-display-in="{{$banner->display_in}}"><i class="fa fa-edit"></i></div>
                                @endcan
                                @can('banner_delete')
                                    <div class="control record-delete" data-action-url="{{ route('admin.banners.destroy', $banner->id) }}"><i class="fa fa-trash"></i></div>
                                @endcan
                            </div>
                        </div>
                    </div>
                @empty
                    <p>No banner(s) found.</p>
                @endforelse
            </div>

        </div>
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
<script>
    $(document).ready(function(){
        $("#seq-edit").on("submit", function(){
            $("#pageloader").fadeIn();
        });//submit
        $('.brwose_btn').on('click', function (e) {
            console.log('here');
            $('#file-manager').modal('show');
            $('#file-manager').find('iframe').attr('src', "{{ route('admin.file.manager') }}?action="+$(this).attr('data-type'));
        })
    });//document ready
	$('body').on('click', '.record-delete', function(e) {
        e.preventDefault()
        var csrfField = document.createElement("input");
        csrfField.setAttribute("type", "hidden");
        csrfField.setAttribute("name", "_token");
		csrfField.setAttribute("value", "{{ csrf_token() }}");
		bootbox.confirm({
			message: "<form class='bootbox-form' id='banner-delete' method='POST'>\
                        <input name='_method' type='hidden' value='DELETE'>\
                        <p>Are you sure to remove this banner?</p>\
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

                    $('#banner-delete').attr('action', url);
                    $('#banner-delete').append(csrfField);
                    $('#banner-delete').submit();
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
        var display = $(this).data('display-in');

        var path = "{{asset('placeholder')}}"
        console.log(link,display,'asdas');
        var video = '';
        if(link){
            video = `<div><video width="100%" autoplay controls muted><source src="${path.replace('placeholder',link)}" type="video/mp4"></video></div>`
        }

		bootbox.confirm("<form class='bootbox-form' id='seq-edit' enctype='multipart/form-data' method='POST'>\
            <input name='_method' type='hidden' value='PATCH'>\
			<label>Sequence</label>\
            <input class='bootbox-input bootbox-input-number form-control' type='number' name='sequence' placeholder='Sequence' value=\"" + seq + "\"><br>\
            <label>Title</label>\
            <input class='bootbox-input bootbox-input-number form-control' type='text' name='title' placeholder='Title' value=\"" + title + "\"><br>\
            <label>Description</label>\
            <textarea class='bootbox-input bootbox-input-number form-control' type='text' name='desc' placeholder='Description'>" + desc + "</textarea><br>\
            <label>Video File</label><strong class='ml-3' style='color:red'>( Max upload size 150 MB )</strong><br>" + video + "<input class='bootbox-input bootbox-input-number form-control' type='file' name='link'><br>\
            <br><label>Display In</label>\
            <div class='radio-btn clearfix'>\
                <div class='radio d-inline-block m-2'>\
                    <input name='display_in' type='radio' class='allow'  value='web' " + ((display =='web') ? 'checked' : '' ) + "/>\
                    <label for='display_in_web'>\
                        Web\
                    </label>\
                </div>\
                <div class='radio d-inline-block m-2'>\
                    <input  name='display_in' type='radio' class='deny' value='mobile' " + ((display =='mobile') ? 'checked' : '' ) + "/>\
                    <label for='display_in_mobile'>\
                        Mobile\
                    </label>\
                </div>\
                <div class='radio d-inline-block m-2'>\
                    <input  name='display_in' type='radio' class='deny' value='both' " + ((display =='both') ? 'checked' : '' ) + "/>\
                    <label for='display_in_mobile'>\
                        Both\
                    </label>\
                </div>\
            </div>\
        </form>", function(result) {
            if(result) {
				var url = $(e.currentTarget).data('action-url');
                console.log('submit ...');
                $('#seq-edit').attr('action', url);
                $('#seq-edit').append(csrfField);
				$('#seq-edit').submit();



            }
        });
	})
</script>



@endsection

