@extends('ClientArea.Desktop.layouts.app')

@section('additionalHead')
    <link rel="stylesheet" href="{{ asset('static/assets/css/login.css') }}">
    <style type="text/css">
        .ui.form .field {
            margin-bottom: 1.5em;
        }
    </style>
@endsection

@section('content')
    <div class="ui middle aligned center aligned grid">
        <div class="login-form-container">
            <div class="ui segment">
                <form class="ui form login-form" method="POST" action="{{ route('login') }}">
                    {{ csrf_field() }}

                    <div class="login-form-title">
                        <h1>登录</h1>
                        <p>你的Cloud Computing账号</p>
                    </div>

                    <div class="ui field{{ $errors->has('email') ? ' error' : '' }}">
                        <div class="ui left icon input">
                            <i class="mail icon"></i>
                            <input type="email" name="email" placeholder="邮箱" value="{{ old('email') }}" required
                                   autofocus>
                        </div>
                    </div>

                    <div class="ui field{{ $errors->has('password') ? ' error' : '' }}">
                        <div class="ui left icon input">
                            <i class="lock icon"></i>
                            <input type="password" name="password" placeholder="密码">
                        </div>
                    </div>

                    <div class="ui field">
                        <div class="ui checkbox">
                            <input type="checkbox" name="remember"{{ old('remember') ? ' checked' : '' }}>
                            <label>记住我的登录状态</label>
                        </div>
                    </div>

                    <button type="submit" class="ui fluid teal submit button">登录</button>

                    <div class="login-form-additional">
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            忘记密码？
                        </a>

                        <span style="float: right">新用户？<a
                                    href="{{ route('register') }}">@lang('clientarea.sign_up')</a></span>
                    </div>

                    @include('ClientArea.Desktop.message.errors')
                </form>
            </div>
        </div>
    </div>
@endsection

@section('additionalFooter')
    <script type="text/javascript">
        $(document).ready(function () {
            $(".ui.checkbox").checkbox();
        });
    </script>
@endsection