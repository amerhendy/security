<a
    href="javascript:void(0)"
    onclick="viewListPers(this,'{{trans('SECLANG::permissionmanager.roles')}}','{{url('Security/user/fetch/Roles')}}')"
    data-entry="{{$entry->getKey()}}"
    data-mdb-ripple-duration="0s"
    class="btn btn-sm btn-success"
    data-button-type="addRolesToUserS"
    data-model-key="UserId"
    data-button-addLink="{{url('Security/user/fetch/AddRoles')}}"
    data-title="0"
>
    <i class="fa-brands fa-critical-role"></i>
</a>
@push('after_scripts') @if (request()->ajax()) @endpush @endif
@loadOnce('addRolesToUserS')
@loadScriptOnce('js/Security/users.js')
@endLoadOnce
@if (!request()->ajax()) @endpush @endif
