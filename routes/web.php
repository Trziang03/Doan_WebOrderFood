<?php
use App\Http\Middleware\AdminRoleMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminTableController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminStaticController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Models\Order;


Route::controller(UserController::class)->group(function () {
    Route::get('/gioithieu', "GioiThieu")->name('user.blog');
    // Route::get('/', "index")->name('user.index');
    Route::get('/menu', "showmenu")->name('user.menu');
    Route::get('/menu/{slug}', "timKiemSanPhamTheoDanhMuc")->name('timkiemsanphamtheodanhmuc');
    Route::get('/tim-kiem', 'timKiemToanBo')->name('user.search.all');
    Route::post('/dangky', "DangKy")->name('dangky');
    Route::post('/dangnhap', "DangNhap")->name('dangnhap');
    Route::get('/logout', "Logout")->name('logout');
    Route::get('detail/{slug}', "ChiTietSanPham")->name("detail");
});

Route::controller(CartController::class)->group(function () {
    Route::get('/shopping-cart', 'index')->name('user.shoppingcart');
    Route::post('/cart/add', 'add')->name('cart.add');
    Route::post('/add-to-cart', 'addToCart')->name('add.to.cart');
    Route::delete('/cart/delete-item/{cart_item_id}', 'deleteItemCart');
    Route::delete('/cart/delete-all', 'deleteAllItem');
    Route::patch('/cart/increase/{cart_item_id}', 'increaseOnQuantity');
    Route::patch('/cart/minus/{cart_item_id}', 'minusOnQuantity');
    Route::post('/cart/submit', 'submitCart')->name('cart.submit');
});

