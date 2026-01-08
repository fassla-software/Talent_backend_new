<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\Gateway\Bkash\ExecutePaymentController;
use App\Http\Controllers\Gateway\PaymentGatewayController;
use App\Http\Controllers\Gateway\PayTabs\ProcessController;
use App\Http\Controllers\PassportStorageSupportController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\GiftController;
use App\Http\Controllers\SegmentController;
use App\Http\Controllers\Admin\PlumberUsersController;
use App\Http\Controllers\ReceivedGiftController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\UniqueProductSubmissionController;
use App\Http\Controllers\FlaggedCategoryController;

use App\Http\Controllers\PlumberCategoryController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DeletionRequestController;
use App\Http\Controllers\PlumberNotificationController;

use App\Http\Controllers\GalleryController;

use App\Http\Controllers\Admin\EmployeeManageController;

use App\Http\Controllers\AdminPlumberNotificationController;
use App\Models\PlumberReceivedGift;
use App\Models\DeletionRequest;
use App\Models\PlumberWithdraw;
use App\Models\User;
use App\Models\Plumber;

use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\DashboardController;

use App\Http\Controllers\Admin\DashboardAnalysisController;


use App\Http\Controllers\Admin\LevelController;

Route::delete('/registration_bonus/{id}', function ($id) {
    DB::table('registration_bonus')->where('id', $id)->delete();
    return back()->with('success', 'Bonus deleted');
})->name('registration_bonus.destroy');


        Route::controller(LevelController::class)->group(function () {
            Route::get('/levels', 'index')->name('level.index');
            Route::get('/level/create', 'create')->name('level.create');
            Route::post('/level/store', 'store')->name('level.store');
            Route::get('/level/{level}', 'show')->name('level.show');
            Route::get('/level/{level}/edit', 'edit')->name('level.edit');
            Route::put('/level/{level}/update', 'update')->name('level.update');
            Route::delete('/level/{level}/destroy', 'destroy')->name('level.destroy');
        });

Route::get('/admin/analysis', [DashboardAnalysisController::class, 'index'])->name('dashboard.analysis');

Route::get('/admin/choose-dashboard', [DashboardController::class, 'chooseDashboard'])->name('admin.choose-dashboard');
Route::get('/admin/set-dashboard/{type}', [DashboardController::class, 'setDashboardType'])->name('admin.set-dashboard');
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.index');

Route::post('/received-gift/bulk-action', [ReceivedGiftController::class, 'bulkAction'])->name('received-gift.bulkAction');
Route::get('/received-gift/bulk-download', [ReceivedGiftController::class, 'bulkDownload'])->name('received-gift.bulkDownload');

Route::post('/admin/role-permission/add-role', [RolePermissionController::class, 'store'])->name('admin.role.store');
Route::get('/admin/role-permission/add-permission', [RolePermissionController::class, 'showAddPermissionForm'])->name('admin.permission.form');
Route::post('/admin/role-permission/add-permission', [RolePermissionController::class, 'addPermission'])->name('admin.permission.store');
Route::delete('/admin/role-permission/delete-permission/{id}', [RolePermissionController::class, 'deletePermission'])->name('admin.permission.delete');

Route::get('/admin/contact-messages', [ContactUsController::class, 'index'])->name('contact.messages');

Route::get('/api/plumber-users/pending-count', function () {
    $count = User::where('status', 'PENDING')
                 ->whereIn('id', Plumber::pluck('user_id')) // Check if user_id exists in plumbers table
                 ->count();
                 
    return response()->json(['count' => $count]);
})->name('api.plumberUsers.pending-count');

Route::get('/api/withdraw/pending-count', function () {
    $count = PlumberWithdraw::where('status', 'Pending')->count();
    return response()->json(['count' => $count]);
})->name('api.withdraw.pending-count');

Route::get('/api/deletion-requests/count', function () {
    $count = DeletionRequest::count();
    return response()->json(['count' => $count]);
})->name('api.deletion-requests.count');

Route::get('/api/received-gift/pending-count', function () {
    $count = PlumberReceivedGift::where('status', 'Pending')->count();
    return response()->json(['count' => $count]);
})->name('api.received-gift.pending-count');

Route::post('/adminplumbernotifications/read/{id}', [AdminPlumberNotificationController::class, 'markAsRead'])->name('adminplumbernotifications.markAsRead');

Route::get('/adminplumbernotifications', [AdminPlumberNotificationController::class, 'index'])->name('adminplumbernotifications.index');
Route::post('/adminplumbernotifications', [AdminPlumberNotificationController::class, 'store'])->name('adminplumbernotifications.store');

Route::put('/flagged-categories/update/{id}', [FlaggedCategoryController::class, 'update'])->name('flagged.update');

