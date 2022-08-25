@extends('layouts.main')

@section('title', 'Venue Map')

@section('page-desc', 'History of Venue Map')

@section("vendor-css")

<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">

@endsection

@section('content')

<style>

    .form-control[readonly] {
        background-color: #fff;
        opacity: 1;
    }

    .card-custom{
        border: 2px solid cadetblue !important;
        background: aliceblue !important;
    }

    .heat_table2{
        font-size: 0.75rem;
    }

    .apexcharts-menu-icon{
        display: none;
    }


    ::backdrop
    {
        background-color: #f8f8f8;
    }

    .font-size-xsmall {
        font-size: .90rem;
        font-weight: bold;
    }
    #image-venue-container {
        background-repeat: no-repeat;
        background-position: center;
        position: relative;
        background-size: 100% auto;
        position: relative;
        overflow-x:hidden;
        overflow-y:hidden;
    }



</style>

<div class="content-body">

    <section id="filter-venues">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Location<span class="text-danger">*</span></label>
                                    <select class="select2 form-control" name="location" id="omy_location" required>
                                        <option value="">Please Select</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->location_uid }}">{{$location->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Venue<span class="text-danger">*</span></label>
                                    <select class="select2 form-control" name="venue" id="omy_venue"  required>
                                        <option value="">Please Select</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Scanner Type<span class="text-danger">*</span></label>
                                    <select class="select2 form-control" name="scanner_type" id="scanner_type" required>
                                        <option value="all">All</option>
                                        <option value="ble">BLE</option>
                                        <option value="wifi">WIFI</option>
                                    </select>
                                </div>
                            </div>

                            

                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Report Date <span class="text-danger">*</span></label>
                                    <input class="form-control flatpickr-range report_date" type="text" name="report_date" id="omy_report_date" value="" required />
                                </div>
                            </div>
                            <div class="col-md-3 pb-1">
                                <button class="btn btn-primary btn-show mt-2">Show</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="report-time" style="display:none" >


        <div class="row match-height">

            <div class="col-md-12 col-12">

                <div class="card">

                            
                    <div class="card-body">
                        Rerport time at <b><span id="report-time-text"></span></b> <button class="btn btn-success btn-sm btn-play">Play</button>
                        <button class="btn btn-warning btn-sm btn-pause" style="display:none">Pause</button>
                    </div>

                </div>
            </div>
            
        </div>
    </section>

    <section id="section-image">


        <div class="row match-height">

            <div class="col-md-12 col-12">

                <div class="card">

                            
                    <div class="card-body zone-creating-container" style="padding: 0px !important;margin-right: auto !important;margin-left: auto !important;">
                        <div id="image-venue-container"></div>
                    </div>

                </div>
            </div>
            
        </div>
    </section>

</div>

@endsection


@section('script')

<!-- BEGIN: Page JS-->
<script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
<!-- END: Page JS-->


<script type="text/javascript">

    var original_html   = '<div class="demo-spacing-0"><div class="alert alert-primary" role="alert"><div class="alert-body">No Data</div></div></div>';


    var icon_ap         = "{{ URL::asset('/images/logo-wifi.png') }}";
    var icon_tag        = "{{ URL::asset('/images/icon-mobile.png') }}";
    var old_venue       = "";
    var old_tag         = "";
    var margin_left     = 0;

    // VAR FOR DRAWING ZONE
    var select = '',
        allData,
        mode,
        current_width,
        current_height,
        ori_width,
        ori_height,
        edit_mode = false,
        current_venue;



    var processInterval, processIntervalMonitor;
    var counter = true;
    var firstRun = true;
    var iconBlinking = false;
    var elem;
</script>

