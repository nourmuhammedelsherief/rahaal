@extends('admin.layouts.master')

@section('title')
    ألاشعارات العامة
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
                <a href="{{url('/admin/public_notifications')}}">ألاشعارات العامة</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>عرض ألاشعارات العامة</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">عرض ألاشعارات العامة
        <small>اضافة جميع ألاشعارات العامة</small>
    </h1>
    @include('flash::message')
@endsection

@section('content')

    <div class="row">

        <div class="col-md-8">
            <!-- BEGIN TAB PORTLET-->
            <form method="post" action="{{route('storePublicNotification')}}" enctype="multipart/form-data" >
                <input type = 'hidden' name = '_token' value = '{{Session::token()}}'>
                <div class="portlet light bordered table-responsive">
                    <div class="portlet-body">
                        <div class="row">
                            <!-- BEGIN SAMPLE FORM PORTLET-->
                            <div class="portlet light bordered table-responsive">
                                <div class="portlet-body form">
                                    <div class="form-horizontal" role="form">
                                        <div class="form-body">
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> اشعار الي </label>
                                                <div class="col-md-9">
                                                    <select name="type" class="form-control" required>
                                                        <option disabled selected> اختر نوع المستخدمين </option>
                                                        <option value="1"> العملاء </option>
                                                        <option value="2"> السائقين </option>
                                                    </select>
                                                    @if ($errors->has('ar_title'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('ar_title') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">العنوان بالعربي</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="ar_title" class="form-control" placeholder="أكتب عنوان الاشعار بالعربية" value="{{old('ar_title')}}">
                                                    @if ($errors->has('ar_title'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('ar_title') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">العنوان بالأنجليزي</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="en_title" class="form-control" placeholder="أكتب عنوان الاشعار بالأنجليزي" value="{{old('en_title')}}">
                                                    @if ($errors->has('en_title'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('en_title') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">العنوان بالأردية</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="ur_title" class="form-control" placeholder="أكتب عنوان الاشعار بالأردية" value="{{old('ur_title')}}">
                                                    @if ($errors->has('ur_title'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('ur_title') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">محتوي  الاشعار بالعربي</label>
                                                <div class="col-md-9">
                                                    <textarea  name="ar_message" class="form-control" placeholder="أكتب  محتوي  الاشعار بالعربي" ></textarea>
                                                    @if ($errors->has('ar_message'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('ar_message') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">محتوي  الاشعار بالأنجليزية</label>
                                                <div class="col-md-9">
                                                    <textarea  name="en_message" class="form-control" placeholder="أكتب  محتوي  الاشعار بالأنجليزية" ></textarea>
                                                    @if ($errors->has('en_message'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('en_message') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">محتوي  الاشعار بالأردية</label>
                                                <div class="col-md-9">
                                                    <textarea  name="ur_message" class="form-control" placeholder="أكتب  محتوي  الاشعار بالأردية" ></textarea>
                                                    @if ($errors->has('ur_message'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('ur_message') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-offset-3 col-md-9">
                                                    <button type="submit" class="btn green" value="حفظ" onclick="this.disabled=true;this.value='تم الارسال, انتظر...';this.form.submit();">حفظ</button>
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

            </form>
            <!-- END TAB PORTLET-->
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>

@endsection
