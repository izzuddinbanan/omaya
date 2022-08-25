@extends('layouts.main')

@section('title', 'Monitor : Venue')

@section('page-desc', 'Monitor Venues to see all detected registered device. ')

@section('app-content-class', 'ecommerce-application')

@section('style')
<style type="text/css">

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

    .item-img {
        max-height: 15.85rem !important;
    }

</style>

<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/pages/app-ecommerce.css') }}">

@endsection
@section('content')

<div class="content-body" id="content-body-tracker">

	<section id="section-image">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">

	                            
                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Location<span class="text-danger">*</span></label>
                                    <select class="select2 form-control" name="monitor_venue" id="monitor_location" required>
                                        <option value="">Please Select</option>
                                        @foreach($locations as $location)
                                        <option value="{{ $location->location_uid }}">{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Search</label>
                                    <input type="text" name="monitor_search" value="" id="monitor_search" class="form-control" placeholder="venue or zone e.g Venue 1 ">
                                </div>
                            </div>
                            <div class="col-md-6 pb-1">
                                <button class="btn btn-primary btn-back mt-2" style="display: none;">Back</button>
                            </div>
	                            
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>

	   
    </section>

    <section id="wishlist" class="grid-view wishlist-items">
    </section>



    <div class="row row-view-tag match-height" style="display: none">


        <div class="col-6">
            <div class="font-size-xsmall text">
            [ Venue : <span class="view-tag-location"></span> ]
            </div>
        </div>

        <!-- <div class="col-6">
            <div class="font-size-xsmall text-right">
                [ POSITION ON MAP AS OF <span class="view-tag-time"></span> ]
            </div>
        </div> -->

        
    </div>
    <div class="row match-height">

        <div class="col-md-12 col-12">

            <div class="card">

                        
                <div class="card-body zone-creating-container" style="padding: 0px !important;margin-right: auto !important;margin-left: auto !important;">
                    <div id="image-venue-container"></div>
                </div>

            </div>
        </div>
        
    </div>



</div>

@endsection

@section('vendor-js')

@endsection




@section('script')

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
        current_venue_uid,
        load_data,
        current_venue;



    var processInterval, processIntervalMonitor;
    var counter = true;
    var firstRun = true;
    var iconBlinking = false;
    var elem;


	
$(document).ready(function() {




    $("#monitor_location").change(function() {

        getListVenue();

    });

    $("#monitor_search").on("keyup", function() {

        getListVenue();
    })

    function getListVenue(){

        $(".match-height").fadeOut(200, function() {

            $(".btn-back").fadeOut();
            $("#wishlist").fadeIn();


            let monitor_location = $("#monitor_location").val();
            let monitor_search   = $("#monitor_search").val();
            
            if(monitor_location == "") return false;

            $.ajax({
                url :"{{ route('admin.monitor.venue.index') }}",
                type:'get',
                data: {
                    'location'      : monitor_location,
                    'search'        : monitor_search,
                },
                success:function(response){

                    if (response['status'] === "success") {
                        
                        $("#wishlist").html(response['data']['html']);


                    }
                    
                }

        
            })

        });
        


    }


    $('body').on('click', '.view-venue-map', function(e){
        e.preventDefault();
        load_data = true;
        current_venue_uid = $(this).attr('id');
        viewVenueMap(current_venue_uid);

    })

    $('body').on('click', '.btn-back', function(e){
        load_data = false;
        getListVenue();
    })

    function viewVenueMap(venue_uid = current_venue_uid) {

        if(venue_uid == "") return false;
        if(load_data == false) return false;

        $("#wishlist").fadeOut(200, function() {

            $(".btn-back").fadeIn()
            $(".match-height").fadeIn();

            $.ajax({
                url:"{{ url('admin/monitor/venue/load-data') }}",
                type:"POST",
                data:{'venue_uid' :venue_uid },
                success:function(response) {

                    $(".badge-ap").remove();
                    $(".icon-ap").remove();
                    $(".icon-tag").remove();
                    $(".badge-rssi").remove();
                    // console.log(response)
                    clearInterval(processInterval);

                    processInterval = setInterval(viewVenueMap, 12000)

                    // if(response["status"] == false) {

                    //     if(firstRun)
                    //     displayMessage(response["message"], 'info');

                    //     resetAll();
                    //     old_venue = "";
                    //     firstRun = false;
                    //     $('.loader-spinner').fadeOut();
                    //     return false;


                    // }

                    // firstRun    = false;

                        current_venue    = response;


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


                                        if(element['status'] != "current") return;
                                        let temp_lat = calculatePoints([element["position_x"], element["position_y"]], ori_width, ori_height, current_width, current_height)

                                        let ap_html = "<i class='fa fa-wifi icon-ap' id='" + element["device_uid"] + "' title='" + element['name'] + "' style='display:none;'></i>";
                                        

                                        $(".zone-creating-container").append(ap_html);
                                        $("#" + element["device_uid"])
                                            .css("display", "block")
                                            .css("position", "absolute")
                                            .css("font-size", size_ap)
                                            .css("color", (element["status"] == "current" ? color_ap_current : color_ap_normal))
                                            .css("left", (temp_lat[0] + margin_left) + "px")
                                            .css("top", temp_lat[1] + "px")
                                            .css("cursor", 'pointer');



                                        let ap_badge = '<span class="badge badge-glow badge-ap" style="display:none;background-color:'+ element['color'] +' !important" id="ap_' + element["device_uid"] + '">'+ element["name"] +'</span>';

                                        $(".zone-creating-container").append(ap_badge);
                                        $("#ap_" + element["device_uid"])
                                            .css("display", "block")
                                            .css("position", "absolute")
                                            .css("font-size", size_badge)
                                            .css("left", (temp_lat[0] + margin_left - (Number(size_ap.replace("px", "")))) + "px")
                                            .css("top", (temp_lat[1] + Number(size_ap.replace("px", ""))) + "px")
                                            .css("cursor", 'pointer');


                                        // $(".view-tag-time").text(current_venue["data"]["device_cache"]["last_detected"]);
                                        $(".view-tag-location").html(current_venue["data"]["venue"]["name"]);




                                        element["devices"].forEach((device) => {


                                            let temp_lat = calculatePoints([device["position_x"], device["position_y"]], ori_width, ori_height, current_width, current_height)


                                            let tag_html = "<i class='fa fa-mobile icon-tag' id='" + device["device_uid"] + "' title='" + device["name"] + "' style='display:none;color:"+ element['color'] +" !important'></i>";

                                            $(".zone-creating-container").append(tag_html);
                                            $("#" + device["device_uid"])
                                                .css("display", "block")
                                                .css("position", "absolute")
                                                .css("font-size", size_tag)
                                                // .css("color", element['color'])
                                                .css("left", (temp_lat[0] + margin_left) + "px")
                                                .css("top", temp_lat[1] + "px")
                                                .css("cursor", 'pointer');


                                            let rssi_html = '<span class="badge badge-glow bg-info badge-rssi" style="display:none;background-color:'+ element["color"] +' !important;" id="rssi_' + device["device_uid"] + '">'+ device["rssi"] +'</span>';
                                            $(".zone-creating-container").append(rssi_html);
                                            $("#rssi_" + device["device_uid"])
                                                .css("display", "block")
                                                .css("font-size", size_badge)
                                                .css("position", "absolute")
                                                .css("left", (temp_lat[0] + (Number(size_tag.replace("px", "")) / 2) + margin_left) + "px")
                                                .css("top", (temp_lat[1]) + "px")
                                                .css("cursor", 'pointer');

                                       });

                                          
                                    });



                                }, 200); //end function settimeout
                                


                                                
                            });

                        });
                    

                }
            });


        })
        



    }


     function resetAll() {

        ori_width   = 0;
        ori_height  = 0;


        zones = [];
        $("#image-venue-container").remove();
        $(".zone-creating-container").html('<div id="image-venue-container">'+ original_html +'</div>')
    }


    function setupZone() {
        layer.removeChildren().draw();        
        drawLayer.removeChildren().draw();        
        drawZones();
    }

});



</script>




@endsection
