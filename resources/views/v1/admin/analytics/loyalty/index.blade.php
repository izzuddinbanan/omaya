@extends('layouts.main')

@section('title', 'Loyalty & Distribution')

@section('page-desc', 'Statistics of Loyalty & Distribution')

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
                            <div class="col-md-5 pb-1">
                                <div class="form-group">
                                    <label for="name">Venue</label>
                                    <select class="select2 form-control venue" name="venue" required>
                                        <option value="">Please Select</option>
                                        @foreach($venues as $venue)
                                            <option value="{{ $venue->venue_uid }}" >{{$venue->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5 pb-1">
                                <div class="form-group">
                                    <label for="name">Report Date</label>
                                    <input class="form-control flatpickr-range report_date" type="text" name="report_date" value="" required />
                                </div>
                            </div>
                            <div class="col-md-2 pb-1">
                                <button class="btn btn-primary btn-show mt-2">Show</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ChartJS section start -->
    <section id="chart">

      <div class="row">
        <div class="col-md-8">
          <div class="card">
              <div class="card-header">
                <h2>New vs Repeat</h2>
              </div>
              <div class="card-body">
                  <div id="chart1"></div>
              </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card">
              <div class="card-header">
                <h2>Day Since Last Visit</h2>
              </div>
              <div class="card-body">
                  <div id="chart2"></div>
              </div>
          </div>
        </div>

        <div class="col-md-12">
          <div class="card">
              <div class="card-header">
                <h2>Repeat Count</h2>
              </div>
              <div class="card-body">
                  <div id="chart3"></div>
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

$(function(){
    $('#chart').hide();

    var options = {
        series: [{
        name: 'Repeat',
        data: [44, 55, 41, 37, 22, 43, 21]
      }, {
        name: 'New',
        data: [53, 32, 33, 52, 13, 43, 32]
      }],
        chart: {
        type: 'bar',
        height: 350,
        stacked: true,
        // dropShadow: {
        //   enabled: true,
        //   blur: 1,
        //   opacity: 0.25
        // }
      },
      plotOptions: {
        bar: {
          horizontal: true,
          barHeight: '50%',
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
        categories: ['8/2', '9/2', '10/2', '11/2', '12/2', '13/2', '14/2'],
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

    var chart = new ApexCharts(document.querySelector("#chart1"), options);
    chart.render();

    var options_2 = {
        series: [188, 124, 118, 1027],
        chart: {
        width: 380,
        type: 'donut',
      },
      labels: ["1-3 Days", "3 Days -1 Week", "1-3 Weeks", ">3 Weeks"],
      plotOptions: {
        pie: {
          startAngle: -90,
          endAngle: 270
        }
      },
      dataLabels: {
        enabled: false
      },
      fill: {
        type: 'gradient',
      },
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 200
          },
          legend: {
            position: 'bottom'
          }
        }
      }]
    };

    var chart2 = new ApexCharts(document.querySelector("#chart2"), options_2);
    chart2.render();

    var chart3 = {
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
      colors: ["#3F51B5", '#2196F3', '#00e396', '#ff5b73'],
      series: [{
          name: "1 Visits",
          data: [60, 90, 45, 70, 70, 80, 89]
        },
        {
          name: "2 Visits",
          data: [90, 56, 70, 60, 77, 75, 40]
        },
        {
          name: "3-5 Visits",
          data: [100, 120, 130, 140, 150, 160, 170]
        },
        {
          name: "More Than 5 Visits",
          data: [177, 192, 183, 102, 199, 189, 190]
        }
      ],
      
 
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
      labels: ['09/02', '10/02', '11/02', '12/02', '13/02', '14/02', '15/02'],
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

    var chartLine = new ApexCharts(document.querySelector('#chart3'), chart3);
    chartLine.render();


    $('.flatpickr-range').flatpickr({
        mode: 'range'
    });

    $('.btn-show').on('click', function(){

        if($('.venue').val() == ''){
            $('.venue').focus();
            return
        }
        
        if($('.time_interval').val() == ''){
            $('.time_interval').focus();
            return
        }
        
        if($('.report_date').val() == ''){
            $('.report_date').focus();
            return
        }

        $('#chart').show();
    })

})

</script>


@endsection


