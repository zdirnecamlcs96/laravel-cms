@extends('adminlte::page')
@section('plugins.Select2', true)

@section('css')
<style>
    #file-wrapper {
        background-color: lightgrey;
    }

    .preview {
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        width: 8rem;
        height: 8rem;
        position: relative;
    }

    .preview .remove-file-btn {
        position: absolute;
        top: .5rem;
        right: .5rem;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="card col-md-10">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="col-auto p-0">
                    <h3 class="card-title">New Product</h3>
                </div>
                <div class="col-auto">
                    <a href="{{route('admin.products.index')}}" class="btn btn-warning btn-sm ">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>


{{Form::open(['route'=>'admin.products.store'])}}
@csrf

<div id="app">
    @include('modules::products.field', [
    'mode' => 'create',
    'submitUrl' => route('admin.products.store')
    ])
</div>

{{Form::close()}}
@include('modules::modals.file-manager')
@endsection


@section('js')
<script>
    window.addEventListener('message', function(e) {
        let data = e.data
        if (typeof data == "string" && data.startsWith("file")) {
            let file = JSON.parse(data.split('|')[1])
            let html = '<div id="image-' + file.id + '" class="col-auto py-2 file-holder" ondragover="allowDrop(event)" draggable="true" ondragstart="drag(event)" ondrop="drop(event)">\
                            <div style="background-image:url(\'' + file.url + '\')" class="preview shadow-sm rounded"> \
                            <span class="remove-file-btn btn btn-light btn-sm shadow-sm"> \
                                <i class="fas fa-times"></i> \
                            </span> \
                            <input type="hidden" name="'+file.action+'[]" value="' + file.id + '"> \
                        </div></div>';


        if(file.action == "thumbnail"){
            $("#thumbnail-file-wrapper").empty();
            $('#thumbnail-file-wrapper').append(html);
        }else{
            $('#additional-images-file-wrapper').append(html)
        }
        }
    });

    $('.brwose_btn').on('click', function (e) {
        $('#file-manager').find('iframe').attr('src', "{{ route('admin.file.manager') }}?action="+$(this).attr('data-type'));
    })

    $('#thumbnail-file-wrapper').on('click', '.remove-file-btn', function() {
        $(this).closest('.file-holder').fadeOut(300, function() { $(this).remove(); })
    })


    $('#additional-images-file-wrapper').on('click', '.remove-file-btn', function() {
        $(this).closest('.file-holder').fadeOut(300, function() { $(this).remove(); })
    })

    createEditor(document.querySelector( '#editor' ))
    $('.select2').select2();

    let dragindex = 0;
    let dropindex = 0;
    let clone="";

    function drag(e)
    {
        console.log('drag')
        e.dataTransfer.setData("text",e.target.id);
    }

    function drop(e)
    {
        e.preventDefault();
        if(!e.target.parentNode.id){
           return e.preventDefault();
        }
        clone = e.target.parentNode.cloneNode(true);

        let data = e.dataTransfer.getData("text");

        if(clone.id !== data) {
            let nodelist=document.getElementById("additional-images-file-wrapper").childNodes;
            for(let i=0;i<nodelist.length;i++)
            {
                if(nodelist[i].id==data)
                {
                    dragindex=i;
                }
            }

            document.getElementById("additional-images-file-wrapper").replaceChild(document.getElementById(data),e.target.parentNode);

            document.getElementById("additional-images-file-wrapper").insertBefore(clone,document.getElementById("additional-images-file-wrapper").childNodes[dragindex]);

        }

    }

    function allowDrop(e)
    {
        e.preventDefault();
    }

</script>
@endsection
