<?php

use App\Http\Middleware\AdminRoleMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminStaticController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminContactController;
use App\Http\Controllers\AdminTableController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

Route::controller(UserController::class)->group(function () {
    Route::get('/gioithieu', "GioiThieu")->name('user.blog');
    Route::get('/gioithieu/timkiem', 'timKiemBaiVietTheoTuKhoa')->name('searchBlog');
    Route::get('/contact', "LienHe")->name('user.contact');
    Route::post('/addContact', 'addContact');
    Route::get('/', "index")->name('user.index');
    Route::get('/menu', "menu")->name('user.menu');
    Route::get('seach/{slug}/{id?}', "TimKiemSanPhamFH")->name('timkiemsanpham');
    Route::get('seach', "TimKiemTheoTuKhoa")->name('timkiemtheotukhoa');
    Route::get('/search', "search")->name('user.search');
    Route::post('/dangky', "DangKy")->name('dangky');
    Route::post('/dangnhap', "DangNhap")->name('dangnhap');
    Route::get('/logout', "Logout")->name('logout');
    Route::get('detail/{slug}', "ChiTietSanPham")->name("detail");
    Route::get('detail/{slug}/{internal_memory}', "LayMauSanPhamTheoBoNho")->name("LayMauSanPhamTheoBoNho");
    Route::get('detail/{slug}/{internal_memory}/{color}', "LayThongTinSanPhamTheoMau")->name("LayThongTinSanPhamTheoMau");
    Route::post('/addContact', 'addContact');
    Route::get('/yeuthich/{sampham}/{user}', 'CapNhapSanPhamYeuThich')->name("SanPhamYeuThich");
    Route::get('/get/{user}/{code}', 'GetDanhSachDanhGia');
    Route::post('/them-danh-gia', 'ThemDanhGia');
    Route::get('/get-rating/{id}/{sao?}', 'getRating');

});

Route::controller(CartController::class)->group(function () {
    Route::get('/shopping-cart', "index")->name('user.shoppingcart');
    Route::get('/add-to-cart/{id}/{quantity}', "addToCart")->name('cart.add');
    Route::get('/cart-delete-item/{id}', 'deleteItemCart')->name('cart.delete_item');
    Route::get('/cart-delete-all', 'deleteAllItem');
    Route::get('/cart-minus-one-variant/{id}', 'minusOnQuantity');
});

// Route::get('/admin/check-stock-variant/{id}', [AdminProductVariantController::class, 'checkStock']);
//Phân quyền quản lý và nhân viên
Route::middleware(['role:QL,NV'])->group(function () {

    //Route dashboard
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/editWebsite', [AdminController::class, 'editWebsite'])->middleware(AdminRoleMiddleware::class)->name('admin.editWebsite');
    Route::post('/admin/editLogo', [AdminController::class, 'editLogo'])->middleware(AdminRoleMiddleware::class)->name('admin.editLogo');

    //Route quan li danh mục
    Route::get('/admin/category', [AdminCategoryController::class, 'index'])->name('admin.category');
    Route::get('/admin/addcategory', [AdminCategoryController::class, 'addCategory'])->name('admin.category.addcategory');
    Route::post('/admin/addcategory/store', [AdminCategoryController::class, 'storeCategory'])->name('admin.category.storecategory');
    Route::get('/admin/editcategory/{id}', [AdminCategoryController::class, 'editCategory'])->name('admin.category.editcategory');
    Route::post('/admin/updatecategory/{id}', [AdminCategoryController::class, 'updateCategory'])->name('admin.category.updatecategory');
    Route::get('/admin/filter-category/{id}', [AdminCategoryController::class, 'filterCategory'])->name('filter.category');
    Route::delete('/admin/deletecategory/{id}', [AdminCategoryController::class, 'deleteCategory'])->name('admin.delete.category');

    //Route profile
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('/admin/editProfile', [AdminController::class, 'editProfile'])->middleware(AdminRoleMiddleware::class)->name('admin.editProfile');
    Route::post('/admin/editAvatar', [AdminController::class, 'editAvatar'])->middleware(AdminRoleMiddleware::class)->name('admin.editAvatar');
    Route::get('/admin/changepw', [AdminController::class, 'changepw'])->name('admin.changepw');
    Route::post('/checkpw', [AdminController::class, 'IsPasswordChange'])->name('profile.checkpw');
    Route::post('/changepw', [AdminController::class, 'UpdatePassword'])->name('profile.changepw');

    //Route quản lí đơn hàng
    Route::get('/admin/order', [AdminOrderController::class, 'index'])->name('admin.order');
    Route::get('/admin/order/change-status/{id}', [AdminOrderController::class, 'changeStatus'])->name('admin.order.change-status');
    Route::get('/admin/order/detail/{id}', [AdminOrderController::class, 'ajaxDetail'])->name('admin.order.ajaxDetail');

    //Route quản lí thống kê
    Route::get('/admin/static', [AdminStaticController::class, 'index'])->name('admin.static');
    Route::post('/admin/statistic', [AdminStaticController::class, 'statistics'])->name('admin.statistic');

    Route::get('/admin/category-specification/{id}', [AdminCategoryController::class, 'loadCategorySpecification']);

    //Route quản lí món ăn
    Route::get('/admin/products', [AdminProductController::class, 'index'])->name('admin.product');
    Route::get('/admin/products/category/{id}', [AdminProductController::class, 'filterByCategory']);
    Route::post('/admin/topping/store', [AdminProductController::class, 'storeTopping'])->name('admin.topping.store');
    Route::post('/admin/size/store', [AdminProductController::class, 'storeSize'])->name('admin.size.store');

    Route::resource('/admin/product', AdminProductController::class);


    //Route quan li lien he trang quản trị
    Route::get('/admin/contact', [AdminContactController::class, 'showListContacts'])->name('admin.contact');
    Route::delete('/admin/contact/delete/{id}', [AdminContactController::class, 'deleteContact'])->name('contact.delete');
    Route::get('/admin/contact/update/{id}', [AdminContactController::class, 'updateContact'])->name('contact.update');


    //quản lý bàn ăn
    Route::get('/admin/table', [AdminTableController::class, 'index'])->name('admin.table');
    Route::post('/admin/table/store', [AdminTableController::class, 'store'])->name('admin.table.store');
    Route::post('/admin/table/update/{id}', [AdminTableController::class, 'update'])->name('admin.table.update');
    Route::get('/admin/table/delete/{id}', [AdminTableController::class, 'destroy'])->name('admin.table.destroy');

    //nhân viên
    Route::get('/admin/staff', [AdminStaffController::class, 'index'])->name('admin.staff');
    Route::get('/admin/staff/create', [AdminStaffController::class, 'create'])->name('admin.staff.create');
    Route::post('/admin/staff/store', [AdminStaffController::class, 'store'])->name('admin.staff.store');
    Route::get('/admin/staff/edit/{id}', [AdminStaffController::class, 'edit'])->name('admin.staff.edit');
    Route::post('/admin/staff/update/{id}', [AdminStaffController::class, 'update'])->name('admin.staff.update');
    Route::delete('/admin/staff/delete/{id}', [AdminStaffController::class, 'destroy'])->name('admin.staff.destroy');

});

