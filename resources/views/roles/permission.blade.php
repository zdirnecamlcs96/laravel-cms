

@foreach (config('modules.permissions') as $module => $permissions)
    <div class="col-md-12 module-group">
        <div class="permission-group-head">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <h4>{{ Str::headline($module) }}</h4>
                </div>

                <div class="col-md-6 col-sm-6">
                    <div class="btn-group permission-group-actions pull-right">
                        <button type="button" class="btn btn-default allow-all">Allow
                            all</button>
                        <button type="button" class="btn btn-default deny-all">Deny
                            all</button>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="col-md-12 mb-3">
            @foreach ($permissions as $permission)
                <div class="permission-row">
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <span class="permission-label">{{ Str::headline($permission) }}</span>
                        </div>

                        @php
                            $hasPermission = $mode === 'update' ? $role->hasPermissionTo("{$module}_{$permission}") : null;
                        @endphp

                        <div class="col-md-6 col-sm-6">
                            <div class="radio-btn clearfix">
                                <div class="radio d-inline-block m-2">
                                    <input name="permissions[{{ "{$module}_{$permission}" }}]" type="radio" class="allow"  value="true" @if($hasPermission) checked @endif/>
                                    <label for="{{ "{$module}_{$permission}_allow" }}">Allow</label>
                                </div>
                                <div class="radio d-inline-block m-2">
                                    <input name="permissions[{{ "{$module}_{$permission}" }}]" type="radio" class="deny" value="false" @if(!$hasPermission) checked @endif/>
                                    <label for="{{ "{$module}_{$permission}_deny" }}">Deny</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endforeach

@push('js')
<script>
    $('.allow-all').on('click', function() {
        $(this).closest('.module-group').find('input[class=allow]').prop("checked", true);
    })
    $('.deny-all').on('click', function() {
        $(this).closest('.module-group').find('input[class*=deny]').prop("checked", true);
    })
</script>
@endpush
