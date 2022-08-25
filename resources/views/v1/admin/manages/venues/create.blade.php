@extends('layouts.main')

@section('title', 'Manage : Venue -> Create')
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

                    <form method="POST" action="{{ route('admin.manage.venue.store') }}" enctype="multipart/form-data" id="form-data">

                        @csrf

                        <div class="card-header">
                            <h4 class="card-title">Add New Venue Record</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="clearfix"></div>
                                
                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Name <span class="text-danger">* <code>unique</code></span></label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="e.g Synchroweb Technology Sdn Bhd" autocomplete="off" name="name" value="{{ old('name') }}" autofocus="" required="" tabindex="1" />
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
                                                <option value="{{ $location->location_uid }}" {{ old('location') ==  $location->location_uid ? 'selected' : ''  }}>{{ ucfirst($location->name) }}</option>
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
                                        <input class="form-control @error('level') is-invalid @enderror" type="text" placeholder="e.g 3 or LG or B1" autocomplete="off" name="level" value="{{ old('level') }}" autofocus="" required="" tabindex="3" />
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
                                        <label for="name">Map <span class="text-danger"><code>Type: jpg, jpeg, png</code></span></label>
                                        <input class="form-control @error('map') is-invalid @enderror dropify map-upload" type="file" autocomplete="off" name="map" value="{{ old('map') }}" accept="image/*" tabindex="5" data-show-remove="false" />
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

                                <input type="hidden" name="points" id="input_points" value="{{ old('points') }}" class="form-control">

                                <div class="col-md-6 pb-1 pt-1">
                                    <div class="form-group">
                                        <label for="name">Space Length <span class="text-danger"><code>Type: In Meter / Number, Integer</code></span></label>
                                        <input class="form-control @error('space_length') is-invalid @enderror" type="text" autocomplete="off" name="space_length" value="{{ old('space_length') }}" tabindex="6" placeholder="100" />
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
                                        <input type="hidden" name="rssi_min" id="rssi_min" class="@error('rssi_min') is-invalid @enderror" value="{{ old('rssi_min') }}" >
                                        <input type="hidden" name="rssi_max" id="rssi_max" class="@error('rssi_max') is-invalid @enderror" value="{{ old('rssi_max') }}">
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
                                    <h6><text class="text-success">Engaged : </text><span class="rssi_max_val"></span> or higher and minimum Dwell Engaged of <span class="dwell_time_val"></span></h6>

                                </div>


                                <div class="col-md-12"></div>


                                <div class="col-md-6">
                                    <div class="form-group pb-1">
                                        <label for="name" class="pb-1">RSSI [ BLE ]</label>
                                        <div id="rssi_strength_ble" class="my-1 mb-3 mt-2"></div>
                                        <input type="hidden" name="rssi_min_ble" id="rssi_min_ble" class="@error('rssi_min_ble') is-invalid @enderror" value="{{ old('rssi_min_ble') }}" >
                                        <input type="hidden" name="rssi_max_ble" id="rssi_max_ble" class="@error('rssi_max_ble') is-invalid @enderror" value="{{ old('rssi_max_ble') }}">
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
                                    <h6><text class="text-success">Engaged : </text><span class="rssi_max_val_ble"></span> or higher and minimum Dwell Engaged of <span class="dwell_time_val"></span></h6>

                                </div>


                                <div class="col-md-12"></div>



                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name"  class="pb-1">Dwell Engaged (Minutes)</label>
                                        <div id="dwell_time" class="xslider my-1 mb-3 mt-2"></div>
                                        <input type="hidden" name="dwell_time" id="" class="@error('dwell_time') is-invalid @enderror" value="{{ old('dwell_time') }}">
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
                                    <input id="submit-hidden" type="submit" style="display: none" />
                                    <button type="button" class="btn btn-primary mr-1 waves-effect waves-float waves-light btn-submit">Create</button>
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

    var select = '',
        allData,
        mode,
        current_width,
        current_height,
        ori_width,
        ori_height,
        edit_mode = false,
        current_venue;



    $(document).ready(function() {


        var old_rssi_passby = {!! json_encode(old('rssi_min', -70)) !!}
        var old_rssi_enter  = {!! json_encode(old('rssi_max', -50)) !!}


        var old_rssi_passby_ble = {!! json_encode(old('rssi_min_ble', -60)) !!}
        var old_rssi_enter_ble  = {!! json_encode(old('rssi_max_ble', -40)) !!}


        
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


        $('.btn-submit').on('click', function(e){
            e.preventDefault();

            if (!$("#form-data")[0].checkValidity()) {
                // If the form is invalid, submit it. The form won't actually submit;
                // this will just cause the browser to display the native HTML5 error messages.
                $("#form-data").find("#submit-hidden").click();
                return false;
            }

            $('#input_points').val(calculatePoints(points, current_width, current_height, ori_width, ori_height).join());
            
            $('#form-data').trigger('submit');
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