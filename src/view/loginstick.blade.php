<?php
$guards=config('auth.guards');
?>

<template id="login-template-admin">
    @if(!auth::guard('Amer')->check())
    <div class="col-sm-5 btn btn-primary" data-bs-target="loginback">دخول الاعضاء</div>
    @endif
    <div id="loginback" style="display:none">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="email" class="swal2-input" name="email" value="" id="email" placeholder="{{ config('Amer.base.authentication_column_name') }}" autofocus>
            <input type="password" class="swal2-input" name="password" id="password" placeholder="password">
            <div class="form-check">
                <input type="checkbox" class="btn-check" id="btn-checka" autocomplete="off" name="remember" data-id="remember" {{ old('remember') ? 'checked' : '' }}/>
                <label class="btn btn-primary" for="btn-checka">{{ __('Remember Me') }}</label>
            </div>
            <button type="button" class="swal2-confirm swal2-styled loginbtn" data-bs-link='{{route("Back.login.api")}}' style="display: inline-block;" aria-label="">{{__('SECLANG::auth.login')}}</button>
            <!-- onclick="backlogin()"-->
        </div>
</template>
<div id='backmenu' style="display:none">
    <li><hr class="dropdown-divider" /></li>
    <li><a class="dropdown-item">{{ amer_auth()->user()->name ?? ''}}</a></li>
    <li><a class="dropdown-item" href="{{ route('admin.account.info') }}">{{ trans('SECLANG::auth.my_account') }}</a></li>
    <li><a class="dropdown-item" href="{{ route('admin.logout-get') }}">{{ trans('SECLANG::auth.logout') }}</a></li>
</div>
@push('after_scripts')
<script>
    @if(auth::guard(config('Amer.Security.auth.middleware_key'))->check())
        $('#backmenu').css("display","block") 
    @endif
</script>
@endpush