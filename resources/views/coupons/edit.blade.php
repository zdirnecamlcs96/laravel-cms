@extends('adminlte::page')
@section('plugins.Select2', true)

@section('css')
<!-- Custom CSS -->
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <div class="col-auto p-0">
                <h3 class="card-title">Edit Coupon</h3>
            </div>
            <div class="col-auto">
                <a href="{{route('admin.coupons.index')}}" class="btn btn-warning btn-sm ">Back</a>
            </div>
        </div>
    </div>
    <!-- /.card-header -->

    <div class="card-body">
        @include('modules::components.alert')
        {{Form::model($coupon,['route'=>['admin.coupons.update',$coupon->id]] )}}
        @csrf
        @method('PUT')

        @include('modules::coupons.field',['mode' => 'edit'])

        <div class="form-group">
            <button type="submit" class="btn btn-success">Update</button>
        </div>
        {{Form::close()}}
    </div>
</div>


@endsection


@section('js')

@endsection