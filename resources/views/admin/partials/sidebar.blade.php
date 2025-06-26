<div class="menu">
    <a href="{{ route('admin.index') }}"><div class="@yield('active-dashboard')">Trang Dashboard</div></a>
    <a href="{{ route('admin.table') }}"><div class="@yield('active-table')">Quản lý Bàn ăn</div></a>
    <a href="{{ route('admin.category') }}"><div class="@yield('active-category')">Quản lý Danh mục</div></a>
    <a href="{{ route('product.index') }}"><div class="@yield('active-product')">Quản lý Món Ăn</div></a>
    <a href="{{ route('admin.order') }}"><div class="@yield('active-order')">Quản lý Đơn hàng</div></a>
    <a href="{{ route('admin.static') }}"><div class="@yield('active')">Quản lý Thống kê</div></a>
    <a href="{{ route('admin.contact') }}"><div class="@yield('active-contact')">Quản lý Liên hệ</div></a>
    <a href="{{ route('admin.staff') }}"><div class="@yield('active-staff')">Quản lý Nhân Viên</div></a>
</div>
