<div class="form-group">
    <label for="thumbnail">Thumbnail</label><strong class="text-danger">*</strong>
    @error('thumbnail') <small class="text-danger">{{$message}} </small> @enderror
    <br />
    <button type="button" class="btn btn-light mb-3 brwose_btn" data-type="thumbnail">
        <i class="fas fa-folder-open"></i> Browse
    </button>
</div>

<div id="thumbnail-file-wrapper" class="rounded d-flex flex-wrap mb-3">
@if($mode == "edit")
<div class="col-auto py-2 file-holder">
    <div style="background-image:url({{$newsEvent->thumbnail}})" class="preview shadow-sm rounded">
        <span class="remove-file-btn btn btn-light btn-sm shadow-sm">
            <i class="fas fa-times"></i>
        </span>
        <input type="hidden" name="thumbnail" value="{{optional($newsEvent->thumbnail_file())->id}}">
    </div>
</div>
@endif
</div>

<div class="form-group">
    <label for="banner">Banner</label><strong class="text-danger">*</strong>
    @error('banner') <small class="text-danger">{{$message}} </small> @enderror
    <br />
    <button type="button" class="btn btn-light mb-3 brwose_btn" data-type="banner">
        <i class="fas fa-folder-open"></i> Browse
    </button>
</div>

<div id="banner-file-wrapper" class="rounded d-flex flex-wrap mb-3">
@if($mode == "edit")
<div class="col-auto py-2 file-holder">
    <div style="background-image:url({{$newsEvent->banner}})" class="preview shadow-sm rounded">
        <span class="remove-file-btn btn btn-light btn-sm shadow-sm">
            <i class="fas fa-times"></i>
        </span>
        <input type="hidden" name="banner" value="{{$newsEvent->banner_file() ? $newsEvent->banner_file()->id : ''}}">
    </div>
</div>
@endif
</div>

<div class="form-group">
    <label for="categories">Categories</label><strong class="text-danger">*</strong>
    @error('categories') <small class="text-danger">{{$message}} </small> @enderror
    {{Form::text('categories',null,['class'=>'form-control','required','data-role'=>"tagsinput"])}}

</div>

<div class="form-group">
    <label for="title">title</label><strong class="text-danger">*</strong>
    @error('title') <small class="text-danger">{{$message}} </small> @enderror
    {{Form::text('title',null,['class'=>'form-control','required'])}}

</div>

<div class="form-group">
    <label for="description">Description</label>
    @error('description') <small class="text-danger">{{$message}} </small> @enderror
    {{Form::textarea('description',null,['class'=>'form-control','placeholder'=>'Description',"id"=>'editor','rows'=>'4'])}}
</div>

<div class="form-group col-6">
    <label for="display_date">Date Display</label>
    @error('display_date') <small class="text-danger">{{$message}} </small> @enderror
    
    <div class="input-group" data-target-input="nearest">
        <input
            autocomplete="off"
            type="text" class="form-control" style="padding: 0.375rem 0.75rem !important;" id="display_date" name="display_date" placeholder="Date display to user" value="{{ old('display_date') ??
                (($newsEvent->display_date ?? false) ? \Carbon\Carbon::parse($newsEvent->display_date)->format('d/m/Y') : '') }}">
        <div class="input-group-append">
            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
        </div>
    </div>
</div>

<div class="form-group col-4">
    <label for="position">Position</label><strong class="text-danger">*</strong>
    @error('position') <small class="text-danger">{{$message}} </small> @enderror
    {{Form::number('position', ($newsEvent->position ?? null), ['class'=>'form-control col-12','id'=>'position','id'=>'position','placeholder'=>'Sequence display in website'])}}
</div>

<div class="form-group col-6">
    <label for="status">Status</label>
    {{Form::select('status', ['1' => 'Active', '0' => 'Inactive'], null, ['class'=>'form-control','id' => 'status' , 'name' => 'status'])}}
</div>

@push('js')
<script>
    $(document).ready(function() {
        $( "#display_date" ).datepicker({
            format: "dd/mm/yyyy",
            changeMonth: true,
            changeYear: true,
            minDate:0
        });
    });
</script>
<script>
    $(document).ready(function(){
        $('.brwose_btn').on('click', function (e) {
            console.log('here');
            $('#file-manager').modal('show');
            $('#file-manager').find('iframe').attr('src', "{{ route('admin.file.manager') }}?action="+$(this).attr('data-type'));
        })
    });
</script>
@endpush
