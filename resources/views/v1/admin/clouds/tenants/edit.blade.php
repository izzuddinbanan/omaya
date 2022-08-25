<!-- Description -->
@extends('layouts.main')

@section('title', 'Clouds : Tenants')

@section('page-desc', 'Manage your tenant data.')


@section('content')
<!-- Zero configuration table -->
<div class="content-body">
    <section id="input-sizing">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">

                    <form method="POST" action="{{ route('admin.cloud.tenant.update', [$tenant->id]) }}">

                        @csrf
                        @method("put")
                        <div class="card-header">
                            <h4 class="card-title">Update Tenant Record</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">


                                <div class="clearfix"></div>
                                <div class="col-md-6">
                                    <div class="form-group pb-1">
                                        <label for="name">Name <span class="text-danger">*</span></label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="e.g Synchroweb Technology Sdn Bhd" autocomplete="off" name="name" value="{{ old('name', $tenant->name) }}" autofocus="" required="" tabindex="1" />
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group pb-1">
                                        <label for="name">Tenant ID <span class="text-danger">*</span></label>
                                        <input class="form-control @error('tenant_id') is-invalid @enderror" type="text" placeholder="e.g admin" autocomplete="off" name="" value="{{ $tenant->tenant_id }}" autofocus="" tabindex="2" readonly="" />
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group pb-1">
                                        <label for="name">Email</label>
                                        <input class="form-control @error('email') is-invalid @enderror" type="email" placeholder="e.g default@gmail.com" autocomplete="off" name="email" value="{{ old('email', $tenant->email) }}" autofocus="" tabindex="6" />
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group pb-1">
                                        <label for="phone">Phone Number</label>
                                        <input class="form-control @error('phone') is-invalid @enderror" type="text" placeholder="e.g 070339988" autocomplete="off" name="phone" value="{{ old('phone', $tenant->phone) }}" autofocus="" tabindex="6" />
                                        @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group pb-1">
                                        <label for="name">Address</label>
                                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" tabindex="7" >{{ old('address', $tenant->address) }}</textarea>
                                        @error('address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12"></div>


                                <div class="col-md-6">
                                    <div class="form-group pb-1">
                                        <label for="name">License Key</label>
                                        <textarea name="license_key" class="form-control @error('license_key') is-invalid @enderror" tabindex="7">{{ old('license_key') }}</textarea>
                                        @error('license_key')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12"></div>

                                <div class="col-12">
                                    <a href="{{ route('admin.cloud.tenant.index') }}">
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