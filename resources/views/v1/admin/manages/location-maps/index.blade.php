<!-- Description -->
@extends('layouts.main')



@section('title', 'Manage : Location Map')
@section('page-desc', 'Manage your location Image/Map.')

@section('button-right')

    @if(able_to("manage", "location", "rw"))

        <a href="{{ route('admin.manage.location.index') }}">
            <button type="button" class="btn btn-primary btn-sm">
                <i data-feather="arrow-left-circle" class="mr-25"></i>
                <span>Back</span>
            </button>
        </a>
    @endif
@endsection

@section('content')
<!-- Zero configuration table -->
<div class="content-body">

        <section id="input-sizing">
            <div class="row match-height">
                <div class="col-md-12 col-12">
                    <div class="card">

                        <form method="POST" action="{{ route('admin.manage.location-map.store') }}" enctype="multipart/form-data" >

                            @csrf

                            <div class="card-header">
                                <h4 class="card-title">Upload Location Image/Map</h4>
                            </div>

                            <div class="card-body">
                                <div class="row">


                                    <div class="clearfix"></div>
                                    <div class="col-md-6 pb-1">
                                        <div class="form-group">
                                            <label for="name">Map <span class="text-danger"><code>Type: jpg, jpeg, png</code></span></label>
                                                

                                            @if(!$cloud->location_image)
                                            <input class="form-control @error('map') is-invalid @enderror dropify map-upload" type="file" autocomplete="off" name="map" value="{{ old('map') }}" accept="image/*" tabindex="5" data-show-remove="false" @if($cloud->location_image) data-default-file="{{ $cloud->location_image ? $cloud->thumbnail_location_image_url : ""  }}" @endif />
                                            @else
                                            <input class="form-control @error('map') is-invalid @enderror map-upload" type="file" autocomplete="off" name="map" value="{{ old('map') }}" accept="image/*" tabindex="5" data-show-remove="false" />

                                            @endif
                                            <div>
                                                <div class="@error('map') is-invalid @enderror"></div>
                                                @error('map')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>


                                        </div>
                                    </div>


                                    <div class="col-md-12"></div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light" tabindex="4">Upload</button>
                                    </div>


                                </div>
                            </div>
                        </form>

                    </div>
                </div>
                
            </div>
        </section>


</div>

<div class="content-body">

        <section id="input-sizing">
            <div class="row match-height">
                <!-- IF ada data location setting -->
                <!-- show image , buat marker -->
                <div class="card-body" style="padding-top: 3px !important;padding-bottom: 3px !important;" id="loc-list-container"></div>
                <div class="col-md-12 col-12">

                    <div class="card">

                        <div class="card-body" style="padding: 0px !important;">
                            <div class="image-venue-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


</div>

@endsection



@section('script')

<script src="{{ url('templates/vuexy/app-assets/js/jquery-ui.js') }}"></script>

<script type="text/javascript">
    var ori_width, ori_height, selected_location = 0;

    var current_data = {!! json_encode($cloud) !!};
    var locations   = {!! json_encode($locations) !!};

    var original_html   = '<div class="demo-spacing-0"><div class="alert alert-primary" role="alert"><div class="alert-body"> Please upload image to set the location(s).</div></div></div>'
    ;

    function getIconWifi(uid, title = "", width = "20px", height = "20px") {

        return "<i class='fa fa-map-marker' id='" + uid + "' style='width:" + width + " !important;height:" + height + "!important' title='" + title + "'></i>";

    }

    $(document).ready(function() {

        console.log(current_data)
        $("img").mousedown(function() {
            return false;
        });

        setPosition();
        initFixDragOnMobile(); // function at script.blade.php

        $('body').on('click', '.find-loc', function(e) {
            e.preventDefault();

            for (oindex = 0; oindex < 6; oindex++) {

                $("#" + $(this).data("uid")).fadeTo('slow', 0.5).fadeTo('slow', 1.0);

            }
        });

        $(window).resize(function() {
            location_map();
            resizeIcon();
        });
    });


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


        $.ajax({
            url:"{{ route('admin.management.location-map.save-position') }}",
            type:'POST',
            data: {
                location_uid: uid,
                position: position,
            },
            success:function(response){

                if(response["status"] == false) {

                    displayMessage(response["message"], 'danger');
                    $(".image-venue-container").html();
                    return false;

                }

                displayMessage(response["message"], 'success');

            }
        });
    }

    function setPosition() {

        ori_width  = current_data["location_image_width"];
        ori_height = current_data["location_image_height"];


        $(".image-venue-container").html("<img draggable='false' src='" + current_data["thumbnail_location_image_url"] + "' class='img img-fluid image-venue' style='border: 2px solid black !important;' id='location_image'/>").promise().done(function() {

            locations.forEach(loc => {

                let badge_html = '<div class="badge badge-secondary mr-25 find-loc" style="cursor: pointer;" title="' + loc["name"] + '" data-uid="' + loc["location_uid"] + '">' +
                    '<i class="fa fa-map-marker"></i>' +
                    '<span>&nbsp;&nbsp;' + loc["name"] + '</span>' +
                    '</div>';

                $("#loc-list-container").append(badge_html);


            //     //wait complete load
                $("#location_image").on('load', function() {

                    let uid = loc['location_uid'];
                    
                    let loc_html = "<i class='fa fa-map-marker' id='" + uid + "' title='" + loc['name'] + "' class='icon-ap'></i>";

                    let posCal = calculateOriPoint(loc["position_x"], loc["position_y"], ori_width, ori_height, $("#location_image").width(), $("#location_image").height());

                    let top  = loc["position_y"] == null ? "50%" : (posCal[1] + "px");
                    let left = loc["position_x"] == null ? "50%" : (posCal[0] + "px");

                    $(".image-venue-container").append(loc_html);

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
                        drag: function(event, ui) {

                            $("#" + uid).css("cursor", "grabbing");
                        },
                        stop: function(event, ui) {

                            $("#" + uid).css("cursor", "grab");

                            let cur_x = ($(this).css("left")).replace('px', '');
                            let cur_y = ($(this).css("top")).replace('px', '');
                            let cur_img_width = $("#location_image").width();
                            let cur_img_height = $("#location_image").height();

                            let position = calculateOriPoint(cur_x, cur_y, cur_img_width, cur_img_height, ori_width, ori_height);


                            savePosition($(this).attr("id"), position);

                        },

                    });
                    @endif
                });
            });
        });
    }
</script>
@endsection