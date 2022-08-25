@extends('layouts.main')

@section('title', 'Visit')

@section('page-desc', 'Statistics of Visit')

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
                                    <label for="name">Unique Count<span class="text-danger">*</span></label>
                                    <select class="select2 form-control" name="unique_count" id="unique_count" required>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Report Date <span class="text-danger">*</span></label>
                                    <input class="form-control flatpickr-range report_date" type="text" name="report_date" id="omy_report_date" value="" required />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Report Type <span class="text-danger">*</span></label>
                                    <select class="select2 form-control" name="report_type" id="report_type" required>
                                        <option value="daily">Daily</option>
                                        <option value="hourly">Hourly</option>
                                    </select>
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


    <!-- ChartJS section start -->
    <section id="report-section-hourly" style="display: none;">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12 text-center">
                    <h3 id="report-header"></h3>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="chart-visit"></div>
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
<!-- <script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script> -->
<script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
<!-- <script src="{{ url('templates/vuexy/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script> -->
<!-- END: Page JS-->

<script>

    var locations = {!! json_encode($locations) !!}
    var min_date = {!! json_encode($min_date) !!}
    min_date = new Date(min_date);

    var chart_destroy = false;
    var chart_destroy_hourly = false;
    var chart = "";
    var chart_hourly = "";


    var preload_date = new Date();
    preload_date.setDate(preload_date.getDate() - 5);

    $(document).ready(function() {

        $("#omy_location").change(function() {

            let omy_location = $(this).val();


            $("#omy_venue").empty();
            $("#omy_venue").append('<option value="">Please Select</option>');
            $("#omy_zone").empty();
            $("#omy_zone").append('<option value="">Please Select</option>');
            

            locations.forEach(function(location) {

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

            if($('#unique_count').val() == '') {

                $('#unique_count').focus();
                displayMessage("Please select Unique Count.", 'warning')
                return;
            }
            $('.loader-spinner').show();


            $("#report-section").hide();
            $("#report-section-hourly").hide();

            let location        = $("#omy_location").val();
            let venue           = $("#omy_venue").val();
            let zone            = $("#omy_zone").val();
            let scanner_type    = $("#scanner_type").val();
            let report_date     = $("#omy_report_date").val();
            let unique_count    = $("#unique_count").val();
            let report_type     = $("#report_type").val();


            $.ajax({
                url :"{{ route('admin.analytics.visit.data') }}",
                type:'POST',
                data: {
                    'location'      : location,
                    'venue'         : venue,
                    'zone'          : zone,
                    'scanner_type'  : scanner_type,
                    'unique_count'  : unique_count,
                    'report_date'   : report_date,
                    'report_type'   : report_type,
                },
                success:function(response){


                    if (response['status'] === "success") {

                        if(response['data'].length <= 0) {

                            displayMessage("No record Found", "success");

                        }else {

                            if(report_type != "hourly") {


                                $("#report-section").show();

                                var options = {
                                  chart: {
                                    height: 328,
                                    type: 'line',
                                    zoom: {
                                      enabled: false
                                    },
                                    dropShadow: {
                                      enabled: true,
                                      top: 3,
                                      left: 2,
                                      blur: 4,
                                      opacity: 1,
                                    }
                                  },
                                  stroke: {
                                    curve: 'smooth',
                                    width: 2
                                  },
                                  colors: ["#3F51B5", '#2196F3', '#00e396', '#e9168b'],
                                  series: response.data.chart,
                                  
                             
                                  markers: {
                                    size: 6,
                                    strokeWidth: 0,
                                    hover: {
                                      size: 9
                                    }
                                  },
                                  grid: {
                                    show: true,
                                    padding: {
                                      bottom: 0
                                    }
                                  },
                                  labels: response.data.date,
                                  xaxis: {
                                    tooltip: {
                                      enabled: false
                                    }
                                  },
                                  legend: {
                                    position: 'top',
                                    horizontalAlign: 'right',
                                  }
                                }


                                if(chart_destroy == false) {


                                    chart = new ApexCharts(document.querySelector("#chart1"), options);
                                    chart.render();
                                    chart_destroy = true;

                                }else {

                                    chart.updateOptions(options)

                                }


                                // Number of Weekly <span id="report-header"></span> at typical Hour
                            }else {



                                $("#report-section-hourly").show();

                                let height_chart = response['data']['row_count'] > 7 ? 'auto' : '200';


                                let arr_report_type = ["visit"];

                                let options_visit = [];
                                for (let i = 0; i < arr_report_type.length; i++) {


                                    options_visit = {
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

                                if(chart_destroy_hourly == false) {


                                    for (let i = 0; i < arr_report_type.length; i++) {

                                        chart_hourly = new ApexCharts(document.querySelector("#chart-" + arr_report_type[i]), options_visit);

                                        chart_hourly.render();

                                    }



                                    chart_destroy_hourly = true;

                                }else {

                                    for (let i = 0; i < arr_report_type.length; i++) {
                                        chart_hourly.updateOptions(options_visit)
                                    }

                                }



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











$(function(){
    // $('#chart').hide();

    // var chart3 = {
    //   chart: {
    //     height: 328,
    //     type: 'line',
    //     zoom: {
    //       enabled: false
    //     },
    //     dropShadow: {
    //       enabled: true,
    //       top: 3,
    //       left: 2,
    //       blur: 4,
    //       opacity: 1,
    //     }
    //   },
    //   stroke: {
    //     curve: 'smooth',
    //     width: 2
    //   },
    //   colors: ["#3F51B5", '#2196F3', '#00e396', '#e9168b'],
    //   series: [{
    //       name: "Total Device",
    //       data: [1000, 1120, 1300, 1889, 1240, 1700, 1200]
    //     },
    //     {
    //       name: "Engage",
    //       data: [368, 423, 538, 200, 349, 500, 309]
    //     },
    //     {
    //       name: "Passby",
    //       data: [623, 759, 702, 544, 490, 290, 350]
    //     },
    //     {
    //       name: "Visitor",
    //       data: [923, 859, 702, 744, 690, 790, 850]
    //     }
    //   ],
      
 
    //   markers: {
    //     size: 6,
    //     strokeWidth: 0,
    //     hover: {
    //       size: 9
    //     }
    //   },
    //   grid: {
    //     show: true,
    //     padding: {
    //       bottom: 0
    //     }
    //   },
    //   labels: ['09/02', '10/02', '11/02', '12/02', '13/02', '14/02', '15/02'],
    //   xaxis: {
    //     tooltip: {
    //       enabled: false
    //     }
    //   },
    //   legend: {
    //     position: 'top',
    //     horizontalAlign: 'right',
    //   }
    // }

    // var chartLine = new ApexCharts(document.querySelector('#chart3'), chart3);
    // chartLine.render();



    // $('.btn-show').on('click', function(){

    //     if($('.venue').val() == ''){
    //         $('.venue').focus();
    //         return
    //     }
        
    //     if($('.time_interval').val() == ''){
    //         $('.time_interval').focus();
    //         return
    //     }
        
    //     if($('.report_date').val() == ''){
    //         $('.report_date').focus();
    //         return
    //     }

    //     // $('#chart').show();
    // })

})

</script>


@endsection


