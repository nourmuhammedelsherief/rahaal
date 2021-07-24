<?php

namespace App\Http\Controllers\AdminController;

use App\City;

use App\Country;
use App\FoodCategory;
use App\Http\Controllers\Controller;
use App\User;
use App\UserDevice;
use App\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
use App\UserDepartment;
use Auth;
use Image;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type)
    {
        if ($type == '1')
        {
            // beneficiary  المستفيد العميل
            $users =User::whereType(1)
                ->orderBy('id','desc')
                ->get();
            return view('admin.users.index',compact('users'));
        }elseif($type == '2'){
            $users =User::whereType(2)
                ->orderBy('id','desc')
                ->get();
            return view('admin.users.drivers.index',compact('users'));
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
            if ($id == 1){
                $countries = Country::all();
                return view('admin.users.create' , compact('countries'));
            }elseif ($id == 2){
                $countries = Country::all();
                return view('admin.users.drivers.create' , compact('countries'));
            }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$type)
    {
        // the beneficiary type  is 2  the restaurant  type  is  2
        if($type == 1){
            // create user
            $this->validate($request, [
                'phone_number'          => 'required|unique:users',
                'country_id'            => 'required|exists:countries,id',
                'name'                  => 'required|max:255',
                'photo'                 => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
                'password'              => 'required|string|min:6',
                'password_confirmation' => 'required|same:password',
                'active'                => 'required',
            ]);

            // end certificate_photo
            $user= User::create([
                'phone_number'    => $request->phone_number,
                'country_id'      => $request->country_id,
                'name'            => $request->name,
                'active'          => $request->active,
                'password'        => Hash::make($request->password),
                'photo'           => $request->file('photo') == null ? null : UploadImage($request->file('photo'), 'photo', '/uploads/users'),
                'type'            => 1,
                'api_token'       => $request->token,
            ]);
            flash('تم أنشاء المستخدم بنجاح')->success();
//            return redirect('admin/users/1');
            return redirect('admin/users/1');

        }
        elseif ($type == 2){
            // create driver
             $this->validate($request, [
                'phone_number'          => 'required|unique:users',
                'email'                 => 'nullable|email|unique:users',
                'name'                  => 'required|max:255',
                'active'                => 'required',
                'photo'                 => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
                'password'              => 'required|string|min:6',
                'password_confirmation' => 'required|same:password',
                'country_id'            => 'required|exists:countries,id',

             ]);
            $user = User::create([
                'phone_number'       => $request->phone_number,
                'latitude'           => $request->latitude,
                'longitude'          => $request->longitude,
                'name'               => $request->name,
                'country_id'         => $request->country_id,
                'active'             => $request->active,
                'password'           => Hash::make($request->password),
                'photo'              => $request->file('photo') == null ? null : UploadImage($request->file('photo'), 'photo', '/uploads/users'),
                'type'               => 2,
            ]);

            flash('تم أنشاء السائق  بنجاح')->success();
            return redirect('admin/users/2');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,$type)
    {
        $countries = Country::all();
        if ($type == 2){
                //1 for restaurant
                $user = User::findOrfail($id);
                return view('admin.users.drivers.edit' ,compact('countries','user'));
            }elseif ($type == 1){
                $user = User::findOrfail($id);
                return view('admin.users.edit_user' ,compact('user' , 'countries'));
            }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id,$type)
    {
        if($type == 2){
            $this->validate($request, [
                'phone_number'          => 'required|unique:users,phone_number,'.$id,
                'name'                  => 'required|max:255',
                'country_id'            => 'required|exists:countries,id',
                'photo'                 => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
            ]);
            $user = User::findOrFail($id);
            $user->update([
                'phone_number'    => $request->phone_number,
                'name'            => $request->name,
                'country_id'      => $request->country_id,
                'photo'           => $request->file('photo') == null ? $user->photo : UploadImage($request->file('photo'), 'photo', '/uploads/users'),
                'type'            => 2,
            ]);
            flash('تم تعديل بيانات السائق')->success();
            return redirect('admin/users/2');
        }
        elseif ($type == 1){
            $this->validate($request, [
                'phone_number'     => 'required|unique:users,phone_number,'.$id,
                'email'            => 'nullable|unique:users,email,'.$id,
                'country_id'       => 'required|exists:countries,id',
                'name'             => 'required|max:255',
                'photo'            => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
            ]);

            $user = User::find($id);

            $user->update([
                'phone_number'    => $request->phone_number,
                'country_id'      => $request->country_id,
                'name'            => $request->name,
                'type'            => 1,
                'photo'           => $request->file('photo') == null ? $user->photo : UploadImage($request->file('photo'), 'photo', '/uploads/users'),
            ]);
            flash('تم تعديل بيانات  المستخدم بنجاح')->success();
            return redirect('admin/users/1');
        }

    }
    public function update_pass(Request $request, $id)
    {
        //
        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed',

        ]);
        $users = User::findOrfail($id);
        $users->password = Hash::make($request->password);

        $users->save();

        return redirect()->back()->with('information', 'تم تعديل كلمة المرور المستخدم');
    }
    public function update_privacy(Request $request, $id)
    {
        //
        $this->validate($request, [
            'active' => 'required',

        ]);
        $users = User::findOrfail($id);
        $users->active =$request->active;
        if ($request->active == "1")
        {
            $jsonObj = array(
                'mobile' => 'tqnee.com.sa',
                'password' => '589935sa',
                'sender'=>'TQNEE',
                'numbers' => $request->phone_number,
                'msg'=>'تم تفعيل حسابك من قبل الادارة',
                'msgId' => rand(1,99999),
                'timeSend' => '0',
                'dateSend' => '0',
                'deleteKey' => '55348',
                'lang' => '3',
                'applicationType' => 68,
            );
            // دالة الإرسال JOSN
            $result=$this->sendSMS($jsonObj);
        }
        $users->save();

        return redirect()->back()->with('information', 'تم تعديل اعدادات المستخدم');
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user->photo != null)
        {
            if (file_exists(public_path('uploads/users/' . $user->photo))) {
                unlink(public_path('uploads/users/' . $user->photo));
            }
        }
        if ($user->driving_licence != null)
        {
            if (file_exists(public_path('uploads/driving_licences/' . $user->driving_licence))) {
                unlink(public_path('uploads/driving_licences/' . $user->driving_licence));
            }
        }
        $user->delete();
        flash('تم الحذف بنجاح')->success();
        return back();
    }
    /**
     * @get  association users that wants to be in association
     * @association
    */
    public function association()
    {
        $associations = User::where('is_join' , '1')
            ->where('association' , '0')
            ->get();
        return view('admin.association.index', compact('associations'));
    }
    /**
     * @association_ok
    */
    public function association_ok($id)
    {
        $user = User::find($id);
        $user->update([
            'is_join'         =>'0',
            'association'         =>'1',
        ]);
        $devicesTokens =  UserDevice::where('user_id',$id)
            ->get()
            ->pluck('device_token')
            ->toArray();
        if ($devicesTokens) {
            sendMultiNotification("الجمعية", "تم بنجاح قبول طبلك للالتحاق بالجمعية" ,$devicesTokens);
        }
        saveNotification($id, "الجمعية" , '1', "تم بنجاح قبول طبلك للالتحاق بالجمعية", null , null);
        flash('تم  قبول المستخدم بنجاح ف الجمعية')->success();
        return redirect()->back();
    }
    public function delete_request($id)
    {
        $user = User::find($id);
        $user->update([
            'is_join'         =>'0',
        ]);
        flash('تم مسح الطلب بنجاح')->success();
        return redirect()->back();
    }
    public function active_user($id , $active)
    {
        $user = User::findOrFail($id);
        $user->update([
            'active'  => $active
        ]);
        flash('تم تغيير الخصوصية بنجاح')->success();
        return redirect()->back();
    }
}
