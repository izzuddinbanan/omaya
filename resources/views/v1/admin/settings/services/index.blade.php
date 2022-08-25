<!-- Description -->
@extends('layouts.main')



@section('title', 'Settings : Service')

@section('page-desc', 'manage service running for system')


@section('content')
<!-- Zero configuration table -->
<div class="content-body">

    <!-- Ajax Sourced Server-side -->
    <section id="ajax-datatable">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-75">General Service</h4>
                        <p>Required service need for system running</p>

                        <!-- Connections -->


                        @foreach($general_services as $service)
                        <div class="d-flex mt-2">
                            <div class="flex-shrink-0">
                                <img src="{{ url($service->images) }}" alt="google" class="me-1" @if($service->service_name != 'redis')height="25" width="125" @else height="35" width="125" @endif />
                            </div>
                            <div class="d-flex align-item-center justify-content-between flex-grow-1">
                                <div class="me-1">
                                    <p class="fw-bolder mb-0">{{ $service->name }}</p>
                                    <span>{!! $service->remarks !!}</span>
                                </div>
                                <div class="mt-50 mt-sm-0">
                                    <div class="form-check form-switch form-check-primary">
                                        <input type="checkbox" class="form-check-input" id="checkboxGoogle" checked disabled />
                                        <label class="form-check-label" for="checkboxGoogle">
                                            <span class="switch-icon-left"><i data-feather="check"></i></span>
                                            <span class="switch-icon-right"><i data-feather="x"></i></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-75">Add-ons Services</h4>
                        <p>Add-ons service for Omaya App</p>

                        @foreach($addon_services as $service)
                            <div class="d-flex mt-2">
                                <div class="flex-shrink-0">
                                    <img src="{{ url($service->images) }}" alt="google" class="me-1" height="25" width="125" />
                                </div>
                                <div class="d-flex align-item-center justify-content-between flex-grow-1">
                                    <div class="me-1">
                                        <p class="fw-bolder mb-0">{{ $service->name }}</p>
                                        <span>{!! $service->remarks !!}</span>
                                    </div>

                
                                    <div class="mt-50 mt-sm-0">
                                        <div class="form-check form-switch form-check-primary">
                                            <input type="checkbox" name="{{ $service->id }}" class="form-check-input addon_services" id="service_{{ $service->id }}" {{ $service->is_enable ? "checked" : "" }} />
                                            <label class="form-check-label" for="service_{{ $service->id }}">
                                                <span class="switch-icon-left"><i data-feather="check"></i></span>
                                                <span class="switch-icon-right"><i data-feather="x"></i></span>
                                            </label>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        @endforeach
                        
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


        $(".addon_services").change(function() {

            let id = this.id;
            id = id.replace('service_','');

            let checked = false;
            if($(this).is(":checked")) checked = true;

            $.ajax({
                url:"{{ route('admin.setting.service.store') }}",
                type:"POST",
                data:{'id' : id, "checked" : checked },
                success:function(response) {

                    if(response.status == true){
                        displayMessage(response.message, 'success')
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



        });

    });

</script>


@endsection