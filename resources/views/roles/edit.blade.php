@extends('adminlte::page')
@section('plugins.Bootbox', true)
@section('css')
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <div class="col-auto p-0">
                <h3 class="card-title">Edit Role</h3>
            </div>
            <div class="col-auto">
                <a href="{{route('admin.roles.index')}}" class="btn btn-warning btn-sm ">Back</a>
            </div>
        </div>
    </div>
    <!-- /.card-header -->

    <div class="card-body">

        {{ Form::open(['route'=> ['admin.roles.update', $role], 'method' => 'PUT']) }}
        @include('modules::roles.field', ['mode' => 'update'])

        <hr>
        <h5 class="mb-4">Permissions</h5>
        @include('modules::roles.permission', ['mode' => 'update'])

        <div class="form-group">
            <button type="submit" class="btn btn-success">Update</button>
        </div>
        {{Form::close()}}
    </div>
</div>


@endsection


@section('js')

@endsection