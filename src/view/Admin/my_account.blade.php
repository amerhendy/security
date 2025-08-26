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
                            <div class="col-md-2 form-group" bp-field-wrapper>
                                @php
                                    $label = trans('SECLANG::user.prefix');
                                    $field = 'title';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <select required class="form-control" name="{{ $field }}">
                                    @foreach (trans('SECLANG::user.prefixs') as $key=>$item)
                                    <?php
                                    $oldprefix=old($field) ? old($field) : $user->$field;
                                    ?>
                                        <option id="{{$key}}" @if($key==$oldprefix)selected @endif>{{$item}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 form-group" bp-field-wrapper>
                                @php
                                    $label = trans('SECLANG::user.firstname');;
                                    $field = 'first_name';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>
                            <div class="col-md-5 form-group" bp-field-wrapper>
                                @php
                                    $label = trans('SECLANG::user.secondname');;
                                    $field = 'last_name';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 form-group" bp-field-wrapper>
                                @php
                                    $label = trans('SECLANG::user.name');
                                    $field = 'name';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>
                            <div class="col-md-2 form-group" bp-field-wrapper>
                                @php
                                    $label = trans('SECLANG::user.gender');
                                    $field = 'gender';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <select required class="form-control" name="{{ $field }}">
                                    @foreach (trans('SECLANG::user.genders') as $key=>$item)
                                    <?php
                                    $oldprefix=old($field) ? old($field) : $user->$field;
                                    ?>
                                        <option id="{{$key}}" @if($key==$oldprefix)selected @endif>{{$item}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-5 form-group" bp-field-wrapper>
                                @php
                                    $label = trans('SECLANG::user.birthdate');
                                    $field = 'birthdate';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input required class="form-control" type="date" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group" bp-field-wrapper>
                                @php
                                    $label = trans('SECLANG::user.username');
                                    $field = 'username';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>

                            <div class="col-md-6 form-group" bp-field-wrapper>
                                @php
                                    $label = "Email";
                                    $field = 'email';
                                @endphp
                                <label class="required">{{ trans("SECLANG::auth.email") }}</label>
                                <input required class="form-control" type="email" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group" bp-field-wrapper>
                                @php
                                    $label = trans('SECLANG::user.mobile');
                                    $field = 'mobile';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 form-group" bp-field-wrapper>
                            <p>
                                @php
                                    $label = trans('SECLANG::user.id');
                                    $field = 'id';
                                @endphp
                                <strong>{{ $label }} : </strong>
                                <em>{{ old($field) ? old($field) : $user->$field }}</em>
                            </p>
                            <p>
                                @php
                                    $label = trans('SECLANG::Teams.teamname');
                                    $field = 'current_team_id';
                                    $oldTeam=old($field) ? old($field) : $user->$field;
                                    if($oldTeam == null){$oldTeam=trans('SECLANG::user.nullTeam');}
                                @endphp
                                <strong>{{ $label }} : </strong>
                                <em>{{ $oldTeam }}</em>
                            </p>
                            <p>
                                @php
                                    $label = trans('SECLANG::user.created_at');
                                    $field = 'created_at';
                                @endphp
                                <strong>{{ $label }} : </strong>
                                <em>{{ old($field) ? old($field) : $user->$field }}</em>
                            </p>
                            <p>
                                @php
                                    $label = trans('SECLANG::user.userstatus');
                                    $field = 'status';
                                    $oldSserStatus=old($field) ? old($field) : $user->$field;
                                    if($oldSserStatus == 'active')
                                        $oldSserStatus='<i class="fas fa-check-circle text-success"></i> '.trans('SECLANG::user.active');
                                    else {
                                        $oldSserStatus='<i class="fas fa-times-circle text-danger"></i> '.trans('SECLANG::user.notactive');
                                    }
                                @endphp
                                <strong>{{ $label }} : </strong>
                                <em>{!! $oldSserStatus !!}</em>
                            </p>
                            <p>
                                @php
                                    $label = trans('SECLANG::user.note');;
                                    $field = 'note';
                                @endphp
                                <strong>{{ $label }} : </strong>
                                <em>{{ old($field) ? old($field) : $user->$field }}</em>
                            </p>
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
                            <div class="col-md-4 form-group" bp-field-wrapper>
                                @php
                                    $label = trans('SECLANG::auth.old_password');
                                    $field = 'old_password';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" value="">
                            </div>

                            <div class="col-md-4 form-group" bp-field-wrapper>
                                @php
                                    $label = trans('SECLANG::auth.new_password');
                                    $field = 'new_password';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" value="">
                            </div>

                            <div class="col-md-4 form-group" bp-field-wrapper>
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
