<a
    href="javascript:void(0)"
    onclick="viewListPers(this,'{{trans('SECLANG::permissionmanager.permission_plural')}}','{{url('Security/user/fetch/Perms')}}')"
    data-entry="{{$entry->getKey()}}"
    data-mdb-ripple-duration="0s"
    class="btn btn-sm btn-success"
    data-button-type="addUserPerms"
    data-model-key="UserId"
    data-button-addLink="{{url('Security/user/fetch/AddPers')}}"
    data-title="0"
>
    <i class="fa fa-lock"></i>
</a>
@push('after_scripts') @if (request()->ajax()) @endpush @endif
@loadOnce('addUserPerms')
    @loadScriptOnce('js/Security/users.js')
@endLoadOnce
@if (!request()->ajax()) @endpush @endif
