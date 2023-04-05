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
                <h3 class="card-title">Coupons</h3>
            </div>
            @can('admin_create')
            <div class="col-auto">
                <a href="{{route('admin.coupons.create')}}" class="btn btn-success btn-sm ">Create</a>
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
                        <th>Code</th>
                        <th>Type</th>
                        <th>Discount</th>
                        <th>Last Modified</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($coupons as $coupon)
                    <tr>
                        <td>{{ $coupon->name }}</td>
                        <td>{{ $coupon->code }}</td>
                        <td>{{ $coupon->applied_type_text }}</td>
                        <td>{{ ($coupon->discount_type == 'Fixed') ? 'RM '.$coupon->discount
                            : $coupon->discount.' % '.( ($coupon->max_discount && $coupon->max_discount > 0 )
                                ? '( ≈ RM '.$coupon->max_discount.' )' : '( ∞ )' ) ?? '' }}</td>
                        @if($coupon->last_modified)
                        <td> {{ $coupon?->last_modified->created_at->format('d/m/Y H:i:s') }} ({{ $coupon?->last_modified?->causer?->name ?? '-' }})</td>
                        @else
                        <td> - </td>
                        @endif
                        <td>{{ $coupon->active ? 'Active' : 'Inactive' }}</td>
                        <td>
                            @can('coupon_update')
                            <a class="btn btn-sm btn-warning" href="{{ route('admin.coupons.edit', $coupon) }}">
                                <i class="fas fa-pen"></i>
                            </a>
                            @endcan
                            @can('coupon_delete')
                            <button class="btn btn-sm btn-danger record-delete" data-action-url="{{ route('admin.coupons.destroy', $coupon) }}">
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
			message: "<form class='bootbox-form' id='coupon-delete' method='POST'>\
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

                    $('#coupon-delete').attr('action', url);
                    $('#coupon-delete').append(csrfField);
                    $('#coupon-delete').submit();
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
