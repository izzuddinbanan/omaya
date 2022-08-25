@extends('layouts.main')

@section('title', 'Enter &  Exit')

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
                                    <label for="name">Venue</label>
                                    <select class="select2 form-control venue" name="venue" required>
                                        <option value="">Please Select</option>
                                        @foreach($venues as $venue)
                                            <option value="{{ $venue->venue_uid }}" >{{$venue->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Time Interval</label>
                                    <select class="select2 form-control venue" name="venue" required>
                                        <option value="">Please Select</option>
                                        <option value="Hourly">Hourly</option>
                                        <option value="Daily">Daily</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 pb-1">
                                <div class="form-group">
                                    <label for="name">Report Date</label>
                                    <input class="form-control flatpickr-range report_date" type="text" name="report_date" value="" required />
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
    <section id="chart">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="chart1"></div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Entry</th>
                                        <th>Exit</th>
                                        <th>Bounce Rate</th>
                                        <th>Percent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Mon, 8 February 2022</td>
                                        <td>10</td>
                                        <td>11</td>
                                        <td>3</td>
                                        <td>114%</td>
                                    </tr>
                                    <tr>
                                        <td>Mon, 9 February 2022</td>
                                        <td>41</td>
                                        <td>30</td>
                                        <td>11</td>
                                        <td>70%</td>
                                    </tr>
                                    <tr>
                                        <td>Mon, 10 February 2022</td>
                                        <td>35</td>
                                        <td>35</td>
                                        <td>2</td>
                                        <td>100%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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

    $('.flatpickr-range').flatpickr({
        mode: 'range'
    });

    var options = {
            series: [{
            name: "Enter",
            data: [10, 41, 35, 51, 49, 62, 56]
        },{
            name: "Exit",
            data: [11, 30, 35, 50, 48, 63, 54]
        }],
            chart: {
            height: 350,
            type: 'line',
            zoom: {
            enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        colors: ['#16c8f1','#7367f0'],
        stroke: {
            curve: 'straight'
        },
        grid: {
            row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
            },
        },
        markers: {
            size: 4,
        },
        xaxis: {
            categories: ['8/2', '9/2', '10/2', '11/2', '12/2', '13/2', '14/2'],
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart1"), options);
    chart.render();

    $('.btn-show').on('click', function(){

        if($('.venue').val() == ''){
            $('.venue').focus();
            return
        }
        
        if($('.report_date').val() == ''){
            $('.report_date').focus();
            return
        }

        var detail_report = $('.detail_report').val();
        if(detail_report != '') $('#report-header').html(detail_report);

        $('#chart').show();
    })

})

</script>


@endsection


