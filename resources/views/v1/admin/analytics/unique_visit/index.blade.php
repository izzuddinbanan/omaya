@extends('layouts.main')

@section('title', 'Unique Visit')

@section('page-desc', 'Statistics of Unique Visit')

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

        <div class="col-md-12">
          <div class="card">
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
    // $('#chart').hide();

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
      colors: ["#3F51B5", '#2196F3', '#00e396'],
      series: [{
          name: "Unique Visitor",
          data: [1000, 1120, 1300, 889, 1240, 700, 200]
        },
        {
          name: "Unique Engage",
          data: [368, 423, 538, 200, 349, 500, 309]
        },
        {
          name: "Unique Passby",
          data: [623, 759, 702, 544, 490, 290, 350]
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

        // $('#chart').show();
    })

})

</script>


@endsection


