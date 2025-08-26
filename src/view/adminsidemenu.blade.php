        <button class="btn btn-toggle list-group-item list-group-item-action rounded" data-bs-toggle="collapse" data-bs-target="#userpermisions-collapse" aria-expanded="false">
        <i class="fa fa-user"></i>صلاحيات المستخدمين
        </button>
        <div class="collapse list-group list-group-flush" id="userpermisions-collapse" style="">
            <a href="{{Sec_url('permission')}}" class="list-group-item list-group-item-action"><i class="fa fa-lock" aria-hidden="true"></i>{{trans('SECLANG::permissionmanager.permission_plural')}}</a>
            <a href="{{Sec_url('role')}}" class="list-group-item list-group-item-action"><i class="fa-brands fa-critical-role"></i>{{trans('SECLANG::permissionmanager.roles')}}</a>
            <a href="{{Sec_url('Teams')}}" class="list-group-item list-group-item-action"><span class="fab fa-servicestack"></span>{{trans('SECLANG::Teams.Teams')}}</a>
            <a href="{{Sec_url('user')}}" class="list-group-item list-group-item-action"><i class="fa fa-users" aria-hidden="true"></i>{{trans('SECLANG::permissionmanager.users')}}</a>
            
        </div>