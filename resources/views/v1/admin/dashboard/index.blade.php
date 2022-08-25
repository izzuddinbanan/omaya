@extends('layouts.main')

@section('title', 'Dashboard')

@section('page-desc', 'Summary')

@section("vendor-css")

<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/charts/apexcharts.css') }}">

@endsection
@section('content')

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
                                            <option value="{{ $location->location_uid }}"  {{ $location->location_uid == session("dashboard_location") ? "selected" : "" }}>{{$location->name}}</option>
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

                        
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ChartJS section start -->
    <section id="chartjs-chart">
       
        <div class="row">
            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card" style="border: 2px solid #6b5fed;">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="card-header flex-column align-items-start pb-0">
                                <div class="avatar bg-light-primary p-50 m-0">
                                    <div class="avatar-content">
                                        <i data-feather="chevrons-right" class="font-medium-5"></i>
                                    </div>
                                </div>
                                <h2 class="fw-bolder mt-1 passby-counter">0</h2>
                                <p class="card-text"  data-bs-toggle="tooltip" title="" data-bs-original-title="Total of user passby from the venue. The range for passby is set at management module" >Passby</p>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="passby-chart pt-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card" style="border: 2px solid #6b5fed;">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card-header flex-column align-items-start pb-0">
                                <div class="avatar bg-light-primary p-50 m-0">
                                    <div class="avatar-content">
                                        <i data-feather="user-check" class="font-medium-5"></i>
                                    </div>
                                </div>
                                <h2 class="fw-bolder mt-1 visit-counter">0</h2>
                                <p class="card-text" data-bs-toggle="tooltip" title="" data-bs-original-title="Total of user visit the venue. is count when user in the passby range and stay more than 1 minutes">Visit</p>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="visit-chart pt-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card" style="border: 2px solid #6b5fed;">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="card-header flex-column align-items-start pb-0">
                                <div class="avatar bg-light-primary p-50 m-0">
                                    <div class="avatar-content">
                                        <i data-feather="stop-circle" class="font-medium-5"></i>
                                    </div>
                                </div>
                                <h2 class="fw-bolder mt-1 engaged-counter">0</h2>
                                <p class="card-text" data-bs-toggle="tooltip" title=""  data-bs-original-title="Total of user stay at venue. is count when user in the engaged range and stay more than specific time.">Engaged</p>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="engaged-chart pt-2"></div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card" style="border: 2px solid #6b5fed;">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="card-header flex-column align-items-start pb-0">
                                <div class="avatar bg-light-primary p-50 m-0">
                                    <div class="avatar-content">
                                        <i data-feather="users" class="font-medium-5"></i>
                                    </div>
                                </div>
                                <h2 class="fw-bolder mt-1 heatmap-counter">0 </h2>
                                <p class="card-text" data-bs-toggle="tooltip" title="" data-bs-original-title="Total of hourly user in venue">Total {{ session('omaya_type') == "workspace" ? "Staff/Visitor" : "User" }}</p>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="heatmap-chart pt-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card" style="border: 2px solid #6b5fed;">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="card-header flex-column align-items-start pb-0">
                                <div class="avatar bg-light-primary p-50 m-0">
                                    <div class="avatar-content">
                                        <i data-feather="users" class="font-medium-5"></i>
                                    </div>
                                </div>
                                <h2 class="fw-bolder mt-1 new-user-counter">0 </h2>
                                <p class="card-text" data-bs-toggle="tooltip" title="" data-bs-original-title="Total of unique user in venue. Is count when user is first time visit the venue">New {{ session('omaya_type') == "workspace" ? "Staff/Visitor" : "User" }}</p>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="new-user-chart pt-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card" style="border: 2px solid #6b5fed;">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="card-header flex-column align-items-start pb-0">
                                <div class="avatar bg-light-primary p-50 m-0">
                                    <div class="avatar-content">
                                        <i data-feather="alert-triangle" class="font-medium-5"></i>
                                    </div>
                                </div>
                                <h2 class="fw-bolder mt-1 return-user-counter">0</h2>
                                <p class="card-text" data-bs-toggle="tooltip" title="" data-bs-original-title="Total of return user in venue. Is count after user have past record in venue">Return {{ session('omaya_type') == "workspace" ? "Staff/Visitor" : "User" }} </p>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="return-user-chart pt-2"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Area Chart Starts -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-baseline flex-sm-row flex-column">
                        <h4 class="card-title">Hourly {{ session('omaya_type') == "workspace" ? "Staff/Visitor" : "User" }} Report</h4>
                        
                    </div>
                    <div class="card-body">
                        <canvas class="chart1 chartjs" data-height="450"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-baseline flex-sm-row flex-column">
                        <h4 class="card-title">Hourly New and Return {{ session('omaya_type') == "workspace" ? "Staff/Visitor" : "User" }} Report</h4>
                    </div>
                    <div class="card-body">
                        <canvas class="chart2 chartjs" data-height="450"></canvas>
                    </div>
                </div>
            </div>

            {{--<div class="col-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-baseline flex-sm-row flex-column">
                        <h4 class="card-title">Daily New and Return {{ session('omaya_type') == "workspace" ? "Staff/Visitor" : "User" }} Report</h4>
                    </div>
                    <div class="card-body">
                        <canvas class="chart3 chartjs" data-height="315"></canvas>
                        <div class="d-flex justify-content-between mt-3 mb-1">
                            <div class="d-flex align-items-center">
                                <i data-feather="user-check" class="font-medium-2" style="color: #28dac6"></i>
                                <span class="fw-bold ms-75 me-25">Return User</span>
                                <!-- <span>- 80%</span> -->
                            </div>
                            <div>
                                <span>72</span>
                                <!-- <i data-feather="arrow-up" class="text-success"></i> -->
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <div class="d-flex align-items-center">
                                <i data-feather="users" class="font-medium-2 text-warning"></i>
                                <span class="fw-bold ms-75 me-25">New User</span>
                                <!-- <span>- 10%</span> -->
                            </div>
                            <div>
                                <span>115</span>
                                <!-- <i data-feather="arrow-up" class="text-success"></i> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>--}}
        </div>
        <!-- Area Chart Ends -->

    </section>
    <!-- ChartJS section end -->

   

