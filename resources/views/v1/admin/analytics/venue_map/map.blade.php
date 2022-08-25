@extends('layouts.main')

@section('title', 'Venue Map')


@section('style')
@endsection

@section('vendor-css')
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/extensions/nouislider.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
@endsection


@section('content')

<style>

    .flatpickr-range[readonly] {
        background-color: #fff;
        opacity: 1;
    }

    .card-body-inner{
        text-align: center;
        /* padding: 10px !important; */
        border: 1px solid;
        border-radius: 14px;
        box-shadow: 4px 3px #000;
    }

    .card-body-inner-total{
        font-size: 20px;
        font-weight: bold;
        padding: 10px;
    }

    .card-body-inner-text{
        padding-bottom: 10px;
    }

    .card-body-inner-footer{
        background-color: #e7e7e7;
        border-top: 1px solid #e1dfdf; 
        border-bottom-left-radius: 14px;    
        border-bottom-right-radius: 14px;
        font-weight: 500;
    }

</style>
<!-- Zero configuration table -->
<div class="content-body">
    <section id="statistic">
        <div class="row">
            <div class="col-md-4 col-4">
                <div class="card">
                    <div class="card-body" style="padding: 0px !important;">
                        <div class="card-body-inner">
                            <div class="card-body-inner-total">123</div>
                            <div class="card-body-inner-text text-primary">UNIQUE VISITOR</div>
                            <div class="card-body-inner-footer p-1"><i data-feather="arrow-down" class="text-danger"></i> 27 % DECREASE YESTERDAY</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-4">
                <div class="card">
                    <div class="card-body" style="padding: 0px !important;">
                        <div class="card-body-inner">
                            <div class="card-body-inner-total">204 Mins</div>
                            <div class="card-body-inner-text text-primary">AVERAGE DWELL TIME</div>
                            <div class="card-body-inner-footer p-1"><i data-feather="arrow-up" class="text-success"></i> 231 % INCREASE YESTERDAY</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-4">
                <div class="card">
                    <div class="card-body" style="padding: 0px !important;">
                        <div class="card-body-inner">
                            <div class="card-body-inner-total">71</div>
                            <div class="card-body-inner-text text-primary">REPEAT VISITOR</div>
                            <div class="card-body-inner-footer p-1"><i data-feather="arrow-down" class="text-danger"></i> 67 % DECREASE YESTERDAY</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Heatmap of [ <i>{{ $venue->location->name }} > {{ $venue->name }}</i> ]</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 pb-1">
                                <div class="form-group">
                                    <label for="name">Date</label>
                                    <input class="form-control flatpickr-range report_date" type="text" name="report_date" value=""  />
                                </div>
                            </div>
                            <div class="col-md-2 pb-1">
                                <div class="form-group">
                                    <label for="name">Time</label>
                                    <input class="form-control" type="text" name="report_date" value="12 AM"  readonly/>
                                </div>
                            </div>
                            <div class="col-md-4 pb-1">
                                <div class="form-group">
                                    <label for="name">Timeline</label>
                                    <div class="progress progress-bar-primary mt-1">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="20" aria-valuemin="20" aria-valuemax="100" style="width: 20%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 pb-1">
                                <button class="btn btn-primary btn-show mt-2">Show</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

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
</div>


@endsection

@section('vendor-js')

@endsection

@section('script')

<script src="{{ url('templates/vuexy/app-assets/js/jquery-ui.js') }}"></script>
<script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>


<script type="text/javascript">


    var original_html   = '<div class="demo-spacing-0"><div class="alert alert-primary" role="alert"><div class="alert-body"> Please select venue to load an image.</div></div></div>';

    var ori_width, ori_height, selected_venue = 0;
    var current_data = {!! json_encode($venue) !!};
    var device_data = {!! json_encode($devices) !!};

    function getIconWifi(uid, title = "", width = "20px", height = "20px") {

        return "<i class='fa fa-wifi' id='"+ uid +"' style='width:"+ width +" !important;height:"+ height +"!important' title='"+ title +"'></i>";

    }

    $(document).ready(function(){

        $('.flatpickr-range').flatpickr();

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
                    let ap_html = "<div  id='"+ uid +"' title='"+ ap['name'] +"' class='icon-ap'> <i class='fa fa-wifi'></i><span class='badge badge-light-dark rounded-pill' style='font-size:14px'>10</span></div>";


                    let posCal  = calculateOriPoint(ap["position_x"], ap["position_y"], ori_width, ori_height, $("#" + selected_venue).width(), $("#" + selected_venue).height());

                    let top     = ap["position_y"] == null ? "50%" : (posCal[1] + "px");
                    let left    = ap["position_x"] == null ? "50%" : (posCal[0] + "px");

                    
                    $(".image-venue-container").append(ap_html);




                    $("#" + uid)
                        .css("position", "absolute")
                        .css("font-size", "18px")
                        .css("color", color_ap)
                        .css("top", top)
                        .css("left", left)
                        .css("width", "50px")
                        .css("height", "50px")
                        .css("background", "-webkit-gradient(linear, left top, left bottom, from(#f30000a6), to(#f3d500c7))")
                        .css("border-radius", "50%")
                        .css("padding", "10px")
                        .css("text-align", "center");

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