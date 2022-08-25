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

                    <form method="POST" action="{{ route('admin.cloud.tenant.store') }}">

                        @csrf

                        <div class="card-header">
                            <h4 class="card-title">Add New Tenant Record</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">


                                <div class="clearfix"></div>
                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Name <span class="text-danger">*</span></label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="e.g Synchroweb Technology Sdn Bhd" autocomplete="off" name="name" value="{{ old('name') }}" autofocus="" required="" tabindex="1" />
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
                                        <input class="form-control @error('tenant_id') is-invalid @enderror" type="text" placeholder="e.g admin" autocomplete="off" name="tenant_id" value="{{ old('tenant_id') }}" autofocus="" required="" tabindex="2" />
                                        @error('tenant_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group  pb-1">
                                        <label for="name">Username <span class="text-danger">*</span></label>
                                        <input class="form-control @error('admin_id') is-invalid @enderror" type="text" placeholder="e.g admin" autocomplete="off" name="admin_id" value="{{ old('admin_id') }}" autofocus="" required="" tabindex="3" />
                                        @error('admin_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12"></div>

                                <div class="col-md-6">
                                    <div class="form-group pb-1">
                                        <label for="name">Password <span class="text-danger">*</span></label>
                                        <input class="form-control @error('password') is-invalid @enderror" type="password" placeholder="e.g password" autocomplete="off" name="password" value="{{ old('password') }}" autofocus="" required="" tabindex="4" />
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group pb-1">
                                        <label for="name">Password Confirmation<span class="text-danger">*</span></label>
                                        <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password" placeholder="e.g password confirmation" autocomplete="off" name="password_confirmation" value="{{ old('password_confirmation') }}" autofocus="" required="" tabindex="5" />
                                        @error('password_confirmation')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12"></div>

                                <div class="col-md-6">
                                    <div class="form-group pb-1">
                                        <label for="name">Email </label>
                                        <input class="form-control @error('email') is-invalid @enderror" type="email" placeholder="e.g default@gmail.com" autocomplete="off" name="email" value="{{ old('email') }}" autofocus=""  tabindex="6" />
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
                                        <input class="form-control @error('phone') is-invalid @enderror" type="text" placeholder="e.g 070339988" autocomplete="off" name="phone" value="{{ old('phone') }}" autofocus="" tabindex="6" />
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
                                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" tabindex="7">{{ old('address') }}</textarea>
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
                                        <label for="name">License Key <span class="text-danger">*</span></label>
                                        <textarea name="license_key" class="form-control @error('license_key') is-invalid @enderror" tabindex="7" required="">{{ old('license_key') }}</textarea>
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
                                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Create</button>
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