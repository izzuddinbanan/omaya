<!-- Description -->
@extends('layouts.main')

@section('title', 'User')

@section('page-desc', 'Update New Venue.')


@section('content')
<!-- Zero configuration table -->
<div class="content-body">
    <section id="input-sizing">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">

                    <form method="POST" action="{{ route('admin.user.update-profile') }}" enctype="multipart/form-data">

                        @csrf

                        <div class="card-header">
                            <h4 class="card-title">Edit User</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="clearfix"></div>

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Username </label>
                                        <input class="form-control @error('username') is-invalid @enderror" type="text" placeholder="e.g admin" autocomplete="off" name="username" value="{{ old('username', $user->username) }}" autofocus="" required="" tabindex="1" />
                                        @error('username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                               
                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Email </label>
                                        <input class="form-control @error('email') is-invalid @enderror" type="text" placeholder="e.g admin@synchroweb.com" autocomplete="off" name="email" value="{{ old('email', $user->email) }}" autofocus="" required="" tabindex="1" />
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="meet_with">Photo <code>Type: jpg, jpeg, png</code></label>
                                        <input class="form-control @error('photo') is-invalid @enderror dropify" type="file" autocomplete="off" name="photo" value="{{ old('photo') }}" accept="image/*" @if($user->photo) data-default-file="{{ $user->getThumbnailImageUrlAttribute(session('tenant_id')) }}" @endif />
                                        @error('photo')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 mt-2">
                                    <a href="{{ route('admin.dashboard') }}">
                                        <button type="button" class="btn btn-outline-secondary waves-effect">Back</button>
                                    </a>
                                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Submit</button>
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
