@extends('layouts.main')

@section('title', 'Manage : Venue -> Update')
@section('page-desc', 'Manage your venue data.')

@section('style')


<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/plugins/forms/pickers/form-flat-pickr.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/plugins/extensions/ext-component-sliders.css') }}">


<style type="text/css">

    #image-venue-container {
        background-repeat: no-repeat;
        background-position: center;
        position: relative;
        background-size: 100% auto;
        position: relative;
        overflow-x:hidden;
        overflow-y:hidden;
    }
    
    .image-div:hover {

        cursor: pointer !important;


    }


    @error('map')

        .dropify-wrapper {

            border: 1px solid #ea5455 !important;
        }

    @enderror 

    @error('location')

        .select2-container {

            border: 1px solid #ea5455 !important;
        }

    @enderror 


</style>
@endsection

@section('vendor-css')
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/extensions/nouislider.min.css') }}">
@endsection


@section('content')
<!-- Zero configuration table -->
<div class="content-body">
    <section id="input-sizing">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">

                    <form method="POST" action="{{ route('admin.manage.venue.update', [$venue->venue_uid]) }}" enctype="multipart/form-data" id="form-data">

                        @csrf
                        @method('put')

                        <div class="card-header">
                            <h4 class="card-title">Update Venue Record</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="clearfix"></div>
                                
                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Name <span class="text-danger">* <code>unique</code></span></label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="e.g Synchroweb Technology Sdn Bhd" autocomplete="off" name="name" value="{{ old('name', $venue->name) }}" autofocus="" required="" tabindex="1" />
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Location <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="location"  required="" tabindex="2">
                                            @foreach($locations as $location)
                                                <option value="{{ $location->location_uid }}" {{ old('location', $venue->location_uid) ==  $location->location_uid ? 'selected' : ''  }}>{{ ucfirst($location->name) }}</option>
                                            @endforeach
                                        </select>
                                        <div>
                                            <div class="@error('location') is-invalid @enderror"></div>
                                            @error('location')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Level <span class="text-danger">*</span></label>
                                        <input class="form-control @error('level') is-invalid @enderror" type="text" placeholder="e.g 3 or LG or B1" autocomplete="off" name="level" value="{{ old('level', $venue->level) }}" autofocus="" required="" tabindex="3" />
                                        @error('level')
                                            <span class="invalid-feedback" role="alert">

                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12"></div>
                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Map <span class="text-danger">*<code>Type: jpg, jpeg, png</code></span></label>
                                        <input class="form-control @error('map') is-invalid @enderror dropify map-upload" type="file" autocomplete="off" name="map" value="{{ old('map') }}" accept="image/*"  required="" tabindex="6" data-show-remove="false"  @if($venue->image) data-default-file="{{ $venue->image ? $venue->image_url : ""  }}" @endif />
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

                                <div class="col-md-12">
                                    <div class="notes"></div>
                                </div>

                                <div class="col-md-12 image-div">
                                    <div id="image-venue-container"></div>
                                </div>

                                <input type="hidden" name="points" id="input_points" value="{{ old('points', $venue->space_length_point) }}" class="form-control">

                                <div class="col-md-6 pb-1 pt-1">
                                    <div class="form-group">
                                        <label for="name">Space Length <span class="text-danger">*<code>Type: In Meter / Number, Integer</code></span></label>
                                        <input class="form-control @error('space_length') is-invalid @enderror" type="text" autocomplete="off" name="space_length" value="{{ old('space_length', $venue->space_length_meter) }}"  required="" />
                                        @error('space_length')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-md-12"></div>

                                <div class="col-md-6">
                                    <div class="form-group pb-1">
                                        <label for="name" class="pb-1">RSSI [ WIFI ]</label>
                                        <div id="rssi_strength" class="my-1 mb-3 mt-2"></div>
                                        <input type="hidden" name="rssi_min" id="rssi_min" class="@error('rssi_min') is-invalid @enderror" value="{{ old('rssi_min', $venue->rssi_min) }}" >
                                        <input type="hidden" name="rssi_max" id="rssi_max" class="@error('rssi_max') is-invalid @enderror" value="{{ old('rssi_max', $venue->rssi_max) }}">
                                        @error('rssi_min')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        @error('rssi_max')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 pb-1">
                                    
                                    <h6>Note RSSI [ WIFI ] :-</h6>
                                    <h6><text class="text-danger">Ignore : </text><span class="rssi_min_val"></span> or lower</h6>
                                    <h6><text class="text-info">Pass By : </text> Between <span class="rssi_min_val"></span> and <span class="rssi_max_val"></span></h6>
                                    <h6><text class="text-primary">Visitor : </text> Between <span class="rssi_min_val"></span> and <span class="rssi_max_val"></span> and Minimum dwell 1 Minutes</h6>
                                    <h6><text class="text-success">Engaged : </text><span class="rssi_max_val"></span> or higher and minimum Dwell time of <span class="dwell_time_val"></span></h6>

                                </div>


                                <div class="col-md-12"></div>


                                <div class="col-md-6">
                                    <div class="form-group pb-1">
                                        <label for="name" class="pb-1">RSSI [ BLE ]</label>
                                        <div id="rssi_strength_ble" class="my-1 mb-3 mt-2"></div>
                                        <input type="hidden" name="rssi_min_ble" id="rssi_min_ble" class="@error('rssi_min_ble') is-invalid @enderror" value="{{ old('rssi_min_ble', $venue->rssi_min_ble) }}" >
                                        <input type="hidden" name="rssi_max_ble" id="rssi_max_ble" class="@error('rssi_max_ble') is-invalid @enderror" value="{{ old('rssi_max_ble', $venue->rssi_max_ble) }}">
                                        @error('rssi_min_ble')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        @error('rssi_max_ble')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 pb-1">
                                    
                                    <h6>Note RSSI [ BLE ] :-</h6>
                                    <h6><text class="text-danger">Ignore : </text><span class="rssi_min_val_ble"></span> or lower</h6>
                                    <h6><text class="text-info">Pass By : </text> Between <span class="rssi_min_val_ble"></span> and <span class="rssi_max_val_ble"></span></h6>
                                    <h6><text class="text-primary">Visitor : </text> Between <span class="rssi_min_val_ble"></span> and <span class="rssi_max_val_ble"></span> and Minimum dwell 1 Minutes</h6>
                                    <h6><text class="text-success">Engaged : </text><span class="rssi_max_val_ble"></span> or higher and minimum Dwell time of <span class="dwell_time_val"></span></h6>

                                </div>


                                <div class="col-md-12"></div>



                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name"  class="pb-1">Dwell Engaged (Minutes)</label>
                                        <div id="dwell_time" class="xslider my-1 mb-3 mt-2"></div>
                                        <input type="hidden" name="dwell_time" id="" class="@error('dwell_time') is-invalid @enderror" value="{{ old('dwell_time', $venue->dwell_time) }}">
                                        @error('dwell_time')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                
                                <div class="col-12">
                                    <a href="{{ route('admin.manage.venue.index') }}">
                                        <button type="button" class="btn btn-outline-secondary waves-effect">Back</button>
                                    </a>
                                    <button type="button" class="btn btn-primary mr-1 waves-effect waves-float waves-light btn-submit">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            
        </div>
    </section>
    <!-- Input Sizing end -->
   
