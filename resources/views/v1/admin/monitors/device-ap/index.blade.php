@extends('layouts.main')

@section('title', 'Monitor : Device [ Access Point ]')

@section('page-desc', 'Monitor all registered device [AP]. ')


@include('layouts.components.datatable')

@section('content')

<div class="content-body">

	<div class="row">

	    <div class="col-lg-3 col-sm-6 col-12">
	        <div class="card bg-gradient-primary">
	            <div class="card-header">
	                <div>
	                    <h2 class="fw-bolder mb-0" > <text id="h-total-all-active">0</text> / <text id="h-total-all">0</text></h2>
	                    <p class="card-text">Total Device</p>
	                </div>
	                <div class="avatar bg-primary p-50 m-0">
	                    <div class="avatar-content">
	                        <i data-feather="cpu" class="font-medium-5"></i>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>

	    <div class="col-lg-3 col-sm-6 col-12">
	        <div class="card bg-gradient-success">
	            <div class="card-header">
	                <div>
	                    <h2 class="fw-bolder mb-0" id="h-total-online">0</h2>
	                    <p class="card-text">Online</p>
	                </div>
	                <div class="avatar bg-success p-50 m-0">
	                    <div class="avatar-content">
	                        <i data-feather="wifi" class="font-medium-5"></i>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>

	    <div class="col-lg-3 col-sm-6 col-12">
	        <div class="card bg-gradient-secondary">
	            <div class="card-header">
	                <div>
	                    <h2 class="fw-bolder mb-0" id="h-total-no-packet">0</h2>
	                    <p class="card-text">No New Packet</p>
	                </div>
	                <div class="avatar bg-secondary p-50 m-0">
	                    <div class="avatar-content">
	                        <i data-feather="loader" class="font-medium-5"></i>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>


	    <div class="col-lg-3 col-sm-6 col-12">
	        <div class="card bg-gradient-danger">
	            <div class="card-header">
	                <div>
	                    <h2 class="fw-bolder mb-0" id="h-total-offline">0</h2>
	                    <p class="card-text">Offline</p>
	                </div>
	                <div class="avatar bg-danger p-50 m-0">
	                    <div class="avatar-content">
	                        <i data-feather="wifi-off" class="font-medium-5"></i>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>


	<!-- Ajax Sourced Server-side -->
    <section id="ajax-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-datatable">

                            <table class="datatables-ajax table">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Name</th>
                                        <th>Mac Address</th>
                                        <th>Vendor</th>
                                        <th>Venue</th>
                                        <th>Zone</th>
                                        <th>Is Active</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp

                                    @foreach($devices as $device)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $device->name }}</td>
                                            <td>{{ $device->mac_address }}</td>
                                            <td>{{ $device->device_type }}</td>
                                            <td>{{ $device->venue->name }}</td>
                                            <td>{!! $device->zone ? $device->zone->name : "<i>none</i>"  !!}</td>
                                            <td>
                                            	
                                            	<button class="btn btn-sm btn-{{ $device->is_active ? 'success' : 'secondary'}}">{{ $device->is_active ? 'Active' : 'Inactive' }}</button>
                                            </td>
                                            <td>
                                            	<button class="btn btn-sm btn-{{ $device->status_color }}">{{ ucfirst($device->status ?? 'Offline') }}</button>
                                            	@if($device->last_seen_at)
                                            	<br>
                                            	<small>{{ converTimeToLocal($device->last_seen_at, session('timezone'), "d M Y h:i:s a") }}
                                            	<br>
                                            	{{ Carbon\Carbon::parse(($device->last_seen_at))->diffForHumans() }}
                                            	</small>
                                            	@endif
                                            	
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Name</th>
                                        <th>Mac Address</th>
                                        <th>Vendor</th>
                                        <th>Venue</th>
                                        <th>Zone</th>
                                        <th>Is Active</th>
                                        <th>Status</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

@endsection






@section('script')

<script type="text/javascript">

	$(document).ready(function() {

        $('.datatables-ajax').DataTable();


		$.ajax({
            url:"{{ route('admin.monitor.device-ap.index') }}",
            type:"get",
            success:function(response) {

            	
            	$("#h-total-all-active").html(response['total']['all-active']).counterUp({ delay: 100, time: 1000 });
            	$("#h-total-all").html(response['total']['all']).counterUp({ delay: 100, time: 1000 });
            	$("#h-total-online").html(response['total']['online']).counterUp({ delay: 100, time: 1000 });
            	$("#h-total-no-packet").html(response['total']['no-new']).counterUp({ delay: 100, time: 1000 });
            	$("#h-total-offline").html(response['total']['offline']).counterUp({ delay: 100, time: 1000 });

            }

        });

	});


</script>

@endsection
