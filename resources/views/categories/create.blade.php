@extends('adminlte::page')

@section('css')
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <div class="col-auto p-0">
                <h3 class="card-title">New Category</h3>
            </div>
            <div class="col-auto">
                <a href="{{route('admin.categories.index')}}" class="btn btn-warning btn-sm ">Back</a>
            </div>
        </div>
    </div>
    <!-- /.card-header -->

    <div class="card-body">

        {{Form::open(['route'=>'admin.categories.store'])}}
        @csrf
        @include('modules::categories.field',['mode' => 'create'])

        <div class="form-group">
            <button type="submit" class="btn btn-success">Create</button>
        </div>
        {{Form::close()}}
    </div>

</div>


@endsection


@section('js')
<script>
</script>
@endsection