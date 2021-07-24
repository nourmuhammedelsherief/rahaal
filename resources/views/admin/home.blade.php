@extends('admin.layouts.master')

@section('title')
    لوحة التحكم
@endsection

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{url('/admin/home')}}"> لوحة التحكم</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>الإحصائيات</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">  الإحصائيات
        <small>عرض الإحصائيات</small>
    </h1>

    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-blue" href="{{ url('/admin/admins') }}">
                <div class="visual">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$admins}}</span>
                    </div>
                    <div class="desc"> عدد المديرين  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-red" href="{{ url('/admin/users/1') }}">
                <div class="visual">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$users}}</span>
                    </div>
                    <div class="desc"> عدد العملاء  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-green" href="{{ url('/admin/users/2') }}">
                <div class="visual">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$drivers}}</span>
                    </div>
                    <div class="desc"> عدد السائقين  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-yellow" href="{{ url('/admin/countries') }}">
                <div class="visual">
                    <i class="fa fa-flag"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$countries}}</span>
                    </div>
                    <div class="desc"> عدد الدول  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-brown" href="{{ url('/admin/trucks_types') }}">
                <div class="visual">
                    <i class="fa fa-truck"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$trucks_types}}</span>
                    </div>
                    <div class="desc"> انواع الشاحنات  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 widget-bg-color-gray" href="{{ url('/admin/vehicle_brands') }}">
                <div class="visual">
                    <i class="fa fa-car"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$vehicle_brands}}</span>
                    </div>
                    <div class="desc"> ماركة الشاحنات  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-blue" href="{{ url('/admin/vehicles') }}">
                <div class="visual">
                    <i class="fa fa-truck"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$vehicles}}</span>
                    </div>
                    <div class="desc">  الشاحنات  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 widget-bg-color-red" href="{{ url('/admin/banks') }}">
                <div class="visual">
                    <i class="fa fa-bank"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$banks}}</span>
                    </div>
                    <div class="desc">  البنوك  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-green" href="{{ url('/admin/orders/0') }}">
                <div class="visual">
                    <i class="fa fa-first-order"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$new}}</span>
                    </div>
                    <div class="desc">  الطلبات  الجديدة  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-blue" href="{{ url('/admin/orders/1') }}">
                <div class="visual">
                    <i class="fa fa-first-order"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$active}}</span>
                    </div>
                    <div class="desc">  الطلبات  النشطة  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-yellow" href="{{ url('/admin/orders/2') }}">
                <div class="visual">
                    <i class="fa fa-first-order"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$finished}}</span>
                    </div>
                    <div class="desc">  الطلبات  النتهية  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-red" href="{{ url('/admin/orders/3') }}">
                <div class="visual">
                    <i class="fa fa-first-order"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$canceled}}</span>
                    </div>
                    <div class="desc">  الطلبات  الملغية  </div>
                </div>
            </a>
        </div>

    </div>
@endsection
