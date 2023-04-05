@can('file_create')
    <div class="form-group">
        <form id="dropzone-form" method="POST" data-action="{{ route('admin.files.store') }}"
            class="dropzone record_dropzone" enctype="multipart/form-data" style="width:100%;">
            {{ csrf_field() }}
        </form>
    </div>
@endcan
<div class="form-group">
    <table class="datatable table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Thumbnail</th>
                <th>Name</th>
                <th>Uploaded At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($files as $file)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <img data-fancybox="gallery" href="{{ asset($file->full_path) }}"
                        src="{{ asset($file->full_path) }}" class="preview" />
                </td>
                <td>{{ $file->original_name }}</td>
                <td>{{ $file->created_at->format('d/m/Y H:i:s') }}</td>
                <td>
                    @if(Route::currentRouteName() == "admin.file.manager")
                        <button class="btn btn-light btn-select" data-id="{{ $file->id }}" data-path="{{ asset($file->full_path) }}">
                            <i class="far fa-check-square"></i>
                        </button>
                    @else
                        @can('file_delete')
                            <button class="btn btn-sm btn-danger record-delete" data-action-url="{{ route('admin.files.destroy', $file) }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        @endcan
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>