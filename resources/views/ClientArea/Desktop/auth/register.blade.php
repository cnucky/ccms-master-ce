@extends('ClientArea.Desktop.layouts.app')

@section('additionalHead')
    <link rel="stylesheet" href="{{ asset('static/assets/css/login.css') }}">
@endsection

@section('content')

    <div class="ui middle aligned center aligned grid">
        <div class="login-form-container">
            <div class="ui segment">
                <form class="ui form login-form" method="POST" action="{{ route('register') }}">
                    {{ csrf_field() }}

                    <div class="login-form-title">
                        <h2>加入Cloud Computing，体验云计算之美</h2>
                    </div>

                    <div class="ui field{{ $errors->has('name') ? ' error' : '' }}">
                        <label>姓名</label>
                        <div class="ui left icon input">
                            <i class="user icon"></i>
                            <input type="text" name="name" placeholder="姓名" value="{{ old('name') }}" required
                                   autofocus>
                        </div>
                    </div>

                    <div class="ui field{{ $errors->has('email') ? ' error' : '' }}">
                        <label>邮箱</label>
                        <div class="ui left icon input">
                            <i class="mail icon"></i>
                            <input type="email" name="email" placeholder="邮箱" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="ui field{{ $errors->has('phone') ? ' error' : '' }}">
                        <label>联系电话</label>
                        <div class="ui left icon input">
                            <i class="mobile icon"></i>
                            <input type="tel" name="phone" placeholder="联系电话" value="{{ old('phone') }}" required>
                        </div>
                    </div>

                    <div class="ui field{{ $errors->has('password') ? ' error' : '' }}">
                        <label>密码</label>
                        <div class="ui left icon input">
                            <i class="lock icon"></i>
                            <input type="password" name="password" placeholder="密码" required>
                        </div>
                    </div>

                    <div class="ui field{{ $errors->has('password_confirmation') ? ' error' : '' }}">
                        <label>确认密码</label>
                        <div class="ui left icon input">
                            <i class="lock icon"></i>
                            <input type="password" name="password" placeholder="确认密码" required>
                        </div>
                    </div>

                    <div class="ui field">
                        <div class="ui checkbox">
                            <input type="checkbox" name="remember" {{ old('remember') ? ' checked ' : '' }}required>
                            <label>已阅读并同意<a href="#">服务条款</a></label>
                        </div>
                    </div>

                    <button type="submit" class="ui fluid teal submit button">注册</button>

                    @include('ClientArea.Desktop.message.errors')
                </form>
            </div>

            <div class="ui message">
                <p style="text-align: center;">已有账号？<a href="{{ route('login') }}">@lang('clientarea.sign_in')</a></p>
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