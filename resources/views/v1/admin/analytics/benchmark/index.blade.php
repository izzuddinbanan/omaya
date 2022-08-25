@extends('layouts.main')

@section('title', 'Analytic : Benchmark')

@section('page-desc', 'Statistics of Benchmark')

@section("vendor-css")

<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/charts/apexcharts.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">


@include('layouts.components.datatables.css')

@endsection

@section('content')

<style>

    .form-control[readonly] {
        background-color: #fff;
        opacity: 1;
    }

    .card-custom{
        border: 2px solid cadetblue !important;
        background: aliceblue !important;
    }

    .heat_table2{
        font-size: 0.75rem;
    }

    .apexcharts-menu-icon{
        display: none;
    }

</style>

<div class="content-body">

    <section id="filter-venues">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">

                

                        <div class="row">
                            
                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Scanner Type<span class="text-danger">*</span></label>
                                    <select class="select2 form-control" name="scanner_type" id="scanner_type" required>
                                        <option value="all">All</option>
                                        <option value="ble">BLE</option>
                                        <option value="wifi">WIFI</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Report Type<span class="text-danger">*</span></label>
                                    <select class="select2 form-control" name="report_type" id="report_type" required>
                                        <option value="all">All</option>
                                        <option value="unique">Unique</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Report Date <span class="text-danger">*</span></label>
                                    <input class="form-control flatpickr-range report_date" type="text" name="report_date" id="omy_report_date" value="" required />
                                </div>
                            </div>
                            <div class="col-md-3 pb-1">
                                <button class="btn btn-primary btn-show mt-2">Show</button>
                            </div>
                        </div>
                            
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Ajax Sourced Server-side -->
    <section id="ajax-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-datatable table-responsive">

                            <table class="datatables-ajax table">
                                <thead>
                                    <tr>
                                        <th>Location</th>
                                        <th>Venue</th>
                                        <th>Zone</th>
                                        <th>Total</th>
                                        <th>Passby</th>
                                        <th>Visit</th>
                                        <th>Engaged</th>
                                        <!-- <th>New</th>
                                        <th>Total Return</th> -->
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- <tr>
                                        <td>Venue 1</td>
                                        <td>Venue 1</td>
                                        <td>Venue 1</td>
                                        <td>90002&nbsp;&nbsp;&nbsp;<span class="badge badge-glow btn-primary"><i class="fa fa-arrow-circle-up text-succes"></i> 10% </span><hr>8888</td>
                                        <td>Venue 1</td>
                                        <td>Venue 1</td>
                                        <td>Venue 1</td>
                                        <td>Venue 1</td>
                                        <td>Venue 1</td>
                                        <td>Venue 1</td>
                                    </tr> -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Location</th>
                                        <th>Venue</th>
                                        <th>Zone</th>
                                        <th>Total</th>
                                        <th>Passby</th>
                                        <th>Visit</th>
                                        <th>Engaged</th>
                                        <!-- <th>New</th>
                                        <th>Total Return</th> -->
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ChartJS section start -->
    <section class="report-section report-total" style="display: none;">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12 text-center">
                    <h3>Hourly Total</h3>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="chart-total"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="report-section report-passby" style="display: none;">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12 text-center">
                    <h3>Hourly Passby</h3>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="chart-passby"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="report-section report-visit" style="display: none;">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12 text-center">
                    <h3>Hourly Visit</h3>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="chart-visit"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="report-section report-engaged" style="display: none;">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12 text-center">
                    <h3>Hourly Engaged</h3>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="chart-engaged"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ChartJS section end -->



    <!-- ChartJS section start -->
    <!-- <section id="report-section" style="display: block;" class="report_section">
        <div class="card">
            <div class="card-header" style="text-align:center;display: block;">
            <h4 class="card-title">Location <i data-feather='arrow-right-circle'></i> Venue <i data-feather='arrow-right-circle'></i> Zone</h4>
            </div>
            <div class="card-body">


                <div id="section_report"></div>

                <div>
                    <div class="row">
                        
                        <div class="col-lg-2 col-6 col-sm-4">
                            <div class="card card-custom">
                                <div class="card-header">
                                    <div>
                                    <h2 class="fw-bolder mb-0" > <text id="h-total-all-active">0</text></h2>
                                        <p class="card-text">Avg Total <br> Device</p>
                                    </div>
                                    <div class="avatar bg-info p-50 m-0">
                                        <div class="avatar-content">
                                            <span>0%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        
                        <div class="col-lg-2 col-6 col-sm-4">
                            <div class="card card-custom">
                                <div class="card-header">
                                    <div>
                                    <h2 class="fw-bolder mb-0" > <text id="h-total-all-active">0</text></h2>
                                        <p class="card-text">Avg Total <br> Device</p>
                                    </div>
                                    <div class="avatar bg-info p-50 m-0">
                                        <div class="avatar-content">
                                            <span>0%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->


    <div class="append_report_section"></div>
    <!-- ChartJS section end -->

