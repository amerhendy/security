@extends(Baseview('app'))
<?php
?>
@section('after_styles')
    <style media="screen">
        .profile-form .required::after {
            content: ' *';
            color: red;
        }
    </style>
@endsection
@if(!auth::guard('Amer')->check())
@endif
@if(!auth::guard('Employers')->check())
@endif
@php
if(auth::guard('Amer')->check()){
    $dashboardroute=Route("Admin");
}
  $breadcrumbs = [
      trans('AMER::crud.admin') => $dashboardroute,
      trans('AMER::auth.my_account') => false,
  ];
@endphp
@section('header')
    <section class="content-header">
        <div class="container-fluid mb-3">
            <h1>{{ trans('AMER::auth.my_account') }}</h1>
        </div>
    </section>
@endsection
@section('content')
<!---------------------------------->
<form class="form" name="form" action="{{ route('admin.account.info.store') }}" method="post">
{!! csrf_field() !!}<input type="hidden" name="_http_referrer" value="{{session('referrer_url_override') ?? old('_http_referrer') ?? \URL::previous() ?? Route($Amer->route.'.index')}}">
<div class="col-lg-8">
        <div class="card padding-10">
            <div class="card-header">
                <h2>
                    <small>{!! trans('SECLANG::auth.update_account_info') !!}.</small>
                </h2>
            </div>
                    <div class="card-body bold-labels">
<!---------------------------------->            
                        <div class="row">
                            <div class="col-md-6 form-group">
                                @php
                                    $label = trans('SECLANG::auth.name');
                                    $field = 'name';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>

                            <div class="col-md-6 form-group">
                                @php
                                    $label = "Email";
                                    $field = 'email';
                                @endphp
                                <label class="required">{{ trans("SECLANG::auth.email") }}</label>
                                <input required class="form-control" type="email" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>
                        </div>
                    </div>
<!---------------------------------->
</div>
    <div class="card-footer">
        <div id="saveActions" class="form-group">
            <input type="hidden" name="_save_action" value="save">
            <button type="submit" class="btn btn-success">
                <span class="fa fa-save" role="presentation" aria-hidden="true"></span>
                <span data-value="save">{{ trans('AMER::actions.save') }}</span>
            </button>
        </div>
    </div>
</form>
</div>
<!---------------------------------->


<!---------------------------------->
<form class="form" name="form" action="{{ route('admin.account.info.store') }}" method="post">
{!! csrf_field() !!}{!! method_field('PUT') !!}<input type="hidden" name="_http_referrer" value="{{session('referrer_url_override') ?? old('_http_referrer') ?? \URL::previous() ?? Route($Amer->route.'.index')}}">
<div class="col-lg-8">
        <div class="card padding-10">
            <div class="card-header">
                <h2>
                    <small>{!! trans('SECLANG::auth.change_password') !!}.</small>
                </h2>
            </div>
                    <div class="card-body bold-labels">
<!---------------------------------->            
                        <div class="row">
                            <div class="col-md-4 form-group">
                                @php
                                    $label = trans('SECLANG::auth.old_password');
                                    $field = 'old_password';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" value="">
                            </div>

                            <div class="col-md-4 form-group">
                                @php
                                    $label = trans('SECLANG::auth.new_password');
                                    $field = 'new_password';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" value="">
                            </div>

                            <div class="col-md-4 form-group">
                                @php
                                    $label = trans('SECLANG::auth.confirm_password');
                                    $field = 'confirm_password';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" value="">
                            </div>
                        </div>
                    </div>
                    <!---------------------------------->
</div>
    <div class="card-footer">
        <div id="saveActions" class="form-group">
            <input type="hidden" name="_save_action" value="save">
            <button type="submit" class="btn btn-success">
                <span class="fa fa-save" role="presentation" aria-hidden="true"></span>
                <span data-value="save">{{ trans('AMER::actions.save') }}</span>
            </button>
        </div>
    </div>
</form>
</div>
<!---------------------------------->
@endsection
