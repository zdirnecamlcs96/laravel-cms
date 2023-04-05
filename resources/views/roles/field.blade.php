<div class="form-group">
    <label for="name">Name</label><strong class="text-danger">*</strong>
    @error('name') <small class="text-danger">{{$message}} </small> @enderror
    {{ Form::text('name', $mode === "update" ? $role->name : null,['class'=>'form-control','placeholder'=>'Name']) }}
</div>