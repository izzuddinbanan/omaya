@extends('layouts.main')

@section('title', 'Cross Path')

@section('page-desc', 'Statistics of Cross Path')

@section("vendor-css")

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

    .anychart-credits{
        display: none;
    }

    .apexcharts-menu-icon{
        display: none;
    }

    #chart1, #chart2{
        width: 100%;
        height: 800px;
        margin: 0;
        padding: 0;
    }

</style>

<div class="content-body">

    <section id="basic-tabs-components">
        <div class="row match-height">
            <!-- Basic Tabs starts -->
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" style="width: 50%;">
                                <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="true">Next 3 Path</a>
                            </li>
                            <li class="nav-item" style="width: 50%;">
                                <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" aria-controls="profile" role="tab" aria-selected="false">Source to Destination</a>
                            </li>
                        </ul>
                        <hr>
                        <div class="tab-content">
                            <div class="tab-pane active" id="home" aria-labelledby="home-tab" role="tabpanel">

                                <div class="row mt-2">
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

                                <hr class="divider-info">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="chart1"></div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane" id="profile" aria-labelledby="profile-tab" role="tabpanel">
                                <div class="row mt-2">
                                    <div class="col-md-3 pb-1">
                                        <div class="form-group">
                                            <label for="name">Start Venue</label>
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
                                            <label for="name">End Venue</label>
                                            <select class="select2 form-control venue" name="venue" required>
                                                <option value="">Please Select</option>
                                                <option value="Level 2" >Rooftop</option>
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

                                <hr class="divider-info">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="chart2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Basic Tabs ends -->
        </div>
    </section>

</div>

@endsection


@section('script')

<!-- BEGIN: Page JS-->
<script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
<script src="{{ url('templates/vuexy/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-core.min.js"></script>
<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-sankey.min.js"></script>
<!-- END: Page JS-->

<script>

$(function(){

    $('.flatpickr-range').flatpickr({
        mode: 'range'
    });

    // create data
    var data = [
        {from: "Synchroweb",  to: "Level 1",  weight:  34 },
        {from: "Synchroweb",  to: "Level 2", weight:  10},
        {from: "Synchroweb",  to: "Level 3",   weight:  20},
        {from: "Level 1",     to: "Level 4",  weight:   30},
        {from: "Level 1",     to: "Level 5",  weight:   14},
        {from: "Level 1",     to: "Level 6",  weight:   23},
        {from: "Level 2",     to: "Level 7",  weight:   12},
        {from: "Level 2",     to: "Level 6",  weight:   1},
        {from: "Level 3",     to: null,  weight:   1},
        {from: "Level 4",     to: "Rooftop",  weight:   10},
        {from: "Level 5",     to: "Rooftop",  weight:   40},
        {from: "Level 6",     to: "Rooftop",  weight:   60},
        {from: "Level 7",     to: "Rooftop",  weight:   50},
    ];

    var chart = anychart.sankey(data);

    chart.nodeWidth("30%");

    chart.container("chart1");

    chart.draw();

    // create data
    var data2 = [
        {from: "Synchroweb",  to: "Rooftop",  weight:  100 },
        {from: "Synchroweb",  to: "Level 1", weight:  34},
        {from: "Synchroweb",  to: "Level 3",   weight:  90},
        {from: "Level 1",     to: "Level 2",  weight:   200},
        {from: "Level 1",     to: "Level 5",  weight:   49},
        {from: "Level 1",     to: "Level 6",  weight:   23},
        {from: "Level 2",     to: "Level 3",  weight:   12},
        {from: "Level 2",     to: "Level 6",  weight:   1},
        {from: "Synchroweb",  to: "Level 7",  weight:   1},
        {from: "Level 6",     to: "Level 4",  weight:   10},
        {from: "Level 5",     to: "Rooftop",  weight:   40},
        {from: "Level 6",     to: "Rooftop",  weight:   60},
        {from: "Level 7",     to: "Rooftop",  weight:   50},
        {from: "Level 3",     to: "Rooftop",  weight:   50},
        {from: "Level 4",     to: "Rooftop",  weight:   50},
    ];

    var chart2 = anychart.sankey(data2);

    chart2.nodeWidth("30%");

    chart2.container("chart2");

    chart2.draw();

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


