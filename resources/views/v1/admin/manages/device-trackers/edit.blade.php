<!-- Description -->
@extends('layouts.main')


@section('title', 'Manage : Device [ Tracker ] -> Update')
@section('page-desc', 'Manage your Device [ Tracker ] data.')


@section('content')
<!-- Zero configuration table -->
<div class="content-body">
    <section id="input-sizing">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">

                    <form method="POST" action="{{ route('admin.manage.device-tracker.update', $device->device_uid) }}">

                        @csrf
                        @method('put')

                        <div class="card-header">
                            <h4 class="card-title">Update Device</h4>
                        </div>
                        <div class="card-body">


                            <div class="row">

                                <div class="col-md-6 pb-1">
                                    <label class="form-check-label mb-50" for="customSwitch11">Is Active</label>
                                    <div class="form-check form-switch form-check-success">
                                        <input type="checkbox" class="form-check-input" id="customSwitch11" {{ old('is_active', $device->is_active) == 1 ? 'checked' : '' }} name="is_active" value="1" />
                                        <label class="form-check-label" for="customSwitch11">
                                            <span class="switch-icon-left"><i data-feather="check"></i></span>
                                            <span class="switch-icon-right"><i data-feather="x"></i></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Name <span class="text-danger">*</span></label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="e.g User tag" autocomplete="off" name="name" value="{{ old('name', $device->name) }}" autofocus="" required="" tabindex="1" />
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Mac Address <span class="text-danger">*</span> <code>unique</code></label>
                                        <input class="form-control @error('mac_address') is-invalid @enderror" type="text" placeholder="e.g 04:D6:AA:DD:72:8C" autocomplete="off" name="mac_address" value="{{ old('mac_address', $device->mac_address_separator) }}" autofocus="" required="" tabindex="2" />
                                        @error('mac_address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Remarks </label>
                                        <textarea class="form-control" name="remarks" placeholder="this device for user enter the mall">{{ old('remarks', $device->remarks) }}</textarea>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                            </div>

                       
                            <div class="row">


                                <div class="col-12 mt-2">
                                    <a href="{{ route('admin.manage.device-tracker.index') }}">
                                        <button type="button" class="btn btn-outline-secondary waves-effect">Back</button>
                                    </a>
                                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Update</button>
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



    
</script>
@endsection