<!-- Description -->
@extends('layouts.main')

@section('vendor-css')
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" />
@endsection

@section('title', 'Settings : Filtering')

@section('page-desc', 'Staff and Network equipments filtering')

@section('content')

<style>
    /* CSS */
.button-update {
    background-color: #8f85f3;
    border: 0 solid #E5E7EB;
    box-sizing: border-box;
    color: #ffffff;
    display: flex;
    font-family: ui-sans-serif,system-ui,-apple-system,system-ui,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
    font-size: 1rem;
    font-weight: 700;
    justify-content: center;
    line-height: 1.75rem;
    padding: .75rem 1.65rem;
    position: relative;
    text-align: center;
    text-decoration: none #000000 solid;
    text-decoration-thickness: auto;
    width: 100%;
    max-width: 460px;
    position: relative;
    cursor: pointer;
    transform: rotate(3deg);
    user-select: none;
    -webkit-user-select: none;
    touch-action: manipulation;
}

.button-update:focus {
    outline: 0;
}

.button-update:after {
    content: '';
    position: absolute;
    border: 1px solid #000000;
    bottom: 4px;
    left: 4px;
    width: calc(100% - 1px);
    height: calc(100% - 1px);
}

.button-update:hover:after {
  bottom: 2px;
  left: 2px;
}

@media (min-width: 768px) {
  .button-update {
    padding: .75rem 3rem;
    font-size: 1.25rem;
  }
}

.topcorner{
   position:absolute;
   top:3px;
   right:0;
  }
</style>

