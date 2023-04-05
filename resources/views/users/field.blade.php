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
    <label for="contact">Contact</label><strong class="text-danger">*</strong>
    @error('phone') <small class="text-danger">{{$message}} </small> @enderror

    <div class="row col-12">
        {{Form::select('phone_code', $countries, null, ['class'=>'form-control col-1'])}}
        {{Form::text('phone',null,['class'=>'form-control ml-2 col-5','placeholder'=>'Contact'])}}
    </div>
</div>


<div class="form-group">
    <label for="nationality">Nationality</label><strong class="text-danger">*</strong>
    @error('nationality') <small class="text-danger">{{$message}} </small> @enderror
    {{Form::select('nationality', $nationalities, null, ['class'=>'form-control col-12'])}}
</div>

<div class="form-group">
    <label for="dob">Date of birth</label><strong class="text-danger">*</strong>
    @error('dob') <small class="text-danger">{{$message}} </small> @enderror
    <div class="input-group" data-target-input="nearest">
        <input
            autocomplete="off"
            type="text" class="form-control" style="padding: 0.375rem 0.75rem !important;" id="dob" name="dob" placeholder="Date of birth" value="{{ old('dob') ??
                (($user->dob ?? false) ? \Carbon\Carbon::parse($user->dob)->format('d/m/Y') : '') }}">
        <div class="input-group-append">
            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="gender">Gender</label>
    {{Form::select('gender', ['Male' => 'Male', 'Female' => 'Female'], null, ['class'=>'form-control'])}}
</div>

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
@endif

@push('js')
<script>
    $(document).ready(function() {
        $( "#dob" ).datepicker({
            format: "dd/mm/yyyy",
            changeMonth: true,
            changeYear: true,
            minDate:0
        });
    });
</script>
@endpush
