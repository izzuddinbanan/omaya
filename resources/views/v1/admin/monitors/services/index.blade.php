<!-- Description -->
@extends('layouts.main')



@section('title', 'Monitor : Service')
@section('page-desc', 'Critical system services health report')


@section('content')
<!-- Zero configuration table -->
<div class="content-body">

    <div class="row">

        @php
        if(!isset($services["services"])) $services["services"] = [];
        @endphp
        @foreach($services["services"] as $service)
            
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card text-center border-{{ $service['status'] == 'active' ? 'success' : 'danger' }}">
                    <div class="card-body">
                        <div class="avatar p-50 mb-1" style="background-color: transparent !important;cursor: default;">
                            @if($service['name'] != "mosquitto")
                            <div class="avatar-content" >
                                <img src="{{ url($service['image']) }}" style="{{ $service['styles']  }}">
                            </div>
                            @else
                            <div class="avatar-content" >
                                <img src="{{ url("images/mqtt.png") }}" style="border-radius: 0px !important;max-height: 40px !important;">
                            </div>
                            @endif
                        </div>
                        <h4 class="card-title">{{ $service["name"] }}</h4>
                        <p class="card-text">
                            <span class="badge badge-glow bg-{{ $service['status'] == 'active' ? 'success' : 'danger' }}">{{ ucwords($service['status']) }}</span>
                        </p>
                        <p class="card-text"><b>Last Checked : </b><br> 
                        @if($service["last_active"])
                        {{ converTimeToLocal($service["last_active"], session('timezone')) }}
                        @else
                        -
                        @endif
                        </p>
                        @if($service["last_active"])
                        {{ Carbon\Carbon::parse(($service["last_active"]))->diffForHumans() }}
                        @else
                        Never Run
                        @endif
                    </div>
                </div>
            </div>
          
        @endforeach

    </div>


</div>


@endsection

@section('script')


<script type="text/javascript">

</script>
@endsection