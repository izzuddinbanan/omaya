<!-- Description -->
@extends('layouts.main')



@section('title', 'Monitor : Scheduler')
@section('page-desc', 'System scheduler for Omaya App')


@section('content')
<!-- Zero configuration table -->
<div class="content-body">

    <div class="row">

        @foreach($schedulers as $scheduler)
            
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card text-center border-{{ ($scheduler['status']??'inactive') == 'active' ? 'success' : 'danger' }}">
                    <div class="card-body">
                        <!-- <div class="avatar p-50 mb-1" style="background-color: transparent !important;cursor: default;">
                            <div class="avatar-content" >
                                <img src="" style="">
                            </div>
                          
                        </div> -->
                        <h4 class="card-title"><b>{{ $scheduler["name"] }}</b></h4>
                        <p class="card-text">
                            <span class="badge badge-glow bg-{{ ($scheduler['status']??'inactive') == 'active' ? 'success' : 'danger' }}">{{ ucwords(($scheduler['status']??'inactive')) }}</span>
                        </p>
                        <p class="card-text"><b>Run :</b><br>
                            {{ $scheduler["run"] }}
                        </p> 
                        <p class="card-text"><b>Last Run :</b><br> 
                        @if($scheduler["last_run_start"] ?? "")
                        {{ converTimeToLocal($scheduler["last_run_start"], session('timezone')) }}
                        @else
                        -
                        @endif
                        @if($scheduler["last_run_start"] ?? "")
                        <br>
                        {{ Carbon\Carbon::parse(($scheduler["last_run_start"]))->diffForHumans() }}
                        @endif
                        </p>
                        <p class="card-text"><b>Scheduler Completed : </b><br>
                        @if($scheduler["last_run_end"] ?? "")
                        {{ converTimeToLocal($scheduler["last_run_end"], session('timezone')) }}
                        @else
                        -
                        @endif
                        </p>
                        
                        <p class="card-text"><b>Time Taken :</b> {{ $scheduler["time_taken"] ?? "" }}</p>
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