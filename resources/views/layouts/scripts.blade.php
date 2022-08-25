
    <!-- BEGIN: Vendor JS-->
    <script src="{{ url('templates/vuexy/app-assets/vendors/js/vendors.min.js') }}"></script>
    <!-- BEGIN Vendor JS-->
    
    @yield('vendor-js')

    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ url('templates/vuexy/app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="{{ url('templates/vuexy/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ url('templates/vuexy/app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ url('templates/vuexy/app-assets/js/core/app.js') }}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
        
    <script src="{{ url('templates/vuexy/assets/js/jquery.counterup.min.js') }}"></script>
    <script src="{{ url('templates/vuexy/assets/js/jquery.waypoints.min.js') }}"></script>


    <!-- END: Page JS-->

    <script src="{{ asset('plugins/sweetalert/sweetalert.min.js') }}"></script>

    <!-- DROPIFY-->
    <script src="{{ url('plugins/dropify/dist/js/dropify.min.js') }}" type="text/javascript"></script>

    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
    
    <!-- TO FIX COUNTER UP ERROR WHEN SCROLLING -->
    <script src="https://unpkg.com/jquery.counterup@2.1.0/jquery.counterup.js"></script>

<script src="{{ url('templates/vuexy/app-assets/vendors/js/konva/konva.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/spectrum-colorpicker2/dist/spectrum.min.js"></script>

<script src="{{ url('templates/vuexy/app-assets/vendors/js/drawarea/drawarea.js') }}"></script>
    <script>

    var size_tag     = "40px";
    var color_tag    = "black";

    var size_badge = "11px";

    var size_ap     = "30px";
    var color_ap    = "black";
    var color_ap_current    = "black";
    var color_ap_normal     = "#4f4d4d";
    var strokeWidth = 2;

    var show_notification = true;

    resizeIcon();
    function resizeIcon() {

        var width = $(window).width(); 
        var height = $(window).height(); 

        if (width >= 640  ){
            size_ap  = "30px";
            size_tag  = "40px";
            size_badge = "11px";

        }
        else {
            size_ap   = "15px";
            size_tag  = "20px";
            size_badge = "5px";

        }

    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

            
    function storeWebModePhp(cur_layout) {


        $.ajax({
            url:"{{ route('admin.set-web-mode') }}",
            type:"POST",
            data:{'cur_layout' : cur_layout },
            success:function(response) {
                
                return;   

            }

        });

    }


        $(function(){

            $('.dropify').dropify();

            /*
            |--------------------------------------------------------------------------
            | FUNCTION TO FIX LOADER
            |--------------------------------------------------------------------------
            |
            | message == message to shown
            | type ==  success || danger || warning || info
            | reload == reload page if needed || default is false
            |
            */
            (function ($) {
                $.each(['show', 'hide'], function (i, ev) {
                    var el = $.fn[ev];
                    $.fn[ev] = function () {
                        this.trigger(ev);
                        return el.apply(this, arguments);
                    };
                });
            })(jQuery);

            $('.loader-spinner').on('show', function() {
                $('.loader-spinner').css('height', $(".app-content").outerHeight())
            });


            // END FUNCTION 

            $.when($('.loader-spinner').fadeOut()).done(function() {
                $(this).hide();

                @include('layouts.notify')

                $(".select2").each(function () {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>');
                    $this.select2({
                        // the following code is used to disable x-scrollbar when click in select input and
                        // take 100% width in responsive also
                        dropdownAutoWidth: true,
                        width: '100%',
                        dropdownParent: $this.parent()
                    });
                });

            });

            /*
            |--------------------------------------------------------------------------
            | CUSTOM SWEET ALERT DELETE MESSAGE
            |--------------------------------------------------------------------------
            |
            */
            window.close_swal = function close_swal() {
                swal.close();
            };


            $('body').on('click', '.ajaxDeleteButton', function(e){
                e.preventDefault();

                var msg = "This data will not be re-usable!";

                if ($(this).hasClass('ajaxDeleteCustom')){
                    var msg = "There have a user was make a reservation for this event!";
                }

                var url = $(this).attr('href');
                swal({
                    title: "Are You Sure?",
                    text: msg,
                    type: "info",
                    showCancelButton: true,
                    confirmButtonClass: "btn btn-success btn-fill",
                    confirmButtonText: "Yes, delete!",
                    cancelButtonText: "No, cancel!",
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                },
                function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: url,
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            type: 'DELETE',
                            dataType: 'json',
                            success: function (response) {

                                var success_delete_message = {!! json_encode(trans('alert.success-delete')) !!}
                                var error_delete_message = {!! json_encode(trans('alert.error-delete')) !!}
                               
                                if(response.status=='ok'){
                                    displayMessage(success_delete_message, 'success', true)
                                }else{
                                    displayMessage(response.message, 'danger')
                                    // displayMessage(error_delete_message, 'danger')
                                    close_swal();
                                }
                            },
                            error: function(xhr, status, error) {

                                setTimeout(function() {
                                    location.reload(true);
                                }, 1000);

                            }
                        });
                    }
                });
            });


            $("#btn-clear-cache").one('click', function() {


                $.ajax({
                    url:"{{ route('admin.clear-cache') }}",
                    type:"POST",
                    success: function (response) {
                
                        if(response.status=='ok'){

                            displayMessage("Cache has been cleared.", 'success', true, 500)
                            
                        }else{

                            displayMessage('Cache cannot clear. Please contact administrator.', 'danger')
                        }

                    },
                    error: function(xhr, status, error) {

                        displayMessage('Cache cannot clear. Please contact administrator.', 'danger' , true)

                    }

                });
            });

            $(".filter-tenant").on("keyup", function () {


                let searchText = $(this).val();


                $(".change-tenant").each(function () {


                    let currentElement = $(this);
                    
                    // if (currentElement.data("tenant").indexOf(searchText) > -1) currentElement.css("display", "block");
                    if (new RegExp(`${searchText}`,'i').test(currentElement.data("tenant"))) currentElement.css("display", "block");
                    else currentElement.attr("style", "display: none !important;");


                });


            });


            $(".change-tenant").one("click", function () {


                let current_tenant = {!! json_encode(session('tenant_id')) !!}
                let tenant = $(this).data("tenant");

                if(current_tenant == tenant) {

                    displayMessage("Please select other tenant", 'warning', false)

                }
                else if (tenant.length > 2 && current_tenant != tenant) {


                    $.ajax({
                        url:"{{ route('admin.web.change-tenant') }}",
                        type:"POST",
                        data: {
                            tenant_id: tenant
                        },
                        success: function (response) {
                    
                            if (response['status'] === "success") {

                                displayMessage("Successfully Change Tenant to <b>" + tenant + "</b>", 'success', true)
                                
                            }else{

                                displayMessage(response['message'], 'danger')
                            }

                        },
                        error: function(xhr, status, error) {

                            displayMessage('Erro to change tenant. Please contact administrator.', 'danger' , true)

                        }

                    });



                }


            });


            // Run notification only at workspace
            @if(session('omaya_type') != 'crowd')

                webNotification();

                setInterval(function(){ 
                    //this code runs every second 
                    webNotification();
                }, 5000);

            @endif

            
        });

        
        function webNotification() {


            let current_width,
            current_height,
            ori_width,
            ori_height,
            current_venue,
            selected_venue;

            if(show_notification == true) {

                $.ajax({
                    url:"{{ route('admin.user.check-notification') }}",
                    type:"POST",
                    data:{},
                    success:function(response) {

                        
                        if(response.status == 'success'){

                            
                            show_notification = false;

                            $('#ModalNotificationTriggerEvent').modal('show');


                            var data = response.data.notification;

                            $('.alert-id').html(data.notification_uid);
                            $('.data-time').html(data.trigger_at);
                            $('.data-event').html(data.rule.name);
                            $('.data-location').html(data.location.name + ' -> ' + data.venue.name + ' -> ' + data.zone.name );
                            $('.data-controller').html(data.controller.name  + ' [ ' + data.controller.mac_address + ' ]');
                            $('.data-tracker').html(data.tracker.name  + ' [ ' + data.tracker.mac_address + ' ]');
                            $('.data-entity').html((data.entity ? (data.entity.name   + ' [ ' + data.entity.type + ' ]') : ''));


                            $("#input_notification_uid").val(data.notification_uid);

                            ori_width       = data.venue.image_width;
                            ori_height      = data.venue.image_height;
                            selected_venue  = 'noti_' + data.venue_uid + '_uid';



                            $("#notification-image-venue-container").html("<img draggable='false' src='"+ data.venue.thumbnail_image_url +"' class='img img-fluid image-venue' id='"+ selected_venue +"'/><div id='notification-canvas-venue-container'></div>").promise().done(function(){

                                // STARRT KONVA
                                setupKonvaElement('notification-canvas-venue-container');

                                // WAIT LOAD IMAGE
                                $("#" + selected_venue).on('load', function() {

                                    $(".icon-tag").remove();
                                    $(".badge-ap").remove();

                                    setTimeout(function() {


                                        current_width   = $("#" + selected_venue).width();
                                        current_height  = $("#" + selected_venue).height();

                                        // MAKE BACKGROUND IMAGE
                                        $("#notification-image-venue-container")
                                        .css("background-image", "url('" + data.venue.thumbnail_image_url + "')")
                                        .css("width", current_width+ 'px')
                                        .css("height", current_height + 'px')
                                        .css("border", "2px solid black");

                                        //remove HTML image
                                        $("#" + selected_venue).remove();

                                        // // RESET KONVA SIZE
                                        resetKonvaElement(current_width, current_height);


                                            
                                            margin_left = $(".notification-zone-creating-container").css('margin-left');
                                            margin_left = Number(margin_left.replace("px", ""));



                                            let temp_lat_ap = calculatePoints([data.controller.position_x, data.controller.position_y], ori_width, ori_height, current_width, current_height)

                                            let ap_html = "<i class='fa fa-wifi icon-ap' id='noti_" + data.controller.device_uid + "' title='" + data.controller.name + "' style='display:none;'></i>";
                                            

                                            $(".notification-zone-creating-container").append(ap_html);
                                            $("#noti_" + data.controller.device_uid)
                                                .css("display", "block")
                                                .css("position", "absolute")
                                                .css("font-size", size_ap)
                                                .css("color", color_ap_current)
                                                .css("left", (temp_lat_ap[0] + margin_left) + "px")
                                                .css("top", temp_lat_ap[1] + "px")
                                                .css("cursor", 'pointer');



                                            // let ap_badge = '<span class="badge badge-glow bg-success badge-ap" style="display:none;" id="ap_noti_' + data.controller.device_uid + '">'+ data.controller.name +'</span>';

                                            // $(".notification-zone-creating-container").append(ap_badge);
                                            // $("#ap_noti_" + data.controller.device_uid)
                                            //     .css("display", "block")
                                            //     .css("position", "absolute")
                                            //     .css("left", (temp_lat_ap[0] + margin_left - (Number(size_ap.replace("px", "")))) + "px")
                                            //     .css("top", (temp_lat_ap[1] + Number(size_ap.replace("px", ""))) + "px")
                                            //     .css("cursor", 'pointer');




                                        
                                            let temp_lat = calculatePoints([data.position_x, data.position_y], ori_width, ori_height, current_width, current_height)


                                            let tag_html = "<i class='fa fa-mobile icon-tag' id='" + data.tracker.device_uid + "' title='" + data.tracker.name + "' style='position:absolute;'></i>";

                                            $(".notification-zone-creating-container").append(tag_html);
                                            $("#" + data.tracker.device_uid)
                                                .css("display", "block")
                                                .css("position", "absolute")
                                                .css("font-size", size_tag)
                                                .css("color", color_tag)
                                                .css("left", (temp_lat[0] + margin_left) + "px")
                                                .css("top", temp_lat[1] + "px")
                                                .css("cursor", 'pointer');



                                    }, 300);

                                });


                            });


                           
                        }
                    
                        return;   

                    }

                });


                var onHiddenNotification = $('#ModalNotificationTriggerEvent');

                // onHidden event
                onHiddenNotification.on('hidden.bs.modal', function () {
                    show_notification = true;
                });


            }
        }



        /*
        |--------------------------------------------------------------------------
        | CUSTOM NOTIFY MESSAGE
        |--------------------------------------------------------------------------
        |
        | message == message to shown
        | type ==  success || danger || warning || info
        | reload == reload page if needed || default is false
        |
        */
        function displayMessage(message, type = 'success', reload = false, time = 1000){

        switch(type) {
            case "error":
                toastr['error'](message, 'Error!', {
                        closeButton: true,
                        tapToDismiss: false,
                        rtl: false,
                        onShown: soundNoti()
                    });

                break;
            case "danger":
                toastr['error'](message, 'Error!', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    onShown: soundNoti()

                });

                break;
            case "warning":
                toastr['warning'](message, 'Warning!', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    onShown: soundNoti()
                });
                break;
            case "info":
                toastr['info'](message, 'Info!', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    onShown: soundNoti()
                });
                break;
            case "success":
                toastr['success'](message, 'Success!', {
                    closeButton: true,
                    rtl: false,
                    // extendedTimeOut: 0,
                    // timeOut: 0,
                    tapToDismiss: false,
                    onShown: soundNoti()
                });
                break;

        }

            if(reload){
                setTimeout(function() {
                    location.reload(true);
                }, time);
            }
        }


        function soundNoti() {
            var sound = new Audio({!! json_encode(url('sounds/alert.mp3')) !!});
            sound.play();
        }


        function randomString(length=30) {
            var chars   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            var result  = '';
            for (var i = length; i > 0; --i) result += chars[Math.round(Math.random() * (chars.length - 1))];

            return result;
        }

        function touchHandler(event) {
            var touch = event.changedTouches[0];

            var simulatedEvent = document.createEvent("MouseEvent");
                simulatedEvent.initMouseEvent({
                touchstart: "mousedown",
                touchmove: "mousemove",
                touchend: "mouseup"
            }[event.type], true, true, window, 1,
                touch.screenX, touch.screenY,
                touch.clientX, touch.clientY, false,
                false, false, false, 0, null);

            touch.target.dispatchEvent(simulatedEvent);
            // event.preventDefault();
        }


        function initFixDragOnMobile() {
            document.addEventListener("touchstart", touchHandler, true);
            document.addEventListener("touchmove", touchHandler, true);
            document.addEventListener("touchend", touchHandler, true);
            document.addEventListener("touchcancel", touchHandler, true);
        }







    </script>