</div>

@endsection

@section('vendor-js')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ url('templates/vuexy/app-assets/vendors/js/konva/konva.min.js') }}"></script>
    <script src="{{ url('templates/vuexy/app-assets/vendors/js/extensions/wNumb.min.js') }}"></script>
    <script src="{{ url('templates/vuexy/app-assets/vendors/js/extensions/nouislider.min.js') }}"></script>

    <!-- END: Page Vendor JS-->
@endsection

@section('script')


<script src="{{ url('templates/vuexy/app-assets/vendors/js/drawarea/drawarea.js') }}"></script>

<script type="text/javascript">


    var venue           = {!! json_encode($venue) !!}
    var image           = venue["image_url"];
    var sl_points       = venue["space_length_point"];


    var select = '',
        allData,
        mode,
        current_width,
        current_height,
        ori_width,
        ori_height,
        edit_mode = false,
        current_venue;


    function setImage(){

        ori_width       = venue["image_width"];
        ori_height      = venue["image_height"];

        $("#image-venue-container").html("<img draggable='false' src='"+ image +"' class='img image-venue' id='uploaded-image'/><div id='canvas-venue-container'></div>").promise().done(function(){


            $("#uploaded-image").addClass('img-fluid');

            // STARRT KONVA
            setupKonvaElement('canvas-venue-container');
            stage.on("dragstart", function(e) {
                e.target.moveTo(drawLayer);
                layer.draw();
                return false;
            }).on("dragend", function(e){
                drawLayer.draw();
                return false;
            }).on("click", function(e) {
                // if (edit_mode) {
                    e.evt.preventDefault();
                
                    if (e.evt.which === 1) {
                        var pos = this.getPointerPosition();
                        var shape = layer.getIntersection(pos);
                        var temp_shape = drawLayer.getIntersection(pos);
                        if (!shape && !temp_shape) points.splice(points.length, 0, Math.round(pos.x), Math.round(pos.y)); drawLine();
                        if(points.length > 4){
                            // RESET IF MORE THAN TWO POINTS
                            drawLayer.removeChildren().draw();
                            points = [];
                        }
                    }
                // } 
                return false;
                    
            });
            // WAIT LOAD IMAGE
            $("#uploaded-image").on('load', function() {
                current_width   = $("#uploaded-image").width();
                current_height  = $("#uploaded-image").height();
                // MAKE BACKGROUND IMAGE
                $("#image-venue-container")
                .css("background-image", "url('" + image + "')")
                .css("background-repeat", 'no-repeat')
                .css("background-size", 'contain')
                .css("background-position", 'center')
                .css("width", current_width+ 'px')
                .css("height", current_height + 'px')
                .css("border", "2px solid black");
                //remove HTML image
                $("#uploaded-image").remove();
                // RESET KONVA SIZE
                resetKonvaElement(current_width, current_height);
                setTimeout(function(){
                    
                    setupLength()
                    
                }, 500); //end function settimeout
                                
            });
            var notes = '<div class="demo-spacing-0"><div class="alert alert-primary" role="alert"><div class="alert-body"> Please draw space length in image below</div></div></div>';
          
            $('.notes').append(notes);
        });
    }

    function setupLength() {
        points  = calculatePoints(sl_points.split(","), ori_width, ori_height, current_width, current_height);
        color   = venue.color;
        layer.removeChildren().draw();        
        drawLayer.removeChildren().draw();     
        drawZones();
        drawLine();
    }


    $(document).ready(function() {

        var old_rssi_passby = {!! json_encode(old('rssi_min', $venue->rssi_min)) !!}
        var old_rssi_enter  = {!! json_encode(old('rssi_max', $venue->rssi_max)) !!}

        var old_rssi_passby_ble = {!! json_encode(old('rssi_min', $venue->rssi_min_ble)) !!}
        var old_rssi_enter_ble  = {!! json_encode(old('rssi_max', $venue->rssi_max_ble)) !!}

        initFixDragOnMobile();
        
        /********************************************
        **   START Slider: RSSI / Signal Strength
        ********************************************/

        var slider = [];
        slider["rssi"] = document.getElementById('rssi_strength');
        noUiSlider.create(slider["rssi"], {
            start: [old_rssi_passby, old_rssi_enter],
            connect: true,
            behaviour: 'drag',
            range:
                {
                    "min": -128,
                    "max": 0
                },
            tooltips: [wNumb({postfix: " dbm for PassBy", decimals: 0}), wNumb({postfix: " dbm for Enter", decimals: 0})],
            pips: {
                mode: 'range',
                density: 5
            }
        });
        slider["rssi"].noUiSlider.on('update', function (values, handle) {
            $("input[name=rssi_min]").val(Math.round(values[0]));
            $("input[name=rssi_max]").val(Math.round(values[1]));

            $(".rssi_min_val").text(Math.round(values[0]) + " rssi");
            $(".rssi_max_val").text(Math.round(values[1]) + " rssi");

        });
        /********************************************
        **   END Slider: RSSI / Signal Strength
        ********************************************/


        /********************************************
        **   START Slider: RSSI BLE / Signal Strength
        ********************************************/

        var slider_ble = [];
        slider_ble["rssi"] = document.getElementById('rssi_strength_ble');
        noUiSlider.create(slider_ble["rssi"], {
            start: [old_rssi_passby_ble, old_rssi_enter_ble],
            connect: true,
            behaviour: 'drag',
            range:
                {
                    "min": -128,
                    "max": 0
                },
            tooltips: [wNumb({postfix: " dbm for PassBy", decimals: 0}), wNumb({postfix: " dbm for Enter", decimals: 0})],
            pips: {
                mode: 'range',
                density: 5
            }
        });
        slider_ble["rssi"].noUiSlider.on('update', function (values, handle) {
            $("input[name=rssi_min_ble]").val(Math.round(values[0]));
            $("input[name=rssi_max_ble]").val(Math.round(values[1]));

            $(".rssi_min_val_ble").text(Math.round(values[0]) + " rssi");
            $(".rssi_max_val_ble").text(Math.round(values[1]) + " rssi");

        });
        /********************************************
        **   END Slider: RSSI BLE/ Signal Strength
        ********************************************/


        $(".xslider").each(function () {
            let current_slider = $(this)[0];
            let current_id = $(current_slider).prop("id");
            slider[current_id] = current_slider;
            let start_number = $(("input[name=" + current_id.replace('slider_','') + "]")).val();
            if(start_number == "") start_number = 10;
            if (typeof current_id == "string") {
                current_slider_option = {
                    range:
                        {
                            "min": 1,
                            "max": 30
                        },
                    start: start_number,
                    tooltips: [wNumb({postfix: " minutes", decimals: 0})],
                    pips: {
                        mode: 'range',
                        density: 5
                    }
                };
                noUiSlider.create(slider[current_id], current_slider_option);
                current_slider.noUiSlider.on('update', function (values, handle) {
                    $(("input[name=" + current_id.replace('slider_','') + "]")).val(Math.round(values));
                    $(".dwell_time_val").text(Math.round(values) + " minutes");

                });
            }
        });



        if(image != null) setImage();

        $('.btn-submit').on('click', function(e){
            e.preventDefault();
            $('#input_points').val(calculatePoints(points, current_width, current_height, ori_width, ori_height).join());
            
            $('#form-data').trigger('submit');

            return false;
        });
        $('.dropify-clear').on('click', function(){
            $("#image-venue-container").html('').css("border", "0px");
        });
        

        $('.map-upload').change(function() {

            readURL();

        });

        $(document).on('keypress',function(e) {
            if(e.which == 13) {
                e.preventDefault();
                $('.btn-submit').trigger("click")
            }
        });


    });
</script>
@endsection