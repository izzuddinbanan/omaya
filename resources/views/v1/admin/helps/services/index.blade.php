<!-- Description -->
@extends('layouts.main')



@section('title', 'Help & Tools : Service')
@section('page-desc', 'Critical system services health report')


@section('content')
<!-- Zero configuration table -->
<div class="content-body">

    <div class="row">

        @foreach($services["services"] ?? [] as $service)
            
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card text-center border-{{ $service['status'] == 'active' ? 'success' : 'danger' }}">
                    <div class="card-body">
                        <div class="avatar p-50 mb-1" style="background-color: transparent !important;cursor: default;">
                            <div class="avatar-content" >
                                <img src="{{ url($service['image']) }}" style="border-radius: 0px !important;max-height: {{ $service['styles']  }} !important;">
                            </div>
                        </div>
                        <h4 class="card-title">{{ $service["name"] }}</h4>
                        <p class="card-text">
                            <span class="badge badge-glow bg-{{ $service['status'] == 'active' ? 'success' : 'danger' }}">{{ ucwords($service['status']) }}</span>
                        </p>
                        <p class="card-text">Last Active : 
                        
                        {{ $service['status_linux'] }}

                    </div>
                    <div class="card-footer" style="padding : 0px;">
                        <button class="btn btn-success btn-restart-service" id="{{ $service['service_name'] }}" style="width: 100% !important;">Restart</button>
                    </div>
                </div>
            </div>
          
        @endforeach

    </div>


</div>


@endsection

@section('script')


<script type="text/javascript">
        
    $(document).ready(function() {


        $(".btn-restart-service").click(function() {

            let id = this.id;

            if(id == "redis" || id == "mosquitto") {

                swal({
                    title: "Are You Sure? ",
                    text: "All data store for processing will be lost.",
                    type: "info",
                    showCancelButton: true,
                    confirmButtonClass: "btn btn-success btn-fill",
                    confirmButtonText: "Yes, Restart!",
                    cancelButtonText: "No, cancel!",
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                },
                function(isConfirm) {
                    if (isConfirm) {

                        restartService(id)
                    }
                });

            }else {

                restartService(id)

            }


        });


        function restartService(id) {

            $.ajax({
                url:"{{ route('admin.help.service.restart') }}",
                type:"POST",
                data:{'id' : id },
                success:function(response) {

                    if(response.status == true){

                        displayMessage(response.message, 'success', true,response.wait_reload)

                    }else{

                        displayMessage(response.message, 'danger')
                        close_swal();
                    }
                },
                error: function(xhr, status, error) {

                    setTimeout(function() {
                        location.reload(true);
                    }, 1000);

                }

            });

        }

    });

</script>

@endsection