@extends('admin.layouts.master')

@section('title')
    الطلبات النشطة
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.bootstrap-rtl.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
    <style>
        #map {
            height: 300px;
            width: 500px;
        }
    </style>
@endsection

@section('page_header')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{url('/admin/home')}}">لوحة التحكم</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{route('orders' , '1')}}">الطلبات النشطة </a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>عرض الطلبات النشطة</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">عرض الطلبات النشطة
        <small>عرض جميع الطلبات النشطة  </small>
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
                        {{--                        <div class="row">--}}
                        {{--                            <div class="col-lg-6">--}}
                        {{--                                <div class="btn-group">--}}
                        {{--                                    <a class="btn sbold green" href="{{route('createTruckType')}}"> إضافة جديد--}}
                        {{--                                        <i class="fa fa-plus"></i>--}}
                        {{--                                    </a>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
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
                            <th> المستخدم </th>
                            <th>  الشاحنة </th>
                            <th>  السائق </th>
                            <th>  سعر التوصيل </th>
{{--                            <th> نوع الطلب </th>--}}
                            {{--                            <th> العمليات </th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i=0 ?>
                        @foreach($orders as $order)
                            <tr class="odd gradeX">
                                <td>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" value="1" />
                                        <span></span>
                                    </label>
                                </td>
                                <td><?php echo ++$i ?></td>
                                <td> {{$order->user->name}} </td>
                                <td> {{$order->truck_type->ar_name}} </td>
                                <td> {{$order->driver->name}} </td>
                                <td> {{$order->delivery_price}} </td>
                                {{--                                <td>--}}
                                {{--                                    <a href="#" class="pop btn btn-success imageresource">--}}
                                {{--                                        عرض نقطة البداية--}}

                                {{--                                        <script>--}}
                                {{--                                            function getLocation()--}}
                                {{--                                            {--}}
                                {{--                                                if (navigator.geolocation)--}}
                                {{--                                                {--}}
                                {{--                                                    navigator.geolocation.getCurrentPosition(showPosition);--}}
                                {{--                                                }--}}
                                {{--                                                else{x.innerHTML="Geolocation is not supported by this browser.";}--}}
                                {{--                                            }--}}

                                {{--                                            function showPosition(position)--}}
                                {{--                                            {--}}
                                {{--                                                lat= position.coords.latitude;--}}
                                {{--                                                lon= position.coords.longitude;--}}

                                {{--                                                document.getElementById('lat').value = lat; //latitude--}}
                                {{--                                                document.getElementById('lng').value = lon; //longitude--}}
                                {{--                                                latlon=new google.maps.LatLng(lat, lon)--}}
                                {{--                                                mapholder=document.getElementById('mapholder')--}}
                                {{--                                                //mapholder.style.height='250px';--}}
                                {{--                                                //mapholder.style.width='100%';--}}

                                {{--                                                var myOptions={--}}
                                {{--                                                    center:latlon,zoom:14,--}}
                                {{--                                                    mapTypeId:google.maps.MapTypeId.ROADMAP,--}}
                                {{--                                                    mapTypeControl:false,--}}
                                {{--                                                    navigationControlOptions:{style:google.maps.NavigationControlStyle.SMALL}--}}
                                {{--                                                };--}}
                                {{--                                                var map = new google.maps.Map(document.getElementById("map"),myOptions);--}}
                                {{--                                                var marker=new google.maps.Marker({position:latlon,map:map,title:"You are here!"});--}}
                                {{--                                            }--}}

                                {{--                                        </script>--}}
                                {{--                                        <script type="text/javascript">--}}
                                {{--                                            var map;--}}

                                {{--                                            function initMap() {--}}


                                {{--                                                var latitude = {{$order->latitude_from}}; // YOUR LATITUDE VALUE--}}
                                {{--                                                var longitude = {{$order->longitude_from}};  // YOUR LONGITUDE VALUE--}}

                                {{--                                                console.log(latitude);--}}
                                {{--                                                console.log(longitude);--}}
                                {{--                                                var myLatLng = {lat: latitude, lng: longitude};--}}

                                {{--                                                map = new google.maps.Map(document.getElementById('map'), {--}}
                                {{--                                                    center: myLatLng,--}}
                                {{--                                                    zoom: 10,--}}
                                {{--                                                    gestureHandling: 'true',--}}
                                {{--                                                    zoomControl: false// disable the default map zoom on double click--}}
                                {{--                                                });--}}




                                {{--                                                var marker = new google.maps.Marker({--}}
                                {{--                                                    position: myLatLng,--}}
                                {{--                                                    map: map,--}}
                                {{--                                                    //title: 'Hello World'--}}

                                {{--                                                    // setting latitude & longitude as title of the marker--}}
                                {{--                                                    // title is shown when you hover over the marker--}}
                                {{--                                                    title: latitude + ', ' + longitude--}}
                                {{--                                                });--}}


                                {{--                                                //Listen for any clicks on the map.--}}
                                {{--                                                google.maps.event.addListener(map, 'click', function(event) {--}}
                                {{--                                                    //Get the location that the user clicked.--}}
                                {{--                                                    var clickedLocation = event.latLng;--}}
                                {{--                                                    //If the marker hasn't been added.--}}
                                {{--                                                    if(marker === false){--}}
                                {{--                                                        //Create the marker.--}}
                                {{--                                                        marker = new google.maps.Marker({--}}
                                {{--                                                            position: clickedLocation,--}}
                                {{--                                                            map: map,--}}
                                {{--                                                            draggable: true //make it draggable--}}
                                {{--                                                        });--}}
                                {{--                                                        //Listen for drag events!--}}
                                {{--                                                        google.maps.event.addListener(marker, 'dragend', function(event){--}}
                                {{--                                                            markerLocation();--}}
                                {{--                                                        });--}}
                                {{--                                                    } else{--}}
                                {{--                                                        //Marker has already been added, so just change its location.--}}
                                {{--                                                        marker.setPosition(clickedLocation);--}}
                                {{--                                                    }--}}
                                {{--                                                    //Get the marker's location.--}}
                                {{--                                                    markerLocation();--}}
                                {{--                                                });--}}



                                {{--                                                function markerLocation(){--}}
                                {{--                                                    //Get location.--}}
                                {{--                                                    var currentLocation = marker.getPosition();--}}
                                {{--                                                    //Add lat and lng values to a field that we can save.--}}
                                {{--                                                    document.getElementById('lat').value = currentLocation.lat(); //latitude--}}
                                {{--                                                    document.getElementById('lng').value = currentLocation.lng(); //longitude--}}
                                {{--                                                }--}}
                                {{--                                            }--}}
                                {{--                                        </script>--}}
                                {{--                                        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFUMq5htfgLMNYvN4cuHvfGmhe8AwBeKU&callback=initMap" async defer></script>--}}
                                {{--                                    </a>--}}
                                {{--                                </td>--}}
                                {{--                                <td>--}}
                                {{--                                    <div class="btn-group">--}}
                                {{--                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> العمليات--}}
                                {{--                                            <i class="fa fa-angle-down"></i>--}}
                                {{--                                        </button>--}}
                                {{--                                        <ul class="dropdown-menu pull-left" role="menu">--}}
                                {{--                                            --}}{{--<li>--}}
                                {{--                                            --}}{{--<a href="">--}}
                                {{--                                            --}}{{--<i class="icon-eye"></i> عرض--}}
                                {{--                                            --}}{{--</a>--}}
                                {{--                                            --}}{{--</li>--}}
                                {{--                                            <li>--}}
                                {{--                                                <a href="{{route('editTruckType' , $order->id)}}">--}}
                                {{--                                                    <i class="icon-docs"></i> تعديل </a>--}}
                                {{--                                            </li>--}}
                                {{--                                            --}}{{--                                            @if( auth()->user()->id != $value->id )--}}
                                {{--                                            <li>--}}
                                {{--                                                <a class="delete_user" data="{{ $order->id }}" data_name="{{ $order->ar_name }}" >--}}
                                {{--                                                    <i class="fa fa-key"></i> مسح--}}
                                {{--                                                </a>--}}
                                {{--                                            </li>--}}

                                {{--                                            --}}{{--@endif--}}
                                {{--                                        </ul>--}}
                                {{--                                    </div>--}}
                                {{--                                </td>--}}
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
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">اغلاق</span></button>
                    <h4 class="modal-title" id="myModalLabel"> عرض المكان </h4>
                </div>
                <div class="modal-body">
                    <div id="map" class="imageresource"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">أغلاق</button>
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

                    window.location.href = "{{ url('/') }}" + "/admin/trucks_types/delete/"+id;


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
