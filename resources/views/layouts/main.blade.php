<!DOCTYPE html>
<html class="loading {{ session('web_mode') }}" data-textdirection="ltr">
<!-- BEGIN: Head-->
@include('layouts.header')
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="">

    <div class="loader-spinner">
        <img src="{{ asset('images/load2.gif')}}" id="image-loader" >
    </div>


    <!-- BEGIN: Header-->
    @include('layouts.navbar')
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    @include('layouts.sidebar')
    <!-- END: Main Menu-->

    <style type="text/css">
        

        #notification-image-venue-container {
            background-repeat: no-repeat;
            background-position: center;
            position: relative;
            background-size: 100% auto;
            position: relative;
            overflow-x:hidden;
            overflow-y:hidden;
        }

    </style>

    <!-- BEGIN: Content-->
    <div class="app-content content @yield('app-content-class') ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper p-0">

            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">@yield('title', 'Page')</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" data-i18n="subtitle">
                                        @yield('page-desc')
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block">
                    @yield('button-right')
                </div>
            </div>




            <!--
            |--------------------------------------------------------------------------
            |                START MODAL NOTIFICATION ON TRIGGER EVENT
            |--------------------------------------------------------------------------

            -->





            <div class="modal-size-default d-inline-block">
                <div class="modal fade text-start modal-danger" id="ModalNotificationTriggerEvent" tabindex="-1" aria-labelledby="myModalLabel18" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">NOTIFICATION [ <span class="alert-id"></span> ]</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post" action="{{ route('admin.user.accept-notification') }}" id="modal-main-update-notification">
                                @csrf
                                <div class="modal-body this-body">
                                    
                                    <div class="row">
                                        <input type="hidden" id="input_notification_uid" name="notification_uid" value="">
                                        
                                        

                                        <div class="col-md-12 pb-1">
                                            
                                            <label><b>Event</b></label><br>
                                            <span class="data-event">-</span>
                                        </div>

                                        <div class="col-md-12 pb-1">
                                            
                                            <label><b>Date & Time</b></label><br>
                                            <span class="data-time">-</span>
                                        </div>
                                        

                                        <div class="col-md-12 pb-1">
                                            
                                            <label><b>Location</b></label><br>
                                            <span class="data-location">-</span>
                                        </div>

                                        <div class="col-md-12 pb-1">
                                            
                                            <label><b>Device [ controller ]</b></label><br>
                                            <span class="data-controller">-</span>
                                        </div>

                                        <div class="col-md-12 pb-1">
                                            
                                            <label><b>Device [ tracker ]</b></label><br>
                                            <span class="data-tracker">-</span>
                                        </div>

                                        <div class="col-md-12 pb-1">
                                            
                                            <label><b>Entity</b></label><br>
                                            <span class="data-entity">-</span>
                                        </div>
                                        
                                  
                                    </div>

                                    <div class="row match-height">

                                        <div class="col-md-12 col-12">

                                            <div class="card">
                                                <div class="card-body notification-zone-creating-container" style="padding: 0px !important;margin-right: auto !important;margin-left: auto !important;">
                                                    <div id="notification-image-venue-container"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary button-submit-notification">Acknowledge</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            
            <!-- 

            |--------------------------------------------------------------------------
            |                END MODAL NOTIFICATION ON TRIGGER EVENT
            |--------------------------------------------------------------------------

            -->

            

            @yield('content')
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    @include('layouts.footer')
    <!-- END: Footer-->

    @include('layouts.scripts')

    @yield('script')

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>
</body>
<!-- END: Body-->

</html>