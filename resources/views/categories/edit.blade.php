@extends('adminlte::page')

@section('css')
<!-- Custom CSS -->
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <div class="col-auto p-0">
                <h3 class="card-title">Edit Category</h3>
            </div>
            <div class="col-auto">
                <a href="{{route('admin.categories.index')}}" class="btn btn-warning btn-sm ">Back</a>
            </div>
        </div>
    </div>
    <!-- /.card-header -->

    <div class="card-body">
        @include('modules::components.alert')
        {{Form::model($category,['route'=>['admin.categories.update',$category->id]] )}}
        @csrf
        @method('PUT')

        @include('modules::categories.field',['mode' => 'edit'])

        <div class="form-group">
            <button type="submit" class="btn btn-success">Update</button>
        </div>
        {{Form::close()}}
    </div>
</div>


@endsection


@section('js')

@endsection