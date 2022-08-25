@extends('layouts.main')

@section('title', 'Monitor : Device [ Tracker ]')

@section('page-desc', 'Monitor all registered device [Tracker]. ')


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
</style>

@endsection
@section('content')

<div class="content-body" id="content-body-tracker">

	<div class="row">

	    <div class="col-lg-3 col-sm-6 col-12">
	        <div class="card bg-gradient-primary">
	            <div class="card-header">
	                <div>
	                    <h2 class="fw-bolder mb-0" > <text id="h-total-all-active">0</text> / <text id="h-total-all">0</text></h2>
	                    <p class="card-text" data-bs-toggle='tooltip' title='' data-bs-original-title='total of onlince device over all registered device.'>Total Device</p>
	                </div>
	                <div class="avatar bg-primary p-50 m-0">
	                    <div class="avatar-content">
	                        <i data-feather="cpu" class="font-medium-5" ></i>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>

	    <div class="col-lg-3 col-sm-6 col-12">
	        <div class="card bg-gradient-success">
	            <div class="card-header">
	                <div>
	                    <h2 class="fw-bolder mb-0" id="h-total-online">0</h2>
	                    <p class="card-text" data-bs-toggle='tooltip' title='' data-bs-original-title='total of device detected by omaya within a specific time'>Online</p>
	                </div>
	                <div class="avatar bg-success p-50 m-0">
	                    <div class="avatar-content">
	                        <i data-feather="wifi" class="font-medium-5"></i>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>

	    <div class="col-lg-3 col-sm-6 col-12">
	        <div class="card bg-gradient-secondary">
	            <div class="card-header">
	                <div>
	                    <h2 class="fw-bolder mb-0" id="h-total-no-packet">0</h2>
	                    <p class="card-text" data-bs-toggle='tooltip' title='' data-bs-original-title='total of device that no packet received from omaya after a specific time'>No New Packet</p>
	                </div>
	                <div class="avatar bg-secondary p-50 m-0">
	                    <div class="avatar-content">
	                        <i data-feather="loader" class="font-medium-5"></i>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>


	    <div class="col-lg-3 col-sm-6 col-12">
	        <div class="card bg-gradient-danger">
	            <div class="card-header">
	                <div>
	                    <h2 class="fw-bolder mb-0" id="h-total-offline">0</h2>
	                    <p class="card-text"  data-bs-toggle='tooltip' title='' data-bs-original-title='total of device that no packet received since device registered'>Offline</p>
	                </div>
	                <div class="avatar bg-danger p-50 m-0">
	                    <div class="avatar-content">
	                        <i data-feather="wifi-off" class="font-medium-5"></i>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>


	<section id="section-image">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">

	                            
                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Device<span class="text-danger">*</span></label>
                                    <select class="select2 form-control" name="monitor_device" id="monitor_device" required>
                                        <option value="">Please Select</option>
                                        @foreach($devices as $device)
                                        <option value="{{ $device->device_uid }}">{{ $device->name }} [{{$device->mac_address}}] {{ $device->entity ? (" - " . $device->entity->name) : "" }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 pb-1">
                                <button class="btn btn-primary btn-show mt-2">Show</button>
                                <button class="btn btn-info mt-2" onclick="openFullscreen();">Full Screen</button>
                            </div>
	                            
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>

	    <div class="row row-view-tag" style="display: none">

            <!-- <div class="col-2 font-size-xsmall font-weight-bold btn-blink" style="cursor: pointer;">
                [ BLINK ]
            </div> -->

            <!-- <div class="col-2">
                <div class="font-size-xsmall">
                [ RSSI : <span class="view-tag-rssi"></span> ]
                </div>
            </div> -->

            <div class="col-4">
                <div class="font-size-xsmall text-center">
                [ Location : <span class="view-tag-location"></span> ]
                </div>
            </div>

            <div class="col-4">
                <div class="font-size-xsmall text-center">
                    [ POSITION ON MAP AS OF <span class="view-tag-time"></span> ]
                </div>
            </div>

            <div class="col-4">
                <div class="font-size-xsmall text-center">
                [ AP : <span class="view-ap"></span> ]
                </div>
            </div>

            
        </div>
        <!-- <div class="row row-view-tag" style="display: none">


            <div class="col-6">
                <div class="font-size-xsmall">
                [ AP : <span class="view-ap"></span> ]
                </div>
            </div>

            
        </div> -->


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

@section('vendor-js')

@endsection




@section('script')


<script type="text/javascript">

	$(document).ready(function() {


		getMonitorDevice();

        clearInterval(processIntervalMonitor);

        processIntervalMonitor = setInterval(getMonitorDevice, 12000)

        $('[data-bs-toggle="tooltip"]').tooltip()

	});


    function getMonitorDevice() {
        $.ajax({
            url:"{{ route('admin.monitor.device-tracker.index') }}",
            type:"get",
            success:function(response) {

                
                if(counter == true) {

                    $("#h-total-all-active").html(response['total']['all-active']).counterUp({ delay: 100, time: 1000 });
                    $("#h-total-all").html(response['total']['all']).counterUp({ delay: 100, time: 1000 });
                    $("#h-total-online").html(response['total']['online']).counterUp({ delay: 100, time: 1000 });
                    $("#h-total-no-packet").html(response['total']['no-new']).counterUp({ delay: 100, time: 1000 });
                    $("#h-total-offline").html(response['total']['offline']).counterUp({ delay: 100, time: 1000 });

                }else {

                    $("#h-total-all-active").html(response['total']['all-active']);
                    $("#h-total-all").html(response['total']['all']);
                    $("#h-total-online").html(response['total']['online']);
                    $("#h-total-no-packet").html(response['total']['no-new']);
                    $("#h-total-offline").html(response['total']['offline']);
                }
                counter = false;
            }

        });
    }


</script>



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


    function openFullscreen() {

        var elem = document.getElementById("section-image");
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) { /* Safari */
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) { /* IE11 */
            elem.msRequestFullscreen();
        }

    }

    document.addEventListener("fullscreenchange", function(){onChangeDevice()}, false);

    $(document).ready(function(){


        // $("#monitor_device").change(function () { onChangeDevice(); })

        $(".btn-show").click(function() {

        	onChangeDevice();
        })

        $(".btn-blink").on("click", function (){


            if (iconBlinking === false) {


                iconBlinking = true;

                for (oindex = 0; oindex < 6; oindex++) {

                    $(".device-icon").fadeTo('slow', 0.5).fadeTo('slow', 1.0);

                }

                iconBlinking = false;


            }


        });


        onChangeDevice();
        




    });





    function onChangeDevice(){


        if($('#monitor_device').val() == "") return false;

        if(old_tag != $('#monitor_device').val() )
            firstRun = true;

        old_tag     = $('#monitor_device').val()

        if(firstRun == true)
        $('.loader-spinner').show();
        $.ajax({
            url:"{{ url('admin/monitor/device-tracker/load-data') }}",
            type:"POST",
            data:{'device_uid' : $('#monitor_device').val() },
            success:function(response) {

                clearInterval(processInterval);

                processInterval = setInterval(onChangeDevice, 12000)

                if(response["status"] == false) {

                    if(firstRun)
                    displayMessage(response["message"], 'info');

                    resetAll();
                    old_venue = "";
                    firstRun = false;
                    $('.loader-spinner').fadeOut();
                    return false;


                }

                firstRun    = false;

                current_venue    = response;
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



                                    let ap_badge = '<span class="badge badge-glow '+(element["status"] == "current" ? 'bg-success' : 'bg-secondary')+' badge-ap" style="display:none;" id="ap_' + element["device_uid"] + '">'+ element["name"] +'</span>';

                                    $(".zone-creating-container").append(ap_badge);
                                    $("#ap_" + element["device_uid"])
                                        .css("display", "block")
                                        .css("position", "absolute")
                                        .css("left", (temp_lat[0] + margin_left - (Number(size_ap.replace("px", "")))) + "px")
                                        .css("top", (temp_lat[1] + Number(size_ap.replace("px", ""))) + "px")
                                        .css("cursor", 'pointer');

                                      
                                });


                                let temp_lat = calculatePoints([current_venue["data"]["device-tracker"]["position_x"], current_venue["data"]["device-tracker"]["position_y"]], ori_width, ori_height, current_width, current_height)


                                let tag_html = "<i class='fa fa-mobile icon-tag' id='" + current_venue["data"]["device-tracker"]["device_uid"] + "' title='" + current_venue["data"]["device-tracker"]["name"] + "' style='display:none;'></i>";

                                $(".zone-creating-container").append(tag_html);
                                $("#" + current_venue["data"]["device-tracker"]["device_uid"])
                                    .css("display", "block")
                                    .css("position", "absolute")
                                    .css("font-size", size_tag)
                                    .css("color", color_tag)
                                    .css("left", (temp_lat[0] + margin_left) + "px")
                                    .css("top", temp_lat[1] + "px")
                                    .css("cursor", 'pointer');


                                let rssi_html = '<span class="badge badge-glow bg-info badge-rssi" style="display:none;" id="rssi_' + current_venue["data"]["device-tracker"]["device_uid"] + '">'+ current_venue["data"]["device_cache"]["rssi"] +'</span>';
                                $(".zone-creating-container").append(rssi_html);
                                $("#rssi_" + current_venue["data"]["device-tracker"]["device_uid"])
                                    .css("display", "block")
                                    .css("position", "absolute")
                                    .css("left", (temp_lat[0] + (Number(size_tag.replace("px", "")) / 2) + margin_left) + "px")
                                    .css("top", (temp_lat[1]) + "px")
                                    .css("cursor", 'pointer');

                    

                                $(".row-view-tag").show();
                                // $(".view-tag-rssi").text(current_venue["data"]["device_cache"]["rssi"]);
                                $(".view-tag-time").text(current_venue["data"]["device_cache"]["last_detected"]);
                                $(".view-tag-location").html(current_venue["data"]["device_cache"]["location"]);

                                $(".view-ap").html(current_venue["data"]["device_cache"]["ap_name"] + " | Dev [" + current_venue["data"]["device-tracker"]["mac_address"] +"]");


                            }, 200); //end function settimeout
                            


                                            
                        });

                    });
                }else {

                    $(".icon-tag").remove();
                    $(".badge-rssi").remove();
                    $(".badge-ap").remove();

                    margin_left = $(".zone-creating-container").css('margin-left');
                    margin_left = Number(margin_left.replace("px", ""));

                    current_venue["data"]["device-ap"].forEach((element) => {


                        let temp_lat = calculatePoints([element["position_x"], element["position_y"]], ori_width, ori_height, current_width, current_height)


                        $("#" + element["device_uid"])
                            .css("color", (element["status"] == "current" ? color_ap_current : color_ap_normal))
                            .css("left", (temp_lat[0] + margin_left) + "px")
                            .css("top", temp_lat[1] + "px")


                        let ap_badge = '<span class="badge badge-glow '+(element["status"] == "current" ? 'bg-success' : 'bg-secondary')+' badge-ap" style="display:none;" id="ap_' + element["device_uid"] + '">'+ element["name"] +'</span>';
                                    
                        $(".zone-creating-container").append(ap_badge);
                        $("#ap_" + element["device_uid"])
                            .css("display", "block")
                            .css("position", "absolute")
                            .css("left", (temp_lat[0] + margin_left - (Number(size_ap.replace("px", "")))) + "px")
                            .css("top", (temp_lat[1] + Number(size_ap.replace("px", ""))) + "px")

                    });


                    let temp_lat = calculatePoints([current_venue["data"]["device-tracker"]["position_x"], current_venue["data"]["device-tracker"]["position_y"]], ori_width, ori_height, current_width, current_height)


                    let tag_html = "<i class='fa fa-mobile icon-tag' id='" + current_venue["data"]["device-tracker"]["device_uid"] + "' title='" + current_venue["data"]["device-tracker"]["name"] + "' style='display:none;'></i>";

                    $(".zone-creating-container").append(tag_html);
                    $("#" + current_venue["data"]["device-tracker"]["device_uid"])
                        .css("display", "block")
                        .css("position", "absolute")
                        .css("font-size", size_tag)
                        .css("color", color_tag)
                        .css("left", (temp_lat[0] + margin_left) + "px")
                        .css("top", temp_lat[1] + "px")
                        .css("cursor", 'pointer');


                    let rssi_html = '<span class="badge badge-glow bg-info badge-rssi" style="display:none;" id="rssi_' + current_venue["data"]["device-tracker"]["device_uid"] + '">'+ current_venue["data"]["device_cache"]["rssi"] +'</span>';
                    $(".zone-creating-container").append(rssi_html);
                    $("#rssi_" + current_venue["data"]["device-tracker"]["device_uid"])
                        .css("display", "block")
                        .css("position", "absolute")
                        .css("left", (temp_lat[0] + (Number(size_tag.replace("px", "")) / 2) + margin_left) + "px")
                        .css("top", (temp_lat[1]) + "px")
                        .css("cursor", 'pointer');
        

                    $(".row-view-tag").show();
                    // $(".view-tag-rssi").text(current_venue["data"]["device_cache"]["rssi"]);
                    $(".view-tag-time").text(current_venue["data"]["device_cache"]["last_detected"]);
                    $(".view-tag-location").html(current_venue["data"]["device_cache"]["location"]);

                    $(".view-ap").html(current_venue["data"]["device_cache"]["ap_name"]);
                        
                    $('.loader-spinner').fadeOut();



                }


            }
        });


    }



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
