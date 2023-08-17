<?php
$guards=config('auth.guards');
?>
@if(array_key_exists('Employers',$guards))
<li><a class="dropdown-item" href="{{route('employerdashboard') }}"><i class="fa fa-users"></i>خدمات العاملين</a></li>
@endif
<div id='employermenu' style="display:none">
    <li><a class="dropdown-item">{{ amer_auth()->user()->fullname ?? '' }}</a></li>
    <li><a class="dropdown-item" href="{{route('employerlogout-post') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{ trans('SECLANG::auth.logout') }}</a></li>
    <form id="logout-form" action="{{ route('employerlogout-post') }}" method="POST" style="display: none;">
            @csrf
    </form>
</div>
@if((!auth::guard('Amer')->check()) OR (!auth::guard('Employers')->check()))
<li><a class="dropdown-item" onclick="loginformshow()"><i class="fa fa-users"></i>تسجيل الدخول</a> </li>
@endif
<div id='backmenu' style="display:none">
    <li><hr class="dropdown-divider" /></li>
    <li><a class="dropdown-item">{{ amer_auth()->user()->name ?? ''}}</a></li>
    <li><a class="dropdown-item" href="{{ route('admin.account.info') }}">{{ trans('SECLANG::auth.my_account') }}</a></li>
    <li><a class="dropdown-item" href="{{ route('admin.logout-get') }}">{{ trans('SECLANG::auth.logout') }}</a></li>
