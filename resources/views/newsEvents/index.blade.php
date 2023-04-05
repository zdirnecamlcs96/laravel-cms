@extends('adminlte::page')
@section('plugins.Bootbox', true)
@section('plugins.Datatables', true)


@section('css')
<!-- Custom CSS -->
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <div class="col-auto p-0">
                <h3 class="card-title">News & Events</h3>
            </div>
            @can('news_event_create')
                <div class="col-auto">
                    <a href="{{route('admin.newsEvents.create')}}" class="btn btn-success btn-sm ">Create</a>
                </div>
            @endcan
        </div>
    </div>
    <!-- /.card-header -->

    <div class="card-body">
        <div class="form-group">
            <table class="datatable table table-responsive">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Thumbnail</th>
                        <th>Banner</th>
                        <th>Categories</th>
                        <th>Title</th>
                        <th>Post Date</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Last Modified</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($newsEvents as $newsEvent)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><img src="{{$newsEvent->thumbnail }}" width="200px"/></td>
                        <td><img src="{{$newsEvent->banner }}" width="200px"/></td>
                        <td>{{ $newsEvent->categories }}</td>
                        <td>{{ $newsEvent->title}}</td>
                        <td>{{ \Carbon\Carbon::parse($newsEvent->display_date)->format('d/m/Y')}}</td>
                        <td>{{ $newsEvent->position}}</td>
                        <td>{{ $newsEvent->status_display}}</td>
                        @if($newsEvent->last_modified)
                        <td> {{ $newsEvent?->last_modified->created_at->format('d/m/Y H:i:s') }} ({{ $newsEvent?->last_modified?->causer?->name ?? '-' }})</td>
                        @else
                        <td> - </td>
                        @endif

                        <td>
                            @can('news_event_update')
                                <a class="btn btn-sm btn-warning" href="{{ route('admin.newsEvents.edit', $newsEvent) }}">
                                    <i class="fas fa-pen"></i>
                                </a>
                            @endcan
                            @can('news_event_delete')
                                <button class="btn btn-sm btn-danger record-delete" data-action-url="{{ route('admin.newsEvents.destroy', $newsEvent) }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection

@section('js')

<script>
    $('body').on('click', '.record-delete', function(e) {
        e.preventDefault()
        var csrfField = document.createElement("input");
        csrfField.setAttribute("type", "hidden");
        csrfField.setAttribute("name", "_token");
		csrfField.setAttribute("value", "{{ csrf_token() }}");
		bootbox.confirm({
			message: "<form class='bootbox-form' id='portfolio-delete' method='POST'>\
                        <input name='_method' type='hidden' value='DELETE'>\
                        <p>Are you sure to remove this newsEvent?</p>\
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

                    $('#portfolio-delete').attr('action', url);
                    $('#portfolio-delete').append(csrfField);
                    $('#portfolio-delete').submit();
				}
			}
		});
    })

    // $(document).ready(function(){
    //     $('.datatable').DataTable();
    // });
</script>
@include('shared.components.datatable')


@endsection
