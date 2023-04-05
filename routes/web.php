<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Local\CMS\Http\Controllers')->group(function () {
    // Auth::routes();
    Route::group(['middleware' => ['auth:admin'], 'as' => 'admin.'], function () {

        Route::get('', function () {
            return redirect()->route('admin.dashboard');
            // return redirect()->route('admin.users.index');
        });
        Route::get('home', function () {
            return redirect()->route('admin.dashboard');
            // return redirect()->route('admin.users.index');
        });

        Route::resource('roles', RoleController::class);

        Route::resource('admins', AdminController::class);

        Route::post('user-reset-password/{user}', 'UserController@resetPassword')->name('users.reset_password');
        Route::resource('users', UserController::class);

        Route::resource('newsEvents', NewsEventController::class);

        Route::get('dashboard', 'DashboardController@index')->name('dashboard');

        // Banner
        Route::resource('banners', 'BannerController');

        // File Manager
        Route::get('file-manager', 'FileController@manager')->name('file.manager');
        Route::resource('files', 'FileController');

        // Portfolio
        Route::resource('portfolios', 'PortfolioController');

        // Products
        Route::resource('products', 'ProductController');

        // Orders
        Route::get('orders/send_email/{id}', [OrderController::class, "sentEmail"])->name('orders.send_email');
        Route::get('orders/donwload-receipt/{id}', [OrderController::class, "downloadReceipt"])->name('orders.download_receipt');
        Route::post('ordeers/change-status', [OrderController::class, "changeStatus"])->name('orders.change_status');
        Route::post('orders/change-tracking-info/{order}', [OrderController::class, "changeTrackingInfo"])->name('orders.change_tracking_info');
        Route::resource('orders', 'OrderController');

        // Coupon
        Route::resource('coupons', 'CouponController');

        Route::resource('shipping_cost_configs', 'ShippingCostConfigController');

        // Product Categories
        Route::resource('categories', 'CategoryController');


        Route::post("ckeditor-upload", [CkeditorController::class, 'uploadImage'])->name("ckeditor.upload");
    });
});
