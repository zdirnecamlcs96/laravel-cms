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
                <h3 class="card-title">Shipping Cost Config</h3>
            </div>
        </div>
    </div>
    <!-- /.card-header -->

    <div class="card-body">
        <div class="form-group">
            {{ Form::open(['route'=> ['admin.shipping_cost_configs.store'], 'method' => 'POST']) }}
            <table class="datatable table">
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Shipping Cost</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>East Malaysia</td>
                        <td><input type="number" name="east_shipping_cost" class="form-control col-6" value="{{$east_shipping_cost->value ?? 0 }}" /> </td>
                        <td>{{ $east_shipping_cost->updated_at ?? '-'}}</td>
                    </tr>
                    <tr>
                        <td>West Malaysia</td>
                        <td><input type="number" name="west_shipping_cost" class="form-control col-6" value="{{$west_shipping_cost->value ?? 0 }}" /> </td>
                        <td>{{ $west_shipping_cost->updated_at ?? '-'}}</td>
                    </tr>
                    <tr>
                        <td>International</td>
                        <td><input type="number" name="international_shipping_cost" class="form-control col-6" value="{{$international_shipping_cost->value ?? 0 }}" /> </td>
                        <td>{{ $international_shipping_cost->updated_at ?? '-'}}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right;"><button type="submit" class="btn btn-success">Update</button></td>
                    </tr>
                </tfoot>
            </table>
            {{Form::close()}}

        </div>
    </div>
</div>


@endsection

@section('js')

<script>
    // $(document).ready(function(){
    //     $('.datatable').DataTable();
    // });
</script>

@include('shared.components.datatable')

@endsection
