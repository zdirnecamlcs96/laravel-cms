<div class="modal fade" id="file-manager" tabindex="-1" role="dialog" aria-labelledby="fileManagerLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileManagerLabel">File Manager</h5>
                <button type="button" class="close" aria-label="Close" id="closeBtn" onClick="closeModalBtn()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <iframe src="{{ route('admin.file.manager') }}" frameborder="0" class="border-0 w-100" style="height: 80vh"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
function closeModalBtn(){
    $('#file-manager').modal('hide');
}
</script>

