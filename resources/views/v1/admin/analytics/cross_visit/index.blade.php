@extends('layouts.main')

@section('title', 'Cross Visit')

@section('page-desc', 'Statistics of Cross Visit')

@section("vendor-css")

<!-- <link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/charts/apexcharts.css') }}"> -->
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
    <section id="report-section" style="display:none;">
        <div class="card">
            <div class="card-body">
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
<!-- <script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script> -->
<script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
<!-- <script src="{{ url('templates/vuexy/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script> -->
<!-- END: Page JS-->

<script>


    var locations = {!! json_encode($locations) !!}
    var min_date = {!! json_encode($min_date) !!}
    min_date = new Date(min_date);


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


            if($('#omy_report_date').val() == '') {

                $('#omy_report_date').focus();
                displayMessage("Please select report date.", 'warning')
                return;
            }
            $('.loader-spinner').show();


            let location        = $("#omy_location").val();
            let venue           = $("#omy_venue").val();
            let report_date     = $("#omy_report_date").val();
            let scanner_type     = $("#scanner_type").val();


            $.ajax({
                url :"{{ route('admin.analytics.cross_visit.data') }}",
                type:'POST',
                data: {
                    'location'      : location,
                    'venue'         : venue,
                    'scanner_type'  : scanner_type,
                    'report_date'   : report_date,
                },
                success:function(response){
                    console.log(response)

                    if (response['status'] === "success") {

                        if(response['data'].length <= 0) {

                            displayMessage("No record Found", "success");

                        }else {


                            // Number of Weekly <span id="report-header"></span> at typical Hour

                            $("#report-section").show();
                            

                            let options = {
                                      series: response['data']['data'],
                                      chart: {
                                      type: 'bar',
                                      height: 350
                                    },
                                    plotOptions: {
                                      bar: {
                                        horizontal: false,
                                        // columnWidth: '55%',
                                        // endingShape: 'rounded'
                                      },
                                    },
                                    dataLabels: {
                                      enabled: false
                                    },
                                    stroke: {
                                      show: true,
                                      width: 2,
                                      colors: ['transparent']
                                    },
                                    xaxis: {
                                      categories: response['data']['date'],
                                    },
                                    fill: {
                                      opacity: 1
                                    },
                                    tooltip: {
                                      y: {
                                        formatter: function (val) {
                                          return  val
                                        }
                                      }
                                    }
                                    };

                                var chart = new ApexCharts(document.querySelector("#chart1"), options);
                                chart.render();
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
    $('#chart').hide();
    $('.show-hourly').hide();



    // var options = {
    //       series: [
    //     {
    //       name: 'Level 5',
    //       data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
    //     }, {
    //       name: 'Level 6',
    //       data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
    //     }, {
    //       name: 'Level 7',
    //       data: [35, 41, 36, 26, 45, 48, 52, 53, 0]
    //     }],
    //       chart: {
    //       type: 'bar',
    //       height: 350
    //     },
    //     plotOptions: {
    //       bar: {
    //         horizontal: false,
    //         // columnWidth: '55%',
    //         // endingShape: 'rounded'
    //       },
    //     },
    //     dataLabels: {
    //       enabled: false
    //     },
    //     stroke: {
    //       show: true,
    //       width: 2,
    //       colors: ['transparent']
    //     },
    //     xaxis: {
    //       categories: ['8/2', '9/2', '10/2', '11/2', '12/2', '13/2', '14/2', '15/2', '16/2'],
    //     },
    //     fill: {
    //       opacity: 1
    //     },
    //     tooltip: {
    //       y: {
    //         formatter: function (val) {
    //           return  val
    //         }
    //       }
    //     }
    //     };

    // var chart = new ApexCharts(document.querySelector("#chart1"), options);
    // chart.render();



    // $('.btn-show').on('click', function(){

    //     if($('.venue').val() == ''){
    //         $('.venue').focus();
    //         return
    //     }
        
    //     if($('.report_date').val() == ''){
    //         $('.report_date').focus();
    //         return
    //     }

    //     var detail_report = $('.detail_report').val();
    //     if(detail_report != '') $('#report-header').html(detail_report);

    //     $('#chart').show();
    // })

    // $('.show-data').on('click', function(){
    //     var options2 = {
    //       series: [{
    //         name: "Venue:",
    //         data: [10, 41, 35, 51, 49, 62, 56]
    //     }],
    //       chart: {
    //       height: 350,
    //       type: 'line',
    //       zoom: {
    //         enabled: false
    //       }
    //     },
    //     dataLabels: {
    //       enabled: false
    //     },
    //     colors: ['#7367f0'],
    //     stroke: {
    //       curve: 'straight'
    //     },
    //     grid: {
    //       row: {
    //         colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
    //         opacity: 0.5
    //       },
    //     },
    //     markers: {
    //         size: 4,
    //     },
    //     xaxis: {
    //       categories: ['8/2', '9/2', '10/2', '11/2', '12/2', '13/2', '14/2'],
    //     }
    //     };

    //     var chart2 = new ApexCharts(document.querySelector("#chart2"), options2);
    //     chart2.render();

    //     $('.show-hourly').show();

    // });

})

</script>


@endsection


