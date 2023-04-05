<div class="row">

    <div class="form-group col-6">
        <label for="name">Name</label><strong class="text-danger">*</strong>
        @error('name') <small class="text-danger">{{$message}} </small> @enderror
        {{Form::text('name',null,['class'=>'form-control','placeholder'=>'Name'])}}
    </div>


    <div class="form-group col-6">
        <label for="code">Code</label><strong class="text-danger">*</strong>
        @error('code') <small class="text-danger">{{$message}} </small> @enderror
        {{Form::text('code',null,['class'=>'form-control','placeholder'=>'Code'])}}
    </div>

    <div class="form-group col-6">
        <label for="applied_type">Applied Type</label><strong class="text-danger">*</strong>
        @error('applied_type') <small class="text-danger">{{$message}} </small> @enderror
        {{Form::select('applied_type',Coupon::APPLIED_TYPE_CODE, null, ['class'=>'form-control col-12'])}}
    </div>


    <div class="form-group col-6">
        <label for="discount_type">Discount Type</label><strong class="text-danger">*</strong>
        @error('discount_type') <small class="text-danger">{{$message}} </small> @enderror
        {{Form::select('discount_type',['Fixed' => 'Fixed', 'Percent' => 'Percent'], null, ['class'=>'form-control col-12','id' => 'discount-type'])}}
    </div>

    <div class="form-group col-6">
        <label for="discount">Value</label><strong class="text-danger">*</strong>
        @error('discount') <small class="text-danger">{{$message}} </small> @enderror
        {{Form::text('discount',null,['class'=>'form-control','placeholder'=>'Value'])}}
    </div>

    <div class="form-group col-6">
        <label for="start_date">Start Date</label><strong class="text-danger">*</strong>
        @error('start_date') <small class="text-danger">{{$message}} </small> @enderror
        <!-- {{Form::date('start_date',null,['class'=>'form-control','placeholder'=>'Start Date'])}} -->
        <div class="input-group" data-target-input="nearest">
            <input
                autocomplete="off"
                type="text" class="form-control" style="padding: 0.375rem 0.75rem !important;" id="start_date" name="start_date" placeholder="Start Date" value="{{ old('start_date') ??
                    (($coupon->start_date ?? false) ? \Carbon\Carbon::parse($coupon->start_date)->format('d/m/Y') : '') }}">
            <div class="input-group-append">
                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
            </div>
        </div>
    </div>

    <div class="form-group col-6">
        <label for="end_date">End Date</label><strong class="text-danger">*</strong>
        @error('end_date') <small class="text-danger">{{$message}} </small> @enderror
        <!-- {{Form::date('end_date',null,['class'=>'form-control','placeholder'=>'End Date'])}} -->
        <div class="input-group" data-target-input="nearest">
            <input
                autocomplete="off"
                type="text" class="form-control" style="padding: 0.375rem 0.75rem !important;" id="end_date" name="end_date" placeholder="End Date" value="{{ old('end_date') ??
                    (($coupon->end_date ?? false) ? \Carbon\Carbon::parse($coupon->end_date)->format('d/m/Y') : '') }}">
            <div class="input-group-append">
                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
            </div>
        </div>
    </div>

    <div class="form-group col-6">
        <label for="min_spend">Min Spend <strong class="text-danger">*</strong></label>
        @error('min_spend') <small class="text-danger">{{$message}}</small> @enderror
        {{Form::number('min_spend',null,['class'=>'form-control','placeholder'=>'Min Spend'])}}
    </div>

    <div class="form-group col-6" id="max-discount-section" hidden>
        <label for="max_discount">Discount Cap <strong class="text-danger">*</strong></label>
        @error('max_discount') <small class="text-danger">{{$message}}</small> @enderror
        {{Form::number('max_discount',null,['class'=>'form-control','placeholder'=>'Max Discount'])}}
    </div>

    <div class="form-group col-6">
        <label for="usage_limit_per_coupon">Usage Limit Per Coupon</label><strong class="text-danger">*</strong> <small class="text-danger">(set -1 as unlimited)</small>
        @error('usage_limit_per_coupon') <small class="text-danger">{{$message}} </small> @enderror
        {{Form::number('usage_limit_per_coupon',null,['class'=>'form-control','placeholder'=>'Usage Limit Per Coupon'])}}
    </div>

    <div class="form-group col-6">
        <label for="usage_limit_per_customer">Usage Limit Per Customer</label><strong class="text-danger">*</strong> <small class="text-danger">(set -1 as unlimited)</small>
        @error('usage_limit_per_customer') <small class="text-danger">{{$message}} </small> @enderror
        {{Form::number('usage_limit_per_customer',null,['class'=>'form-control','placeholder'=>'Usage Limit Per Customer'])}}
    </div>


    <div class="form-group col-12">
        <label for="active">Active</label><strong class="text-danger">*</strong>
        {{Form::select('active', ['1' => 'Active', '0' => 'Inactive'], null, ['class'=>'form-control'])}}
    </div>


</div>

@push('js')
<script>
    $(document).ready(function() {

        console.log('listen....')
        $('#discount-type').change(function() {
            if($(this).find(":selected").val() == 'Percent'){
                $('#max-discount-section').removeAttr('hidden',true)
            }else{
                $('#max-discount-section').attr('hidden',true)
            }
        })

        $('#discount-type').trigger('change');

        $( "#start_date" ).datepicker({
            format: "dd/mm/yyyy",
            changeMonth: true,
            changeYear: true,
            minDate:0
        });

        $( "#end_date" ).datepicker({
            format: "dd/mm/yyyy",
            changeMonth: true,
            changeYear: true,
            minDate:0
        });
    });
</script>
@endpush

