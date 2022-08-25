@extends('layouts.main')

@section('title', 'Manage : Venue -> Map')
@section('page-desc', 'Manage your venue data.')

@section('button-right')

    @if(able_to("manage", "venue", "r"))
        <a href="{{ route('admin.manage.venue.index') }}">
            <button type="button" class="btn btn-primary btn-sm">
                <i data-feather="arrow-left-circle" class="mr-25"></i>
                <span>Back</span>
            </button>
        </a>
    @endif
@endsection

@section('style')


@endsection

@section('vendor-css')
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/extensions/nouislider.min.css') }}">
@endsection


@section('content')
<!-- Zero configuration table -->
<div class="content-body">
    <section id="input-sizing">
   


        <div class="row match-height">
            <div class="card-body" style="padding-top: 3px !important;padding-bottom: 3px !important;" id="ap-list-container">
                
            </div>
            <div class="col-md-12 col-12">

                <div class="card">

                    
                    <div class="card-body" style="padding: 0px !important;">
                        <div class="image-venue-container"></div>
                    </div>

                </div>
            </div>
            
        </div>
    </section>
    <!-- Input Sizing end -->
   
</div>


@endsection

@section('vendor-js')

@endsection

@section('script')

<script src="{{ url('templates/vuexy/app-assets/js/jquery-ui.js') }}"></script>

<script type="text/javascript">


    var original_html   = '<div class="demo-spacing-0"><div class="alert alert-primary" role="alert"><div class="alert-body"> Please select venue to load an image.</div></div></div>';

    var ori_width, ori_height, selected_venue = 0;
    var current_data = {!! json_encode($venue) !!};
    var device_data = {!! json_encode($devices) !!};

    function getIconWifi(uid, title = "", width = "20px", height = "20px") {

        return "<i class='fa fa-wifi' id='"+ uid +"' style='width:"+ width +" !important;height:"+ height +"!important' title='"+ title +"'></i>";

    }

    $(document).ready(function(){

        $("img").mousedown(function(){
            return false;
        });

        initFixDragOnMobile(); // function at script.blade.php

        setPositionAp();

        $('body').on('click', '.find-ap', function(e){
            
            e.preventDefault();

            for (oindex = 0; oindex < 6; oindex++) {

                $("#" + $(this).data("uid")).fadeTo('slow', 0.5).fadeTo('slow', 1.0);


            }


        });


        $(window).resize(function() {
            resizeIcon();
        });




    });



    function setPositionAp() {

        ori_width   = current_data["image_width"];
        ori_height  = current_data["image_height"];

        selected_venue = current_data["venue_uid"];


        $(".image-venue-container").html("<img draggable='false' src='"+ current_data["thumbnail_image_url"] +"' class='img img-fluid image-venue' style='border: 2px solid black !important' id='"+ selected_venue +"'/>").promise().done(function(){


            $("#ap-list-container").html("");

            device_data.forEach(ap => {

                let badge_html = '<div class="badge bg-success find-ap" style="cursor: pointer;margin-right: 4px;" title="'+ ap["name"] +'" data-uid="'+ ap["device_uid"] +'">'
                                    +'<i class="fa fa-wifi"></i>'
                                    +'<span>&nbsp;&nbsp;'+ ap["name"] + '</span>'
                                +'</div>';

                $("#ap-list-container").append(badge_html);


                //wait complete load
                $("#" + selected_venue).on('load', function() {

                    let uid     = ap['device_uid'];
                    let ap_html = "<i class='fa fa-wifi' id='"+ uid +"' title='"+ ap['name'] +"' class='icon-ap'></i>";


                    let posCal  = calculateOriPoint(ap["position_x"], ap["position_y"], ori_width, ori_height, $("#" + selected_venue).width(), $("#" + selected_venue).height());

                    let top     = ap["position_y"] == null ? "50%" : (posCal[1] + "px");
                    let left    = ap["position_x"] == null ? "50%" : (posCal[0] + "px");

                    
                    $(".image-venue-container").append(ap_html);




                    $("#" + uid)
                        .css("position", "absolute")
                        .css("font-size", size_ap)
                        .css("color", color_ap)
                        .css("top", top)
                        .css("left", left)
                        .css("cursor", 'grab');  


                    //if have permission then can drag
                    @if(session('permission') == "rw")
                    $("#" + uid).draggable({

                        containment: ".image-venue",
                        scroll: true,
                        drag: function (event, ui) {

                            $("#" + uid).css("cursor", "grabbing");
                        },
                        stop: function (event, ui) {

                            $("#" + uid).css("cursor", "grab");

                            let cur_x           = ($(this).css("left")).replace('px', '');
                            let cur_y           = ($(this).css("top")).replace('px', '');
                            let cur_img_width   = $("#" + selected_venue).width();
                            let cur_img_height  = $("#" + selected_venue).height();

                            let position = calculateOriPoint(cur_x, cur_y, cur_img_width, cur_img_height, ori_width, ori_height);

                            savePosition($(this).attr("id"), position);
                            
                        },

                    });
                    @endif
                });


            });
       

        });

    }



    function resetAll() {

        $(".image-venue-container").html(original_html);
        ori_width   = 0;
        ori_height  = 0;
        $("#ap-list-container").html("");
        $(".icon-ap").remove();
    }

    function calculateOriPoint(pos_x, pos_y, cur_width, cur_height, to_append_width, to_append_height) {
        

        let calX =  (pos_x * to_append_width) / cur_width;
        let calY =  (pos_y * to_append_height) / cur_height;
        // console.log(pos_x+"||"+ pos_y+"||"+ cur_width+"||"+ cur_height+"||"+ to_append_width +"||"+ to_append_height)

        return [calX, calY];


    }

    function savePosition(uid, position) {

        $('.loader-spinner').show();

        $.ajax({
            url:"{{ route('admin.manage.venue.ap-position') }}",
            type:'POST',
            data: {
                device_uid: uid,
                position: position,
            },
            success:function(response){

                $('.loader-spinner').hide();
                if(response["status"] == false) {

                    displayMessage(response["message"], 'danger');
                    $(".image-venue-container").html();
                    return false;

                }

                displayMessage(response["message"], 'success');

            }
        });
    }



</script>
@endsection