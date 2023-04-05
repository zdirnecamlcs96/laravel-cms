<div class="row">

    <div class="form-group col-12">
        <label for="name">Name</label><strong class="text-danger">*</strong>
        @error('name') <small class="text-danger">{{$message}} </small> @enderror
        {{Form::text('name',null,['class'=>'form-control','placeholder'=>'Name'])}}
    </div>

    <div class="form-group col-12">
        <label for="active">Active</label>
        {{Form::select('active', ['1' => 'Active', '0' => 'Inactive'], null, ['class'=>'form-control'])}}
    </div>

</div>