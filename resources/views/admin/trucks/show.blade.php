@extends('admin.layouts.master')

@section('title')
     الشاحنات
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
                <a href="{{route('vehicles')}}"> الشاحنات </a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>عرض  الشاحنات</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">عرض  الشاحنات</h1>
@endsection

@section('content')

    <div class="row">

        <div class="col-md-8">
            <!-- BEGIN TAB PORTLET-->
            <form method="post" action="#" enctype="multipart/form-data" >
                <input type = 'hidden' name = '_token' value = '{{Session::token()}}'>
                <div class="portlet light bordered table-responsive">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-anchor font-green-sharp"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">عرض  شاحنه  </span>
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
                                                <label class="col-md-3 control-label"> المستخدم </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="ar_name" class="form-control" disabled value="{{$truck->user->name}}" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> نوع الشاحنة </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="ar_name" class="form-control" disabled value="{{$truck->truck_type->ar_name}}" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> ماركة الشاحنة </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="ar_name" class="form-control" disabled value="{{$truck->vehicle_brand->ar_name}}" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">  موديل  التصنيع </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="ar_name" class="form-control" disabled value="{{$truck->model_year}}" required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> رقم  اللوحة </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="plate_number" class="form-control" disabled value="{{$truck->plate_number}}" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> اقصي حمولة بالطن </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="plate_number" class="form-control" disabled value="{{$truck->maximum_round}}" required>
                                                </div>
                                            </div>
                                            <div class="form-body">
                                                <div class="form-group ">
                                                    <label class="control-label col-md-3">صوره الهويه او الأقامة</label>
                                                    <div class="col-md-9">
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                                @if($truck->id_photo !==null)
                                                                    <a href="#" class="pop">
                                                                        <img class="imageresource" src='{{ asset("uploads/id_photos/$truck->id_photo") }}'>
                                                                        اضغط للتكبير
                                                                    </a>
                                                                @endif

                                                            </div>
                                                            <div>

{{--                                                            <span class="btn red btn-outline btn-file">--}}
{{--                                                                <span class="fileinput-new"> اختر الصورة </span>--}}
{{--                                                                <span class="fileinput-exists"> تغيير </span>--}}
{{--                                                                <input type="file" name="photo"> </span>--}}
{{--                                                                <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> إزالة </a>--}}



                                                            </div>
                                                        </div>
                                                        @if ($errors->has('photo'))
                                                            <span class="help-block">
                                                               <strong style="color: red;">{{ $errors->first('photo') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="form-body">
                                                <div class="form-group ">
                                                    <label class="control-label col-md-3">استمارة السيارة</label>
                                                    <div class="col-md-9">
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                                @if($truck->car_form !==null)
                                                                    <a href="#" class="pop">
                                                                        <img class="imageresource"  src='{{ asset("uploads/car_forms/$truck->car_form") }}'>
                                                                        اضغط للتكبير
                                                                    </a>
                                                                @endif
                                                            </div>
                                                            <div>
{{--                                                            <span class="btn red btn-outline btn-file">--}}
{{--                                                                <span class="fileinput-new"> اختر الصورة </span>--}}
{{--                                                                <span class="fileinput-exists"> تغيير </span>--}}
{{--                                                                <input type="file" name="photo"> </span>--}}
{{--                                                                <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> إزالة </a>--}}



                                                            </div>
                                                        </div>
                                                        @if ($errors->has('photo'))
                                                            <span class="help-block">
                                                               <strong style="color: red;">{{ $errors->first('photo') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="form-body">
                                                <div class="form-group ">
                                                    <label class="control-label col-md-3"> رخصة السائق </label>
                                                    <div class="col-md-9">
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                                @if($truck->driver_license !==null)
                                                                    <a href="#" class="pop">
                                                                        <img class="imageresource"  src='{{ asset("uploads/driver_licenses/$truck->driver_license") }}'>
                                                                        اضغط للتكبير
                                                                    </a>
                                                                @endif
                                                            </div>
                                                            <div>
{{--                                                            <span class="btn red btn-outline btn-file">--}}
{{--                                                                <span class="fileinput-new"> اختر الصورة </span>--}}
{{--                                                                <span class="fileinput-exists"> تغيير </span>--}}
{{--                                                                <input type="file" name="photo"> </span>--}}
{{--                                                                <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> إزالة </a>--}}



                                                            </div>
                                                        </div>
                                                        @if ($errors->has('photo'))
                                                            <span class="help-block">
                                                               <strong style="color: red;">{{ $errors->first('photo') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>

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
{{--                <div class="form-actions">--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-md-offset-3 col-md-9">--}}
{{--                            <button type="submit" class="btn green" value="حفظ" onclick="this.disabled=true;this.value='تم الارسال, انتظر...';this.form.submit();">حفظ</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </form>
            <!-- END TAB PORTLET-->
        </div>
    </div>





    <!-- Creates the bootstrap modal where the image will appear -->
    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel"> تكبير الصورة </h4>
                </div>
                <div class="modal-body">
                    <img src="" id="imagepreview" style="width: 400px; height: 264px;" >
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">أغلاق</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>
    <script>
        $(".pop").on("click", function() {
            $('#imagepreview').attr('src', $('.imageresource').attr('src')); // here asign the image to the modal when the user click the enlarge link
            $('#imagemodal').modal('show'); // imagemodal is the id attribute assigned to the bootstrap modal, then i use the show function
        });
    </script>
@endsection
