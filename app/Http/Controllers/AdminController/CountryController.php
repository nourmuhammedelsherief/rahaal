<?php

namespace App\Http\Controllers\AdminController;

use App\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Country::orderBy('id' , 'desc')->get();
        return view('admin.countries.index' , compact('countries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.countries.create');
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
            'ar_name'   => 'required',
            'en_name'   => 'required',
            'ur_name'   => 'required',
            'code'      => 'required',
            'ar_currency'      => 'required',
            'en_currency'      => 'required',
            'ur_currency'      => 'required',
        ]);
        // create new country
        Country::create($request->all());
        flash('تم أنشاء  الدولة بنجاح')->success();
        return redirect()->route('Country');
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
        $country = Country::findOrFail($id);
        return view('admin.countries.edit' , compact('country'));
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
        $this->validate($request , [
            'ar_name'   => 'required',
            'en_name'   => 'required',
            'ur_name'   => 'required',
            'code'      => 'required',
        ]);
        // update country
        $country = Country::findOrFail($id);
        $country->update($request->all());
        flash('تم تعديل  الدولة بنجاح')->success();
        return redirect()->route('Country');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $country = Country::findOrFail($id);
        if ($country->users->count() > 0)
        {
            flash('لا يمكنك مسح هذه  الدولة  لأنها مستخدمة')->error();
            return redirect()->route('Country');
        }else{
            $country->delete();
            flash('تم مسح  الدولة بنجاح')->success();
            return redirect()->route('Country');
        }
    }
}
