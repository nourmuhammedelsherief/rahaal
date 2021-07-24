<?php

namespace App\Http\Controllers\AdminController;

use App\TruckType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TruckTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trucks = TruckType::orderBy('id' , 'desc')->get();
        return view('admin.trucks.index' , compact('trucks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.trucks.create');
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
        // Store new Truck
        TruckType::create([
            'ar_name'   => $request->ar_name,
            'en_name'   => $request->en_name,
            'ur_name'   => $request->ur_name,
        ]);
        flash('تم أنشاء  نوع الشاحنة بنجاح')->success();
        return redirect()->route('TruckType');
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
        $truck  = TruckType::findOrFail($id);
        return view('admin.trucks.edit' , compact('truck'));
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
        $truck  = TruckType::findOrFail($id);
        $this->validate($request , [
            'ar_name'    => 'required',
            'en_name'    => 'required',
            'ur_name'    => 'required',
        ]);
        // update Truck
        $truck->update([
            'ar_name'   => $request->ar_name,
            'en_name'   => $request->en_name,
            'ur_name'   => $request->ur_name,
        ]);
        flash('تم تعديل نوع الشاحنة بنجاح')->success();
        return redirect()->route('TruckType');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $truck  = TruckType::findOrFail($id);
        $truck->delete();
        flash('تم حذف نوع الشاحنة بنجاح')->success();
        return redirect()->route('TruckType');
    }
}