<script>



    var locations = {!! json_encode($locations) !!}
    var min_date = {!! json_encode($min_date) !!}
    min_date = new Date(min_date);


    var preload_date = new Date();
    preload_date.setDate(preload_date.getDate() - 5);


     $(document).ready(function() {

        $("#omy_location").change(function() {

            let omy_location = $(this).val();
            
            $("#omy_venue").empty();
            $("#omy_venue").append('<option value="">Please Select</option>');
            $("#omy_zone").empty();
            $("#omy_zone").append('<option value="">Please Select</option>');

            locations.forEach(function(location) {



                if(omy_location == location['location_uid']) {

                    location['venues'].forEach(function(venue) {

                        if(venue['image'] != null || venue['image'] != "" ) {

                            $("#omy_venue").append('<option value='+ venue['venue_uid'] +'>'+ venue['name'] +'</option>');
                        }

                    });

                }

            });


        });






        $('.flatpickr-range').flatpickr({
            minDate : min_date,
            maxDate : 'today',
            dateFormat: "d-m-Y",
            defaultDate: 'today'
        });



        $('.btn-show').on('click', function(){

            $("#report-time").hide();

            if($('#omy_location').val() == '') {

                $('#omy_location').focus();
                displayMessage("Please select location.", 'warning')
                return;
            }

            if($('#omy_venue').val() == '') {

                $('#omy_venue').focus();
                displayMessage("Please select venue.", 'warning')
                return;
            }


            if($('#omy_report_date').val() == '') {

                $('#omy_report_date').focus();
                displayMessage("Please select report date.", 'warning')
                return;
            }
            $('.loader-spinner').show();


            let location        = $("#omy_location").val();
            let venue           = $("#omy_venue").val();
            let report_date     = $("#omy_report_date").val();
            let scanner_type     = $("#scanner_type").val();


            $.ajax({
                url :"{{ route('admin.analytics.venue_map.data') }}",
                type:'POST',
                data: {
                    'location'      : location,
                    'venue'         : venue,
                    'scanner_type'  : scanner_type,
                    'report_date'   : report_date,
                },
                success:function(response){


                    $(".btn-pause").trigger("click");

                    if (response['status'] === "success") {

                        if(response['data'].length <= 0) {

                            displayMessage("No record Found", "success");

                        }else {


                            // Number of Weekly <span id="report-header"></span> at typical Hour

                            $("#report-section").show();

                            current_venue    = response;
                            $("#report-time").show();

                            if(old_venue != response["data"]["venue"]["venue_uid"]){

                                old_venue = response["data"]["venue"]["venue_uid"];
                                resetAll();


                                ori_width       = current_venue["data"]["venue"]["image_width"];
                                ori_height      = current_venue["data"]["venue"]["image_height"];
                                selected_venue  = current_venue["data"]["venue"]["venue_uid"];
                                    

                                $("#image-venue-container").html("<img draggable='false' src='"+ current_venue["data"]["venue"]["thumbnail_image_url"] +"' class='img img-fluid image-venue' id='"+ selected_venue +"'/><div id='canvas-venue-container'></div>").promise().done(function(){


                                    // STARRT KONVA
                                    setupKonvaElement('canvas-venue-container');

                                    // WAIT LOAD IMAGE
                                    $("#" + selected_venue).on('load', function() {


                                        current_width   = $("#" + selected_venue).width();
                                        current_height  = $("#" + selected_venue).height();


                                        // MAKE BACKGROUND IMAGE
                                        $("#image-venue-container")
                                        .css("background-image", "url('" + current_venue["data"]["venue"]["thumbnail_image_url"] + "')")
                                        .css("width", current_width+ 'px')
                                        .css("height", current_height + 'px')
                                        .css("border", "2px solid black");

                                        //remove HTML image
                                        $("#" + selected_venue).remove();

                                        // RESET KONVA SIZE
                                        resetKonvaElement(current_width, current_height);

                                        setTimeout(function(){
                                            $('.loader-spinner').fadeOut();
                                            
                                            margin_left = $(".zone-creating-container").css('margin-left');
                                            margin_left = Number(margin_left.replace("px", ""));

                                            current_venue["data"]["device-ap"].forEach((element) => {

                                                let temp_lat = calculatePoints([element["position_x"], element["position_y"]], ori_width, ori_height, current_width, current_height)

                                                let ap_html = "<i class='fa fa-wifi icon-ap zone-"+ element['zone_uid'] +"' id='" + element["device_uid"] + "' title='" + element['name'] + "' style='display:none;'></i>";
                                                

                                                $(".zone-creating-container").append(ap_html);
                                                $("#" + element["device_uid"])
                                                    .css("display", "block")
                                                    .css("position", "absolute")
                                                    .css("font-size", size_ap)
                                                    .css("color", color_ap_current)
                                                    .css("left", (temp_lat[0] + margin_left) + "px")
                                                    .css("top", temp_lat[1] + "px")
                                                    .css("cursor", 'pointer');



                                                let ap_badge = '<span class="badge badge-glow bg-success badge-ap" style="display:none;" id="ap_' + element["device_uid"] + '">'+ element["name"] +'</span>';

                                                $(".zone-creating-container").append(ap_badge);
                                                $("#ap_" + element["device_uid"])
                                                    .css("display", "block")
                                                    .css("position", "absolute")
                                                    .css("left", (temp_lat[0] + margin_left - (Number(size_ap.replace("px", "")))) + "px")
                                                    .css("top", (temp_lat[1] + Number(size_ap.replace("px", ""))) + "px")
                                                    .css("cursor", 'pointer');

                                                  
                                            });




                                        }, 200); //end function settimeout
                                        


                                                        
                                    });

                                });
                            }

                            
                        }


                    }else {

                        displayMessage(response['message'], response["status"])

                    }

                    $('.loader-spinner').hide();

                    
                }

        
            })

        });



        $('body').on('click', '.btn-play', function(e){
            

            cur_time_index = 0;
            interval_time = window.setInterval(function(){
               
                $(".badge-total").remove();

                $("#report-time-text").text(time_arr[cur_time_index])



                current_venue["data"]["device-ap"].forEach((element) => {


                    let ap_badge = '<span class="badge badge-glow bg-info badge-total" style="display:none;" id="total_' + element['zone_uid'] + '">'+ current_venue["data"]["heatmap_zone"][element['zone_uid']][cur_time_index]['total'] +'</span>';

                    let temp_lat = calculatePoints([element["position_x"], element["position_y"]], ori_width, ori_height, current_width, current_height)


                    $(".zone-creating-container").append(ap_badge);
                    $("#total_" + element['zone_uid'])
                        .css("display", "block")
                        .css("position", "absolute")
                        .css("left", (temp_lat[0] + margin_left - (Number(size_ap.replace("px", "")))) + "px")
                        .css("top", (temp_lat[1] + (Number(size_ap.replace("px", "")) * 2)) + "px")
                        .css("cursor", 'pointer');


                      
                });


    
                cur_time_index++;

            }, 2000);


            $(".btn-play").hide()
            $(".btn-pause").show()
            
        });


        $('body').on('click', '.btn-pause', function(e){
            

            clearInterval(interval_time) 

            $(".btn-play").show()
            $(".btn-pause").hide()

            
        });



    });
    
    var time_arr = ["12AM", "1AM", "2AM", "3AM", "4AM", "5AM", "6AM", "7AM", "8AM", "9AM", "10AM", "11AM", "12PM", "1PM", "2PM", "3PM", "4PM", "5PM", "6PM", "7PM", "8PM", "9PM", "10PM", "11PM"];
    var interval_time = "";
    
    var cur_time_index = "";

    function resetAll() {

        ori_width   = 0;
        ori_height  = 0;


        zones = [];
        $("#image-venue-container").remove();
        $(".zone-creating-container").html('<div id="image-venue-container">'+ original_html +'</div>')
        $(".row-view-tag").hide();
    }


    function setupZone() {
        layer.removeChildren().draw();        
        drawLayer.removeChildren().draw();        
        drawZones();
    }

</script>


@endsection


