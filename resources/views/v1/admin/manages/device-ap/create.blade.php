<!-- Description -->
@extends('layouts.main')


@section('title', 'Manage : Device [ Access Point ] -> Create')
@section('page-desc', 'Manage your Device [AP] data.')


@section('style')


<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/plugins/forms/pickers/form-flat-pickr.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/plugins/extensions/ext-component-sliders.css') }}">

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

                    <form method="POST" action="{{ route('admin.manage.device-ap.store') }}">

                        @csrf

                        <div class="card-header">
                            <h4 class="card-title">Add New Device</h4>
                        </div>
                        <div class="card-body">


                            <div class="row">

                                <div class="col-md-6 pb-1">
                                    <label class="form-check-label mb-50" for="customSwitch11">Is Active</label>
                                    <div class="form-check form-switch form-check-success">
                                        <input type="checkbox" class="form-check-input" id="customSwitch11" {{ old('is_active', 1) == 1 ? 'checked' : '' }} name="is_active" value="1" />
                                        <label class="form-check-label" for="customSwitch11">
                                            <span class="switch-icon-left"><i data-feather="check"></i></span>
                                            <span class="switch-icon-right"><i data-feather="x"></i></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Name <span class="text-danger">*</span></label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="e.g AP Sushi King Cashier AP" autocomplete="off" name="name" value="{{ old('name') }}" autofocus="" required="" tabindex="1" />
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Mac Address <span class="text-danger">*</span> <code>unique</code></label>
                                        <input class="form-control @error('mac_address') is-invalid @enderror" type="text" placeholder="e.g 04:D6:AA:DD:72:8C" autocomplete="off" name="mac_address" value="{{ old('mac_address') }}" autofocus="" required="" tabindex="2" />
                                        @error('mac_address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- <div  class="col-md-2 pb-1">
                                    <br>
                                    <button type="button" class="btn btn-outline-primary mr-1 waves-effect waves-float waves-light btn-add"><span class="fa fa-plus"></span> Add</button>
                                </div> -->


                            </div>

                            <div class="row append">
                                <!-- <div class="append col-md-12">

                                    <div class="copied row"> 
                                        <div class="col-md-5 pb-1">
                                            <label for="name">Sensor Alias <span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" placeholder="e.g AP 1" name="dev_alias[]" autocomplete="off" value="" tabindex="1" />
                                            @error('dev_alias')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                
                                        <div class="col-md-5 pb-1">
                                            <label for="name">Sensor Tag <span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" placeholder="e.g 58:C1:7A:C7:E7:1C" name="dev_tag[]" autocomplete="off" value="" tabindex="1" />
                                            @error('dev_tag')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 pb-1">
                                            <button type="button" class="btn btn-outline-danger mr-1 mt-2 waves-effect waves-float waves-light btn-remove"><span class="fa fa-trash"></span> Remove</button>
                                        </div>
                                    </div>

                                </div> -->
                            </div>


                            <div class="row">

                                <!-- <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Name <span class="text-danger">*</span></label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="e.g AP Sushi King Cashier AP" autocomplete="off" name="name" value="{{ old('name') }}" autofocus="" required="" tabindex="1" />
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Mac Address <span class="text-danger">*</span></label>
                                        <input class="form-control @error('mac_address') is-invalid @enderror" type="text" placeholder="e.g 04:D6:AA:DD:72:8C" autocomplete="off" name="mac_address" value="{{ old('mac_address') }}" autofocus="" required="" tabindex="2" />
                                        @error('mac_address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div> -->
                            </div>


                            <div class="row">
                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Type <span class="text-danger">*</span></label>
                                        <select class="select2 form-control type" name="type" tabindex="3" required>
                                            <option value="">Please Select</option>
                                            @foreach(config('custom.access_point_type') as $key => $val)
                                                <option value="{{ $key }}" {{ $key == old('type') ? 'selected' : '' }}>{{$val}}</option>
                                            @endforeach
                                        </select>
                                        @error('type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Location <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="location" id="input_location"  required="" tabindex="4">
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
                                        <label for="name">Venue <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="venue" id="input_venue"  required="" tabindex="5">
                                           <option value="">Please Select</option>
                                        </select>
                                        <div>
                                            <div class="@error('venue') is-invalid @enderror"></div>
                                            @error('venue')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Zone</label>
                                        <select class="select2 form-control" name="zone" id="input_zone"  tabindex="5">
                                           <option value="">Please Select</option>
                                        </select>
                                        <div>
                                            <div class="@error('zone') is-invalid @enderror"></div>
                                            @error('zone')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12 pb-1">
                                    <label class="form-check-label mb-50" for="customSwitch10">Enable Default RSSI [ Venue RSSI Setting ]</label>
                                    <div class="form-check form-switch form-check-primary">
                                        <input type="checkbox" class="form-check-input" id="customSwitch10" {{ old('enable', 1) == 1 ? "checked" : "" }} name="enable" value="1" tabindex="6" />
                                        <label class="form-check-label" for="customSwitch10">
                                            <span class="switch-icon-left"><i data-feather="check"></i></span>
                                            <span class="switch-icon-right"><i data-feather="x"></i></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 rssi_field" id="rssi">
                                    <div class="form-group">
                                        <label for="name">RSSI [ WIFI ]  <span class="text-danger">*</span></label>
                                        <div id="rssi_strength" class="my-1 mb-3 mt-2"></div>
                                        <input type="hidden" name="rssi_min" value="{{ old('rssi_min') }}" >
                                        <input type="hidden" name="rssi_enter" value="{{ old('rssi_enter') }}">
                                        @error('rssi_min')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        @error('rssi_enter')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-md-6 rssi_field" id="rssi">
                                    <div class="form-group">
                                        <label for="name">RSSI [ BLE ]  <span class="text-danger">*</span></label>
                                        <div id="rssi_strength_ble" class="my-1 mb-3 mt-2"></div>
                                        <input type="hidden" name="rssi_min_ble" value="{{ old('rssi_min_ble') }}" >
                                        <input type="hidden" name="rssi_enter_ble" value="{{ old('rssi_enter_ble') }}">
                                        @error('rssi_min_ble')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        @error('rssi_enter_ble')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-12 mt-2">
                                    <a href="{{ route('admin.manage.device-ap.index') }}">
                                        <button type="button" class="btn btn-outline-secondary waves-effect">Back</button>
                                    </a>
                                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Create</button>
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

<script type="text/javascript">

    var old_venue = {!! json_encode(old('venue')) !!}
    var old_zone  = {!! json_encode(old('zone')) !!}
    
    $(document).ready(function() {


        onChangeLocation();
        enableDefault();

        var old_rssi_passby = {!! json_encode(old('rssi_min', -70)) !!}
        var old_rssi_enter = {!! json_encode(old('rssi_enter', -50)) !!}


        var old_rssi_passby_ble = {!! json_encode(old('rssi_min_ble', -60)) !!}
        var old_rssi_enter_ble = {!! json_encode(old('rssi_enter_ble', -40)) !!}

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
                    "min": -100,
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
            $("input[name=rssi_enter]").val(Math.round(values[1]));

        });

        /********************************************
        **   END Slider: RSSI / Signal Strength
        ********************************************/



        /********************************************
        **   START Slider: RSSI / Signal Strength
        ********************************************/
        var slider_ble = [];



        slider_ble["rssi"] = document.getElementById('rssi_strength_ble');

        noUiSlider.create(slider_ble["rssi"], {
            start: [old_rssi_passby_ble, old_rssi_enter_ble],
            connect: true,
            behaviour: 'drag',
            range:
                {
                    "min": -100,
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
            $("input[name=rssi_enter_ble]").val(Math.round(values[1]));

        });

        /********************************************
        **   END Slider: RSSI / Signal Strength
        ********************************************/




        $("#input_location").change(function() {


            onChangeLocation();

            $("#input_zone").empty();
            $("#input_zone").append('<option value="">Please Select</option>');

        });

        $("#input_venue").change(function() {


            onChangeVenue();

        });




        $('.btn-add').on('click', function(){

            let divCloned = '<div class="copied row">' +
                                '<div class="col-md-5 pb-1">' +
                                    '<label for="name">Sensor Alias <span class="text-danger">*</span></label>' +
                                    '<input class="form-control" type="text" placeholder="e.g AP 1" name="dev_alias[]" autocomplete="off" value="" tabindex="1" />' +
                                '</div>' +
                        
                                '<div class="col-md-5 pb-1">' +
                                    '<label for="name">Sensor Tag <span class="text-danger">*</span></label>' +
                                    '<input class="form-control" type="text" placeholder="e.g 58:C1:7A:C7:E7:1C" name="dev_tag[]" autocomplete="off" value="" tabindex="1" />' +
                                '</div>' +

                                '<div class="col-md-2 pb-1">' +
                                    '<button type="button" class="btn btn-outline-danger mr-1 mt-2 waves-effect waves-float waves-light btn-remove"><span class="fa fa-trash"></span> Remove</button>' +
                                '</div>' +
                            '</div>';

            $('.append').append(divCloned);

        })        

        $('body').on('click', '.btn-remove',  function(){

            if($('.copied').length > 1){
                $(this).closest('.copied').remove()
            }

        })

        $("#customSwitch10").on('change', function() {

            enableDefault();

        });



    });


    function enableDefault() {

        if ($("#customSwitch10").is(':checked')) {
            $(".rssi_field").hide();
        } else {
            $(".rssi_field").show();
        }

    }


    function onChangeLocation() {


        $.ajax({
            url:"{{ route('admin.info.list-venue') }}",
            type:"POST",
            data:{'location_uid' : $("#input_location").val() },
            success:function(response) {

                $("#input_venue").empty();
                $("#input_venue").append('<option value="">Please Select</option>');
                response.forEach(venue => {
                    
                    $("#input_venue").append('<option value="'+ venue['venue_uid'] +'" '+ (old_venue == venue['venue_uid'] ? 'selected' : '' ) +'>'+ venue['name'] +'</option>')
                })

                onChangeVenue();


            },
            error: function(xhr, status, error) {

                // setTimeout(function() {
                //     location.reload(true);
                // }, 1000);

            }

        });

    }


    function onChangeVenue() {


        $.ajax({
            url:"{{ route('admin.info.list-zone') }}",
            type:"POST",
            data:{'venue_uid' : $("#input_venue").val() },
            success:function(response) {

                $("#input_zone").empty();
                $("#input_zone").append('<option value="">Please Select</option>');
                response.forEach(zone => {
                    
                    $("#input_zone").append('<option value="'+ zone['zone_uid'] +'" '+ (old_zone == zone['zone_uid'] ? 'selected' : '' ) +'>'+ zone['name'] +'</option>')
                })


            },
            error: function(xhr, status, error) {

                // setTimeout(function() {
                //     location.reload(true);
                // }, 1000);

            }

        });

    }
</script>
@endsection