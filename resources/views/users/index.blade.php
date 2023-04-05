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
                <h3 class="card-title">Users</h3>
            </div>
            @can('user_create')
            <div class="col-auto">
                <a href="{{route('admin.users.create')}}" class="btn btn-success btn-sm ">Create</a>
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
                        <th>Date of birth</th>
                        <th>Gender</th>
                        <th>Active</th>
                        @foreach ($hiddenFields as $name => $value)
                        <th scope="col"><a class="list-sort text-muted">{{ __($name) }}</a></th>
                        @endforeach
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone_code ? ('+'.$user->phone_code) : '' }}{{ $user->phone ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($user->dob)->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $user->gender ?? '-' }}</td>
                        <td>{{ $user->active ? 'Active' : 'Inactive' }}</td>
                        <td>{{ $user->formatted_billing_address ?? '-'}}</td>
                        <td>{{ $user->formatted_shipping_address ?? '-'}}</td>
                        <td>
                            <a class="btn btn-sm btn-primary reset-password" data-action-url="{{ route('admin.users.reset_password', $user) }}">
                                Reset Password
                            </a>
                            @can('user_update')
                            <a class="btn btn-sm btn-warning" href="{{ route('admin.users.edit', $user) }}">
                                <i class="fas fa-pen"></i>
                            </a>
                            @endcan
                            @can('user_delete')
                            <button class="btn btn-sm btn-danger record-delete" data-action-url="{{ route('admin.users.destroy', $user) }}">
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

    $('body').on('click', '.reset-password', function(e) {
        e.preventDefault()
        var csrfField = document.createElement("input");
        csrfField.setAttribute("type", "hidden");
        csrfField.setAttribute("name", "_token");
		csrfField.setAttribute("value", "{{ csrf_token() }}");
		bootbox.confirm({
			message: "<form class='bootbox-form' id='reset-password' method='POST'>\
                        <p>Are you sure to reset password for this user?</p>\
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

                    $('#reset-password').attr('action', url);
                    $('#reset-password').append(csrfField);
                    $('#reset-password').submit();
				}
			}
		});
    })

    // $(document).ready(function(){
    //     $('.datatable').DataTable();
    // });
</script>
@php
    $hiddenColumns = [];
    foreach ($hiddenFields as $field){
        array_push($hiddenColumns,[ "targets" => $field, "visible" => false, "sortable" => false ]);
    }
    $exportTitle = "CLM User Report ".now()->toDateTimeString();
@endphp
@include('shared.components.datatable',['columnsVariable' => $hiddenColumns, 'exportTitle' => $exportTitle])




@endsection
