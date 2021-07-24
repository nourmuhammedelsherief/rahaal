@extends('admin.layouts.master')

@section('title')
    العمولة المستحقة للسائقين
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.bootstrap-rtl.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
@endsection

@section('page_header')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{url('/admin/home')}}">لوحة التحكم</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{route('Commission')}}">العمولة المستحقة للسائقين</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>عرض العمولة المستحقة للسائقين</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">عرض العمولة المستحقة للسائقين
        <small>عرض جميع العمولة المستحقة للسائقين</small>
    </h1>
@endsection

@section('content')
    @if (session('msg'))
        <div class="alert alert-danger">
            {{ session('msg') }}
        </div>
    @endif
    @include('flash::message')
    <div class="row">
        <div class="col-lg-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light bordered table-responsive">
                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-lg-6">
                                {{--                                <div class="btn-group">--}}
                                {{--                                    <a class="btn sbold green" href="/admin/add/user/1"> إضافة جديد--}}
                                {{--                                        <i class="fa fa-plus"></i>--}}
                                {{--                                    </a>--}}
                                {{--                                </div>--}}
                            </div>

                        </div>
                    </div>
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="sample_1">
                        <thead>
                        <tr>
                            <th>
                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                    <input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes" />
                                    <span></span>
                                </label>
                            </th>
                            <th></th>
                            <th> السائق </th>
                            <th> رقم الهاتف </th>
                            <th> العمولة </th>
                            <th> صوره التحويل </th>
                            <th> العمليات </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i=0 ?>
                        @foreach($commissions as $commission)
                            <tr class="odd gradeX">
                                <td>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" value="1" />
                                        <span></span>
                                    </label>
                                </td>
                                <td><?php echo ++$i ?></td>
                                <td> {{$commission->user->name}} </td>
                                <td> {{$commission->user->phone_number}} </td>
                                <td> {{-$commission->amount}} </td>
                                <td>
                                    @if($commission->commission_photo != null)
                                        <a href="#" class="pop">
                                            <img class="imageresource" src="{{asset('/uploads/transfer_photos/'.$commission->commission_photo)}}" height="50" width="50">
                                        </a>
                                    @endif
                                </td>

                                <td>
                                    <a class="btn btn-success" href="{{route('CommissionDone' , $commission->id)}}"> تم التأكد </a>
                                    {{--                                    <a class="btn btn-danger" href="{{route('chargeNotDone' , $commission->id)}}"> الغاء </a>--}}
                                    <a class="delete_user btn btn-danger" data="{{ $commission->id }}" data_name="" >
                                        <i class="fa fa-key"></i> الغاء
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>

    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel"> صورة التحويل البنكي للعميل لدفع العمولة </h4>
                </div>
                <div class="modal-body">
                    <img src="" id="imagepreview" style="width: 400px; height: 264px;" >
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">اغلاق</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ URL::asset('admin/js/datatable.js') }}"></script>
    <script src="{{ URL::asset('admin/js/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/datatables.bootstrap.js') }}"></script>
    <script src="{{ URL::asset('admin/js/table-datatables-managed.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/ui-sweetalert.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="X-CSRF-TOKEN"]').attr('content');

            $('body').on('click', '.delete_user', function() {
                var id = $(this).attr('data');

                var swal_text = 'حذف ' + $(this).attr('data_name') + '؟';
                var swal_title = 'هل أنت متأكد من الحذف ؟';

                swal({
                    title: swal_title,
                    text: swal_text,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-warning",
                    confirmButtonText: "تأكيد",
                    cancelButtonText: "إغلاق",
                    closeOnConfirm: false
                }, function() {

                    window.location.href = "{{ url('/') }}" + "/admin/commissions/CommissionNotDone/"+id;


                });

            });

        });
    </script>
    <script>
        $(".pop").on("click", function() {
            $('#imagepreview').attr('src', $('.imageresource').attr('src')); // here asign the image to the modal when the user click the enlarge link
            $('#imagemodal').modal('show'); // imagemodal is the id attribute assigned to the bootstrap modal, then i use the show function
        });
    </script>
@endsection
