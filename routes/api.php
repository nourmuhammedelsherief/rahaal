<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/**
 *  Developed By Nour Muhammad El sheriff
 *  01119399781
 *  nourmuhammed20121994@gmail.com
 */
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->group(function () {
    Route::group(['middleware' => ['cors', 'localization']], function () {
        Route::get('/countries', 'Api\AuthUserController@countries');
        /*user register*/
        Route::post('/user_register_mobile', 'Api\AuthUserController@registerMobile');
        Route::post('/user_phone_verification', 'Api\AuthUserController@register_phone_post');
        Route::post('/user_resend_code', 'Api\AuthUserController@resend_code');
        Route::post('/user_register', 'Api\AuthUserController@register');
        Route::post('/user_login', 'Api\AuthUserController@login');
        Route::post('/user_forget_password', 'Api\AuthUserController@forgetPassword');
        Route::post('/user_confirm_reset_code', 'Api\AuthUserController@confirmResetCode');
        Route::post('/user_reset_password', 'Api\AuthUserController@resetPassword');
        Route::get('/get_users', 'Api\AuthUserController@get_users');
        /*end user register*/
        /*driver register*/
        Route::post('/driver_register_mobile', 'Api\AuthController@registerMobile');
        Route::post('/driver_phone_verification', 'Api\AuthController@register_phone_post');
        Route::post('/driver_resend_code', 'Api\AuthController@resend_code');
        Route::post('/driver_register', 'Api\AuthController@register');
        Route::post('/driver_login', 'Api\AuthController@login');
        Route::post('/driver_forget_password', 'Api\AuthController@forgetPassword');
        Route::post('/driver_confirm_reset_code', 'Api\AuthController@confirmResetCode');
        Route::post('/driver_reset_password', 'Api\AuthController@resetPassword');
        Route::get('/get_drivers', 'Api\AuthController@get_drivers');
        Route::get('/get_driver_location/{id}', 'Api\AuthController@get_driver_location');
        /*end driver register*/

        Route::get('/terms_and_conditions', 'Api\ProfileController@terms_and_conditions');
        Route::get('/about_us', 'Api\ProfileController@about_us');
        Route::get('/get_range', 'Api\ProfileController@get_range');
        Route::get('/get_user_data/{id}', 'Api\ProfileController@get_user_data');
        Route::post('/disconnect-room', 'Api\MessageController@disconnect_room');

        Route::get('/get_trucks_types', 'Api\TruckController@get_trucks_types');
        Route::get('/get_vehicle_brands', 'Api\TruckController@get_vehicle_brands');
        Route::get('/bank_info', 'Api\OfferController@bank_info');
        Route::get('/settings', 'Api\DetailsController@settings');

    });

    Route::group(['middleware' => ['auth:api', 'cors', 'localization']], function () {
        /**
         *  Start User Routes
         */
        //====================user app ====================
        Route::post('/user_change_password', 'Api\AuthUserController@changePassword');
        Route::post('/user_change_phone_number', 'Api\AuthUserController@change_phone_number');
        Route::post('/user_check_code_change_phone_number', 'Api\AuthUserController@check_code_changeNumber');
        Route::post('/user_edit_account', 'Api\AuthUserController@user_edit_account');
        //===============logout========================
        Route::post('/user_logout', 'Api\AuthUserController@logout');

        /**
         *  End User Routes
         */

        /**
         *  Start Driver Routes
         */
        //====================user app ====================
        Route::post('/driver_change_password', 'Api\AuthController@changePassword');
        Route::post('/driver_change_phone_number', 'Api\AuthController@change_phone_number');
        Route::post('/driver_check_code_change_phone_number', 'Api\AuthController@check_code_changeNumber');
        Route::post('/driver_edit_account', 'Api\AuthController@user_edit_account');
        Route::post('/driver_availability', 'Api\AuthController@driver_availability');
        Route::post('/driver_update_location', 'Api\AuthController@driver_update_location');

        //===============logout========================
        Route::post('/driver_logout', 'Api\AuthUserController@logout');
        /**
         *  End Driver Routes
         */
        /**
         *  Start Restaurants Routes
         */
        /*notification*/
        Route::get('/list_notifications', 'Api\ApiController@listNotifications');
        Route::post('/delete_Notifications/{id}', 'Api\ApiController@delete_Notifications');
        Route::get('/read_all_notification', 'Api\ApiController@read_all_notification');
        Route::get('/read_notification/{id}', 'Api\ApiController@read_notification');

        /*notification*/

        //==============================================================================================//

        // electronic pocket routes
        Route::post('/charge_electronic_wallet', 'Api\UserController@charge_electronic_wallet');
        Route::get('/pull_my_balance', 'Api\UserController@pull_balance');
        Route::get('/check_my_balance', 'Api\UserController@check_my_balance');
        Route::get('/history', 'Api\UserController@history');

        /**
         *  start Order Routes
         */

        Route::post('/create_order', 'Api\OrderController@createOrder');
        Route::post('/finish_order/{order_id}', 'Api\OrderController@finish_order');
        Route::get('/user_cancel_order/{order_id}', 'Api\OrderController@user_cancel_order');

        Route::get('/user_orders/{status}', 'Api\OrderController@user_orders');
        Route::get('/driver_orders/{status}', 'Api\OrderController@driver_orders');

        /**
         *  End Order Routes
         */

        // Commission Routes
        Route::get('/get_commission', 'Api\UserController@get_commission');
        Route::post('/pay_commission', 'Api\UserController@pay_commission');

        // user_places routes
        Route::post('create_place', 'Api\UserController@create_place');
        Route::post('edit_place/{id}', 'Api\UserController@edit_place');
        Route::get('delete_place/{id}', 'Api\UserController@delete_place');
        Route::get('user_places', 'Api\UserController@user_places');


        /**
         *  Start offers routes
         */
        Route::post('/create_offer', 'Api\OfferController@create_offer');
        Route::get('/offers/{order_id}', 'Api\OfferController@get_offers');
        Route::get('/accept_offer/{offer_id}', 'Api\OfferController@accept_offer');
        /**
         *  End offers routes
         */

        /**
         * start trucks routes
         */
        Route::post('/create_truck', 'Api\TruckController@create_truck');
        Route::post('/edit_truck/{id}', 'Api\TruckController@edit_truck');
        Route::get('/delete_truck/{id}', 'Api\TruckController@delete_truck');
        Route::get('/my_truck', 'Api\TruckController@my_truck');
        /**
         * End trucks routes
         */

        // rates
        Route::post('/rate', 'Api\OfferController@rate');

        // messages routes
        Route::post('/connect-room', 'Api\MessageController@connect_room');
        Route::post('/send_message', 'Api\MessageController@send_message');
        Route::get('/my_conversations', 'Api\MessageController@my_conversations');
        Route::get('/conversations_messages/{id}', 'Api\MessageController@conversations_messages');
        Route::post('/upload_image', 'Api\MessageController@upload_image');


//    ===========refreshToken ====================

        Route::post('/refresh-device-token', 'Api\DetailsController@refreshDeviceToken');
        Route::post('/refreshToken', 'Api\DetailsController@refreshToken');
        //===============logout========================
        Route::post('/logout', 'Api\AuthController@logout');

    });
});
