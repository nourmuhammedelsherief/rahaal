<?php

namespace App\Http\Controllers\Api;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use function Matrix\trace;
use Validator;
use App;
use Auth;
use App\User;
use App\City;
use App\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmCode;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    public function registerMobile(Request $request)
    {

        $rules = [
            'phone_number' => 'required|unique:users',
            'country_id' => 'required|exists:countries,id'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $code = mt_rand(1000, 9999);
        $country_code = App\Country::find($request->country_id)->code;
        $check = substr($request->input('phone_number'), 0, 2) === "05";
        if ($check == true) {
            $phone = $country_code . ltrim($request->phone_number, '0');
        } else {
            $phone = $country_code . $request->phone_number;
        }
        $user = User::wherePhone_number($request->phone_number)->first();
        if ($user == null) {
            $body = trans('messages.confirmation_code') . $code;
            Yamamah($phone, $body);
        } else {
            $errors = [
                'key' => 'user_register_mobile',
                'value' => trans('messages.uRegisteredBefore')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
        $created = App\PhoneVerification::create([
            'code' => $code,
            'phone_number' => $request->phone_number
        ]);
        return ApiController::respondWithSuccess([]);
    }

    public function register_phone_post(Request $request)
    {
        $rules = [
            'code' => 'required',
            'phone_number' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $user = App\PhoneVerification::where('phone_number', $request->phone_number)->orderBy('id', 'desc')->first();

        if ($user) {

            if ($user->code == $request->code) {
                $successLogin = ['key' => 'message',
                    'value' => trans('messages.activation_code_success')
                ];
                return ApiController::respondWithSuccess($successLogin);
            } else {
                $errorsLogin = ['key' => 'message',
                    'value' => trans('messages.error_code')
                ];
                return ApiController::respondWithErrorClient(array($errorsLogin));
            }

        } else {

            $errorsLogin = ['key' => 'message',
                'value' => trans('messages.error_code')
            ];
            return ApiController::respondWithErrorClient(array($errorsLogin));
        }
    }

    public function resend_code(Request $request)
    {

        $rules = [
            'phone_number' => 'required',
            'country_id' => 'required|exists:countries,id'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $country_code = App\Country::find($request->country_id)->code;
        $check = substr($request->input('phone_number'), 0, 2) === "05";
        if ($check == true) {
            $phone = $country_code . ltrim($request->phone_number, '0');
        } else {
            $phone = $country_code . $request->phone_number;
        }
        $code = mt_rand(1000, 9999);
        $body = trans('messages.confirmation_code') . $code;
        Yamamah($phone, $body);
        $created = App\PhoneVerification::create([
            'code' => $code,
            'phone_number' => $request->phone_number
        ]);
        return $created
            ? ApiController::respondWithSuccess(trans('messages.success_send_code'))
            : ApiController::respondWithServerErrorObject();

    }

    public function register(Request $request)
    {
        $rules = [
            'phone_number' => 'required|unique:users',
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|max:255',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
            'device_token' => 'required',
            'latitude'=>'required',
            'longitude'=>'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));
        $all = [];

        $user = User::create([
            'phone_number' => $request->phone_number,
            'country_id' => $request->country_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'name' => $request->name,
//                'description'        => $request->description,
//                'city_id'            => $request->city_id,
            'active' => 0,
//                'is_join'            => $request->is_join == null ? '0' : $request->is_join,
//                'identity_number'    => $request->is_join == '1' ? $request->identity_number : null,
            'password' => Hash::make($request->password),
//                'photo'              => $request->file('photo') == null ? null : UploadImage($request->file('photo'), 'photo', '/uploads/users'),
            'type' => 2,
        ]);

        $user->update(['api_token' => generateApiToken($user->id, 10)]);

        App\PhoneVerification::where('phone_number', $request->phone_number)->orderBy('id', 'desc')->delete();

        //save_device_token....
        $created = ApiController::createUserDeviceToken($user->id, $request->device_token, $request->device_type);

        return $user
            ? ApiController::respondWithSuccess(new App\Http\Resources\User($user))
            : ApiController::respondWithServerErrorArray();

    }

    public function login(Request $request)
    {

        $rules = [
            'phone_number' => 'required',
            'password' => 'required',
            'device_token' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));


        if (Auth::attempt(['phone_number' => $request->phone_number, 'password' => $request->password, 'type' => 2])) {

            if (Auth::user()->active == 0) {
                $errors = ['key' => 'message',
                    'value' => trans('messages.Sorry_your_membership_was_stopped_by_Management')
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }

            //save_device_token....
            $created = ApiController::createUserDeviceToken(Auth::user()->id, $request->device_token, $request->device_type);

            $all = User::where('phone_number', $request->phone_number)->first();
            $all->update(['api_token' => generateApiToken($all->id, 10)]);
            $user = User::where('phone_number', $request->phone_number)->first();

            return $created
                ? ApiController::respondWithSuccess(new App\Http\Resources\User($user))
                : ApiController::respondWithServerErrorArray();
        } else {
            $user = User::wherePhone_number($request->phone_number)->first();
            if ($user == null) {
                $errors = [
                    'key' => 'message',
                    'value' => trans('messages.Wrong_phone'),
                ];
                return ApiController::respondWithErrorNOTFoundArray(array($errors));
            } else {
                $errors = [
                    'key' => 'message',
                    'value' => trans('messages.error_password'),
                ];
                return ApiController::respondWithErrorNOTFoundArray(array($errors));
            }
        }

    }

    public function forgetPassword(Request $request)
    {
        $rules = [
            'phone_number' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $user = User::where('phone_number', $request->phone_number)
            ->where('type', 2)
            ->first();
        if ($user != null) {
            $code = mt_rand(1000, 9999);
            $country_code = $user->country->code;
            $check = substr($request->input('phone_number'), 0, 2) === "05";
            if ($check == true) {
                $phone = $country_code . ltrim($request->phone_number, '0');
            } else {
                $phone = $country_code . $request->phone_number;
            }
            $body = trans('messages.confirmation_code') . $code;
            Yamamah($phone, $body);
            $updated = App\User::where('phone_number', $request->phone_number)
                ->where('type', 2)
                ->update([
                    'verification_code' => $code,
                ]);
            $success = ['key' => 'message',
                'value' => trans('messages.success_send_code')
            ];

            return $updated
                ? ApiController::respondWithSuccess($success)
                : ApiController::respondWithServerErrorObject();
        } else {
            $errorsLogin = ['key' => 'message',
                'value' => trans('messages.Wrong_phone')
            ];
            return ApiController::respondWithErrorClient(array($errorsLogin));
        }
    }

    public function confirmResetCode(Request $request)
    {

        $rules = [
            'phone_number' => 'required',
            'code' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $user = App\User::where('phone_number', $request->phone_number)
            ->where('verification_code', $request->code)
            ->where('type', 2)
            ->first();
        if ($user) {
            $updated = App\User::where('phone_number', $request->phone_number)
                ->where('verification_code', $request->code)
                ->where('type', 2)
                ->update([
                    'verification_code' => null
                ]);
            $success = ['key' => 'message',
                'value' => trans('messages.code_success')
            ];
            return $updated
                ? ApiController::respondWithSuccess($success)
                : ApiController::respondWithServerErrorObject();
        } else {

            $errorsLogin = ['key' => 'message',
                'value' => trans('messages.error_code')
            ];
            return ApiController::respondWithErrorClient(array($errorsLogin));
        }


    }

    public function resetPassword(Request $request)
    {
        $rules = [
            'phone_number' => 'required|numeric',
//            'phone'                 => 'required',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $user = User::where('phone_number', $request->phone_number)->first();
//        $user = User::wherePhone($request->phone)->first();

        if ($user)
            $updated = $user->update(['password' => Hash::make($request->password)]);
        else {
            $errorsLogin = ['key' => 'message',
                'value' => trans('messages.Wrong_phone')
            ];
            return ApiController::respondWithErrorClient(array($errorsLogin));
        }


        return $updated
            ? ApiController::respondWithSuccess(trans('messages.Password_reset_successfully'))
            : ApiController::respondWithServerErrorObject();
    }

    public function changePassword(Request $request)
    {

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required',
            'password_confirmation' => 'required|same:new_password',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $error_old_password = ['key' => 'message',
            'value' => trans('messages.error_old_password')
        ];
        if (!(Hash::check($request->current_password, $request->user()->password)))
            return ApiController::respondWithErrorNOTFoundObject(array($error_old_password));
//        if( strcmp($request->current_password, $request->new_password) == 0 )
//            return response()->json(['status' => 'error', 'code' => 404, 'message' => 'New password cant be the same as the old one.']);

        //update-password-finally ^^
        $updated = $request->user()->update(['password' => Hash::make($request->new_password)]);

        $success_password = ['key' => 'message',
            'value' => trans('messages.Password_reset_successfully')
        ];

        return $updated
            ? ApiController::respondWithSuccess($success_password)
            : ApiController::respondWithServerErrorObject();
    }

    public function change_phone_number(Request $request)
    {
        $rules = [
            'phone_number' => 'required|numeric|unique:users,phone_number,' . $request->user()->id,
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $code = mt_rand(1000, 9999);
        $country_code = $request->user()->country->code;
        $check = substr($request->input('phone_number'), 0, 2) === "05";
        if ($check == true) {
            $phone = $country_code . ltrim($request->phone_number, '0');
        } else {
            $phone = $country_code . $request->phone_number;
        }
        $body = trans('messages.confirmation_code') . $code;
        Yamamah($phone, $body);
        $updated = App\User::where('id', Auth::user()->id)->update([
            'verification_code' => $code,
        ]);
        $success = ['key' => 'message',
            'value' => trans('messages.success_send_code')
        ];
        return $updated
            ? ApiController::respondWithSuccess($success)
            : ApiController::respondWithServerErrorObject();
    }

    public function check_code_changeNumber(Request $request)
    {
        $rules = [
            'code' => 'required',
            'phone_number' => 'required|numeric|unique:users,phone_number,' . $request->user()->id,
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $user = App\User::where('id', Auth::user()->id)->where('verification_code', $request->code)->first();
        if ($user) {
            $updated = $user->update([
                'verification_code' => null,
                'phone_number' => $request->phone_number,
            ]);

            $success = ['key' => 'message',
                'value' => trans('messages.phone_changed_successfully')
            ];
            return $updated
                ? ApiController::respondWithSuccess($success)
                : ApiController::respondWithServerErrorObject();
        } else {

            $errorsLogin = ['key' => 'message',
                'value' => trans('messages.error_code')
            ];
            return ApiController::respondWithErrorClient(array($errorsLogin));
        }
    }

    public function user_edit_account(Request $request)
    {
        $rules = [
            'email' => 'nullable|email|unique:users,email,' . $request->user()->id,
            'name' => 'nullable|max:255',
            'country_id' => 'sometimes|exists:countries,id',
            'photo' => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
//            'driving_licence'       => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
//            'identity_number'       => 'nullable',
//            'device_token'          => 'nullable',
//            'latitude'              => 'nullable',
//            'longitude'             => 'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $user = User::where('id', $request->user()->id)->first();

        $updated = $user->update([
            'email' => $request->email == null ? $user->email : $request->email,
            'name' => $request->name == null ? $user->name : $request->name,
            'country_id' => $request->country_id == null ? $user->country_id : $request->country_id,
            'photo' => $request->photo == null ? $user->photo : UploadImageEdit($request->file('photo'), 'photo', '/uploads/users', $request->user()->photo),
            'latitude' => $request->latitude == null ? $user->latitude : $request->latitude,
            'longitude' => $request->longitude == null ? $user->longitude : $request->longitude,
//            'driving_licence' =>  $request->file('driving_licence') == null ? $user->driving_licence : UploadImage($request->file('driving_licence'), 'photo', '/uploads/driving_licences'),
//            'identity_number' =>  $request->identity_number == null ? $user->identity_number : $request->identity_number,
        ]);
        return ApiController::respondWithSuccess(new \App\Http\Resources\User($user));
    }

    public function logout(Request $request)
    {

        $rules = [
            'device_token' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $exists = App\UserDevice::where('id', $request->user()->id)->where('device_token', $request->device_token)->get();

        if (count($exists) !== 0) {
            foreach ($exists as $new) {
                $new->delete();
            }

        }
        $users = User::where('id', $request->user()->id)->first()->update(
            [
                'api_token' => null
            ]
        );
        return $users
            ? ApiController::respondWithSuccess([])
            : ApiController::respondWithServerErrorArray();
    }

    public function driver_availability(Request $request)
    {
        $user = $request->user();
        $rules = [
            'availability' => 'required|in:0,1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $user->update([
            'availability' => $request->availability,
        ]);
        return ApiController::respondWithSuccess([
            'key' => 'driver_availability',
            'value' => trans('messages.availabilityChanged')
        ]);
    }

    public function driver_update_location(Request $request)
    {
        $rules = [
            'latitude' => 'required',
            'longitude' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        // update driver Location
        $driver = $request->user();
        $driver->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
        return ApiController::respondWithSuccess(new \App\Http\Resources\User($driver));
    }

    public function get_driver_location($id)
    {
        $driver = User::find($id);
        if ($driver) {
            $success = [
                'latitude' => $driver->latitude,
                'longitude' => $driver->longitude,
            ];
            return ApiController::respondWithSuccess($success);
        } else {
            $errors = [
                'key' => 'get_driver_location',
                'value' => trans('messages.no_driver_found')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
}