<!-- Zero configuration table -->
<div class="content-body">

    <!-- Start Automatic Filtering -->
    <!-- <section id="nav-filled">
        <div class="row match-height">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        
                    <form method="POST" action="{{ route('admin.setting.filtering.update_auto_filter_venue') }}">

                        @csrf

                        <div class="card-header">
                            <h4 class="card-title">Automatic Filtering</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="clearfix"></div>

                                <div class="col-md-4 pb-1">
                                    <div class="form-group">
                                        <label for="name">Venue <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="venue" id="venue">
                                            <option value="All Venue">All Venue</option>
                                            @foreach($venues as $venue)
                                            <option value="{{ $venue->venue_uid}}">{{ $venue->venue_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 pb-1">
                                    <div class="custom-control custom-checkbox mt-2 float-right" >
                                        <input type="checkbox" class="custom-control-input" id="enable-filter" name="filter_status" value="1"/>
                                        <label class="custom-control-label" for="enable-filter">Enable Filter</label>
                                    </div>
                                </div>

                                <div class="col-md-12 pb-1">
                                    <p> Staff devices are all those dwelling more than 
                                        <a href="#" id="staff_dwell_hour" data-type="number">0</a> 
                                        hours per day at least  
                                        <a href="#" id="staff_day" data-type="number">0</a> 
                                        days a week. Network equipment devices are all those dwelling more than 
                                        <a href="#" id="dev_dwell_hour" data-type="number">0</a> 
                                        hours per day at least  <a href="#" id="dev_day" data-type="number">0</a> 
                                        days a week. 

                                        <input type="hidden" id='staff_dwell_hour2' name="staff_dwell_hour"/>
                                        <input type="hidden" id='dev_dwell_hour2' name="dev_dwell_hour"/>
                                        <input type="hidden" id='staff_day2' name="staff_day"/>
                                        <input type="hidden" id='dev_day2' name="dev_day"/>
                                    </p>
                                </div>

                                <div class="col-12">
                                    <badge class="badge badge-info float-left" style="color: black; padding: 15px; display:none" id="dwell_last_update" > Last Update : <span id="date_dwell_last_update"> </span></badge>
                                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light float-right">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    </div>
                </div>
            </div>
        </div>
    </section>   -->
    <!-- End Automatic Filtering -->

    <section id="nav-filled">
        <div class="row match-height">
            <!-- Filled Tabs starts -->
            <div class="col-xl-12 col-lg-12">
                <div class="card" >
                    <div class="card-body">
                        
                    <form method="POST" action="{{ route('admin.setting.filtering.update') }}">

                        @csrf

                        <div class="card-header">
                            <h4 class="card-title">Random MAC Address Filtering</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="clearfix"></div>

                                <div class="col-lg-12 col-md-12 pb-1">
                                    <p>Fortunately it is easy to identify randomized MAC addresses. There is a bit which gets set in the OUI portion of a MAC address to signify a randomized / locally administered address. The quick synopsis is look at the <b>second character</b> in a MAC address, if it is a <code><b>2, 6, A, or E</b></code> it is a randomized address.</p>
                                </div>

                               
                                <div class="col-lg-12 col-md-12 pb-1">
                                    <div class="row custom-options-checkable">
                                        <div class="col-md-3">
                                            <input class="custom-option-item-check" type="radio" name="is_filter_mac_random" id="customOptionsCheckableRadios1" value="1" {{ $tenant->is_filter_mac_random == 1 ? 'checked' : '' }} />
                                            <label class="custom-option-item p-1" for="customOptionsCheckableRadios1" style="text-align: center; height: 150px;">
                                                <b class="d-block pb-1" style="padding: 8%;font-size:14px">Enable Randomized Filtering</b>
                                                <small class="d-block">We will count only valid MAC Address based on logic</small>
                                            </label>
                                        </div>

                                        <div class="col-md-3">
                                            <input class="custom-option-item-check" type="radio" name="is_filter_mac_random" id="customOptionsCheckableRadios2" value="0" {{ $tenant->is_filter_mac_random == 0 ? 'checked' : '' }}/>
                                            <label class="custom-option-item p-1" for="customOptionsCheckableRadios2"  style="text-align: center;  height: 150px;">
                                                <b class="d-block pb-1" style="padding: 8%;font-size:14px">Disable Randomized Filtering</b>
                                                <small class="d-block">We Will Count All Mac Address</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light pull-right">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    </div>
                </div>
            </div>
            <!-- Filled Tabs ends -->
        </div>
    </section>
    <!-- End OUI Filtering -->

    <!-- Start OUI Filtering -->
    <section id="nav-filled">
        <div class="row match-height">
            <!-- Filled Tabs starts -->
            <div class="col-xl-12 col-lg-12">
                <div class="card" >

                    @if(session('role') == 'superuser')
                    <div class="topcorner">
                        <button class="button-update btn-update-list" role="button">Update List</button>
                    </div>
                    @endif

                    <div class="card-body">
                        
                    <form method="POST" action="{{ route('admin.setting.filtering.update') }}">

                        @csrf


                        <div class="card-header">
                            <h4 class="card-title">OUI Filtering</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="clearfix"></div>

                                <div class="col-lg-12 col-md-12 pb-1">
                                    <p>An OUI (Organizationally Unique Identifier) is a 24-bit number that uniquely identifies a vendor or manufacturer. 
                                        They are purchased and assigned by the IEEE. 
                                        The OUI is basically the first three octets of a MAC address.</p>
                                </div>

                               
                                <div class="col-lg-12 col-md-12 pb-1">
                                    <div class="row custom-options-checkable">
                                        <div class="col-md-3">
                                            <input class="custom-option-item-check" type="radio" name="is_filter_oui" id="customOptionsCheckableRadios8" value="1" {{ $tenant->is_filter_oui == 1 ? 'checked' : '' }} />
                                            <label class="custom-option-item p-1" for="customOptionsCheckableRadios8" style="text-align: center; height: 150px;">
                                                <b class="d-block pb-1" style="padding: 8%;font-size:14px">Enable OUI Filtering</b>
                                                <small class="d-block">We will count only valid MAC Address base on OUI Database</small>
                                            </label>
                                        </div>

                                        <div class="col-md-3">
                                            <input class="custom-option-item-check" type="radio" name="is_filter_oui" id="customOptionsCheckableRadios9" value="0" {{ $tenant->is_filter_oui == 0 ? 'checked' : '' }}/>
                                            <label class="custom-option-item p-1" for="customOptionsCheckableRadios9"  style="text-align: center;  height: 150px;">
                                                <b class="d-block pb-1" style="padding: 8%;font-size:14px">Disable OUI Filtering</b>
                                                <small class="d-block">We Will Count All Mac Address</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light pull-right">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    </div>
                </div>
            </div>
            <!-- Filled Tabs ends -->
        </div>
    </section>
    <!-- End OUI Filtering -->

    <!-- Start Random MAC Address Filtering -->
    <!-- <section id="nav-filled">
        <div class="row match-height">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        
                    <form method="POST" action="{{ route('admin.setting.filtering.update') }}">

                        @csrf


                        <div class="card-header">
                            <h4 class="card-title">Random MAC Address Filtering</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="clearfix"></div>

                                <div class="col-lg-12 col-md-6 pb-1">
                                    <p>MAC address randomization is a privacy technique whereby mobile devices rotate through 
                                        random hardware addresses in order to prevent observers from singling out their traffic 
                                        or physical location from other nearby devices.</p>
                                </div>

                                <div class="col-lg-12 col-md-6 pb-1">
                                    <div class="row custom-options-checkable">
                                        <div class="col-md-3">
                                            <input class="custom-option-item-check" type="radio" name="mac_random_status" id="customOptionsCheckableRadios3" value="1" {{ $tenant->mac_random_status == 1 ? 'checked' : '' }} />
                                            <label class="custom-option-item p-1" for="customOptionsCheckableRadios3" style="text-align: center; height: 150px;">
                                                <b class="d-block pb-1" style="padding: 8%;font-size:14px">Enable Filtering</b>
                                                <small class="d-block">We will exclude Random MAC Address from All Calculation</small>
                                            </label>
                                        </div>

                                        <div class="col-md-3">
                                            <input class="custom-option-item-check" type="radio" name="mac_random_status" id="customOptionsCheckableRadios4" value="0" {{ $tenant->mac_random_status == 0 ? 'checked' : '' }}/>
                                            <label class="custom-option-item p-1" for="customOptionsCheckableRadios4"  style="text-align: center;  height: 150px;">
                                                <b class="d-block pb-1" style="padding: 8%;font-size:14px">Disable Filtering</b>
                                                <small class="d-block">We Will Count All Mac Address</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light float-right">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    </div>
                </div>
            </div>
        </div>
    </section> -->
    <!-- End Random MAC Address Filtering -->

    <!-- Start Dwell Filtering -->
    <section id="nav-filled">
        <div class="row match-height">
            <!-- Filled Tabs starts -->
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        
                    <form method="POST" action="{{ route('admin.setting.filtering.update') }}">

                        @csrf


                        <div class="card-header">
                            <h4 class="card-title">Dwell Filtering</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="clearfix"></div>

                                <div class="col-lg-12 col-md-12  pb-1">
                                    <p>Dwell filtering will remove / ignore device that has dwell time less than <b>
                                    <a href="#" id="dwell_filtering" data-type="number">{{ $tenant->remove_dwell_time }}</a>     
                                    second(s)</b> from calculation.</p>
                                </div>

                                <div class="col-lg-12 col-md-12 pb-1">
                                    <div class="row custom-options-checkable">
                                        <div class="col-md-3">
                                            <input class="custom-option-item-check" type="radio" name="is_filter_dwell_time" id="customOptionsCheckableRadios5" value="1" {{ $tenant->is_filter_dwell_time == 1 ? 'checked' : '' }} />
                                            <label class="custom-option-item p-1" for="customOptionsCheckableRadios5" style="text-align: center; height: 150px;">
                                                <b class="d-block pb-1" style="padding: 8%;font-size:14px">Enable Dwell Filtering</b>
                                                <small class="d-block">We will ignore user with dwell time zero or less than {{ $tenant->remove_dwell_time }} second(s).</small>
                                            </label>
                                        </div>

                                        <div class="col-md-3">
                                            <input class="custom-option-item-check" type="radio" name="is_filter_dwell_time" id="customOptionsCheckableRadios6" value="0" {{ $tenant->is_filter_dwell_time == 0 ? 'checked' : '' }}/>
                                            <label class="custom-option-item p-1" for="customOptionsCheckableRadios6"  style="text-align: center;  height: 150px;">
                                                <b class="d-block pb-1" style="padding: 8%;font-size:14px">Disable Dwell Filtering</b>
                                                <small class="d-block">We count all device</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id='remove_dwell_time1' name="remove_dwell_time"/>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light pull-right">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    </div>
                </div>
            </div>
            <!-- Filled Tabs ends -->
        </div>
    </section>
    <!-- End Dwell Filtering -->


</div>

@endsection

@section('vendor-js')
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

@endsection

@section('script')

<script type="text/javascript">

    $(function(){

        $('.btn-update-list').on('click', function(){

            $(this).html('<span class="fa fa-spinner fa-pulse"></span> &nbsp;Loading...')

            $.ajax({
                url     : "{{route('admin.setting.filtering.ajax_update_oui_list')}}",
                method  : 'POST',
                data    : {'_token': '{{ csrf_token() }}'},
                type    : 'JSON',
                success : function(resp){

                    if(resp['status'] == 'success'){
                        displayMessage("OUI List successfully updated")
                        
                    }
                    else{
                        displayMessage("OUI List Failed", 'danger')
                    }

                    $('.btn-update-list').html('Update List')
                   

                }

            });

        })


        //START EDITABLE
        $.fn.editable.defaults.mode = 'inline';
        $.fn.editableform.buttons =
        '<button type="submit" class="btn btn-primary btn-sm editable-submit">' +
            '<i class="fa fa-fw fa-check"></i>' +
            '</button>' +
        '<button type="button" class="btn btn-warning btn-sm editable-cancel">' +
            '<i class="fa fa-fw fa-times"></i>' +
            '</button>';

       

        // $('#staff_dwell_hour').editable({
        //     title: 'Enter Number of Hours',
        //     tpl:'<input type="number"  min="0" max="20">'
        // });

        // $('#dev_dwell_hour').editable({
        //     title: 'Enter Number of Hours',
        //     tpl:'<input type="number"  min="0" max="20">'
        // });

        // $('#staff_day').editable({
        //     title: 'Enter Number of Days',
        //     tpl:'<input type="number"  min="1" max="7">'
        // }); 

        // $('#dev_day').editable({
        //     title: 'Enter Number of Days',
        //     tpl:'<input type="number"  min="1" max="7">'
        // });


        // $('#staff_dwell_hour').on('save', function(e, params) {
        //     let valuenew=parseInt(params.newValue)+1;
        //     if(parseInt(params.newValue) >= parseInt(document.getElementById("dev_dwell_hour").innerHTML)){
        //         document.getElementById("dev_dwell_hour").innerHTML=valuenew;
        //         $('#dev_dwell_hour').editable('setValue',valuenew);
        //         document.getElementById("dev_dwell_hour2").value=valuenew;
        //         document.getElementById("staff_dwell_hour2").value=parseInt(params.newValue);
        //     }else{
        //         document.getElementById("staff_dwell_hour2").value=parseInt(params.newValue);
        //     }
        // });

        // $('#dev_dwell_hour').on('save', function(e, params) {
        //     let valuenew=parseInt(params.newValue)-1;
        //     if(parseInt(params.newValue) <=parseInt(document.getElementById("staff_dwell_hour").innerHTML)){
        //         document.getElementById("staff_dwell_hour").innerHTML=valuenew;
        //         $('#staff_dwell_hour').editable('setValue',valuenew);
        //         document.getElementById("staff_dwell_hour2").value=valuenew;
        //         document.getElementById("dev_dwell_hour2").value=parseInt(params.newValue);
        //     }else{
        //         document.getElementById("dev_dwell_hour2").value=parseInt(params.newValue);
            
        //     }
        // });

        // $('#staff_day').on('save', function(e, params) {
        //     let valuenew=parseInt(params.newValue);
        //     document.getElementById("staff_day2").value=parseInt(params.newValue);

        // });

        // $('#dev_day').on('save', function(e, params) {
        //     let valuenew=parseInt(params.newValue);
        //     document.getElementById("dev_day2").value=parseInt(params.newValue);

        // });
        //END EDITABLE

        // $('#enable-filter').on('click', function(){
        //     if($(this).is(':checked')){
        //         $('#staff_dwell_hour').editable('option', 'disabled', false);
        //         $('#dev_dwell_hour').editable('option', 'disabled', false);
        //         $('#dev_day').editable('option', 'disabled', false);
        //         $('#staff_day').editable('option', 'disabled', false);
        //     }
        //     else{
        //         $('#staff_dwell_hour').editable('option', 'disabled', true);
        //         $('#dev_dwell_hour').editable('option', 'disabled', true);
        //         $('#dev_day').editable('option', 'disabled', true);
        //         $('#staff_day').editable('option', 'disabled', true);
        //     }
        // });

        // $('#venue').on('change', function(e){
        //     e.preventDefault();

        //     let venue = $(this).val();

        //     if(venue != 'All Venue'){
                
        //         //send ajax to get venue details
        //         $.ajax({
        //             url     : "ajax-auto-filter-venue", // route('admin.setting.filtering.ajax_auto_filter_venue')
        //             method  : 'POST',
        //             data    : {'_token': '{{ csrf_token() }}', 'venue_uid': venue },
        //             type    : 'JSON',
        //             success : function(resp){

        //                 if(resp.filter_status > 0){
                        
        //                     $('#enablefilter').prop('checked', true);
        //                     $('#dwell_last_update').show();
        //                     $('#staff_dwell_hour').editable('option', 'disabled', false);
        //                     $('#dev_dwell_hour').editable('option', 'disabled', false);
        //                     $('#dev_day').editable('option', 'disabled', false);
        //                     $('#staff_day').editable('option', 'disabled', false);

        //                 }else{

        //                     $('#dwell_last_update').hide();
        //                     $('#enablefilter').prop('checked', false);
        //                     $('#staff_dwell_hour').editable('option', 'disabled', true);
        //                     $('#dev_dwell_hour').editable('option', 'disabled', true);
        //                     $('#dev_day').editable('option', 'disabled', true);
        //                     $('#staff_day').editable('option', 'disabled', true);

        //                 }

        //                 document.getElementById("date_dwell_last_update").innerHTML=resp.dwell_last_update;

        //                 document.getElementById("staff_dwell_hour2").value=resp.staff_dwell_hour;
        //                 document.getElementById("staff_dwell_hour").innerHTML=resp.staff_dwell_hour;
        //                 $('#staff_dwell_hour').editable('setValue',resp.staff_dwell_hour);

        //                 document.getElementById("dev_dwell_hour2").value=resp.dev_dwell_hour;
        //                 document.getElementById("dev_dwell_hour").innerHTML=resp.dev_dwell_hour;
        //                 $('#dev_dwell_hour').editable('setValue',resp.dev_dwell_hour);


        //                 document.getElementById("dev_day2").value=resp.dev_day;
        //                 document.getElementById("dev_day").innerHTML=resp.dev_day;
        //                 $('#dev_day').editable('setValue',resp.dev_day);

        //                 document.getElementById("staff_day2").value=resp.staff_day;
        //                 document.getElementById("staff_day").innerHTML=resp.staff_day;
        //                 $('#staff_day').editable('setValue',resp.staff_day);

        //             }

        //         });

        //     }
        // });

        $('#dwell_filtering').editable({
            // title: 'Enter Number of Hours',
            tpl:'<input type="number"  min="1" max="300">'
        }); 

        var hiddenVal = '{{ $tenant->remove_dwell_time }}';
        
        $('#remove_dwell_time1').val(hiddenVal);
        $('#dwell_filtering').on('save', function(e, params) {
            
            let valuenew=parseInt(params.newValue);

            $('#remove_dwell_time1').val(valuenew);
            
            console.log(valuenew);
        });

    });
    
</script>
@endsection