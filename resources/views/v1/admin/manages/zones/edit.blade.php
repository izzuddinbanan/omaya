<!-- Description -->
@extends('layouts.main')

@section('title', 'Manage : Zone -> Update')
@section('page-desc', 'Manage your zone data.')


@section('content')
<!-- Zero configuration table -->
<div class="content-body">
    <section id="input-sizing">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">

                    <form method="POST" action="{{ route('admin.manage.zone.update', [$zone->zone_uid]) }}">

                        @csrf
                        @method('put')

                        <div class="card-header">
                            <h4 class="card-title">Update Zone Record</h4>
                        </div>

                        <div class="card-body">
                            <div class="row">


                                <div class="clearfix"></div>
                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Name <span class="text-danger">* <code>unique</code></span></label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="e.g Meeting Room" autocomplete="off" name="name" value="{{ old('name', $zone->name) }}" autofocus="" required="" tabindex="1" />
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Location <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="location" id="input_location"  required="" tabindex="2">
                                            @foreach($locations as $location)
                                                <option value="{{ $location->location_uid }}" {{ old('location', $zone->location_uid) ==  $location->location_uid ? 'selected' : ''  }}>{{ ucfirst($location->name) }}</option>
                                            @endforeach
                                        </select>
                                        <div>
                                            <div class="@error('location') is-invalid @enderror"></div>
                                            @error('location')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Venue <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="venue" id="input_venue"  required="" tabindex="3">
                                           <option value="">Please Select</option>
                                        </select>
                                        <div>
                                            <div class="@error('venue') is-invalid @enderror"></div>
                                            @error('venue')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12"></div>

                                <div class="col-md-6">
                                    <div class="form-group pb-1">
                                        <label for="name">Remark</label>
                                        <textarea name="remark" class="form-control @error('remark') is-invalid @enderror" tabindex="4" placeholder="e.g Only staff here ...">{{ old('remark', $zone->remark) }}</textarea>
                                        @error('remark')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12"></div>

                                <div class="col-12">
                                    <a href="{{ route('admin.manage.zone.index') }}">
                                        <button type="button" class="btn btn-outline-secondary waves-effect" tabindex="6">Back</button>
                                    </a>
                                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light" tabindex="5">Update</button>
                                </div>


                            </div>
                        </div>
                    </form>

                </div>
            </div>
            
        </div>
    </section>
    <!-- Input Sizing end -->
   
</div>

@endsection

@section('script')

<script type="text/javascript">


    var old_venue = {!! json_encode(old('venue', $zone->venue_uid)) !!}
    
    $(document).ready(function() {


        onChangeLocation()


        $("#input_location").change(function() {


            onChangeLocation();

        });


    })


    function onChangeLocation() {


        $.ajax({
            url:"{{ route('admin.info.list-venue') }}",
            type:"POST",
            data:{'location_uid' : $("#input_location").val() },
            success:function(response) {


                $("#input_venue").empty();
                $("#input_venue").append('<option value="">Please Select</option>');
                response.forEach(venue => {
                    
                    $("#input_venue").append('<option value="'+ venue['venue_uid'] +'" '+ (old_venue == venue['venue_uid'] ? 'selected' : '' ) +'>'+ venue['name'] +'</option>')
                })
                let venues = response;



            },
            error: function(xhr, status, error) {

                // setTimeout(function() {
                //     location.reload(true);
                // }, 1000);

            }

        });

    }


</script>
@endsection