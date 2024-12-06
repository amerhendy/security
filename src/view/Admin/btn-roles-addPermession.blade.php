<a
    href="javascript:void(0)"
    onclick="addToRoleBTN(this,'{{trans('SECLANG::permissionmanager.permission_plural')}}','{{url('Security/role/fetch/Perms')}}')"
    data-entry="{{$entry->getKey()}}"
    data-mdb-ripple-duration="0s"
    class="btn btn-sm btn-success"
    data-buttontype="addRolesPerms"
    data-ModalId="addRolesPerms"
    data-ResultFn="addRolesPerms"
    data-ModelKey="RoleId"
    data-addLink="{{url('Security/role/fetch/AddPerms')}}"
    data-title="0"
>
    <i class="fa fa-lock"></i>
</a>
@push('after_scripts')
<?php
?>
@if (request()->ajax()) @endpush @endif
@loadScriptOnce('js/Security/roles.js')
@loadOnce('addRolesPerms')
<script>
    const addPermToRoleLink="{{url('Security/role/fetch/AddPerms')}}";
</script>
@endLoadOnce
@if (!request()->ajax()) @endpush @endif
