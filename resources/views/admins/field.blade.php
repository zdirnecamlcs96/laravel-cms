<div class="form-group">
    <label for="name">Name</label><strong class="text-danger">*</strong>
    @error('name') <small class="text-danger">{{$message}} </small> @enderror
    {{Form::text('name',null,['class'=>'form-control','placeholder'=>'Name'])}}
</div>

<div class="form-group">
    <label for="email">Email</label><strong class="text-danger">*</strong>
    @error('email') <small class="text-danger">{{$message}} </small> @enderror
    {{Form::email('email',null,['class'=>'form-control','placeholder'=>'Email'])}}
</div>

<div class="form-group">
    <label for="contact">Contact</label>
    @error('contact') <small class="text-danger">{{$message}} </small> @enderror
    {{Form::text('contact',null,['class'=>'form-control','placeholder'=>'Contact'])}}
</div>

@if (config('modules.role_management'))
    <div class="form-group">
        <label for="active">Roles</label>
        {{ Form::select('roles[]', $roles, null, ['multiple' => 'multiple', 'class'=>'form-control select2']) }}
    </div>
@endif

<div class="form-group">
    <label for="active">Active</label>
    {{Form::select('active', ['1' => 'Active', '0' => 'Inactive'], null, ['class'=>'form-control'])}}
</div>

@if($mode == "create")
<div class="form-group">
    <label for="password">Password</label><strong class="text-danger">*</strong>
    @error('password') <small class="text-danger">{{$message}} </small> @enderror
    {{ Form::password('password', ['placeholder'=>'Password', 'class'=>'form-control'] ) }}
</div>

<div class="form-group">
    <label for="confirm_password">Confirm Password</label><strong class="text-danger">*</strong>
    @error('confirm_password') <small class="text-danger">{{$message}} </small> @enderror
    {{ Form::password('confirm_password', ['placeholder'=>'Password', 'class'=>'form-control'] ) }}
</div>
@else
<div class="form-group">
    <label for="password">New Password</label>
    @error('password') <small class="text-danger">{{$message}} </small> @enderror
    {{ Form::password('password', ['placeholder'=>'Password', 'class'=>'form-control'] ) }}
</div>

<div class="form-group">
    <label for="confirm_password">Confirm Password</label>
    @error('confirm_password') <small class="text-danger">{{$message}} </small> @enderror
    {{ Form::password('confirm_password', ['placeholder'=>'Password', 'class'=>'form-control'] ) }}
</div>

@endif

@push('js')
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endpush
