<?php

namespace App\Http\Controllers\AdminController;

use App\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banks = Bank::orderBy('id' , 'desc')->get();
        return view('admin.banks.index' , compact('banks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.banks.create');
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
            'account_number' => 'required',
            'IBAN_number' => 'required'
        ]);
        Bank::create($request->all());
        flash('تم أضافه بيانات البنك بنجاح')->success();
        return redirect()->route('Bank');
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
        $bank = Bank::findOrFail($id);
        return view('admin.banks.edit' , compact('bank'));
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
        $bank = Bank::findOrFail($id);
        $this->validate($request , [
            'ar_name'   => 'required',
            'en_name'   => 'required',
            'ur_name'   => 'required',
            'account_number' => 'required',
            'IBAN_number' => 'required'
        ]);
        $bank->update($request->all());
        flash('تم تعديل بيانات البنك بنجاح')->success();
        return redirect()->route('Bank');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bank = Bank::findOrFail($id);
        $bank->delete();
        flash('تم حذف بيانات البنك بنجاح')->success();
        return redirect()->route('Bank');
    }
}
