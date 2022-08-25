@extends('layouts.main')

@section('title', 'Analytic : Dwell Time')

@section('page-desc', 'Statistics of Dwell Time')

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
                                    <select class="select2 form-control" name="type" id="omy_type" required>
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
<!-- <script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script> -->
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

            if($('#omy_type').val() == '') {

                $('#omy_type').focus();
                displayMessage("Please select type.", 'warning')
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
            let type            = $("#omy_type").val();
            let report_date     = $("#omy_report_date").val();


            $.ajax({
                url :"{{ url('admin/analytic/dwell-time/data') }}",
                type:'POST',
                data: {
                    'location'      : location,
                    'venue'         : venue,
                    'zone'          : zone,
                    'type'          : type,
                    'report_date'   : report_date,
                },
                success:function(response){


                        console.log(response);


                    if (response['status'] === "success") {

                        $("#report-section").show();


                        var options = {
                              series: response.data.chart,
                              chart: {
                              type: 'bar',
                              height: 350,
                              stacked: true,
                              dropShadow: {
                                enabled: true,
                                blur: 1,
                                opacity: 0.25
                              }
                            },
                            plotOptions: {
                              bar: {
                                horizontal: false,
                                barHeight: '60%',
                              },
                            },
                            dataLabels: {
                              enabled: false
                            },
                            stroke: {
                              width: 2,
                            },
                            title: {
                              text: ''
                            },
                            xaxis: {
                              categories: response.data.date,
                            },
                            yaxis: {
                              title: {
                                text: undefined
                              },
                            },
                            tooltip: {
                              shared: false,
                              y: {
                                formatter: function (val) {
                                  return val
                                }
                              }
                            },
                            // fill: {
                            //   type: 'pattern',
                            //   opacity: 1,
                            //   pattern: {
                            //     style: ['circles', 'slantedLines', 'verticalLines', 'horizontalLines', 'circles', 'slantedLines', 'horizontalLines'], // string or array of strings
                            
                            //   }
                            // },
                            states: {
                              hover: {
                                filter: 'none'
                              }
                            },
                            legend: {
                              position: 'right',
                              offsetY: 40
                            }
                            };


                            if(chart_destroy == false) {


                                chart = new ApexCharts(document.querySelector("#chart1"), options);
                                chart.render();
                                chart_destroy = true;

                            }else {

                                chart.updateOptions(options)

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


<script>


    $(document).ready(function() {

        

        // var options = {
        //   series: [{
        //   name: '< 15 Min',
        //   data: [44, 55, 41, 37, 22, 43, 21]
        // }, {
        //   name: '15-30 MIn',
        //   data: [53, 32, 33, 52, 13, 43, 32]
        // }, {
        //   name: '30 Min-1 Hour',
        //   data: [12, 17, 11, 9, 15, 11, 20]
        // }, {
        //   name: '1-2 Hour',
        //   data: [9, 7, 5, 8, 6, 9, 4]
        // },
        // {
        //   name: '2-4 Hour',
        //   data: [9, 7, 5, 8, 6, 9, 4]
        // }, {
        //   name: '4-8 Hour',
        //   data: [9, 7, 5, 8, 6, 9, 4]
        // }, {
        //   name: '> 8 Hour',
        //   data: [9, 7, 5, 8, 6, 9, 4]
        // }],
        //   chart: {
        //   type: 'bar',
        //   height: 350,
        //   stacked: true,
        //   dropShadow: {
        //     enabled: true,
        //     blur: 1,
        //     opacity: 0.25
        //   }
        // },
        // plotOptions: {
        //   bar: {
        //     horizontal: false,
        //     barHeight: '60%',
        //   },
        // },
        // dataLabels: {
        //   enabled: false
        // },
        // stroke: {
        //   width: 2,
        // },
        // title: {
        //   text: ''
        // },
        // xaxis: {
        //   categories: ['8/2asdsd', '9/2', '10/2', '11/2', '12/2', '13/2', '14/2'],
        // },
        // yaxis: {
        //   title: {
        //     text: undefined
        //   },
        // },
        // tooltip: {
        //   shared: false,
        //   y: {
        //     formatter: function (val) {
        //       return val
        //     }
        //   }
        // },
        // // fill: {
        // //   type: 'pattern',
        // //   opacity: 1,
        // //   pattern: {
        // //     style: ['circles', 'slantedLines', 'verticalLines', 'horizontalLines', 'circles', 'slantedLines', 'horizontalLines'], // string or array of strings
        
        // //   }
        // // },
        // states: {
        //   hover: {
        //     filter: 'none'
        //   }
        // },
        // legend: {
        //   position: 'right',
        //   offsetY: 40
        // }
        // };

        // var chart = new ApexCharts(document.querySelector("#chart1"), options);
        // chart.render();

    });


// $(function(){
//     $('#chart').hide();

//     var options = {
//           series: [{
//           name: '< 15 Min',
//           data: [44, 55, 41, 37, 22, 43, 21]
//         }, {
//           name: '15-30 MIn',
//           data: [53, 32, 33, 52, 13, 43, 32]
//         }, {
//           name: '30 Min-1 Hour',
//           data: [12, 17, 11, 9, 15, 11, 20]
//         }, {
//           name: '1-2 Hour',
//           data: [9, 7, 5, 8, 6, 9, 4]
//         },
//         {
//           name: '2-4 Hour',
//           data: [9, 7, 5, 8, 6, 9, 4]
//         }, {
//           name: '4-8 Hour',
//           data: [9, 7, 5, 8, 6, 9, 4]
//         }, {
//           name: '> 8 Hour',
//           data: [9, 7, 5, 8, 6, 9, 4]
//         }],
//           chart: {
//           type: 'bar',
//           height: 350,
//           stacked: true,
//           dropShadow: {
//             enabled: true,
//             blur: 1,
//             opacity: 0.25
//           }
//         },
//         plotOptions: {
//           bar: {
//             horizontal: false,
//             barHeight: '60%',
//           },
//         },
//         dataLabels: {
//           enabled: false
//         },
//         stroke: {
//           width: 2,
//         },
//         title: {
//           text: ''
//         },
//         xaxis: {
//           categories: ['8/2', '9/2', '10/2', '11/2', '12/2', '13/2', '14/2'],
//         },
//         yaxis: {
//           title: {
//             text: undefined
//           },
//         },
//         tooltip: {
//           shared: false,
//           y: {
//             formatter: function (val) {
//               return val
//             }
//           }
//         },
//         fill: {
//           type: 'pattern',
//           opacity: 1,
//           pattern: {
//             style: ['circles', 'slantedLines', 'verticalLines', 'horizontalLines', 'circles', 'slantedLines', 'horizontalLines'], // string or array of strings
        
//           }
//         },
//         states: {
//           hover: {
//             filter: 'none'
//           }
//         },
//         legend: {
//           position: 'right',
//           offsetY: 40
//         }
//         };

//         var chart = new ApexCharts(document.querySelector("#chart1"), options);
//         chart.render();



// })

</script>


@endsection


