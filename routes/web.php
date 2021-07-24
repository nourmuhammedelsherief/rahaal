<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
//    \Illuminate\Support\Facades\Artisan::call('check::commission');
    return view('welcome');
});
Route::get('/check-status/{id?}/{id1?}', 'Api\UserController@fatooraStatus');
Route::get('/check-status-payOrder/{id?}/{id1?}', 'Api\UserController@fatooraStatusPayOrder');

Route::get('/fatoora/success', function(){
    return view('fatoora');
})->name('fatoora-success');

Route::get('/chat' , function (){
    return view('chat');
});
Route::get('export', 'HomeController@export')->name('export');
Route::get('importExportView', 'HomeController@importExportView');
Route::post('import', 'HomeController@import')->name('import');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
/*admin panel routes*/

Route::get('/admin/home', ['middleware'=> 'auth:admin', 'uses'=>'AdminController\HomeController@index'])->name('admin.home');

Route::prefix('admin')->group(function () {

    Route::get('login', 'AdminController\Admin\LoginController@showLoginForm')->name('admin.login');
    Route::post('login', 'AdminController\Admin\LoginController@login')->name('admin.login.submit');
    Route::get('password/reset', 'AdminController\Admin\ForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::post('password/email', 'AdminController\Admin\ForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
    Route::get('password/reset/{token}', 'AdminController\Admin\ResetPasswordController@showResetForm')->name('admin.password.reset');
    Route::post('password/reset', 'AdminController\Admin\ResetPasswordController@reset')->name('admin.password.update');
    Route::post('logout', 'AdminController\Admin\LoginController@logout')->name('admin.logout');


    Route::group(['middleware'=> ['web','auth:admin']],function(){
        // public notifications
        Route::get('public_notifications' , 'AdminController\HomeController@public_notifications')->name('public_notifications');
        Route::post('store_public_notifications' , 'AdminController\HomeController@store_public_notifications')->name('storePublicNotification');
        Route::get('user_notifications' , 'AdminController\HomeController@user_notifications')->name('user_notifications');
        Route::post('storeUserNotification' , 'AdminController\HomeController@store_user_notifications')->name('storeUserNotification');


        Route::get('setting','AdminController\SettingController@index')->name('settings');
        Route::post('add/settings','AdminController\SettingController@store');
        Route::post('drivers_commission','AdminController\SettingController@drivers_commission')->name('drivers_commission');

        Route::get('pages/about','AdminController\PageController@about');
        Route::post('add/pages/about','AdminController\PageController@store_about');

        Route::get('pages/terms','AdminController\PageController@terms');
        Route::post('add/pages/terms','AdminController\PageController@store_terms');

        Route::get('users/{type}','AdminController\UserController@index');
        Route::get('add/user/{type}','AdminController\UserController@create');
        Route::post('add/user/{type}','AdminController\UserController@store');
        Route::get('edit/user/{id}/{type}','AdminController\UserController@edit');
        Route::get('edit/userAccount/{id}/{type}','AdminController\UserController@edit_account');
        Route::post('update/userAccount/{id}/{type}','AdminController\UserController@update_account');
        Route::post('update/user/{id}/{type}','AdminController\UserController@update');
        Route::post('update/pass/{id}','AdminController\UserController@update_pass');
        Route::post('update/privacy/{id}','AdminController\UserController@update_privacy');
        Route::get('delete/{id}/user','AdminController\UserController@destroy');
        Route::get('active_user/{id}/{status}','AdminController\UserController@active_user')->name('active_user');

        // Cities  Routes
        Route::get('countries','AdminController\CountryController@index')->name('Country');
        Route::get('countries/create','AdminController\CountryController@create')->name('createCountry');
        Route::post('countries/store','AdminController\CountryController@store')->name('storeCountry');
        Route::get('countries/edit/{id}','AdminController\CountryController@edit')->name('editCountry');
        Route::post('countries/update/{id}','AdminController\CountryController@update')->name('updateCountry');
        Route::get('countries/delete/{id}','AdminController\CountryController@destroy')->name('deleteCountry');



        // =============================== start TruckType ==============================
        Route::get('trucks_types','AdminController\TruckTypeController@index')->name('TruckType');
        Route::get('trucks_types/create','AdminController\TruckTypeController@create')->name('createTruckType');
        Route::post('trucks_types/store','AdminController\TruckTypeController@store')->name('storeTruckType');
        Route::get('trucks_types/{id}/edit','AdminController\TruckTypeController@edit')->name('editTruckType');
        Route::post('trucks_types/update/{id}','AdminController\TruckTypeController@update')->name('updateTruckType');
        Route::get('trucks_types/delete/{id}','AdminController\TruckTypeController@destroy')->name('deleteTruckType');
        // =============================== end TruckType ================================

        // =============================== start banks ==============================
        Route::get('banks','AdminController\BankController@index')->name('Bank');
        Route::get('banks/create','AdminController\BankController@create')->name('createBank');
        Route::post('banks/store','AdminController\BankController@store')->name('storeBank');
        Route::get('banks/{id}/edit','AdminController\BankController@edit')->name('editBank');
        Route::post('banks/update/{id}','AdminController\BankController@update')->name('updateBank');
        Route::get('banks/delete/{id}','AdminController\BankController@destroy')->name('deleteBank');
        // =============================== end banks ================================


        // =============================== start VehicleBrand ==============================
        Route::get('vehicle_brands','AdminController\VehicleBrandController@index')->name('VehicleBrand');
        Route::get('vehicle_brands/create','AdminController\VehicleBrandController@create')->name('createVehicleBrand');
        Route::post('vehicle_brands/store','AdminController\VehicleBrandController@store')->name('storeVehicleBrand');
        Route::get('vehicle_brands/{id}/edit','AdminController\VehicleBrandController@edit')->name('editVehicleBrand');
        Route::post('vehicle_brands/update/{id}','AdminController\VehicleBrandController@update')->name('updateVehicleBrand');
        Route::get('vehicle_brands/delete/{id}','AdminController\VehicleBrandController@destroy')->name('deleteVehicleBrand');
        Route::get('active_truck/{id}/{status}','AdminController\VehicleBrandController@active_truck')->name('active_truck');

        // =============================== end VehicleBrand ================================

        Route::get('vehicles','AdminController\VehicleBrandController@vehicles')->name('vehicles');
        Route::get('trucks/show/{id}','AdminController\VehicleBrandController@show_truck')->name('show_truck');
        Route::get('trucks/delete/{id}','AdminController\VehicleBrandController@delete_truck');

        Route::get('orders/{status}','AdminController\HomeController@orders')->name('orders');

        // =============================== start Ranges ==============================
        Route::get('range','AdminController\RangeController@index')->name('Range');
        Route::get('range/create','AdminController\RangeController@create')->name('createRange');
        Route::post('range/store','AdminController\RangeController@store')->name('storeRange');
        Route::get('range/{id}/edit','AdminController\RangeController@edit')->name('editRange');
        Route::post('range/update/{id}','AdminController\RangeController@update')->name('updateRange');
        Route::get('range/delete/{id}','AdminController\RangeController@destroy')->name('deleteRange');
        // =============================== end Ranges ============================TruckType

        Route::get('pulls','AdminController\SettingController@pulls')->name('Pull');
        Route::get('wallet_charging','AdminController\SettingController@wallet_charging')->name('wallet_charging');
        Route::get('PullDone/{id}','AdminController\SettingController@PullDone')->name('PullDone');
        Route::get('chargeDone/{id}','AdminController\SettingController@chargeDone')->name('chargeDone');
        Route::get('chargeNotDone/{id}','AdminController\SettingController@chargeNotDone')->name('chargeNotDone');


        // commission
        Route::get('commissions','AdminController\SettingController@commissions')->name('Commission');
        Route::get('commissions/CommissionDone/{id}','AdminController\SettingController@CommissionDone')->name('CommissionDone');
        Route::get('commissions/CommissionNotDone/{id}','AdminController\SettingController@CommissionNotDone')->name('CommissionNotDone');


        Route::get('remove-social/{id}', 'AdminController\FoodController@removesocial')->name('imageDeptRemove');


        // Admins Route
        Route::resource('admins', 'AdminController\AdminController');

        Route::get('/profile', [
            'uses' => 'AdminController\AdminController@my_profile',
            'as' => 'my_profile' // name
        ]);
        Route::post('/profileEdit', [
            'uses' => 'AdminController\AdminController@my_profile_edit',
            'as' => 'my_profile_edit' // name
        ]);
        Route::get('/profileChangePass', [
            'uses' => 'AdminController\AdminController@change_pass',
            'as' => 'change_pass' // name
        ]);
        Route::post('/profileChangePass', [
            'uses' => 'AdminController\AdminController@change_pass_update',
            'as' => 'change_pass' // name
        ]);

        Route::get('/admin_delete/{id}', [
            'uses' => 'AdminController\AdminController@admin_delete',
            'as' => 'admin_delete' // name
        ]);

    });



});
Route::get('/Privacy-Policy' , function ()
{
   return view('admin.privacyAndPolicy');
});