</div>

@endsection


@section('script')

<!-- BEGIN: Page JS-->
<script src="{{ url('templates/vuexy/app-assets/vendors/js/charts/apexcharts.min.js') }}" type="text/javascript"></script>
<script src="{{ url('templates/vuexy/app-assets/vendors/js/charts/chart.min.js') }}" type="text/javascript"></script>
<!-- <script src="{{ url('templates/vuexy/app-assets/js/scripts/pages/dashboard-analytics.js') }}"></script> -->
<!-- <script src="{{ url('templates/vuexy/app-assets/js/scripts/charts/chart-chartjs.js') }}"></script> -->
<!-- END: Page JS-->

<script>

    var text_user = {!! json_encode(session('omaya_type')) !!}

    text_user = text_user == "workspace" ? "Staff/Visitor" : "User";

    var locations = {!! json_encode($locations) !!}

    var default_venue = {!! json_encode(session('dashboard_venue')) !!}
    $(document).ready(function() {

        onChangeLocation();
        $("#omy_location").change(function() {
            onChangeLocation()
        });

        $("#omy_venue").change(function() { onChangeVenue(); });

        

        $('[data-bs-toggle="tooltip"]').tooltip()








    });


    function onChangeLocation() {

        $("#omy_venue").empty();
        $("#omy_venue").append('<option value="">Please Select</option>');
        let omy_location = $("#omy_location").val();
        


        locations.forEach(function(location) {


            if(omy_location == location['location_uid']) {


                location['venues'].forEach(function(venue) {

                    $("#omy_venue").append('<option value='+ venue['venue_uid'] +' '+ (default_venue == venue['venue_uid'] ? 'selected' : '') +'>'+ venue['name'] +'</option>');

                });

            }

        });

        onChangeVenue()

    }


    function onChangeVenue() {


        if($("#omy_venue").val() == "" || $("#omy_location").val() == "") return false;
        $.ajax({
            url :"{{ route('admin.dashboard.data') }}",
            type:'POST',
            data: {
                'location'      : $("#omy_location").val(),
                'venue'         : $("#omy_venue").val(),
            },
            success:function(response){


                if (response['status'] === "success") {

                    // PASSBY CHART
                    //////////////////////
                    var $passbyChart = document.querySelector('.passby-chart');
                    var passbyChartOptions, passbyChart;

                    passbyChartOptions = {
                        chart: {
                          height: 100,
                          type: 'area',
                          toolbar: {
                            show: false
                          },
                          sparkline: {
                            enabled: true
                          },
                          grid: {
                            show: false,
                            padding: {
                              left: 0,
                              right: 0
                            }
                          }
                        },
                        colors: [window.colors.solid.primary],
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          curve: 'smooth',
                          width: 2.5
                        },
                        fill: {
                          type: 'gradient',
                          gradient: {
                            shadeIntensity: 0.9,
                            opacityFrom: 0.7,
                            opacityTo: 0.5,
                            stops: [0, 80, 100]
                          }
                        },
                        series: response['data']['series']['passby'],
                        xaxis: {
                          categories: response['data']['categories']['passby'],
                        },
                        yaxis: [
                          {
                            y: 0,
                            offsetX: 0,
                            offsetY: 0,
                            padding: { left: 0, right: 0 }
                          }
                        ],
                        tooltip: {
                          x: { show: true },
                          y: { show: true },
                        }
                    };

                    passbyChart = new ApexCharts($passbyChart, passbyChartOptions);
                    passbyChart.render();

                    $(".passby-counter").html(response['data']['series']['passby'][0]['data'][response['data']['series']['passby'][0]['data'].length - 1]).counterUp({ delay: 100, time: 1000 });




                    // VISIT CHART
                    //////////////////////
                    var $visitChart = document.querySelector('.visit-chart');
                    var visitChartOptions, visitChart;

                    visitChartOptions = {
                        chart: {
                          height: 100,
                          type: 'area',
                          toolbar: {
                            show: false
                          },
                          sparkline: {
                            enabled: true
                          },
                          grid: {
                            show: false,
                            padding: {
                              left: 0,
                              right: 0
                            }
                          }
                        },
                        colors: [window.colors.solid.primary],
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          curve: 'smooth',
                          width: 2.5
                        },
                        fill: {
                          type: 'gradient',
                          gradient: {
                            shadeIntensity: 0.9,
                            opacityFrom: 0.7,
                            opacityTo: 0.5,
                            stops: [0, 80, 100]
                          }
                        },
                        series: response['data']['series']['visit'],
                        xaxis: {
                          categories: response['data']['categories']['visit'],
                        },
                        yaxis: [
                          {
                            y: 0,
                            offsetX: 0,
                            offsetY: 0,
                            padding: { left: 0, right: 0 }
                          }
                        ],
                        tooltip: {
                          x: { show: true },
                          y: { show: true },
                        }
                    };

                    visitChart = new ApexCharts($visitChart, visitChartOptions);
                    visitChart.render();

                    $(".visit-counter").html(response['data']['series']['visit'][0]['data'][response['data']['series']['visit'][0]['data'].length - 1]).counterUp({ delay: 100, time: 1000 });



                    // ENGAGED CHART
                    //////////////////////
                    var $engagedChart = document.querySelector('.engaged-chart');
                    var engagedChartOptions, engagedChart;

                    engagedChartOptions = {
                        chart: {
                          height: 100,
                          type: 'area',
                          toolbar: {
                            show: false
                          },
                          sparkline: {
                            enabled: true
                          },
                          grid: {
                            show: false,
                            padding: {
                              left: 0,
                              right: 0
                            }
                          }
                        },
                        colors: [window.colors.solid.primary],
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          curve: 'smooth',
                          width: 2.5
                        },
                        fill: {
                          type: 'gradient',
                          gradient: {
                            shadeIntensity: 0.9,
                            opacityFrom: 0.7,
                            opacityTo: 0.5,
                            stops: [0, 80, 100]
                          }
                        },
                        series: response['data']['series']['engaged'],
                        xaxis: {
                          categories: response['data']['categories']['engaged'],
                        },
                        yaxis: [
                          {
                            y: 0,
                            offsetX: 0,
                            offsetY: 0,
                            padding: { left: 0, right: 0 }
                          }
                        ],
                        tooltip: {
                          x: { show: true },
                          y: { show: true },
                        }
                    };

                    engagedChart = new ApexCharts($engagedChart, engagedChartOptions);
                    engagedChart.render();

                    $(".engaged-counter").html(response['data']['series']['engaged'][0]['data'][response['data']['series']['engaged'][0]['data'].length - 1]).counterUp({ delay: 100, time: 1000 });



                    // NEW USER CHART
                    //////////////////////
                    var $newChart = document.querySelector('.new-user-chart');
                    var newChartOptions, newChart;

                    newChartOptions = {
                        chart: {
                          height: 100,
                          type: 'area',
                          toolbar: {
                            show: false
                          },
                          sparkline: {
                            enabled: true
                          },
                          grid: {
                            show: false,
                            padding: {
                              left: 0,
                              right: 0
                            }
                          }
                        },
                        colors: [window.colors.solid.primary],
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          curve: 'smooth',
                          width: 2.5
                        },
                        fill: {
                          type: 'gradient',
                          gradient: {
                            shadeIntensity: 0.9,
                            opacityFrom: 0.7,
                            opacityTo: 0.5,
                            stops: [0, 80, 100]
                          }
                        },
                        series: response['data']['series']['new_device'],
                        xaxis: {
                          categories: response['data']['categories']['new_device'],
                        },
                        yaxis: [
                          {
                            y: 0,
                            offsetX: 0,
                            offsetY: 0,
                            padding: { left: 0, right: 0 }
                          }
                        ],
                        tooltip: {
                          x: { show: true },
                          y: { show: true },
                        }
                    };

                    newChart = new ApexCharts($newChart, newChartOptions);
                    newChart.render();

                    $(".new-user-counter").html(response['data']['series']['new_device'][0]['data'][response['data']['series']['new_device'][0]['data'].length - 1]).counterUp({ delay: 100, time: 1000 });



                    // RETURN USER CHART
                    //////////////////////
                    var $returnChart = document.querySelector('.return-user-chart');
                    var returnChartOptions, returnChart;

                    returnChartOptions = {
                        chart: {
                          height: 100,
                          type: 'area',
                          toolbar: {
                            show: false
                          },
                          sparkline: {
                            enabled: true
                          },
                          grid: {
                            show: false,
                            padding: {
                              left: 0,
                              right: 0
                            }
                          }
                        },
                        colors: [window.colors.solid.primary],
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          curve: 'smooth',
                          width: 2.5
                        },
                        fill: {
                          type: 'gradient',
                          gradient: {
                            shadeIntensity: 0.9,
                            opacityFrom: 0.7,
                            opacityTo: 0.5,
                            stops: [0, 80, 100]
                          }
                        },
                        series: response['data']['series']['return_device'],
                        xaxis: {
                          categories: response['data']['categories']['return_device'],
                        },
                        yaxis: [
                          {
                            y: 0,
                            offsetX: 0,
                            offsetY: 0,
                            padding: { left: 0, right: 0 }
                          }
                        ],
                        tooltip: {
                          x: { show: true },
                          y: { show: true },
                        }
                    };

                    returnChart = new ApexCharts($returnChart, returnChartOptions);
                    returnChart.render();

                    $(".return-user-counter").html(response['data']['series']['return_device'][0]['data'][response['data']['series']['return_device'][0]['data'].length - 1]).counterUp({ delay: 100, time: 1000 });



                    // RETURN USER CHART
                    //////////////////////
                    var $heatmapChart = document.querySelector('.heatmap-chart');
                    var heatmapOptions, heatmapChart;

                    heatmapOptions = {
                        chart: {
                          height: 100,
                          type: 'area',
                          toolbar: {
                            show: false
                          },
                          sparkline: {
                            enabled: true
                          },
                          grid: {
                            show: false,
                            padding: {
                              left: 0,
                              right: 0
                            }
                          }
                        },
                        colors: [window.colors.solid.primary],
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          curve: 'smooth',
                          width: 2.5
                        },
                        fill: {
                          type: 'gradient',
                          gradient: {
                            shadeIntensity: 0.9,
                            opacityFrom: 0.7,
                            opacityTo: 0.5,
                            stops: [0, 80, 100]
                          }
                        },
                        series: response['data']['series']['heatmap'],
                        xaxis: {
                          categories: response['data']['categories']['heatmap'],
                        },
                        yaxis: [
                          {
                            y: 0,
                            offsetX: 0,
                            offsetY: 0,
                            padding: { left: 0, right: 0 }
                          }
                        ],
                        tooltip: {
                          x: { show: true },
                          y: { show: true },
                        }
                    };

                    heatmapChart = new ApexCharts($heatmapChart, heatmapOptions);
                    heatmapChart.render();

                    $(".heatmap-counter").html(response['data']['series']['heatmap'][0]['data'][response['data']['series']['heatmap'][0]['data'].length - 1]).counterUp({ delay: 100, time: 1000 });


                    ///////////////////////////

                    // HOURLY CHART
                    // ////////////////////////
                    var chartWrapper = $('.chartjs'),
                        chart1 = $('.chart1'),
                        chart2 = $('.chart2'),
                        chart3 = $('.chart3');

                    // Color Variables
                    var primaryColorShade = '#836AF9',
                        yellowColor = '#ffe800',
                        successColorShade = '#28dac6',
                        warningColorShade = '#ffe802',
                        warningLightColor = '#FDAC34',
                        infoColorShade = '#299AFF',
                        greyColor = '#4F5D70',
                        blueColor = '#2c9aff',
                        blueLightColor = '#84D0FF',
                        greyLightColor = '#EDF1F4',
                        tooltipShadow = 'rgba(0, 0, 0, 0.25)',
                        lineChartPrimary = '#666ee8',
                        lineChartDanger = '#ff4961',
                        labelColor = '#6e6b7b',
                        grid_line_color = 'rgba(200, 200, 200, 0.2)'; // RGBA color helps in dark layout

                        // Detect Dark Layout
                        if ($('html').hasClass('dark-layout')) {
                        labelColor = '#b4b7bd';
                        }

                        // Wrap charts with div of height according to their data-height
                        if (chartWrapper.length) {
                        chartWrapper.each(function () {
                        $(this).wrap($('<div style="height:' + this.getAttribute('data-height') + 'px"></div>'));
                        });
                        }


                    // Line AreaChart
                    // --------------------------------------------------------------------
                    if (chart1.length) {
                        new Chart(chart1, {
                            type: 'line',
                            plugins: [
                            // to add spacing between legends and chart
                            {
                                beforeInit: function (chart) {
                                chart.legend.afterFit = function () {
                                    this.height += 20;
                                };
                                }
                            }
                            ],
                            options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            legend: {
                                position: 'top',
                                align: 'start',
                                labels: {
                                usePointStyle: true,
                                padding: 25,
                                boxWidth: 9
                                }
                            },
                            layout: {
                                padding: {
                                top: -20,
                                bottom: -20,
                                left: -20
                                }
                            },
                            tooltips: {
                                // Updated default tooltip UI
                                shadowOffsetX: 1,
                                shadowOffsetY: 1,
                                shadowBlur: 8,
                                shadowColor: tooltipShadow,
                                backgroundColor: window.colors.solid.white,
                                titleFontColor: window.colors.solid.black,
                                bodyFontColor: window.colors.solid.black
                            },
                            scales: {
                                xAxes: [
                                {
                                    display: true,
                                    gridLines: {
                                    color: 'transparent',
                                    zeroLineColor: grid_line_color
                                    },
                                    scaleLabel: {
                                    display: true
                                    },
                                    ticks: {
                                    fontColor: labelColor
                                    }
                                }
                                ],
                                yAxes: [
                                {
                                    display: true,
                                    gridLines: {
                                    color: 'transparent',
                                    zeroLineColor: grid_line_color
                                    },
                                    ticks: {
                                    stepSize: 100,
                                    min: 0,
                                    fontColor: labelColor
                                    },
                                    scaleLabel: {
                                    display: true
                                    }
                                }
                                ]
                            }
                            },
                            data: {
                                labels: response['data']['categories']['return_device'],
                                datasets: [
                                    {
                                    label: 'Engage',
                                    data: response['data']['series']['engaged'][0]['data'],
                                    lineTension: 0,
                                    backgroundColor: blueColor,
                                    pointStyle: 'circle',
                                    borderColor: 'transparent',
                                    pointRadius: 0.5,
                                    pointHoverRadius: 5,
                                    pointHoverBorderWidth: 5,
                                    pointBorderColor: 'transparent',
                                    pointHoverBackgroundColor: blueColor,
                                    pointHoverBorderColor: window.colors.solid.white
                                    },
                                    {
                                    label: 'Visit',
                                    data: response['data']['series']['visit'][0]['data'],
                                    lineTension: 0,
                                    backgroundColor: blueLightColor,
                                    pointStyle: 'circle',
                                    borderColor: 'transparent',
                                    pointRadius: 0.5,
                                    pointHoverRadius: 5,
                                    pointHoverBorderWidth: 5,
                                    pointBorderColor: 'transparent',
                                    pointHoverBackgroundColor: blueLightColor,
                                    pointHoverBorderColor: window.colors.solid.white
                                    },
                                    {
                                    label: 'Passby',
                                    data: response['data']['series']['passby'][0]['data'],
                                    lineTension: 0,
                                    backgroundColor: greyLightColor,
                                    pointStyle: 'circle',
                                    borderColor: 'transparent',
                                    pointRadius: 0.5,
                                    pointHoverRadius: 5,
                                    pointHoverBorderWidth: 5,
                                    pointBorderColor: 'transparent',
                                    pointHoverBackgroundColor: greyLightColor,
                                    pointHoverBorderColor: window.colors.solid.white
                                    }
                                ]
                            }
                        });
                    }




                    ////////////////////////////////////


                    if (chart2.length) {
                        new Chart(chart2, {
                            type: 'bar',
                            plugins: [
                            // to add spacing between legends and chart
                            {
                                beforeInit: function (chart) {
                                chart.legend.afterFit = function () {
                                    this.height += 20;
                                };
                                }
                            }
                            ],
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                legend: {
                                    position: 'top',
                                    align: 'start',
                                    labels: {
                                    usePointStyle: true,
                                    padding: 25,
                                    boxWidth: 9
                                    }
                                },
                                layout: {
                                    padding: {
                                    top: -20,
                                    bottom: -20,
                                    left: -20
                                    }
                                },
                                tooltips: {
                                    // Updated default tooltip UI
                                    shadowOffsetX: 1,
                                    shadowOffsetY: 1,
                                    shadowBlur: 8,
                                    shadowColor: tooltipShadow,
                                    backgroundColor: window.colors.solid.white,
                                    titleFontColor: window.colors.solid.black,
                                    bodyFontColor: window.colors.solid.black
                                },
                                scales: {
                                    xAxes: [
                                    {
                                        display: true,
                                        gridLines: {
                                        color: 'transparent',
                                        zeroLineColor: grid_line_color
                                        },
                                        scaleLabel: {
                                        display: true
                                        },
                                        ticks: {
                                        fontColor: labelColor
                                        }
                                    }
                                    ],
                                    yAxes: [
                                    {
                                        display: true,
                                        gridLines: {
                                        color: 'transparent',
                                        zeroLineColor: grid_line_color
                                        },
                                        ticks: {
                                        stepSize: 100,
                                        min: 0,
                                        fontColor: labelColor
                                        },
                                        scaleLabel: {
                                        display: true
                                        }
                                    }
                                    ]
                                }
                            },
                            data: {
                                labels: response['data']['categories']['return_device'],
                                datasets: [
                                    {
                                    label: 'Return ' + text_user,
                                    data: response['data']['series']['return_device'][0]['data'],
                                    lineTension: 0,
                                    backgroundColor: '#7367f0',
                                    pointStyle: 'circle',
                                    borderColor: 'transparent',
                                    pointRadius: 0.5,
                                    pointHoverRadius: 5,
                                    pointHoverBorderWidth: 5,
                                    pointBorderColor: 'transparent',
                                    pointHoverBackgroundColor: blueColor,
                                    pointHoverBorderColor: window.colors.solid.white
                                    },
                                    {
                                    label: 'New ' + text_user,
                                    data: response['data']['series']['new_device'][0]['data'],
                                    lineTension: 0,
                                    backgroundColor: '#9a94d3',
                                    pointStyle: 'circle',
                                    borderColor: 'transparent',
                                    pointRadius: 0.5,
                                    pointHoverRadius: 5,
                                    pointHoverBorderWidth: 5,
                                    pointBorderColor: 'transparent',
                                    pointHoverBackgroundColor: blueLightColor,
                                    pointHoverBorderColor: window.colors.solid.white
                                    }
                                ]
                            }
                        });
                    }




                }
                   
            }

    
        })




    }




    $(function(){
        'use strict';

        

        // if (chart2.length) {
        //     new Chart(chart2, {
        //         type: 'bar',
        //         plugins: [
        //         // to add spacing between legends and chart
        //         {
        //             beforeInit: function (chart) {
        //             chart.legend.afterFit = function () {
        //                 this.height += 20;
        //             };
        //             }
        //         }
        //         ],
        //         options: {
        //             responsive: true,
        //             maintainAspectRatio: false,
        //             legend: {
        //                 position: 'top',
        //                 align: 'start',
        //                 labels: {
        //                 usePointStyle: true,
        //                 padding: 25,
        //                 boxWidth: 9
        //                 }
        //             },
        //             layout: {
        //                 padding: {
        //                 top: -20,
        //                 bottom: -20,
        //                 left: -20
        //                 }
        //             },
        //             tooltips: {
        //                 // Updated default tooltip UI
        //                 shadowOffsetX: 1,
        //                 shadowOffsetY: 1,
        //                 shadowBlur: 8,
        //                 shadowColor: tooltipShadow,
        //                 backgroundColor: window.colors.solid.white,
        //                 titleFontColor: window.colors.solid.black,
        //                 bodyFontColor: window.colors.solid.black
        //             },
        //             scales: {
        //                 xAxes: [
        //                 {
        //                     display: true,
        //                     gridLines: {
        //                     color: 'transparent',
        //                     zeroLineColor: grid_line_color
        //                     },
        //                     scaleLabel: {
        //                     display: true
        //                     },
        //                     ticks: {
        //                     fontColor: labelColor
        //                     }
        //                 }
        //                 ],
        //                 yAxes: [
        //                 {
        //                     display: true,
        //                     gridLines: {
        //                     color: 'transparent',
        //                     zeroLineColor: grid_line_color
        //                     },
        //                     ticks: {
        //                     stepSize: 100,
        //                     min: 0,
        //                     max: 400,
        //                     fontColor: labelColor
        //                     },
        //                     scaleLabel: {
        //                     display: true
        //                     }
        //                 }
        //                 ]
        //             }
        //         },
        //         data: {
        //             labels: [
        //                 '12:00 AM',
        //                 '01:00 AM',
        //                 '02:00 AM',
        //                 '03:00 AM',
        //                 '04:00 AM',
        //                 '05:00 AM',
        //                 '06:00 AM',
        //                 '07:00 AM',
        //                 '08:00 AM',
        //                 '09:00 AM',
        //                 '10:00 AM',
        //                 '11:00 AM',
        //                 '12:00 PM',
        //                 '01:00 PM',
        //                 '02:00 PM',
        //                 '03:00 PM',
        //                 '04:00 PM',
        //             ],
        //             datasets: [
        //                 {
        //                 label: 'Return User',
        //                 data: [40, 55, 45, 75, 65, 55, 70, 60, 100, 98, 90, 120, 125, 140, 155, 155, 155],
        //                 lineTension: 0,
        //                 backgroundColor: '#7367f0',
        //                 pointStyle: 'circle',
        //                 borderColor: 'transparent',
        //                 pointRadius: 0.5,
        //                 pointHoverRadius: 5,
        //                 pointHoverBorderWidth: 5,
        //                 pointBorderColor: 'transparent',
        //                 pointHoverBackgroundColor: blueColor,
        //                 pointHoverBorderColor: window.colors.solid.white
        //                 },
        //                 {
        //                 label: 'New User',
        //                 data: [70, 85, 75, 150, 100, 140, 110, 105, 160, 150, 125, 190, 200, 240, 275, 190, 200],
        //                 lineTension: 0,
        //                 backgroundColor: '#9a94d3',
        //                 pointStyle: 'circle',
        //                 borderColor: 'transparent',
        //                 pointRadius: 0.5,
        //                 pointHoverRadius: 5,
        //                 pointHoverBorderWidth: 5,
        //                 pointBorderColor: 'transparent',
        //                 pointHoverBackgroundColor: blueLightColor,
        //                 pointHoverBorderColor: window.colors.solid.white
        //                 }
        //             ]
        //         }
        //     });
        // }

        // if (chart3.length) {
        //     var doughnutExample = new Chart(chart3, {
        //         type: 'doughnut',
        //         options: {
        //             responsive: true,
        //             maintainAspectRatio: false,
        //             responsiveAnimationDuration: 500,
        //             cutoutPercentage: 60,
        //             legend: { display: false },
        //             tooltips: {
        //             callbacks: {
        //                 label: function (tooltipItem, data) {
        //                 var label = data.datasets[0].labels[tooltipItem.index] || '',
        //                     value = data.datasets[0].data[tooltipItem.index];
        //                 var output = ' ' + label + ' : ' + value + ' %';
        //                 return output;
        //                 }
        //             },
        //             // Updated default tooltip UI
        //             shadowOffsetX: 1,
        //             shadowOffsetY: 1,
        //             shadowBlur: 8,
        //             shadowColor: tooltipShadow,
        //             backgroundColor: window.colors.solid.white,
        //             titleFontColor: window.colors.solid.black,
        //             bodyFontColor: window.colors.solid.black
        //             }
        //         },
        //         data: {
        //             datasets: [
        //             {
        //                 labels: ['Return User', 'New User'],
        //                 data: [72, 115],
        //                 backgroundColor: [successColorShade, warningLightColor, window.colors.solid.primary],
        //                 borderWidth: 0,
        //                 pointStyle: 'rectRounded'
        //             }
        //             ]
        //         }
        //     });
        // }

        
    });













</script>


@endsection


