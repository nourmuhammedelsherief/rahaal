<?php
/**
 *  Developed By Nour Muhammad El sheriff
 *  01119399781
 *  nourmuhammed20121994@gmail.com
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

class AuthUserController extends Controller
{
    public function countries()
    {
        $countries = App\Country::all();
        if ($countries->count() > 0)
        {
            return ApiController::respondWithSuccess( App\Http\Resources\Country::collection($countries));
        }else{
            $errors  =  [
                'key'   => 'countries',
                'value' => trans('messages.noCountries'),
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    public function registerMobile(Request $request) {

        $rules = [
            'phone_number' => 'required|unique:users',
            'country_id'   => 'required|exists:countries,id'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $code = mt_rand(1000, 9999);
        $country_code = App\Country::find($request->country_id)->code;
        $check = substr($request->input('phone_number'), 0, 2) === "05";
        if ($check == true)
        {
            $phone = $country_code.ltrim($request->phone_number , '0');
        }else{
            $phone = $country_code.$request->phone_number;
        }
        $user = User::wherePhone_number($request->phone_number)->first();
        if ($user == null)
        {
            $body = trans('messages.confirmation_code').$code;
            Yamamah($phone , $body);
        }else{
            $errors = [
                'key'    => 'user_register_mobile',
                'value'  => trans('messages.uRegisteredBefore')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
        $created = App\PhoneVerification::create([
            'code'=>$code,
            'phone_number'=>$request->phone_number
        ]);
        return  ApiController::respondWithSuccess([]);
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

        $user= App\PhoneVerification::where('phone_number',$request->phone_number)->orderBy('id','desc')->first();

        if ($user){

            if($user->code == $request->code){
                $successLogin = ['key'=>'message',
                    'value'=> trans('messages.activation_code_success')
                ];
                return ApiController::respondWithSuccess($successLogin);
            }else{
                $errorsLogin = ['key'=>'message',
                    'value'=> trans('messages.error_code')
                ];
                return ApiController::respondWithErrorClient(array($errorsLogin));
            }

        }else{

            $errorsLogin = ['key'=>'message',
                'value'=> trans('messages.error_code')
            ];
            return ApiController::respondWithErrorClient(array($errorsLogin));
        }
    }
    public function resend_code(Request $request){

        $rules = [
            'phone_number' => 'required',
            'country_id'   => 'required|exists:countries,id'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $country_code = App\Country::find($request->country_id)->code;
        $check = substr($request->input('phone_number'), 0, 2) === "05";
        if ($check == true)
        {
            $phone = $country_code.ltrim($request->phone_number , '0');
        }else{
            $phone = $country_code. $request->phone_number;
        }
        $code = mt_rand(1000, 9999);
        $body = trans('messages.confirmation_code').$code;
        Yamamah($phone , $body);
        $created = App\PhoneVerification::create([
            'code'=>$code,
            'phone_number'=>$request->phone_number
        ]);
        return $created
            ? ApiController::respondWithSuccess( trans('messages.success_send_code'))
            : ApiController::respondWithServerErrorObject();

    }
    public function register(Request $request)
    {
        $rules = [
            'country_id'   => 'required|exists:countries,id',
            'phone_number'          => 'required|unique:users',
            'email'                 => 'nullable|unique:users',
            'name'                  => 'required|max:255',
            'photo'                 => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
            'password'              => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
//            'association'           => 'required|in:0,1',
//            'is_join'               => 'required|in:0,1',
            'device_token'          => 'required',
//            'identity_number'       => 'required_if:is_join,1',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));
        $all=[];

        $user = App\User::create([
            'phone_number'       => $request->phone_number,
            'country_id'         => $request->country_id,
            'name'               => $request->name,
            'email'              => $request->email,
            'active'             => 1,
            'type'               => 1,
//            'association'        => $request->association,
//            'is_join'            => $request->is_join == null ? '0' : $request->is_join,
//            'identity_number'    => $request->is_join == '1' ? $request->identity_number : null,
            'password'           => Hash::make($request->password),
            'photo'              => $request->file('photo') == null ? null :UploadImage($request->file('photo'), 'photo', '/uploads/users'),
        ]);

        $user->update(['api_token' => generateApiToken($user->id, 10)]);
        App\PhoneVerification::where('phone_number',$request->phone_number)->orderBy('id','desc')->delete();

        //save_device_token....
        $created = ApiController::createUserDeviceToken($user->id, $request->device_token, $request->device_type);
        return $user
            ? ApiController::respondWithSuccess(new App\Http\Resources\User($user))
            : ApiController::respondWithServerErrorArray();
    }
    public function login(Request $request) {

        $rules = [
            'phone_number'  => 'required',
            'password'      => 'required',
            'device_token'  => 'required',
//            'device_type'   => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));


        if (Auth::attempt(['phone_number' => $request->phone_number, 'password' => $request->password, 'type'=>1])) {

            if (Auth::user()->active == 0){
                $errors = ['key'=>'message',
                    'value'=> trans('messages.Sorry_your_membership_was_stopped_by_Management')
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }

            //save_device_token....
            $created = ApiController::createUserDeviceToken(Auth::user()->id, $request->device_token, $request->device_type);

            $all = App\User::where('phone_number', $request->phone_number)->first();
            $all->update(['api_token' => generateApiToken($all->id, 10)]);
            $user =  App\User::where('phone_number', $request->phone_number)->first();
            return $created
                ? ApiController::respondWithSuccess(new App\Http\Resources\User($user))
                : ApiController::respondWithServerErrorArray();
        }else{
            $user = User::wherePhone_number($request->phone_number)->first();
            if ($user == null)
            {
                $errors = [
                    'key'=>'message',
                    'value'=>trans('messages.Wrong_phone'),
                ];
                return ApiController::respondWithErrorNOTFoundArray(array($errors));
            }else{
                $errors = [
                    'key'=>'message',
                    'value'=>trans('messages.error_password'),
                ];
                return ApiController::respondWithErrorNOTFoundArray(array($errors));
            }
        }
    }
    public function forgetPassword(Request $request) {
        $rules = [
            'phone_number' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $user = User::where('phone_number',$request->phone_number)
            ->where('type' , 1)
            ->first();
        if ($user != null)
        {
            $code = mt_rand(1000, 9999);
            $country_code = $user->country->code;
            $check = substr($request->input('phone_number'), 0, 2) === "05";
            if ($check == true)
            {
                $phone = $country_code.ltrim($request->phone_number , '0');
            }else{
                $phone = $country_code.$request->phone_number;
            }
            $body = trans('messages.confirmation_code').$code;
            Yamamah($phone , $body);
            $updated=  App\User::where('phone_number',$request->phone_number)
                ->where('type' , 1)
                ->update([
                    'verification_code'=>$code,
                ]);
            $success = ['key'=>'message',
                'value'=> trans('messages.success_send_code')
            ];

            return $updated
                ? ApiController::respondWithSuccess($success)
                : ApiController::respondWithServerErrorObject();
        }else{
            $errorsLogin = ['key'=>'message',
                'value'=> trans('messages.Wrong_phone')
            ];
            return ApiController::respondWithErrorClient(array($errorsLogin));
        }
    }
    public function confirmResetCode(Request $request){

        $rules = [
            'phone_number' => 'required',
            'code' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $user= App\User::where('phone_number',$request->phone_number)
            ->where('verification_code',$request->code)
            ->where('type' , 1)
            ->first();
        if ($user){
            $updated=  App\User::where('phone_number',$request->phone_number)
                ->where('verification_code',$request->code)
                ->where('type' , 1)
                ->update([
                'verification_code'=>null
            ]);
            $success = ['key'=>'message',
                'value'=> trans('messages.code_success')
            ];
            return $updated
                ? ApiController::respondWithSuccess($success)
                : ApiController::respondWithServerErrorObject();
        }else{

            $errorsLogin = ['key'=>'message',
                'value'=> trans('messages.error_code')
            ];
            return ApiController::respondWithErrorClient(array($errorsLogin));
        }


    }
    public function resetPassword(Request $request) {
        $rules = [
            'phone_number'          => 'required|numeric',
            'password'              => 'required',
            'password_confirmation' => 'required|same:password',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $user = App\User::where('phone_number',$request->phone_number)
            ->where('type' , 1)
            ->first();
        if($user)
            $updated = $user->update(['password' => Hash::make($request->password)]);
        else{
            $errorsLogin = ['key'=>'message',
                'value'=> trans('messages.Wrong_phone')
            ];
            return ApiController::respondWithErrorClient(array($errorsLogin));
        }


        return $updated
            ? ApiController::respondWithSuccess(trans('messages.Password_reset_successfully'))
            : ApiController::respondWithServerErrorObject();
    }
    public function changePassword(Request $request) {
        $rules = [
            'current_password'      => 'required',
            'new_password'          => 'required',
            'password_confirmation' => 'required|same:new_password',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $error_old_password = ['key'=>'message',
            'value'=> trans('messages.error_old_password')
        ];
        if (!(Hash::check($request->current_password, Auth::user()->password)))
            return ApiController::respondWithErrorNOTFoundObject(array($error_old_password));

        //update-password-finally ^^
        $updated = Auth::user()->update(['password' => Hash::make($request->new_password)]);
        $success_password = ['key'=>'message',
            'value'=> trans('messages.Password_reset_successfully')
        ];
        return $updated
            ? ApiController::respondWithSuccess($success_password)
            : ApiController::respondWithServerErrorObject();
    }
    public function change_phone_number(Request $request)
    {
        $rules = [
            'phone_number' => 'required|numeric|unique:users,phone_number,'.$request->user()->id,
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $code = mt_rand(1000, 9999);
        $country_code = $request->user()->country->code;
        $check = substr($request->input('phone_number'), 0, 2) === "05";
        if ($check == true)
        {
            $phone = $country_code.ltrim($request->phone_number , '0');
        }else{
            $phone = $country_code.$request->phone_number;
        }
        $body = trans('messages.confirmation_code').$code;
        Yamamah($phone , $body);
        $updated=  App\User::where('id',Auth::user()->id)->update([
            'verification_code'=>$code,
        ]);
        $success = ['key'=>'message',
            'value'=> trans('messages.success_send_code')
        ];
        return $updated
            ? ApiController::respondWithSuccess($success)
            : ApiController::respondWithServerErrorObject();
    }
    public function check_code_changeNumber(Request $request)
    {
        $rules = [
            'code' => 'required',
            'phone_number' => 'required|numeric|unique:users,phone_number,'.$request->user()->id,
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $user= App\User::where('id',Auth::user()->id)->where('verification_code', $request->code)->first();
        if ($user){
            $updated=  $user->update([
                'verification_code'=>null,
                'phone_number'=>$request->phone_number,
            ]);

            $success = ['key'=>'message',
                'value'=> trans('messages.phone_changed_successfully')
            ];
            return $updated
                ? ApiController::respondWithSuccess($success)
                : ApiController::respondWithServerErrorObject();
        }else{

            $errorsLogin = ['key'=>'message',
                'value'=> trans('messages.error_code')
            ];
            return ApiController::respondWithErrorClient(array($errorsLogin));
        }
    }
    public function logout(Request $request)
    {
        $rules = [
            'device_token'     => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $exists = App\UserDevice::where('id',Auth::user()->id)->where('device_token',$request->device_token)->get();

        if (count($exists) !== 0){
            foreach ($exists  as $new){
                $new->delete();
            }

        }
        $users=  App\User::where('id',Auth::user()->id)->first()->update(
            [
                'api_token'=>null
            ]
        );
        return $users
            ? ApiController::respondWithSuccess([])
            : ApiController::respondWithServerErrorArray();


    }
    public function user_edit_account(Request $request)
    {
        $rules = [
            'email'             => 'nullable|email|unique:users,email,'.$request->user()->id,
            'name'              => 'nullable',
            'country_id'        => 'nullable|exists:countries,id',
            'photo'             => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $user= App\User::where('id',$request->user()->id)->first();

        $updated=  $user->update([
            'country_id'      =>  $request->country_id == null ? $user->country_id : $request->country_id,
            'name'            =>  $request->name == null ? $user->name : $request->name,
            'photo'           =>  $request->photo == null ? $user->photo : UploadImageEdit($request->file('photo'), 'photo', '/uploads/users',$request->user()->photo),
        ]);
        return ApiController::respondWithSuccess(new App\Http\Resources\User($user));
    }
    public function change_address(Request $request)
    {
        $rules = [
            'address'   => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $user= App\User::where('id',$request->user()->id)->first();

        $updated=  $user->update([
            'address'=>  $request->address
        ]);

        $success = ['key'=>'address',
            'value'=> User::find($request->user()->id)->address
        ];
        return $updated
            ? ApiController::respondWithSuccess($success)
            : ApiController::respondWithServerErrorObject();
    }
    public  function sendSMS($jsonObj)
    {
        $contextOptions['http'] = array('method' => 'POST', 'header'=>'Content-type: application/json', 'content'=> json_encode($jsonObj), 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
        $contextResouce  = stream_context_create($contextOptions);
        $url = "http://www.alfa-cell.com/api/msgSend.php";
        $arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
        $result = $arrayResult[0];

        return $result;
    }
    /**
     * @change_state
     */
    public function change_state(Request $request)
    {
        $user = User::find($request->user()->id);
        if ($user != null)
        {
            $updated=  $user->update([
                'state'=>  $request->state
            ]);

            $success = ['key'=>'state',
                'value'=> User::find($request->user()->id)->state
            ];
            return $updated
                ? ApiController::respondWithSuccess($success)
                : ApiController::respondWithServerErrorObject();
        }else{
            $successPermission = ['key'=>'order',
                'value'=> trans(' !!!!!!!!!!عفوا لا يوجد هذا المستخدم  !!!!')
            ];
            return ApiController::respondWithErrorClient(array($successPermission));
        }
    }
    public function get_users()
    {
        $users = User::whereType(1)
            ->where('active' , 1)
            ->get();
        if ($users->count() > 0)
        {
            return ApiController::respondWithSuccess(App\Http\Resources\User::collection($users));
        }else{
            $errors = [
                'key'   => 'get_users',
                'value' => trans('messages.no_users_found'),
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    public function association_check(Request $request)
    {
        $rules = [
            'is_join'         => 'required|in:0,1',
            'identity_number' => 'required_if:is_join,1',
            'place_name'      => 'required_if:is_join,1',
            'latitude'        => 'required_if:is_join,1',
            'longitude'       => 'required_if:is_join,1',
            'description'     => 'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $user = $request->user();
        if ($user->association == '0')
        {
            // check   if the user are recorded in to the database as a association benefit
            $check_user = App\AssociationBenefit::where('identify_number' , $request->identity_number)->first();
            if ($check_user)
            {
                if ($request->is_join == '1')
                {
                    $user->update([
                        'is_join'          => $request->is_join,
                        'association'      => '1',
                        'identity_number'  => $request->identity_number == null ? $user->identity_number : $request->identity_number,
                    ]);
                    // create a user place that will be free delivery
                    App\UserPlace::create([
                        'name' => $request->place_name,
                        'user_id' => $request->user()->id,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'association'      => '1',      // fixed place
                        'description' => $request->description == null ? null :$request->description,
                    ]);
                    $success = [
                        'key'    => 'association_check',
                        'value'  => trans('messages.success_join_association')
                    ];
                    return ApiController::respondWithSuccess($success);
                }
            }else{
                $errors = [
                    'key'   => 'association_check',
                    'value' => trans('messages.fail_join_association')
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }
        }elseif ($user->association == '1'){
            $errors = [
                'key'   => 'association_check',
                'value' => trans('messages.exists_association')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
}
