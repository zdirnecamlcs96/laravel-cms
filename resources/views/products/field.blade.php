<div class="row justify-content-center">
    <div class="card col-md-10">
        <div class="card-header">
            <h3 class="card-title">Product Details</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row form-group">
                <div class="form-group col-12">

                    <label for="categories">Categories</label><strong class="text-danger">*</strong>
                    @error('categories') <small class="text-danger">{{$message}} </small> @enderror
                    {{Form::select('categories[]',$categories,null,['class'=>'form-control select2','multiple'=>true])}}
                </div>

                <div class="form-group col-6">
                    <label for="name">Name</label><strong class="text-danger">*</strong>
                    @error('name') <small class="text-danger">{{$message}} </small> @enderror
                    {{Form::text('name',null,['class'=>'form-control','placeholder'=>'Name'])}}
                </div>


                <div class="form-group col-6">
                    <label for="quantity">Quantity</label><strong class="text-danger">*</strong>
                    @error('quantity') <small class="text-danger">{{$message}} </small> @enderror
                    {{Form::number('quantity',null,['class'=>'form-control','placeholder'=>'Quantity','id'=>'quantity'])}}
                </div>

                <div class="form-group col-12">
                    <label for="description">Description</label><strong class="text-danger">*</strong>
                    @error('description') <small class="text-danger">{{$message}} </small> @enderror
                    {{Form::textarea('description',null,['class'=>'form-control','placeholder'=>'Description',"id"=>'editor','rows'=>'4'])}}
                </div>

                <div class="form-group col-3" :class="{ 'd-none': hasSku }">
                    <label for="price">Price</label><strong class="text-danger">*</strong>
                    @error('price') <small class="text-danger">{{$message}} </small> @enderror
                    {{Form::number('price', ($product->formatted_price ?? null), ['class'=>'form-control col-12','id'=>'product_price','id'=>'price','step'=>'.01'])}}
                </div>
                <div class="form-group col-3" :class="{ 'd-none': hasSku }">
                    <label for="special_price" @click='toggleSku(!$hasSku)'>Special Price</label>
                    @error('special_price') <small class="text-danger">{{$message}} </small> @enderror
                    {{Form::number('special_price', ($product->formatted_special_price ?? null), ['class'=>'form-control col-12','id'=>'special_price','id'=>'special_price','step'=>'.01'])}}
                </div>

                <div class="form-group col-3">
                    <label for="weight">Weight</label><strong class="text-danger">*</strong>
                    @error('weight') <small class="text-danger">{{$message}} </small> @enderror
                    <div class="input-group">
                        {{Form::number('weight', ($product->_formatted_weight ?? null), ['class'=>'form-control col-12','id'=>'weight','id'=>'weight'])}}
                        <div class="input-group-append">
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>
                </div>

                {{-- <div class="form-group col-3">
                    <label for="special_price_type">Special Price Type</label>
                    @error('special_price_type') <small class="text-danger">{{$message}} </small> @enderror
                    {{Form::select('special_price_type',['Fixed' => 'Fixed'], null, ['class'=>'form-control col-12','id'=>'special_price_type'])}}
                </div> --}}



                <div class="form-group col-6" :class="{ 'd-none': hasSku }">
                    <label for="special_price_start">Special Price Start Date</label>
                    @error('special_price_start') <small class="text-danger">{{$message}} </small> @enderror
                    <div class="input-group" data-target-input="nearest">
                        <input
                            autocomplete="off"
                            type="text" class="form-control" style="padding: 0.375rem 0.75rem !important;" id="special_price_start" name="special_price_start" placeholder="Special Price Start Date" value="{{ old('special_price_start') ??
                                (($product->special_price_start ?? false) ? \Carbon\Carbon::parse($product->special_price_start)->format('d/m/Y') : '') }}">
                        <div class="input-group-append">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>

                <div class="form-group col-6" :class="{ 'd-none': hasSku }">
                    <label for="special_price_end">Special Price End Date</label>
                    @error('special_price_end') <small class="text-danger">{{$message}} </small> @enderror
                    <div class="input-group" data-target-input="nearest">
                        <input
                            autocomplete="off"
                            type="text" class="form-control" style="padding: 0.375rem 0.75rem !important;" id="special_price_end" name="special_price_end" placeholder="Special Price End Date" value="{{ old('special_price_end') ??
                                (($product->special_price_end ?? false) ? \Carbon\Carbon::parse($product->special_price_end)->format('d/m/Y') : '') }}">
                        <div class="input-group-append">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>

                <div class="form-group col-6">
                    <label for="active">Active</label>
                    {{Form::select('active', ['1' => 'Active', '0' => 'Inactive'], null, ['class'=>'form-control'])}}
                </div>

                <div class="form-group col-6">
                    <label for="is_feature">Is Featured</label>
                    {{Form::select('is_feature', ['0' => 'No', '1' => 'Yes'], null, ['class'=>'form-control'])}}
                </div>
                <div class="form-group col-6">
                    <label for="sequence">Sequence</label><strong class="text-danger">*</strong>
                    {{Form::number('sequence', ($product->sequence ?? 1 ), ['class'=>'form-control'])}}
                </div>

            </div>
        </div>
    </div>

    <div class="card col-md-10">
        <div class="card-header">
            <h3 class="card-title">Media</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">

            <div class="row form-group">

                <div class="form-group col-12">
                    <label for="thumbnail">Thumbnail</label><strong class="text-danger">*</strong>
                    @error('thumbnail') <small class="text-danger">{{$message}} </small> @enderror
                    <br />
                    <button type="button" class="btn btn-light mb-3 brwose_btn" data-type="thumbnail">
                        <i class="fas fa-folder-open"></i> Browse
                    </button>

                    <div id="thumbnail-file-wrapper" class="rounded d-flex flex-wrap mb-3">
                        @if($mode == "edit")
                        <div class="col-auto py-2 file-holder">
                            <div style="background-image:url({{$product->thumbnail_file()->full_path ?? '#'}})" class="preview shadow-sm rounded">
                                <span class="remove-file-btn btn btn-light btn-sm shadow-sm">
                                    <i class="fas fa-times"></i>
                                </span>
                                <input type="hidden" name="thumbnail[]" value="{{$product->thumbnail_file()->id ?? ''}}">
                            </div>
                        </div>
                        @endif
                    </div>

                </div>


                <div class="form-group col-12">
                    <label for="additional_images">Additional Images</label><strong class="text-danger">*</strong>
                    @error('additional_images') <small class="text-danger">{{$message}} </small> @enderror
                    <br />
                    <button type="button" class="btn btn-light mb-1 brwose_btn" data-type="additional_images">
                        <i class="fas fa-folder-open"></i> Browse
                    </button>
                    <p class="text-muted m-0"><small>Drag the image(s) below to rearrange their sequence.</small></p>

                    <div id="additional-images-file-wrapper" class="rounded d-flex flex-wrap mb-3">
                        @if($mode == "edit")
                        @foreach($product->additional_images_file() as $additional_image)
                        <div id="image-{{$additional_image->id}}" class="col-auto py-2 file-holder" ondragover="allowDrop(event)" draggable="true" ondragstart="drag(event)" ondrop="drop(event)" style="cursor: pointer;">
                            <div style="background-image:url({{$additional_image->full_path}})" class="preview shadow-sm rounded">
                                <span class="remove-file-btn btn btn-light btn-sm shadow-sm">
                                    <i class="fas fa-times"></i>
                                </span>
                                <input type="hidden" name="additional_images[]" value="{{$additional_image->id}}">
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>

                </div>

            </div>
        </div>
    </div>

    <option-container :skus-data="{{$mode == "edit" ? $skus_data :json_encode(old('skus_data'))}}" :variants-data="{{$variants_data ?? " []"}}" store-url="{{ $submitUrl }}" @update-sku-data='toggleSku'/>


    {{-- <div class="card">
        <div class="card-header">
            <h3 class="card-title">Options</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row form-group">

            </div>

        </div>
    </div> --}}

    {{--
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Variants</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row form-group">
                <variant-container :prop-options-data="{{json_encode(old('options'))}}" />

            </div>

        </div>
    </div> --}}

</div>
</div>

@push('js')
<script>
    $(document).ready(function() {
        $( "#special_price_start" ).datepicker({
            format: "dd/mm/yyyy",
            changeMonth: true,
            changeYear: true,
            minDate:0
        });
        
        $( "#special_price_end" ).datepicker({
            format: "dd/mm/yyyy",
            changeMonth: true,
            changeYear: true,
            minDate:0
        });
        $('.brwose_btn').on('click', function (e) {
            console.log('here');
            $('#file-manager').modal('show');
            $('#file-manager').find('iframe').attr('src', "{{ route('admin.file.manager') }}?action="+$(this).attr('data-type'));
        });
    });
</script>
@endpush