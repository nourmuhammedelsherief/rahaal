@extends('admin.layouts.master')

@section('title')
    الدول
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ URL::asset('admin/css/bootstrap-fileinput.css') }}">
@endsection


@section('page_header')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{url('/admin/home')}}">لوحة التحكم</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{route('Country')}}">الدول </a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>عرض الدول</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">عرض الدول
        <small>اضافة جميع الدول  </small>
    </h1>
@endsection

@section('content')

    <div class="row">

        <div class="col-md-8">
            <!-- BEGIN TAB PORTLET-->
            <form method="post" action="{{route('storeCountry')}}" enctype="multipart/form-data" >
                <input type = 'hidden' name = '_token' value = '{{Session::token()}}'>
                <div class="portlet light bordered table-responsive">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-anchor font-green-sharp"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">إضافة دوله جديدة </span>
                        </div>

                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <!-- BEGIN SAMPLE FORM PORTLET-->
                            <div class="portlet light bordered table-responsive">
                                <div class="portlet-body form">
                                    <div class="form-horizontal" role="form">
                                        <div class="form-body">
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> أسم الدولة بالعربي</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="ar_name" class="form-control" placeholder="أسم الدولة بالعربي" value="{{old('ar_name')}}" required>
                                                    @if ($errors->has('ar_name'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('ar_name') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> أسم الدولة بالأنجليزي </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="en_name" class="form-control" placeholder="أسم الدولة بالأنجليزي " value="{{old('en_name')}}" required>
                                                    @if ($errors->has('en_name'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('en_name') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> أسم الدولة بالأوردو </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="ur_name" class="form-control" placeholder="أسم الدولة بالأوردو " value="{{old('ur_name')}}" required>
                                                    @if ($errors->has('ur_name'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('ur_name') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> كود الدولة </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="code" class="form-control" placeholder="كود الدولة" value="{{old('code')}}" required>
                                                    @if ($errors->has('code'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('code') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> عملة الدولة بالعربي </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="ar_currency" class="form-control" placeholder="عملة الدولة بالعربي" value="{{old('ar_currency')}}" required>
                                                    @if ($errors->has('ar_currency'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('ar_currency') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> عملة الدولة بالأنجليزي </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="en_currency" class="form-control" placeholder="عملة الدولة بالأنجليزي" value="{{old('en_currency')}}" required>
                                                    @if ($errors->has('en_currency'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('en_currency') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> عملة الدولة بالأردو </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="ur_currency" class="form-control" placeholder="عملة الدولة بالأردو" value="{{old('ur_currency')}}" required>
                                                    @if ($errors->has('ur_currency'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('ur_currency') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END SAMPLE FORM PORTLET-->


                        </div>


                        <!-- END CONTENT BODY -->

                        <!-- END CONTENT -->


                    </div>
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn green" value="حفظ" onclick="this.disabled=true;this.value='تم الارسال, انتظر...';this.form.submit();">حفظ</button>
                        </div>
                    </div>
                </div>
            </form>
            <!-- END TAB PORTLET-->





        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>

@endsection