//Phân quyền quản lý và nhân viên
Route::middleware(['role:QL,NV'])->group(function () {
    //Route dashboard
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/editWebsite', [AdminController::class, 'editWebsite'])->middleware(AdminRoleMiddleware::class)->name('admin.editWebsite');
    Route::post('/admin/editLogo', [AdminController::class, 'editLogo'])->middleware(AdminRoleMiddleware::class)->name('admin.editLogo');

    //Route quản lí danh mục
    Route::get('/admin/category', [AdminCategoryController::class, 'index'])->name('admin.category');
    Route::get('/admin/addcategory', [AdminCategoryController::class, 'addCategory'])->name('admin.category.addcategory');
    Route::post('/admin/addcategory/store', [AdminCategoryController::class, 'storeCategory'])->name('admin.category.storecategory');
    Route::get('/admin/editcategory/{id}', [AdminCategoryController::class, 'editCategory'])->name('admin.category.editcategory');
    Route::post('/admin/updatecategory/{id}', [AdminCategoryController::class, 'updateCategory'])->name('admin.category.updatecategory');
    Route::get('/admin/filter-category/{id}', [AdminCategoryController::class, 'filterCategory'])->name('filter.category');
    Route::delete('/admin/deletecategory/{id}', [AdminCategoryController::class, 'deleteCategory'])->name('admin.delete.category');

    //Route profile
    Route::get('/admin/profile/{id}', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('/admin/editProfile', [AdminController::class, 'editProfile'])->middleware(AdminRoleMiddleware::class)->name('admin.editProfile');
    Route::post('/admin/editAvatar', [AdminController::class, 'editAvatar'])->middleware(AdminRoleMiddleware::class)->name('admin.editAvatar');
    Route::get('/admin/changepw', [AdminController::class, 'changepw'])->name('admin.changepw');
    Route::post('/checkpw', [AdminController::class, 'IsPasswordChange'])->name('profile.checkpw');
    Route::post('/changepw', [AdminController::class, 'UpdatePassword'])->name('profile.changepw');

    //Route quản lí đơn hàng
    Route::get('/admin/order', [AdminOrderController::class, 'index'])->name('admin.order');
    Route::get('/admin/order/change-status/{id}', [AdminOrderController::class, 'changeStatus'])->name('admin.order.change-status');
    Route::get('/admin/order/detail/{id}', [AdminOrderController::class, 'ajaxDetail'])->name('admin.order.ajaxDetail');
    Route::delete('/admin/order/delete/{id}', [AdminOrderController::class, 'destroy'])->name('admin.order.delete');

    //Route quản lí thống kê
    Route::get('/admin/statistical', [AdminStaticController::class, 'index'])->name('admin.static');
    Route::get('/admin/statistic', [AdminStaticController::class, 'statistics'])->name('admin.statistic');

    //Route quản lí món ăn
    Route::get('/admin/products', [AdminProductController::class, 'index'])->name('admin.product');
    Route::get('/admin/products/category/{id}', [AdminProductController::class, 'filterByCategory']);
    Route::get('/admin/product/search', [AdminProductController::class, 'search'])->name('admin.product.search');
    Route::get('/admin/product/filter', [AdminProductController::class, 'filter'])->name('admin.product.filter');
    Route::post('/admin/topping/store', [AdminProductController::class, 'storeTopping'])->name('admin.topping.store');
    Route::post('/admin/size/store', [AdminProductController::class, 'storeSize'])->name('admin.size.store');
    // Route::get('/product/info/{id}', [AdminProductController::class, 'getInfo']);

    Route::resource('/admin/product', AdminProductController::class);

    //quản lý bàn ăn
    Route::get('/admin/table', [AdminTableController::class, 'index'])->name('admin.table');
    Route::post('/admin/table/store', [AdminTableController::class, 'store'])->name('admin.table.store');
    Route::post('/admin/table/update/{id}', [AdminTableController::class, 'update'])->name('admin.table.update');
    Route::get('/table/{id}/generate-qr', [AdminTableController::class, 'generateQR']);
    Route::get('/admin/table/status', [AdminTableController::class, 'getStatuses']);
    Route::get('/table/check-status', [AdminTableController::class, 'checkStatus']);
    Route::get('/table/check-name', [AdminTableController::class, 'checkName']);

});

//Phân quyền quản lý
Route::middleware(['role:QL'])->group(function () {
    //quản lý nhân viên
    Route::get('/admin/staff', [AdminStaffController::class, 'index'])->name('admin.staff');
    Route::post('/admin/staff/store', [AdminStaffController::class, 'store'])->name('admin.staff.store');
    Route::get('/admin/staff/{id}/edit', [AdminStaffController::class, 'edit'])->name('admin.staff.edit');
    Route::post('/admin/staff/{id}/update', [AdminStaffController::class, 'update'])->name('admin.staff.update');
    Route::delete('/admin/staff/{id}', [AdminStaffController::class, 'destroy'])->name('admin.staff.destroy');
});

//Xác nhận đặt hàng và thanh toán
Route::controller(OrderController::class)->group(function () {
    Route::post('/payment/{id}', 'updatePaymentMethod')->name('payment.update');
    Route::get('/payment/confirm/{order_code}', 'index')->name('user.payment');
    Route::post('/payment/{id}/cancel',  'cancel')->name('payment.cancel');
    Route::get('/qr-info', 'showQR')->name('user.qr.info');
});

//Route profile
Route::controller(ProfileController::class)->group(function () {
    Route::get('/trangcanhan', 'index')->name('profile.index');
    Route::post('/trangcanhan/editInfo', 'editInfo')->name('profile.editInfo');
    Route::post('/trangcanhan/editImage', 'editImage')->name('profile.editImage');
    Route::get('/doimatkhau', 'ChangePwd')->name('profile.changepassword');
    Route::post('/kiemtrapassword', 'IsPasswordChange')->name('profile.ispassword');
    Route::post('/submitchange', 'UpdatePassword')->name('profile.submitchange');
});

Route::view('/404', 'errors.404');

Route::get('/debug-session', function () {
    return session()->all(); // xem toàn bộ session
});

Route::get('/api/order-status/{id}', function ($id) {
    $order = Order::with('orderStatus')->find($id);

    if (!$order) {
        return response()->json(['status' => 'Không tìm thấy đơn hàng'], 404);
    }

    return response()->json([
        'status' => $order->orderStatus->name
    ]);
});

Route::get('/api/admin/orders/latest', function () {
    $latestOrder = Order::latest('updated_at')->first();

    return response()->json([
        'id' => $latestOrder->id,
        'code' => $latestOrder->order_code,
        'status' => $latestOrder->orderStatus->name ?? 'Không rõ',
        'updated_at' => $latestOrder->updated_at->timestamp,
        'updated_at_text' => $latestOrder->updated_at->format('H:i:s d/m/Y'),
    ]);
});

Route::get('/admin/order/{order}/actions-html', function (Order $order) {
    return view('User.partials.order_actions', ['order' => $order]);
});
