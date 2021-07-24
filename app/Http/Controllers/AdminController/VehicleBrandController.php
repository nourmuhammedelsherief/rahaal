<?php

namespace App\Http\Controllers\AdminController;

use App\Truck;
use App\VehicleBrand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VehicleBrandController extends Controller
{
    // عرض  الشاحنه في لوحه  التحكم وحذفها وتعديل بيانات  الشاحنه  في apis
    public function vehicles()
    {
        $vehicles = Truck::orderBy('id' , 'desc')->get();
        return view('admin.brands.vehicles' , compact('vehicles'));
    }
    public function show_truck($id)
    {
        $truck = Truck::findOrFail($id);
        return view('admin.trucks.show' , compact('truck'));
    }
    public function delete_truck($id)
    {
        $vehcile = Truck::findOrFail($id);
        @unlink(public_path('/uploads/id_photos/'.$vehcile->id_photo));
        @unlink(public_path('/uploads/car_forms/'.$vehcile->car_form));
        @unlink(public_path('/uploads/driver_licenses/'.$vehcile->driver_license));
        $vehcile->delete();
        flash('تم مسح بيانات  الشاحنة بنجاح')->success();
        return redirect()->back();
    }
    public function active_truck($id , $status)
    {
        $truck = Truck::findOrFail($id);
        $truck->update([
            'status'  => $status
        ]);
        flash('تم تغييرالتفعيل بنجاح')->success();
        return redirect()->back();
    }    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = VehicleBrand::orderBy('id' , 'desc')->get();
        return view('admin.brands.index' , compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request , [
            'ar_name'    => 'required',
            'en_name'    => 'required',
            'ur_name'    => 'required',
        ]);
        // Store new Vehicle Brand
        VehicleBrand::create([
            'ar_name'   => $request->ar_name,
            'en_name'   => $request->en_name,
            'ur_name'   => $request->ur_name,
        ]);
        flash('تم أنشاء ماركة الشاحنة بنجاح')->success();
        return redirect()->route('VehicleBrand');
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
    public function edit($id)
    {
        $brand = VehicleBrand::findOrFail($id);
        return view('admin.brands.edit' , compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $brand = VehicleBrand::findOrFail($id);
        $this->validate($request , [
            'ar_name'    => 'required',
            'en_name'    => 'required',
            'ur_name'    => 'required',
        ]);
        // update Vehicle Brand
        $brand->update([
            'ar_name'   => $request->ar_name,
            'en_name'   => $request->en_name,
            'ur_name'   => $request->ur_name,
        ]);
        flash('تم تعديل ماركة الشاحنة بنجاح')->success();
        return redirect()->route('VehicleBrand');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $brand = VehicleBrand::findOrFail($id);
        $brand->delete();
        flash('تم مسح ماركة الشاحنة بنجاح')->success();
        return redirect()->route('VehicleBrand');
    }
}
