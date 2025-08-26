<a
href="javascript:void(0)"
onclick="addToRoleBTN(this,'{{trans('SECLANG::permissionmanager.users')}}','{{url('Security/role/fetch/Users')}}','users')"
data-entry="{{$entry->getKey()}}"
data-mdb-ripple-duration="0s"
class="btn btn-sm btn-success"
data-buttontype="addRolesUsers"
data-ModalId="addRolesUser"
data-ResultFn="addRolesUser"
data-ModelKey="RoleId"
data-addLink="{{url('Security/role/fetch/AddUsers')}}"
>
    <i class="fa fa-users"></i>
</a>
@push('after_scripts') @if (request()->ajax()) @endpush @endif
@loadOnce('addRolesUsers')
<script>
    const addUsersToRoleLink="{{url('Security/role/fetch/AddUsers')}}";
</script>
@endLoadOnce
@if (!request()->ajax()) @endpush @endif