Route::post('/flagged-categories/store', [FlaggedCategoryController::class, 'store'])->name('flagged.store');

Route::get('/gallery/export', [GalleryController::class, 'export'])->name('gallery.export');

Route::delete('/gallery/delete-all', [GalleryController::class, 'deleteAll'])->name('gallery.deleteAll');

Route::delete('dashboard/gallery/{id}', [GalleryController::class, 'delete'])->name('gallery.delete');

Route::put('/received-gift/{giftId}/update-status', [ReceivedGiftController::class, 'updateStatus'])->name('received-gift.updateStatus');

Route::put('/admin/employee/{id}', [EmployeeManageController::class, 'update'])->name('admin.employee.update');

Route::delete('/gallery/{id}', [GalleryController::class, 'delete'])->name('gallery.delete');

Route::delete('/plumber_categories/bulk-destroy', [FlaggedCategoryController::class, 'bulkDestroy'])
    ->name('plumber_categories.bulk_destroy');

Route::delete('/categories/bulk-destroy', [CategoryController::class, 'bulkDestroy'])
    ->name('categories.bulk_destroy');

Route::get('/dashboard/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::post('/dashboard/gallery/upload', [GalleryController::class, 'upload'])->name('gallery.upload');

Route::prefix('admin')->group(function () {
    Route::get('/send-notification', [PlumberNotificationController::class, 'create'])->name('plumber.send-notification-form');
    Route::post('/send-notification', [PlumberNotificationController::class, 'sendNotificationMultiple'])->name('plumber.send-notification-multiple');
});
Route::get('/admin/deletion-requests', [DeletionRequestController::class, 'showRequests'])->middleware('auth');


Route::delete('/plumber_categories/{id}', [CategoryController::class, 'destroy'])->name('plumber_categories.destroy');

Route::post('/register-device-token', [NotificationController::class, 'registerDeviceToken']);

// routes/web.php or routes/api.php (if it's an API route)
Route::post('/send-notification', [NotificationController::class, 'sendNotification']);
Route::get('/send-notification', [NotificationController::class, 'sendTestNotification']);

Route::get('/download-template', [PlumberCategoryController::class, 'downloadTemplate'])->name('downloadTemplate');
Route::get('/download-product-category-template', [ProductCategoryController::class, 'downloadTemplate'])->name('downloadProductCategoryTemplate');
Route::get('/flagged-categories', [FlaggedCategoryController::class, 'index'])->name('flagged.index');

Route::get('/product-categories/upload', [ProductCategoryController::class, 'showUploadForm'])->name('product_categories.upload_form');
Route::post('/product-categories/upload', [ProductCategoryController::class, 'upload'])->name('product_categories.upload');

Route::get('/plumber-categories/upload', [PlumberCategoryController::class, 'showUploadForm'])->name('plumber_categories.upload_form');
Route::post('/plumber-categories/upload', [PlumberCategoryController::class, 'upload'])->name('plumber_categories.upload');

// Route to display categories with product_flag set to true
Route::get('/categories/flagged', [FlaggedCategoryController::class, 'index'])->name('categories.flagged');

Route::get('/unique-product-form', [UniqueProductSubmissionController::class, 'showForm'])->name('unique.product.form');
Route::post('/unique-product-submit', [UniqueProductSubmissionController::class, 'submitProduct'])->name('unique.product.submit');



Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');

Route::post('/withdraw/upload', [WithdrawController::class, 'upload'])->name('withdraw.upload');
Route::get('/admin/plumber-users', [PlumberUsersController::class, 'index'])->name('admin.plumberUsers');

Route::get('/withdraw', [WithdrawController::class, 'index'])->name('withdraw.index');
Route::delete('/withdraw/destroy/{id}', [WithdrawController::class, 'destroy'])->name('withdraw.destroy');

Route::get('/withdraws/logs/{userId}', [WithdrawController::class, 'logs'])->name('withdraw.logs');
// Route to download withdrawal requests of a user
Route::get('/withdraw/{userId}/download', [WithdrawController::class, 'downloadUserWithdrawals'])->name('withdraw.downloadUser');
Route::patch('/withdraw/{withdraw}/status' ,[WithdrawController::class, 'updateStatus'])->name('withdraw.updateStatus');

Route::post('/admin/plumber-users/reset-fiscal-year', [PlumberUsersController::class, 'resetFiscalYear'])->name('admin.plumberUsers.resetFiscalYear');

Route::get('/withdraw/download', [WithdrawController::class, 'download'])->name('withdraw.download');
Route::post('/upload-excel', [WithdrawController::class, 'upload'])->name('upload.excel');


Route::put('/category/{id}', [CategoryController::class, 'updateCategory']);

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/add-edit-category', [CategoryController::class, 'addEditCategory'])->name('add-edit-category');
Route::delete('/add-edit-category/destroy/{id}', [CategoryController::class, 'destroy'])->name('add-edit-category.destroy');

Route::get('/received-gift', [ReceivedGiftController::class, 'index'])->name('received-gift.index');
Route::get('/received-gift/{userId}/download', [ReceivedGiftController::class, 'downloadUserGifts'])->name('received-gift.downloadGift');

Route::put('/admin/plumber-users/{id}/approve', [PlumberUsersController::class, 'approve'])->name('admin.plumberUsers.approve');
Route::put('/admin/plumber-users/{id}/reject', [PlumberUsersController::class, 'reject'])->name('admin.plumberUsers.reject');
Route::get('/admin/plumber-users', [PlumberUsersController::class, 'index'])->name('admin.plumberUsers');
Route::get('/admin/plumber-users/{id}', [PlumberUsersController::class, 'show'])->name('admin.plumberUsers.show');
Route::get('/admin/plumber-users/{id}/edit', [PlumberUsersController::class, 'edit'])->name('admin.plumberUsers.edit');
Route::patch('/admin/update/{id}', [PlumberUsersController::class, 'update'])->name('admin.plumberUsers.update');
Route::delete('/admin/plumber/destroy/{id}', [PlumberUsersController::class, 'destroy'])->name('admin.plumberUsers.destroy');


Route::put('/segments/update-withdraw-points', [SegmentController::class, 'updateWithdrawPoints'])->name('segments.updateWithdrawPoints');
Route::put('/segments/{id}', [SegmentController::class, 'update'])->name('segments.update');
Route::post('/segments/create', [SegmentController::class, 'store'])->name('segments.store');
Route::get('/segments', [SegmentController::class, 'index'])->name('segments.index');

Route::put('/gift/{id}', 'GiftController@update');
Route::get('/gift/list', [GiftController::class, 'index'])->name('gift.list');
Route::get('/gift/create', [GiftController::class, 'create'])->name('gift.create');
Route::get('/points', [GiftController::class, 'pointsPage'])->name('points.index');
Route::post('/gift/store', [GiftController::class, 'store'])->name('gift.store');
Route::delete('/gift/destroy/{id}', [GiftController::class, 'destroy'])->name('gift.destroy');


use App\Http\Controllers\Admin\NewPageController;

Route::get('/proxy-image', [NewPageController::class, 'proxyImage'])->name('proxy.image');

// Correct route definition
Route::get('admin/new-page', [\App\Http\Controllers\Admin\NewPageController::class, 'index'])->name('admin.newPage.index');

// Change language
Route::get('/change-language', function () {
    if (request()->language) {
        App::setLocale(request()->language);
        session()->put('locale', request()->language);
    }

    return back();
})->name('change.language');

// Install Passport and storage routes
Route::controller(PassportStorageSupportController::class)->group(function () {
    Route::get('/install-passport', 'index')->name('passport.install.index');
    Route::get('/seeder-run', 'seederRun')->name('seeder.run.index');
    Route::get('/storage-install', 'storageInstall')->name('storage.install.index');
});

Route::controller(AuthController::class)->group(function () {
    Route::get('/auth/{provider}/callback', 'callback');
    Route::post('/auth/{provider}/callback', 'callback');
});

// Payment gateway routes
Route::controller(PaymentGatewayController::class)->group(function () {
    //payment routes
    Route::get('/order/{payment}/payment', 'payment')->name('order.payment');

    //success and cancel routes for payment
    Route::get('/order/{payment}/payment/success', 'paymentSuccess')->name('order.payment.success');
    Route::get('/order/{payment}/payment/cancel', 'paymentCancel')->name('order.payment.cancel');

    //success and cancel routes for callback
    Route::get('/payment/{payment}/callback-success', 'success')->name('payment.success');
    Route::get('/payment/{payment}/callback-cancel', 'cancel')->name('payment.cancel');

    //success and cancel routes for callback
    Route::post('/payment/{payment}/callback-success', 'success')->name('payment.success');
    Route::post('/payment/{payment}/callback-cancel', 'cancel')->name('payment.cancel');
});

// Bkash Payment execute
Route::get('/bkash-payment/{payment}/execute', [ExecutePaymentController::class, 'index'])->name('bkash.payment.execute');

// Paytabs payment execute
// Route::get('/paytabs/{payment}/callback', [ProcessController::class, 'callback'])->name('paytabs.payment.callback');
Route::post('/paytabs/{payment}/callback', [ProcessController::class, 'callback'])->name('paytabs.payment.callback');

// handle frontend page load
Route::get('/{any}', function () {

    // manage admin and shop routes
    if (request()->is('admin/*', 'shop/*')) {
        return abort(404);
    }

    return view('app');
})->where('any', '.*');
