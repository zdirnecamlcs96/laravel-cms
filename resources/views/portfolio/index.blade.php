@extends('adminlte::page')

@section('plugins.FancyBox', true)
@section('plugins.Bootbox', true)
@section('plugins.Datatables', true)

@section('css')
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
        <div class="d-flex align-items-center">
            <div class="col-auto p-0">
                <h3 class="card-title">Portfolio</h3>
            </div>
            <div class="col-auto">
                <a href="{{route('admin.portfolios.create')}}" class="btn btn-success btn-sm ">Create</a>
            </div>
        </div>
    </div>
    <!-- /.card-header -->

    <div class="card-body">
        <div class="form-group">
            <table class="datatable table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Thumbnail</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Uploaded At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($portfolios as $portfolio)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @forelse ($portfolio->images as $image)
                                @if ($loop->count > 1 && $loop->iteration == 2)
                                    <div class="d-none">
                                @endif
                                <img data-fancybox="gallery-{{$portfolio->id}}" href="{{ asset($image->file_path) }}" src="{{ asset($image->file_path) }}" class="preview" />
                                @if ($loop->count > 1 && $loop->last)
                                    <div>
                                @endif
                            @empty
                                <p>Image(s) not found.</p>
                            @endforelse
                        </td>
                        <td>{{ $portfolio->title }}</td>
                        <td>{{ $portfolio->description }}</td>
                        <td>{{ optional($portfolio->created_at)->format('d M Y') }}</td>
                        <td>
                            <a class="btn btn-sm btn-warning" href="{{ route('admin.portfolios.edit', $portfolio) }}">
                                <i class="fas fa-pen"></i>
                            </a>
                            <button class="btn btn-sm btn-danger record-delete" data-action-url="{{ route('admin.portfolios.destroy', $portfolio) }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
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
    $('.datatable').DataTable({
        dom: "<'row'<'col-sm-6'l> <'col-sm-6'f>>" +
            "<'row'<'col-sm-12' <'table-responsive' tr>>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    })
    $('body').on('click', '.record-delete', function(e) {
        e.preventDefault()
        var csrfField = document.createElement("input");
        csrfField.setAttribute("type", "hidden");
        csrfField.setAttribute("name", "_token");
		csrfField.setAttribute("value", "{{ csrf_token() }}");
		bootbox.confirm({
			message: "<form class='bootbox-form' id='portfolio-delete' method='POST'>\
                        <input name='_method' type='hidden' value='DELETE'>\
                        <p>Are you sure to remove this portfolio?</p>\
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
</script>
@if(session('success'))
<script>
    bootbox.alert("{{ session('success') }}");
</script>
@endif
@endsection