</div>

@endsection

@section('vendor-js')
@include('layouts.components.datatables.js')
@endsection


@section('script')

<!-- BEGIN: Page JS-->
<script src="{{ url('templates/vuexy/app-assets/vendors/js/charts/apexcharts.min.js') }}" type="text/javascript"></script>
<!-- <script src="{{ url('templates/vuexy/app-assets/vendors/js/charts/chart.min.js') }}" type="text/javascript"></script> -->
<script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
<script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
<!-- <script src="{{ url('templates/vuexy/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script> -->
<!-- END: Page JS-->
<script>

    var min_date = {!! json_encode($min_date) !!}
    min_date = new Date(min_date);

    var chart_destroy = false;
    var chart  = [];
    // var chart['passby']  = "";
    // var chart['visit']   = "";
    // var chart['engaged'] = "";

    var report_date  = "";
    var scanner_type = "";
    var report_type  = "";

    var preload_date = new Date();
    preload_date.setDate(preload_date.getDate() - 5);

    $(document).ready(function() {

        $('.datatables-ajax').DataTable();


        $('.flatpickr-range').flatpickr({
            mode: 'range',
            minDate : min_date,
            maxDate : 'today',
            dateFormat: "d-m-Y",
            defaultDate: [(min_date.getTime() > preload_date.getTime() ? min_date : preload_date), 'today']
        });




        $('.btn-show').on('click', function(){

    

            if($('#scanner_type').val() == '') {

                $('#scanner_type').focus();
                displayMessage("Please select scanner type.", 'warning')
                return;
            }

            if($('#report_type').val() == '') {

                $('#report_type').focus();
                displayMessage("Please select report type.", 'warning')
                return;
            }

            if($('#omy_report_date').val() == '') {

                $('#omy_report_date').focus();
                displayMessage("Please select report date.", 'warning')
                return;
            }

            $('.loader-spinner').show();

            $(".report-section").hide();

           
            scanner_type    = $("#scanner_type").val();
            report_date     = $("#omy_report_date").val();
            report_type     = $("#report_type").val();

            $(".report_section").remove();

            $.ajax({
                url :"{{ route('admin.analytics.benchmark.data') }}",
                type:'POST',
                data: {
                    'scanner_type'  : scanner_type,
                    'report_date'   : report_date,
                },
                success:function(response){

                    if (response['status'] === "success") {

                        if ($.fn.dataTable.isDataTable('.datatables-ajax')) {

                            $(".datatables-ajax").DataTable().destroy();

                        }

                        report_type = report_type == "all" ? "" : "unique_"


                        let table_str = "";


                        for (let x = 0; x < response['data'].length; x++) {

                            table_str += "<tr>";
                            table_str += "<td>" + response['data'][x]['location_name'] + "</td>";
                            table_str += "<td>" + response['data'][x]['venue_name'] + "</td>";
                            table_str += "<td>" + response['data'][x]['zone_name'] + "</td>";

                            table_str += "<td>" + response['data'][x][report_type + 'total'] + "<hr style='margin-bottom:2px;margin-top:2px;'  width='50%'>" + " <span class='badge badge-glow bg-"+ (response['data'][x]['perc_'+report_type + 'total'] > 0 ? 'success' : 'danger') +"'><i class='fa fa-arrow-circle-"+ (response['data'][x]['perc_'+report_type + 'total'] > 0 ? 'up' : 'down') +" text-succes'></i> "+ response['data'][x]['perc_' + report_type + 'total'] +"%</span> " +"<hr  style='margin-bottom:2px;margin-top:2px;' width='50%'>"+ response['data'][x]['old_'+report_type + 'total'] +  "</td>";

                            table_str += "<td>" + response['data'][x][report_type + 'passby'] + "<hr style='margin-bottom:2px;margin-top:2px;'  width='50%'>" + " <span class='badge badge-glow bg-"+ (response['data'][x]['perc_'+report_type + 'passby'] > 0 ? 'success' : 'danger') +"'><i class='fa fa-arrow-circle-"+ (response['data'][x]['perc_'+report_type + 'passby'] > 0 ? 'up' : 'down') +" text-succes'></i> "+ response['data'][x]['perc_' + report_type + 'passby'] +"%</span> " +"<hr  style='margin-bottom:2px;margin-top:2px;' width='50%'>"+ response['data'][x]['old_'+report_type + 'passby'] +  "</td>";
                            table_str += "<td>" + response['data'][x][report_type + 'visit']+"<hr style='margin-bottom:2px;margin-top:2px;' width='50%'>"+ " <span class='badge badge-glow bg-"+ (response['data'][x]['perc_'+report_type + 'visit'] > 0 ? 'success' : 'danger') +"'><i class='fa fa-arrow-circle-"+ (response['data'][x]['perc_'+report_type + 'visit'] > 0 ? 'up' : 'down') +" text-succes'></i> "+ response['data'][x]['perc_' + report_type + 'visit'] +"%</span> " +"<hr style='margin-bottom:2px;margin-top:2px;' width='50%'>"+ response['data'][x]['old_'+report_type + 'visit'] +  "</td>";
                            table_str += "<td>" + response['data'][x][report_type + 'engaged']+"<hr style='margin-bottom:2px;margin-top:2px;' width='50%'>"+ " <span class='badge badge-glow bg-"+ (response['data'][x]['perc_'+report_type + 'engaged'] > 0 ? 'success' : 'danger') +"'><i class='fa fa-arrow-circle-"+ (response['data'][x]['perc_'+report_type + 'engaged'] > 0 ? 'up' : 'down') +" text-succes'></i> "+ response['data'][x]['perc_' + report_type + 'engaged'] +"%</span> " +"<hr style='margin-bottom:2px;margin-top:2px;' width='50%'>"+ response['data'][x]['old_'+report_type + 'engaged'] +  "</td>";
                            // table_str += "<td>" + response['data'][x]['new_device']+ " <span class='badge badge-glow bg-"+ (response['data'][x]['perc_new_device'] > 0 ? 'success' : 'danger') +"'><i class='fa fa-arrow-circle-"+ (response['data'][x]['perc_new_device'] > 0 ? 'up' : 'down') +" text-succes'></i> "+ response['data'][x]['perc_new_device'] +"%</span> " +"<hr width='50%'>"+ response['data'][x]['old_new_device'] +  "</td>";
                            // table_str += "<td>" + response['data'][x]['return_device']+ " <span class='badge badge-glow bg-"+ (response['data'][x]['perc_return_device'] > 0 ? 'success' : 'danger') +"'><i class='fa fa-arrow-circle-"+ (response['data'][x]['perc_return_device'] > 0 ? 'up' : 'down') +" text-succes'></i> "+ response['data'][x]['perc_return_device'] +"%</span> " +"<hr width='50%'>"+ response['data'][x]['old_return_device'] +  "</td>";

                            table_str += "<td><button class='btn btn-sm btn-primary btn-more-details' data-location='"+ response['data'][x]['location_uid'] +"' data-venue='"+ response['data'][x]['venue_uid'] +"' data-zone='"+ response['data'][x]['zone_uid'] +"' data-bs-toggle='tooltip' title='' data-bs-original-title='More Details'><i class='fa fa-calendar'></i></button></td>";

                            table_str += "</tr>";


                        }

                        $(".datatables-ajax>tbody").html(table_str);


                        $(".datatables-ajax").DataTable();


                        

            //             response['data'].forEach(function(data) {

            //                 if(data['zone_name'] == null) data['zone_name'] = '-';
                            
            //                 let html = 

            // '<section style="display: block;" class="report_section">'+
            //     '<div class="card">'+
            //         '<div class="card-header" style="text-align:center;display: block;">'+
            //         '<h4 class="card-title" data-bs-toggle="tooltip" title="" data-bs-original-title="Location -> Venue -> Zone">'+ data['location_name'] +' <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right-circle"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg> '+ data['venue_name'] +' <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right-circle"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg> '+ data['zone_name'] +'</h4>'+
            //         '<hr width="80%" style="margin: auto !important;">'+
            //         '</div>'+
            //         '<div class="card-body">'+
            //             '<div id="section_report"></div>'+

            //                 '<div>' +
            //                     '<div class="row">'+
            //                         '<div class="col-lg-2 col-6 col-sm-4">'+
            //                             '<div class="card card-custom">'+
            //                                 '<div class="card-header">'+
            //                                     '<div>'+
            //                                     '<h2 class="fw-bolder mb-0" ><text id="now-total-active-'+ data['row_uid'] +'">0</text></h2>'+
            //                                         '<p class="card-text">Avg Total <br> Device</p>'+
            //                                     '</div>'+
            //                                     '<div class="avatar '+ ((data['perc_total'] < 0 ) ? ' bg-danger ' : ' bg-success ') +' p-75 m-0">'+
            //                                         '<div class="avatar-content">'+
            //                                             '<span id="now-perc-total-active-'+ data['row_uid'] +'"></span><span>%</span>'+
            //                                         '</div>'+
            //                                     '</div>'+
            //                                 '</div>'+
            //                             '</div>'+
            //                         '</div>'+
            //                         '<div class="col-lg-2 col-6 col-sm-4">'+
            //                             '<div class="card card-custom">'+
            //                                 '<div class="card-header">'+
            //                                     '<div>'+
            //                                     '<h2 class="fw-bolder mb-0" ><text id="now-total-passby-'+ data['row_uid'] +'">0</text></h2>'+
            //                                         '<p class="card-text">Avg Total <br> Passby</p>'+
            //                                     '</div>'+
            //                                     '<div class="avatar '+ ((data['perc_passby'] < 0 ) ? ' bg-danger ' : ' bg-success ') +' p-75 m-0">'+
            //                                         '<div class="avatar-content">'+
            //                                             '<span id="now-perc-total-passby-'+ data['row_uid'] +'"></span><span>%</span>'+
            //                                         '</div>'+
            //                                     '</div>'+
            //                                 '</div>'+
            //                             '</div>'+
            //                         '</div>'+
            //                         '<div class="col-lg-2 col-6 col-sm-4">'+
            //                             '<div class="card card-custom">'+
            //                                 '<div class="card-header">'+
            //                                     '<div>'+
            //                                     '<h2 class="fw-bolder mb-0" ><text id="now-total-visit-'+ data['row_uid'] +'">0</text></h2>'+
            //                                         '<p class="card-text">Avg Total <br> Visit</p>'+
            //                                     '</div>'+
            //                                     '<div class="avatar '+ ((data['perc_visit'] < 0 ) ? ' bg-danger ' : ' bg-success ') +' p-75 m-0">'+
            //                                         '<div class="avatar-content">'+
            //                                             '<span id="now-perc-total-visit-'+ data['row_uid'] +'"></span><span>%</span>'+
            //                                         '</div>'+
            //                                     '</div>'+
            //                                 '</div>'+
            //                             '</div>'+
            //                         '</div>'+
            //                         '<div class="col-lg-2 col-6 col-sm-4">'+
            //                             '<div class="card card-custom">'+
            //                                 '<div class="card-header">'+
            //                                     '<div>'+
            //                                     '<h2 class="fw-bolder mb-0" ><text id="now-total-engaged-'+ data['row_uid'] +'">0</text></h2>'+
            //                                         '<p class="card-text">Avg Total <br> Engaged</p>'+
            //                                     '</div>'+
            //                                     '<div class="avatar '+ ((data['perc_engaged'] < 0 ) ? ' bg-danger ' : ' bg-success ') +' p-75 m-0">'+
            //                                         '<div class="avatar-content">'+
            //                                             '<span id="now-perc-total-engaged-'+ data['row_uid'] +'"></span><span>%</span>'+
            //                                         '</div>'+
            //                                     '</div>'+
            //                                 '</div>'+
            //                             '</div>'+
            //                         '</div>'+

            //                         '<div class="col-lg-2 col-6 col-sm-4">'+
            //                             '<div class="card card-custom">'+
            //                                 '<div class="card-header">'+
            //                                     '<div>'+
            //                                     '<h2 class="fw-bolder mb-0" ><text id="now-total-new_device-'+ data['row_uid'] +'">0</text></h2>'+
            //                                         '<p class="card-text">Avg Total <br> New</p>'+
            //                                     '</div>'+
            //                                     '<div class="avatar '+ ((data['perc_new_device'] < 0 ) ? ' bg-danger ' : ' bg-success ') +' p-75 m-0">'+
            //                                         '<div class="avatar-content">'+
            //                                             '<span id="now-perc-total-new_device-'+ data['row_uid'] +'"></span><span>%</span>'+
            //                                         '</div>'+
            //                                     '</div>'+
            //                                 '</div>'+
            //                             '</div>'+
            //                         '</div>'+

            //                         '<div class="col-lg-2 col-6 col-sm-4">'+
            //                             '<div class="card card-custom">'+
            //                                 '<div class="card-header">'+
            //                                     '<div>'+
            //                                     '<h2 class="fw-bolder mb-0" ><text id="now-total-return_device-'+ data['row_uid'] +'">0</text></h2>'+
            //                                         '<p class="card-text">Avg Total <br> Return</p>'+
            //                                     '</div>'+
            //                                     '<div class="avatar '+ ((data['perc_return_device'] < 0 ) ? ' bg-danger ' : ' bg-success ') +' p-75 m-0">'+
            //                                         '<div class="avatar-content">'+
            //                                             '<span id="now-perc-total-return_device-'+ data['row_uid'] +'"></span><span>%</span>'+
            //                                         '</div>'+
            //                                     '</div>'+
            //                                 '</div>'+
            //                             '</div>'+
            //                         '</div>'+
            //                     '</div>'+




            //                     '<hr>'+
            //                     '<div class="row">'+
            //                         '<div class="col-lg-2 col-6 col-sm-4">'+
            //                             '<div class="card card-custom">'+
            //                                 '<div class="card-header">'+
            //                                     '<div>'+
            //                                     '<h2 class="fw-bolder mb-0" ><text id="prev-total-active-'+ data['row_uid'] +'">0</text></h2>'+
            //                                         '<p class="card-text">Prev Avg Total <br>Device</p>'+
            //                                     '</div>'+
            //                                 '</div>'+
            //                             '</div>'+
            //                         '</div>'+
            //                         '<div class="col-lg-2 col-6 col-sm-4">'+
            //                             '<div class="card card-custom">'+
            //                                 '<div class="card-header">'+
            //                                     '<div>'+
            //                                     '<h2 class="fw-bolder mb-0" ><text id="prev-total-passby-'+ data['row_uid'] +'">0</text></h2>'+
            //                                         '<p class="card-text">Prev Avg Total <br>Passby </p>'+
            //                                     '</div>'+
            //                                 '</div>'+
            //                             '</div>'+
            //                         '</div>'+
            //                         '<div class="col-lg-2 col-6 col-sm-4">'+
            //                             '<div class="card card-custom">'+
            //                                 '<div class="card-header">'+
            //                                     '<div>'+
            //                                     '<h2 class="fw-bolder mb-0" ><text id="prev-total-visit-'+ data['row_uid'] +'">0</text></h2>'+
            //                                         '<p class="card-text">Prev Avg Total <br>Visit </p>'+
            //                                     '</div>'+
            //                                 '</div>'+
            //                             '</div>'+
            //                         '</div>'+
            //                         '<div class="col-lg-2 col-6 col-sm-4">'+
            //                             '<div class="card card-custom">'+
            //                                 '<div class="card-header">'+
            //                                     '<div>'+
            //                                     '<h2 class="fw-bolder mb-0" ><text id="prev-total-engaged-'+ data['row_uid'] +'">0</text></h2>'+
            //                                         '<p class="card-text">Prev Avg Total <br>Engaged </p>'+
            //                                     '</div>'+
            //                                 '</div>'+
            //                             '</div>'+
            //                         '</div>'+

            //                         '<div class="col-lg-2 col-6 col-sm-4">'+
            //                             '<div class="card card-custom">'+
            //                                 '<div class="card-header">'+
            //                                     '<div>'+
            //                                     '<h2 class="fw-bolder mb-0" ><text id="prev-total-new_device-'+ data['row_uid'] +'">0</text></h2>'+
            //                                         '<p class="card-text">Prev Avg Total <br> New </p>'+
            //                                     '</div>'+
            //                                 '</div>'+
            //                             '</div>'+
            //                         '</div>'+

            //                         '<div class="col-lg-2 col-6 col-sm-4">'+
            //                             '<div class="card card-custom">'+
            //                                 '<div class="card-header">'+
            //                                     '<div>'+
            //                                     '<h2 class="fw-bolder mb-0" ><text id="prev-total-return_device-'+ data['row_uid'] +'">0</text></h2>'+
            //                                         '<p class="card-text">Prev Avg Total <br> Return </p>'+
            //                                     '</div>'+
            //                                 '</div>'+
            //                             '</div>'+
            //                         '</div>'+

            //                     '</div>'+
            //                 '</div>'+
            //             '</div>'+
            //         '</div>'+
            //     '</section>';


            //             $(".append_report_section").append(html)


            //             $("#now-total-active-" + data['row_uid']).html(data[report_type +'total']).counterUp({ delay: 100, time: 1000 });
            //             $("#now-perc-total-active-" + data['row_uid']).html(data['perc_'+ report_type +'total']).counterUp({ delay: 100, time: 1000 });
            //             $("#prev-total-active-" + data['row_uid']).html(data['old_'+ report_type +'total']).counterUp({ delay: 100, time: 1000 });


            //             $("#now-total-passby-" + data['row_uid']).html(data[report_type +'passby']).counterUp({ delay: 100, time: 1000 });
            //             $("#now-perc-total-passby-" + data['row_uid']).html(data['perc_'+ report_type +'passby']).counterUp({ delay: 100, time: 1000 });
            //             $("#prev-total-passby-" + data['row_uid']).html(data['old_'+ report_type +'passby']).counterUp({ delay: 100, time: 1000 });

            //             $("#now-total-visit-" + data['row_uid']).html(data[report_type +'visit']).counterUp({ delay: 100, time: 1000 });
            //             $("#now-perc-total-visit-" + data['row_uid']).html(data['perc_'+ report_type +'visit']).counterUp({ delay: 100, time: 1000 });
            //             $("#prev-total-visit-" + data['row_uid']).html(data['old_'+ report_type +'visit']).counterUp({ delay: 100, time: 1000 });


            //             $("#now-total-engaged-" + data['row_uid']).html(data[report_type +'engaged']).counterUp({ delay: 100, time: 1000 });
            //             $("#now-perc-total-engaged-" + data['row_uid']).html(data['perc_'+ report_type +'engaged']).counterUp({ delay: 100, time: 1000 });
            //             $("#prev-total-engaged-" + data['row_uid']).html(data['old_'+ report_type +'engaged']).counterUp({ delay: 100, time: 1000 });


            //             $("#now-total-new_device-" + data['row_uid']).html(data[report_type +'new_device']).counterUp({ delay: 100, time: 1000 });
            //             $("#now-perc-total-new_device-" + data['row_uid']).html(data['perc_'+ report_type +'new_device']).counterUp({ delay: 100, time: 1000 });
            //             $("#prev-total-new_device-" + data['row_uid']).html(data['old_'+ report_type +'new_device']).counterUp({ delay: 100, time: 1000 });

            //             $("#now-total-return_device-" + data['row_uid']).html(data[report_type +'return_device']).counterUp({ delay: 100, time: 1000 });
            //             $("#now-perc-total-return_device-" + data['row_uid']).html(data['perc_'+ report_type +'return_device']).counterUp({ delay: 100, time: 1000 });
            //             $("#prev-total-return_device-" + data['row_uid']).html(data['old_'+ report_type +'return_device']).counterUp({ delay: 100, time: 1000 });


            //             });





       

                        $('[data-bs-toggle="tooltip"]').tooltip()
                    }else {

                        displayMessage(response['message'], response["status"])

                    }

                    $('.loader-spinner').hide();

                    
                }

        
            })


            

        });

    
   
        $('body').on('click', '.btn-more-details', function(e){

            $('.loader-spinner').show();



            $.ajax({
                url :"{{ route('admin.analytics.benchmark.heatmap') }}",
                type:'POST',
                data: {
                    'report_type'      : report_type,
                    'scanner_type'     : scanner_type,
                    'report_date'      : report_date,
                    'location'         : $(this).data("location"),
                    'venue'            : $(this).data("venue"),
                    'zone'             : $(this).data("zone"),
                },
                success:function(response){

                    

                    if (response['status'] === "success") {

                        if(response['data'].length <= 0) {

                            displayMessage("No record Found", "success");

                        }else {


                            // Number of Weekly <span id="report-header"></span> at typical Hour

                            $(".report-section").show();

                            let height_chart = response['data']['row_count'] > 7 ? 'auto' : '200';


                            let arr_report_type = ["total", "passby", "visit", "engaged"];

                            let options = [];
                            for (let i = 0; i < arr_report_type.length; i++) {


                                options[arr_report_type[i]] = {
                                    series: response['data']['chart'][arr_report_type[i]],
                                    chart: {
                                        height: height_chart,
                                        type: 'heatmap',

                                    },
                                    stroke: {
                                        width: 0
                                    },
                                    plotOptions: {
                                        heatmap: {
                                            radius: 0,
                                            enableShades: false,
                                            colorScale: {
                                                ranges: response['data']['range']
                                            
                                            },
                                        
                                        }
                                    },
                                    title: {
                                      text: 'HeatMap Chart for ' + arr_report_type[i],
                                    },
                                    dataLabels: {
                                        enabled: true,
                                        style: {
                                            colors: ['#fff']
                                        }
                                    },
                                    xaxis: {
                                        type: 'category',
                                    },
                                    
                                };


                            }

                            if(chart_destroy == false) {


                                for (let i = 0; i < arr_report_type.length; i++) {

                                    chart[arr_report_type[i]] = new ApexCharts(document.querySelector("#chart-" + arr_report_type[i]), options[arr_report_type[i]]);
                                    chart[arr_report_type[i]].render();

                                }



                                chart_destroy = true;

                            }else {

                                for (let i = 0; i < arr_report_type.length; i++) {
                                    chart[arr_report_type[i]].updateOptions(options[arr_report_type[i]])
                                }

                            }


                            $([document.documentElement, document.body]).animate({
                                scrollTop: $(".report-total").offset().top - 100
                            }, 100);
                        }


                    }else {

                        displayMessage(response['message'], response["status"])

                    }

                    $('.loader-spinner').hide();

                    
                }

        
            })



        });
    
    });

</script>


@endsection


