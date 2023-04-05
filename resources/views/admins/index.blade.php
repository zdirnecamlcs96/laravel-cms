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
                <h3 class="card-title">Admins</h3>
            </div>
            @can('admin_create')
                <div class="col-auto">
                    <a href="{{route('admin.admins.create')}}" class="btn btn-success btn-sm ">Create</a>
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
                        <th>No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        @if (config('modules.role_management'))
                            <th>Roles</th>
                        @endif
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($admins as $admin)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ $admin->contact ?? '-' }}</td>
                        @if (config('modules.role_management'))
                            <td>{{ $admin->roles?->implode('name', ', ') }}</td>
                        @endif
                        <td>{{ $admin->active ? 'Active' : 'Inactive' }}</td>
                        <td>
                            @can('admin_update')
                                <a class="btn btn-sm btn-warning" href="{{ route('admin.admins.edit', $admin) }}">
                                    <i class="fas fa-pen"></i>
                                </a>
                            @endcan
                            @can('admin_delete')
                                <button class="btn btn-sm btn-danger record-delete" data-action-url="{{ route('admin.admins.destroy', $admin) }}">
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
                        <p>Are you sure to remove this admin?</p>\
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
    
    $(document).ready(function(){
        $('.datatable').DataTable();
    });
</script>



@endsection