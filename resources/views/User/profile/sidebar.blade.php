<div class="col_change_password" style="width:20%;">
        <div class="col_change_password_1">
                <div class="item @yield('active_profile')"><a href="{{route('profile.index')}}"><i class="fas fa-user"></i>Thông tin cá nhân</a></div>
                <div class="item @yield('active_changepassword')"><a href="{{route('profile.changepassword')}}"><i class="fas fa-unlock-alt"></i>Đổi mật khẩu</a></div>
        </div>
</div>
