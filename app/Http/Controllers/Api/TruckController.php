<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Brand;
use App\Truck;
use App\TruckType;
use App\VehicleBrand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class TruckController extends Controller
{
    public function get_trucks_types()
    {
        $trucks = TruckType::all();
        if ($trucks->count() > 0)
        {
            return ApiController::respondWithSuccess(\App\Http\Resources\TruckType::collection($trucks));
        }else{
            $errors = [
                'key'   => 'get_trucks_types',
                'value' => trans('messages.noTrucks')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    public function get_vehicle_brands()
    {
        $brands = VehicleBrand::all();
        if ($brands->count() > 0)
        {
            return ApiController::respondWithSuccess(Brand::collection($brands));
        }else{
            $errors = [
                'key'   => 'get_vehicle_brands',
                'value' => trans('messages.noVehicleBrands')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    public function create_truck(Request $request)
    {
        $rules = [
            'truck_type_id'    => 'required|exists:truck_types,id',
            'vehicle_brand_id'    => 'required|exists:vehicle_brands,id',
            'model_year'   => 'required',
            'plate_number'   => 'required',
            'maximum_round'   => 'required',
            'id_photo'   => 'required|mimes:jpeg,bmp,png,jpg|max:5000',
            'car_form'   => 'required|mimes:jpeg,bmp,png,jpg|max:5000',
            'driver_license'   => 'required|mimes:jpeg,bmp,png,jpg|max:5000',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));
        // create new truck
        $truck = Truck::create([
            'user_id'        => $request->user()->id,
            'status'         => '0',
            'truck_type_id' => $request->truck_type_id,
            'vehicle_brand_id' => $request->vehicle_brand_id,
            'model_year' => $request->model_year,
            'plate_number' => $request->plate_number,
            'maximum_round' => $request->maximum_round,
            'id_photo' => $request->file('id_photo') == null ? null :UploadImage($request->file('id_photo'), 'id_photo', '/uploads/id_photos'),
            'car_form' => $request->file('car_form') == null ? null :UploadImage($request->file('car_form'), 'car_form', '/uploads/car_forms'),
            'driver_license' => $request->file('driver_license') == null ? null :UploadImage($request->file('driver_license'), 'driver_license', '/uploads/driver_licenses'),
        ]);
        return ApiController::respondWithSuccess(new \App\Http\Resources\Truck($truck));
    }
    public function edit_truck(Request $request , $id)
    {
        $truck = Truck::find($id);
        if ($truck != null && $truck->user_id == $request->user()->id)
        {
            $rules = [
                'truck_type_id'    => 'nullable|exists:truck_types,id',
                'vehicle_brand_id'    => 'nullable|exists:vehicle_brands,id',
                'model_year'   => 'nullable',
                'plate_number'   => 'nullable',
                'maximum_round'   => 'nullable',
                'id_photo'   => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
                'car_form'   => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
                'driver_license'   => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));
            // edit truck
            $truck->update([
                'user_id'        => $request->user()->id,
                'truck_type_id' => $request->truck_type_id == null ? $truck->truck_type_id : $request->truck_type_id,
                'vehicle_brand_id' => $request->vehicle_brand_id == null ? $truck->vehicle_brand_id : $request->vehicle_brand_id,
                'model_year' => $request->model_year == null ? $truck->model_year :$request->model_year,
                'plate_number' => $request->plate_number == null ? $truck->plate_number : $request->plate_number,
                'maximum_round' => $request->maximum_round == null ? $truck->maximum_round : $request->maximum_round,
                'id_photo' => $request->file('id_photo') == null ? $truck->id_photo :UploadImageEdit($request->file('id_photo'), 'id_photo', '/uploads/id_photos' , $truck->id_photo),
                'car_form' => $request->file('car_form') == null ? $truck->car_form :UploadImageEdit($request->file('car_form'), 'car_form', '/uploads/car_forms' ,$truck->car_form),
                'driver_license' => $request->file('driver_license') == null ? $truck->driver_license :UploadImageEdit($request->file('driver_license'), 'driver_license', '/uploads/driver_licenses' , $truck->driver_license),
            ]);
            return ApiController::respondWithSuccess(new \App\Http\Resources\Truck($truck));
        }else{
            $errors = [
                'key'   => 'edit_truck',
                'value' => trans('messages.truckNotFound')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    public function delete_truck(Request $request , $id)
    {
        $truck = Truck::find($id);
        if ($truck != null && $truck->user_id == $request->user()->id)
        {
            @unlink(public_path('/uploads/id_photos/'.$truck->id_photo));
            @unlink(public_path('/uploads/car_forms/'.$truck->car_form));
            @unlink(public_path('/uploads/driver_licenses/'.$truck->driver_license));
            $truck->delete();
            $success = [
                'key'    => 'delete_truck',
                'value'  => trans('messages.truckDeleted')
            ];
            return ApiController::respondWithSuccess($success);
        }else{
            $errors = [
                'key'   => 'edit_truck',
                'value' => trans('messages.truckNotFound')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    public function my_truck(Request $request)
    {
        $trucks = Truck::whereUserId($request->user()->id)
            ->where('status' , '1')
            ->get();
        if ($trucks->count() > 0)
        {
            return ApiController::respondWithSuccess(\App\Http\Resources\Truck::collection($trucks));
        }else{
            $errors = [
                'key'   => 'my_truck',
                'value' => trans('messages.onTrucks')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
}
