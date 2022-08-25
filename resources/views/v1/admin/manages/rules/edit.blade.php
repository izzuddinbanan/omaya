<!-- Description -->
@extends('layouts.main')

@section('title', 'Manage : Rule -> Update')
@section('page-desc', 'Manage your rule data.')


@section('vendor-css')
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/plugins/forms/pickers/form-flat-pickr.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/plugins/forms/pickers/form-pickadate.css') }}">
@endsection


@section('content')
<!-- Zero configuration table -->
<div class="content-body">
    <section id="input-sizing">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">

                    <form method="POST" action="{{ route('admin.manage.rule.update', [$rule->rule_uid]) }}">

                        @csrf
                        @method('put')
                        <div class="card-header">
                            <h4 class="card-title">Update Rule Record</h4>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-6 pb-1">
                                    <label class="form-check-label mb-50" for="customSwitch11">Is Active</label>
                                    <div class="form-check form-switch form-check-success">
                                        <input type="checkbox" class="form-check-input" id="customSwitch11" {{ old('is_active', $rule->is_active) == 1 ? 'checked' : '' }} name="is_active" value="1"/>
                                        <label class="form-check-label" for="customSwitch11">
                                            <span class="switch-icon-left"><i data-feather="check"></i></span>
                                            <span class="switch-icon-right"><i data-feather="x"></i></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Name <span class="text-danger">* <code>unique</code></span></label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="e.g Button Click" autocomplete="off" name="name" value="{{ old('name', $rule->name) }}" autofocus="" required="" tabindex="1" @if($rule->is_default) disabled @endif/>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Type <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="type" id="rule_type"  required="" tabindex="2" {{ $rule->is_default ? 'disabled' : '' }}>
                                            <option value="">Please Select</option>
                                            @foreach(config('custom.rules.type') as $key => $val)
                                                <option value="{{ $key }}" {{ old('type', $rule->type) ==  $key ? 'selected' : ''  }}>{{ ucfirst($val) }}</option>
                                            @endforeach
                                        </select>
                                        <div>
                                            <div class="@error('type') is-invalid @enderror"></div>
                                            @error('type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Identifier <span class="text-danger">*</span></label>
                                        @if($rule->is_default) 
                                        <input type="text" name="" value="All Device Tracker" disabled class="form-control">
                                        @else
                                        <select class="select2 form-control" name="identifier" id="rule_identifier"  required="" tabindex="">
                                            <option value="">Please Select</option>
                                        </select>
                                        @endif
                                        <div>
                                            <div class="@error('identifier') is-invalid @enderror"></div>
                                            @error('identifier')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                @if($rule->is_default == false)

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Event <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="event" id="rule_event" required="" tabindex="">
                                            <option value="">Please Select</option>
                                        </select>
                                        <div>
                                            <div class="@error('event') is-invalid @enderror"></div>
                                            @error('event')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @endif

             

                            
                            </div>

                            @if($rule->is_default )

                                @if($rule->name != "Button Click")
                                <div class="row">
                                    

                                    <div class="col-md-6 pb-1">
                                        <div class="form-group">
                                            <label for="name">Comparison <span class="text-danger">*</span></label>
                                            <input type="text" name="" value="Less Than" disabled class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6 pb-1">
                                        <div class="form-group">
                                            <label for="name">Value <span class="text-danger">*</span></label>
                                            <input class="form-control @error('value') is-invalid @enderror" type="number" placeholder="e.g 12" autocomplete="off" name="value" value="{{ old('value', $rule->value) }}" autofocus=""/>
                                            @error('value')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                </div>
                                @endif



                            @else

                                <div class="row" id="event_location_fied" style="display: none;">


                                    <div class="col-md-6 pb-1">
                                        <div class="form-group">
                                            <label for="name">Comparison <span class="text-danger">*</span></label>
                                            <select class="select2 form-control" name="comparison" tabindex="">
                                                <option value="">Please Select</option>
                                                @foreach(config('custom.rules.comparison') as $key => $val)
                                                    <option value="{{ $key }}" {{ $key == old('comparison', $rule->comparison) ? 'selected' : '' }}>{{ $val }}</option>
                                                @endforeach
                                            </select>
                                            <div>
                                                <div class="@error('comparison') is-invalid @enderror"></div>
                                                @error('comparison')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 pb-1">
                                        <div class="form-group">
                                            <label for="name">Value <span class="text-danger">*</span></label>
                                            <input class="form-control @error('value') is-invalid @enderror" type="number" placeholder="e.g 12" autocomplete="off" name="value" value="{{ old('value', $rule->value) }}" autofocus=""/>
                                            @error('value')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                </div>

                                <div class="row" id="event_device_fied" style="display: none;">
                                    
                                    <div class="col-md-4 pb-1">
                                        <div class="form-group">
                                            <label for="name">Location <span class="text-danger">*</span></label>
                                            <select class="select2 form-control" name="location" id="rule_location" tabindex="">
                                                <option value="">Please Select</option>
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


                                    <div class="col-md-4 pb-1">
                                        <div class="form-group">
                                            <label for="name">Venue </label>
                                            <select class="select2 form-control" name="venue" id="rule_venue" tabindex="">
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

                                    <div class="col-md-4 pb-1">
                                        <div class="form-group">
                                            <label for="name">Zone </label>
                                            <select class="select2 form-control" name="zone" id="rule_zone" tabindex="">
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

                                </div>




                            @endif

                            

                            <div class="row">
                                

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Start Time <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control flatpickr-time text-start @error('start_time') is-invalid @enderror" placeholder="HH:MM" autocomplete="off" name="start_time" value="{{ old('start_time', $rule->start_time_action) }}" autofocus=""  required="" />
                                        @error('start_time')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">End Time <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control flatpickr-time text-start @error('end_time') is-invalid @enderror" placeholder="HH:MM" autocomplete="off" name="end_time" value="{{ old('end_time', $rule->stop_time_action) }}" autofocus=""  required="" />
                                        @error('end_time')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>



                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Action / Send <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="send[]" id="" tabindex="" multiple required>
                                            @foreach(config('custom.rules.trigger') as $key => $val)
                                                <option value="{{ $key }}" {{ in_array($key, old('send', $rule->action)) ? 'selected' : '' }}>{{ ucfirst($val) }}</option>
                                            @endforeach
                                        </select>
                                        <div>
                                        <div class="@error('send') is-invalid @enderror"></div>
                                            @error('send')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Action / Send To User Role <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="send_to[]" id="" multiple required>
                                            @foreach($data['admin_group'] as $admin)
                                                <option value="{{ $admin->admin_group }}" {{ in_array($admin->admin_group, old('send_to', $rule->send_to_role)) ? 'selected' : '' }}>{{ $admin->admin_group }}</option>
                                            @endforeach
                                        </select>
                                        <div>
                                        <div class="@error('send_to') is-invalid @enderror"></div>
                                            @error('send_to')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Action / Send for Every <span class="text-danger">*</span><code>in second</code></label>
                                        <input class="form-control @error('action_every') is-invalid @enderror" type="text" placeholder="e.g 30" autocomplete="off" name="action_every" value="{{ old('action_every', $rule->action_every) }}" autofocus="" required="" tabindex="1" />
                                        @error('action_every')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                            </div>

                            <div class="col-12">
                                <a href="{{ route('admin.manage.rule.index') }}">
                                    <button type="button" class="btn btn-outline-secondary waves-effect" tabindex="">Back</button>
                                </a>
                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light" tabindex="">Update</button>
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

<script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
<script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script>
<script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/pickadate/picker.time.js') }}"></script>
<script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/pickadate/legacy.js') }}"></script>
<script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
@endsection



@section('script')

<script type="text/javascript">

    var old_identifier = {!! json_encode(old('identifier', $rule->identifier)) !!}
    var old_event      = {!! json_encode(old('event', $rule->event)) !!}
    var old_location   = {!! json_encode(old('location', $rule->location_uid)) !!}
    var old_venue      = {!! json_encode(old('venue', $rule->venue_uid)) !!}
    var old_zone       = {!! json_encode(old('zone', $rule->zone_uid)) !!}

    $(document).ready(function(){

        onChangeType();



        $("#rule_type").change(function(){ onChangeType() });

        $("#rule_location").change(function() { onChangeLocation() });

        $("#rule_venue").change(function() { onChangeVenue() });

        if($('.flatpickr-time').length){
          $('.flatpickr-time').flatpickr({
            enableTime: true,
            noCalendar: true
          });
        }

    });


    function onChangeType() {

        $("#rule_identifier").empty();
        $("#rule_identifier").append('<option value="">Please Select</option>');

        $("#rule_event").empty();
        $("#rule_event").append('<option value="">Please Select</option>');
        $("#rule_location").empty();
        $("#rule_location").append('<option value="">Please Select</option>');


        $("#rule_venue").empty();
        $("#rule_venue").append('<option value="">Please Select</option>');

        $("#rule_zone").empty();
        $("#rule_zone").append('<option value="">Please Select</option>');
        

        let type = $("#rule_type").val();
        if(type == "") return false;

        $.ajax({
            url:"{{ route('admin.manage.rule.ajax.get-type') }}",
            type:"POST",
            data:{'type' : type },
            success:function(response) {
                

                $("#event_device_fied").hide();
                $("#event_location_fied").hide();

                if(response["status"] != "success") {

                    displayMessage(response["message"], response["status"]);
                    return false;

                }else{

                    // $("#rule_identifier").empty();
                    // $("#rule_identifier").append('<option value="">Please Select</option>');

                    response['data']['identifier'].forEach(identifier => {
                        
                        $("#rule_identifier").append('<option value="'+ identifier['uid'] +'" '+ (old_identifier == identifier['uid'] ? 'selected' : '' ) +'>'+ identifier['name'] +'</option>')
                    })


                    // $("#rule_event").empty();
                    // $("#rule_event").append('<option value="">Please Select</option>');

                    for (let [key, value] of Object.entries(response['data']['events'])) {

                        $("#rule_event").append('<option value="'+ key +'" '+ (old_event == key ? 'selected' : '' ) +'>'+ value +'</option>')
                    }
            


                    // $("#rule_location").empty();
                    // $("#rule_location").append('<option value="">Please Select</option>');

                    if(response['data']['locations'].length > 0) {

                        $("#event_device_fied").show();
                        $("#event_location_fied").hide();

                        response['data']['locations'].forEach(location => {
                            
                            $("#rule_location").append('<option value="'+ location['location_uid'] +'" '+ (old_location == location['location_uid'] ? 'selected' : '' ) +'>'+ location['name'] +'</option>')
                        })

                    }else {

                        $("#event_location_fied").show();
                        $("#event_device_fied").hide();

                    }

                    onChangeLocation();


                }



                
            }

        });

    }


    function onChangeLocation() {

        $("#rule_venue").empty();
        $("#rule_venue").append('<option value="">Please Select</option>');

        $.ajax({
            url:"{{ route('admin.manage.rule.ajax.get-venue') }}",
            type:"POST",
            data:{'location_uid' : $("#rule_location").val() },
            success:function(response) {
               

                
                response.forEach(venue => {
                    
                    $("#rule_venue").append('<option value="'+ venue['venue_uid'] +'" '+ (old_venue == venue['venue_uid'] ? 'selected' : '' ) +'>'+ venue['name'] +'</option>')
                })

                onChangeVenue();



            }
        });

    }


    function onChangeVenue() {


        $("#rule_zone").empty();
        $("#rule_zone").append('<option value="">Please Select</option>');
        $.ajax({
            url:"{{ route('admin.manage.rule.ajax.get-zone') }}",
            type:"POST",
            data:{'location_uid' : $("#rule_location").val(), 'venue_uid' : $("#rule_venue").val() },
            success:function(response) {

                
                response.forEach(zone => {
                    
                    $("#rule_zone").append('<option value="'+ zone['zone_uid'] +'" '+ (old_zone == zone['zone_uid'] ? 'selected' : '' ) +'>'+ zone['name'] +'</option>')
                })


            }
        });

    }



</script>
@endsection