//Phân quyền quản lý
Route::middleware(['role:QL'])->group(function () { });

//Phân quyền quản lý , nhân viên và khách hàng
Route::middleware(['role:QL,NV,KH'])->group(function () { });

//phân quyền khách hàng
Route::middleware(['role:KH'])->group(function () {

    //xác nhận đặt hàng và thanh toán
    Route::controller(OrderController::class)->group(function () {
        Route::get('/payment', 'index')->name('user.payment');
        Route::post('/payment', 'completePayment')->name('complete-payment');
        Route::post('/add-voucher', 'addVoucher')->name('user.addvoucher');
        Route::get('/table/{id}', 'orderByTable')->name('order.table');

    });

    Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('add.to.cart');
    Route::post('/order/buy-now', [CartController::class, 'buyNow'])->name('buynow');

    //Route profile
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/trangcanhan', 'index')->name('profile.index');
        Route::post('/trangcanhan/editInfo', 'editInfo')->name('profile.editInfo');
        Route::post('/trangcanhan/editImage', 'editImage')->name('profile.editImage');
        Route::get('/lichsudonhang', 'order_history')->name('profile.order_history');
        Route::put('/lichsudonhang/cancel/{id}', 'cancel')->name('profile.cancel');
        Route::get('/sanphamyeuthich', 'favourite_product')->name('profile.favourite_product');
        Route::get('/sanphamyeuthich/unLike/{id}', 'unLike')->name('profile.unLike');
        Route::get('/lichsudanhgia', 'review_history')->name('profile.review_history');
        Route::get('/doimatkhau', 'ChangePwd')->name('profile.changepassword');
        Route::post('/kiemtrapassword', 'IsPasswordChange')->name('profile.ispassword');
        Route::post('/submitchange', 'UpdatePassword')->name('profile.submitchange');
    });
});
//Route quan li danh mục
Route::controller(AdminCategoryController::class)->group(
    function () {
        Route::get('/admin/addcategory', [AdminCategoryController::class, 'addCategory'])->name('admin.category.addcategory');
        Route::post('/admin/addcategory/store', [AdminCategoryController::class, 'storeCategory'])->name('admin.category.storecategory');
        Route::get('/admin/editcategory/{id}', [AdminCategoryController::class, 'editCategory'])->name('admin.category.editcategory');
        Route::post('/admin/updatecategory//{id}', [AdminCategoryController::class, 'updateCategory'])->name('admin.category.updatecategory');
        Route::get('/admin/filter-category/{id}', [AdminCategoryController::class, 'filterCategory'])->name('filter.category');
        Route::delete('/admin/deletecategory/{id}', [AdminCategoryController::class, 'deleteCategory'])->name('admin.delete.category');
    }
);


