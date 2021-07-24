<div class="page-sidebar-wrapper">
    <!-- BEGIN SIDEBAR -->
    <div class="page-sidebar navbar-collapse collapse">

        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
            <li class="sidebar-toggler-wrapper hide">
                <div class="sidebar-toggler">
                    <span></span>
                </div>
            </li>

            <li class="nav-item start active open" >
                <a href="/admin/home" class="nav-link nav-toggle">
                    <i class="icon-home"></i>
                    <span class="title">الرئيسية</span>
                    <span class="selected"></span>

                </a>
            </li>
            <li class="heading">
                <h3 class="uppercase">القائمة الجانبية</h3>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admins') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-users" style="color: aqua;"></i>
                    <span class="title">المشرفين</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{ url('/admin/admins') }}" class="nav-link ">
                            <span class="title">عرض المشرفين</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/admins/create') }}" class="nav-link ">
                            <span class="title">اضافة مشرف</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ strpos(URL::current(), 'admin/users/0') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-users" style="color: aqua;"></i>
                    <span class="title">المستخدمين</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{ url('/admin/users/1') }}" class="nav-link ">
                            <span class="title"> المستخدمين </span>
                            <?php $users = \App\User::where('type' , '1')->get()->count(); ?>
                            <span class="badge badge-success">{{$users}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/users/2') }}" class="nav-link ">
                            <span class="title"> السائقين </span>
                            <?php $drivers = \App\User::where('type' , '2')->get()->count(); ?>
                            <span class="badge badge-success">{{$drivers}}</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ strpos(URL::current(), 'admin/users/0') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-bell" style="color: aqua;"></i>
                    <span class="title">الأشعارات</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ strpos(URL::current(), 'admin/public_notifications') !== false ? 'active' : '' }}">
                        <a href="{{url('/admin/public_notifications')}}" class="nav-link ">
                            <i class="fa fa-bell-o" style="color: aqua;"></i>
                            <span class="title">ألاشعارات العامه</span>
                            <span class="pull-right-container"></span>

                        </a>
                    </li>
                    <li class="nav-item {{ strpos(URL::current(), 'admin/user_notifications') !== false ? 'active' : '' }}">
                        <a href="{{url('/admin/user_notifications')}}" class="nav-link ">
                            <i class="fa fa-bell-o" style="color: aqua;"></i>
                            <span class="title"> اشعارات لأشخاص محددين </span>
                            <span class="pull-right-container"></span>

                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admin/countries') !== false ? 'active' : '' }}">
                <a href="{{url('/admin/countries')}}" class="nav-link ">
                    <i class="icon-flag" style="color: aqua;"></i>
                    <span class="title"> الدول </span>
                    <span class="pull-right-container"></span>
                    <?php $countries = \App\Country::count(); ?>
                    <span class="badge badge-success">{{$countries}}</span>
                </a>
            </li>
            <li class="nav-item {{ strpos(URL::current(), 'admin/trucks_types') !== false ? 'active' : '' }}">
                <a href="{{url('/admin/trucks_types')}}" class="nav-link ">
                    <i class="fa fa-truck" style="color: aqua;"></i>
                    <span class="title"> انوأع الشاحنات </span>
                    <span class="pull-right-container"></span>
                    <?php $truck_types = \App\TruckType::count(); ?>
                    <span class="badge badge-success">{{$truck_types}}</span>
                </a>
            </li>
            <li class="nav-item {{ strpos(URL::current(), 'admin/vehicle_brands') !== false ? 'active' : '' }}">
                <a href="{{url('/admin/vehicle_brands')}}" class="nav-link ">
                    <i class="fa fa-car" style="color: aqua;"></i>
                    <span class="title"> ماركة الشاحنات </span>
                    <span class="pull-right-container"></span>
                    <?php $brands = \App\VehicleBrand::count(); ?>
                    <span class="badge badge-success">{{$brands}}</span>
                </a>
            </li>
            <li class="nav-item {{ strpos(URL::current(), 'admin/vehicles') !== false ? 'active' : '' }}">
                <a href="{{url('/admin/vehicles')}}" class="nav-link ">
                    <i class="fa fa-truck" style="color: aqua;"></i>
                    <span class="title">  الشاحنات </span>
                    <span class="pull-right-container"></span>
                    <?php $trucks = \App\Truck::count(); ?>
                    <span class="badge badge-success">{{$trucks}}</span>
                </a>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admin/orders/0') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-first-order" style="color: aqua;"></i>
                    <span class="title">الطلبات</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{ url('/admin/orders/0') }}" class="nav-link ">
                            <span class="title"> الجديدة </span>
                            <?php $new = \App\Order::where('status' , '0')->get()->count(); ?>
                            <span class="badge badge-success">{{$new}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/orders/1') }}" class="nav-link ">
                            <span class="title"> النشطة </span>
                            <?php $active = \App\Order::where('status' , '1')->get()->count(); ?>
                            <span class="badge badge-success">{{$active}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/orders/2') }}" class="nav-link ">
                            <span class="title"> المنتهية </span>
                            <?php $finished = \App\Order::where('status' , '2')->get()->count(); ?>
                            <span class="badge badge-success">{{$finished}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/orders/3') }}" class="nav-link ">
                            <span class="title"> الملغية </span>
                            <?php $canceled = \App\Order::where('status' , '3')->get()->count(); ?>
                            <span class="badge badge-success">{{$canceled}}</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admin/banks') !== false ? 'active' : '' }}">
                <a href="{{url('/admin/banks')}}" class="nav-link ">
                    <i class="fa fa-bank" style="color: aqua;"></i>
                    <span class="title">  البنوك </span>
                    <span class="pull-right-container"></span>
                    <?php $banks = \App\Bank::count(); ?>
                    <span class="badge badge-success">{{$banks}}</span>
                </a>
            </li>


            <li class="nav-item {{ strpos(URL::current(), 'admin/wallet_charging') !== false ? 'active' : '' }}">
                <a href="{{url('/admin/wallet_charging')}}" class="nav-link ">
                    <i class="fa fa-google-wallet" style="color: aqua;"></i>
                    <span class="title"> شحنات المحفظه الألكترونية </span>
                    <span class="pull-right-container"></span>
                    <?php
                    $wallet_charging = \App\Electronic_wallet::where('payment_photo' , '!=' , null)
                        ->where('checked_amount' , '!=' , 0)
                        ->count();
                    ?>
                    <span class="badge badge-success">{{$wallet_charging}}</span>
                </a>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admin/commissions') !== false ? 'active' : '' }}">
                <a href="{{url('/admin/commissions')}}" class="nav-link ">
                    <i class="fa fa-percent" style="color: aqua;"></i>
                    <span class="title"> العمولة المستحقة للسائقين </span>
                    <span class="pull-right-container"></span>
                    <?php
                    $wallet_charging = \App\Electronic_wallet::where('commission_photo' , '!=' , null)
                        ->where('checked_amount' , '!=' , 0)
                        ->count();
                    ?>
                    <span class="badge badge-success">{{$wallet_charging}}</span>
                </a>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admin/pulls') !== false ? 'active' : '' }}">
                <a href="{{url('/admin/pulls')}}" class="nav-link ">
                    <i class="fa fa-money" style="color: aqua;"></i>
                    <span class="title"> طلبات سحب  الرصيد </span>
                    <span class="pull-right-container"></span>
                    <?php $balance_requests = \App\Electronic_wallet::where('pull_request' , '1')->get()->count(); ?>
                    <span class="badge badge-success">{{$balance_requests}}</span>
                </a>
            </li>
            <li class="nav-item {{ strpos(URL::current(), 'admin/settings') !== false ? 'active' : '' }}">
                <a href="{{route('settings')}}" class="nav-link ">
                    <i class="fa fa-cogs" style="color: aqua;"></i>
                    <span class="title"> الأعدادات </span>
                    <span class="pull-right-container">
            </span>

                </a>
            </li>


            <li class="nav-item {{ strpos(URL::current(), 'admin/pages') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cog" style="color: aqua;"></i>
                    <span class="title">الصفحات</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item  ">
                        <a href="{{url('/admin/pages/about')}}" class="nav-link ">
                            <span class="title">من نحن</span>
                        </a>
                    </li>
                    <li class="nav-item  ">
                        <a href="{{url('/admin/pages/terms')}}" class="nav-link ">
                            <span class="title">الشروط والاحكام</span>
                        </a>
                    </li>




                </ul>
            </li>



        </ul>
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
