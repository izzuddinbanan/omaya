@extends('layouts.main')

@section('title', 'Analytic : Heatmap')

@section('page-desc', 'Statistics of Heatmap')

@section("vendor-css")

<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/charts/apexcharts.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">

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

                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Location<span class="text-danger">*</span></label>
                                    <select class="select2 form-control" name="location" id="omy_location" required>
                                        <option value="">Please Select</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->location_uid }}">{{$location->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Venue<span class="text-danger">*</span></label>
                                    <select class="select2 form-control" name="venue" id="omy_venue"  required>
                                        <option value="">Please Select</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Zone</label>
                                    <select class="select2 form-control" name="zone" id="omy_zone">
                                        <option value="">Please Select</option>
                                    </select>
                                </div>
                            </div>
                        </div>

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

    <!-- ChartJS section start -->
    <section id="report-section" style="display: none;">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12 text-center">
                    <h3 id="report-header"></h3>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="chart1"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ChartJS section end -->

</div>

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

    var locations = {!! json_encode($locations) !!}
    var min_date = {!! json_encode($min_date) !!}
    min_date = new Date(min_date);

    var chart_destroy = false;
    var chart = "";


    var preload_date = new Date();
    preload_date.setDate(preload_date.getDate() - 5);

    $(document).ready(function() {

        $("#omy_location").change(function() {

            let omy_location = $(this).val();
            $("#omy_venue").empty();
            $("#omy_venue").append('<option value="">Please Select</option>');
            $("#omy_zone").empty();
            $("#omy_zone").append('<option value="">Please Select</option>')

            locations.forEach(function(location) {


;

                if(omy_location == location['location_uid']) {

                    location['venues'].forEach(function(venue) {

                        $("#omy_venue").append('<option value='+ venue['venue_uid'] +'>'+ venue['name'] +'</option>');

                    });

                }

            });


        });


        $("#omy_venue").change(function() {

            let omy_location = $("#omy_location").val();
            let omy_venue    = $("#omy_venue").val();
            if(omy_location == "" || omy_venue == "") return;
            
                $("#omy_zone").empty();
                $("#omy_zone").append('<option value="">Please Select</option>');
            
            locations.forEach(function(location) {



                if(omy_location == location['location_uid']) {

                    location['venues'].forEach(function(venue) {

                        if(omy_venue == venue['venue_uid']) {

                            venue['zones'].forEach(function(zone) {

                                $("#omy_zone").append('<option value='+ zone['zone_uid'] +'>'+ zone['name'] +'</option>');
                            });
                        }

                    });

                }

            });


        });



        $('.flatpickr-range').flatpickr({
            mode: 'range',
            minDate : min_date,
            maxDate : 'today',
            dateFormat: "d-m-Y",
            defaultDate: [(min_date.getTime() > preload_date.getTime() ? min_date : preload_date), 'today']
        });




        $('.btn-show').on('click', function(){

            if($('#omy_location').val() == '') {

                $('#omy_location').focus();
                displayMessage("Please select location.", 'warning')
                return;
            }

            if($('#omy_venue').val() == '') {

                $('#omy_venue').focus();
                displayMessage("Please select venue.", 'warning')
                return;
            }

            if($('#scanner_type').val() == '') {

                $('#scanner_type').focus();
                displayMessage("Please select Scanner Type.", 'warning')
                return;
            }


            if($('#omy_detail').val() == '') {

                $('#omy_detail').focus();
                displayMessage("Please select detail.", 'warning')
                return;
            }


            if($('#omy_report_date').val() == '') {

                $('#omy_report_date').focus();
                displayMessage("Please select report date.", 'warning')
                return;
            }
            $('.loader-spinner').show();


            let location        = $("#omy_location").val();
            let venue           = $("#omy_venue").val();
            let zone            = $("#omy_zone").val();
            let scanner_type    = $("#scanner_type").val();
            let report_date     = $("#omy_report_date").val();


            $.ajax({
                url :"{{ route('admin.analytics.heatmap.data') }}",
                type:'POST',
                data: {
                    'location'      : location,
                    'venue'         : venue,
                    'zone'          : zone,
                    'scanner_type'  : scanner_type,
                    'report_date'   : report_date,
                },
                success:function(response){


                    if (response['status'] === "success") {

                        if(response['data'].length <= 0) {

                            displayMessage("No record Found", "success");

                        }else {


                            let height_chart = response['data']['row_count'] > 10 ? 'auto' : '200';

                            // Number of Weekly <span id="report-header"></span> at typical Hour

                            $("#report-section").show();
                            var options = {
                                series: response['data']['chart'],
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
                                  text: 'HeatMap Chart'
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

                                // var chart = new ApexCharts(document.querySelector("#chart1"), options);
                            if(chart_destroy == false) {


                                chart = new ApexCharts(document.querySelector("#chart1"), options);
                                chart.render();
                                chart_destroy = true;

                            }else {

                                chart.updateOptions(options)

                            }
                        }


                    }else {

                        displayMessage(response['message'], response["status"])

                    }

                    $('.loader-spinner').hide();

                    
                }

        
            })

        });
    });


// function getRandomInt(max) {
//   return Math.floor(Math.random() * max);
// }

// $(function(){
    // $('#chart').hide();
    
    // var options = {
    //         series: [
    //         ],
    //         chart: {
    //         height: 350,
    //         type: 'heatmap',
    //         },
    //         stroke: {
    //         width: 0
    //         },
    //         plotOptions: {
    //         heatmap: {
    //             radius: 0,
    //             enableShades: false,
    //             colorScale: {
    //             ranges: [{
    //                 from: 0,
    //                 to: 20,
    //                 color: '#008FFB',
    //                 name: 'Below 20',
    //                 },
    //                 {
    //                 from: 21,
    //                 to: 40,
    //                 color: '#0035f5'
    //                 },
    //                 {
    //                 from: 41,
    //                 to: 60,
    //                 color: '#04cf0a'
    //                 },
    //                 {
    //                 from: 61,
    //                 to: 80,
    //                 color: '#0ff500'
    //                 },
    //                 {
    //                 from: 81,
    //                 to: 100,
    //                 color: '#80f500'
    //                 },
    //                 {
    //                 from: 101,
    //                 to: 120,
    //                 color: '#00f54b'
    //                 },
    //                 {
    //                 from: 121,
    //                 to: 140,
    //                 color: '#b9f500'
    //                 },
    //                 {
    //                 from: 141,
    //                 to: 160,
    //                 color: '#F5CB00'
    //                 },
    //                 {
    //                 from: 161,
    //                 to: 180,
    //                 color: '#f55a00'
    //                 },
    //                 {
    //                 from: 181,
    //                 to: 200,
    //                 color: '#f50000',
    //                 name: 'More than 180',

    //                 },
    //             ],
    //             },
            
    //         }
    //         },
    //         dataLabels: {
    //         enabled: true,
    //         style: {
    //             colors: ['#fff']
    //         }
    //         },
    //         xaxis: {
    //         type: 'category',
    //         },
            
    //     };

    //     var chart = new ApexCharts(document.querySelector("#chart1"), options);
    //     chart.render();


    // $('.btn-show').on('click', function(){

    //     if($('.venue').val() == ''){
    //         $('.venue').focus();
    //         return
    //     }
        
    //     if($('.detail_report').val() == ''){
    //         $('.detail_report').focus();
    //         return
    //     }
        
    //     if($('.report_date').val() == ''){
    //         $('.report_date').focus();
    //         return
    //     }

    //     var detail_report = $('.detail_report').val();
    //     if(detail_report != '') $('#report-header').html(detail_report);

        // $('#chart').show();
    // })

// })

</script>


@endsection