</div>
                                    <script>
                                        
                                        function chooseclass(e) {
                                            if($(e).attr('id') == 'chooseemployer'){
                                                $('#chooseemployer').removeClass("btn-primary");$('#chooseemployer').addClass("btn-success");
                                                $('#chooseback').removeClass("btn-success");$('#chooseback').addClass("btn-primary");
                                                $('.employerlogin').css('display','block');$('.backlogin').css('display','none');
                                            }
                                            if($(e).attr('id') == 'chooseback'){
                                                $('#chooseback').removeClass("btn-primary");$('#chooseback').addClass("btn-success");
                                                $('#chooseemployer').removeClass("btn-success");$('#chooseemployer').addClass("btn-primary");
                                                $('.employerlogin').css('display','none');$('.backlogin').css('display','block');
                                            }
                                        }
                                        function loginformshow(){
                                            var loginform=`
                                            <div class="row" id='loginbtnrow' style="">
                                            @if(!auth::guard('Amer')->check())
                                                <div class="col-sm-5 btn btn-primary" id="chooseback" onclick="chooseclass(this)">دخول الاعضاء</div>
                                                @endif
                                                @if(array_key_exists('Employers',$guards))
                                            @if(!auth::guard('Employers')->check())
                                                <div class="col-sm-5 btn btn-primary" onclick="chooseclass(this)" id="chooseemployer">دخول العاملين</div>
                                            @endif
                                            @endif
                                            </div>
                                            <div class="backlogin" style="display:none">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                <input type="email" class="swal2-input" name="email" value="" id="email" placeholder="{{ config('Amer.base.authentication_column_name') }}" autofocus>
                                                <input type="password" class="swal2-input" name="password" id="password" placeholder="password">
                                                <div class="form-check">
                                                    <input type="checkbox" class="btn-check" id="btn-checka" autocomplete="off" name="remember" data-id="remember" {{ old('remember') ? 'checked' : '' }}/>
                                                    <label class="btn btn-primary" for="btn-checka">{{ __('Remember Me') }}</label>
                                                </div>
                                                <button type="button" class="swal2-confirm swal2-styled" style="display: inline-block;" aria-label="" onclick="backlogin()">{{__('SECLANG::auth.login')}}</button>
                                            </div>
                                            <div class="employerlogin" style="display:none">
                                                    @csrf
                                                    <input id="nid" oninput="checknid()" type="number" class="swal2-input" name="nid" value="{{ old('nid') }}" required autocomplete="nid" placeholder="{{ __('auth.nid') }}" autofocus>
                                                    <input id="uid" type="text" oninput="checkuid()" class="swal2-input @errors('uid') is-invalid @enderrors" name="uid" required autocomplete="current-uid" placeholder="{{ __('auth.uid') }}">
                                                    <div class="form-check">
                                                    <input type="checkbox" class="btn-check" id="btn-check" autocomplete="off" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}/>
                                                    <label class="btn btn-primary" for="btn-check">{{ __('Remember Me') }}</label>
                                                    </div>
                                                    <button type="button" class="swal2-confirm swal2-styled" style="display: inline-block;" aria-label="" onclick="preConfirm()">{{__('SECLANG::auth.login')}}</button>
                                            </div>
                                `;
                                        /* global Swal */ 
                                        Swal.fire({
                                            title:"{{__('SECLANG::auth.login')}}",
                                            html:loginform,
                                            showCancelButton:false,
                                            showConfirmButton:false,
                                        })
                                        }
                                        
                                        function backlogin(){
                                            email=$('#email').val()
                                            password=$('#password').val()
                                            checka=$('#btn-checka:checked').val();
                                            if(checka == 'on'){checka = 'on';}else{checka='off';}
                                            if(!email || !password){
                                                $('.swal2-validation-message').css('display','flex');
                                                return $('.swal2-validation-message').html("من فضلك ادخل البيانات الغير مكتملة");
                                            }
                                            $.ajax({
                                                url:'{{route("Back.login.api")}}',
                                                type:'POST',
                                                headers: {
                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                    },
                                                data:{email:email,password:password,remember:checka},
                                                success:function(data){
                                                    if(data['errors'])
                                                    {
                                                        $('.swal2-validation-message').css('display','flex');
                                                        if(data['errors']['email']){
                                                            return $('.swal2-validation-message').html(data['errors']['email']);
                                                            }
                                                            if(data['errors']['password']){
                                                            return $('.swal2-validation-message').html(data['errors']['password']);
                                                            }
                                                    }else{
                                                        location.replace("{{Route('Admin')}}")
                                                    }
                                                    
                                                },error: function(e, xhr, opt) {
                                                        alert("error", "Error requesting " + opt + ": " + xhr.status + " " + xhr.responseText);
                                                    }
                                            });
                                        }
                                        function preConfirm(){
                                            nid=$('#nid').val()
                                            uid=$('#uid').val()
                                            if(!nid || !uid){
                                                $('.swal2-validation-message').css('display','flex');
                                                return $('.swal2-validation-message').html("من فضلك ادخل البيانات الغير مكتملة");
                                            }
                                            $.ajax({
                                                url:'{{route("Employer.login.api")}}',
                                                type:'POST',
                                                headers: {
                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                    },
                                                data:{nid:nid,uid:uid},
                                                success:function(data){
                                                    if(data['errors'])
                                                    {
                                                        $('.swal2-validation-message').css('display','flex');
                                                        if(data['errors']['nid']){
                                                            return $('.swal2-validation-message').html(data['errors']['nid']);
                                                            }
                                                            if(data['errors']['uid']){
                                                            return $('.swal2-validation-message').html(data['errors']['uid']);
                                                            }
                                                    }else{
                                                        location.reload();
                                                    }
                                                    
                                                },
        error: function(e, xhr, opt) {
            alert("error", "Error requesting " + opt + ": " + xhr.status + " " + xhr.responseText);
        }
                                            });
                                        }
                                    </script>
                                
                                <script>
                                                    function checkuid(){
                                                        let uidtext=document.getElementById('uid').value;
                                                        if(uidtext.length !== 5){
                                                            $('#uid').addClass("is-invalid");
                                                        }else{
                                                            $('#uid').removeClass("is-invalid");
                                                        }
                                                    }
                                                    function checknid(){
                                                        let nidtext=document.getElementById('nid').value;
                                                        if(nidtext.length < 14){
                                                            $('#nid').addClass("is-invalid");
                                                        }else if(nidtext.length > 14){
                                                            $('#nid').addClass("is-invalid");
                                                        }else{
                                                            $('#nid').removeClass("is-invalid");
                                                        }
                                                    }
                                                </script>
@section('scripts')
<script>
    @if(auth::guard('Employers')->check())
        $('#employermenu').css('display','block');
    @endif
    @if(auth::guard(config('amerSecurity.auth.middleware_key'))->check())
        $('#backmenu').css("display","block") 
    @endif
</script>
@endsection