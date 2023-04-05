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
                <h3 class="card-title">Categories</h3>
            </div>
            @can('product_category_create')
            <div class="col-auto">
                <a href="{{route('admin.categories.create')}}" class="btn btn-success btn-sm ">Create</a>
            </div>
            @endcan
        </div>
    </div>
    <!-- /.card-header -->

    <div class="card-body">
        <div class="form-group">
            <table class="datatable table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Active</th>
                        <th>Last Modified</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->active ? 'Active' : 'Inactive' }}</td>
                        @if($category->last_modified)
                        <td> {{ $category?->last_modified->created_at->format('d/m/Y H:i:s') }} ({{ $category?->last_modified?->causer?->name ?? '-' }})</td>
                        @else
                        <td> - </td>
                        @endif
                        <td>{{ $category->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>
                            @can('product_category_update')
                            <a class="btn btn-sm btn-warning" href="{{ route('admin.categories.edit', $category) }}">
                                <i class="fas fa-pen"></i>
                            </a>
                            @endcan
                            @can('product_category_delete')
                            <button class="btn btn-sm btn-danger record-delete" data-action-url="{{ route('admin.categories.destroy', $category) }}">
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
                        <p>Are you sure to remove this user?</p>\
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
