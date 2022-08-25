<!-- Description -->
@extends('layouts.main')

@section('title', 'Update Password')



@section('content')
<!-- Zero configuration table -->
<div class="content-body">
    <section id="input-sizing">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">

                    <form method="POST" action="{{ route('admin.user.update-password') }}">

                        @csrf

                        <div class="card-body">
                            <div class="row">
                                <div class="clearfix"></div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="account-old-password">Old Password</label>
                                        <div class="input-group form-password-toggle mb-2">
                                            <input type="password" class="form-control @error('old_password') is-invalid @enderror" name="old_password" placeholder="Old Password" />
                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                        </div>
                                        @error('old_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12"></div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="account-new-password">New Password</label>
                                        <div class="input-group form-password-toggle mb-2">
                                            <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="New Password" />
                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                        </div>
                                        @error('new_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="account-retype-new-password">Retype New Password</label>
                                        <div class="input-group form-password-toggle mb-2">
                                            <input type="password" class="form-control @error('confirm_new_password') is-invalid @enderror " name="confirm_new_password" placeholder="New Password" />
                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                        </div>
                                        @error('confirm_new_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 mt-2">
                                    <button type="button" class="btn btn-outline-secondary waves-effect" onclick="goBack()">Back</button>
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

@section('script')

<script type="text/javascript">

function goBack() {
    window.history.back();
}


</script>
@endsection