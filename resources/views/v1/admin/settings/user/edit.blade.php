<!-- Description -->
@extends('layouts.main')


@section('title', 'Settings : User -> Update')
@section('page-desc', 'Manage your user data.')


@section('content')
<!-- Zero configuration table -->
<div class="content-body">
    <section id="input-sizing">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">

                    <form method="POST" action="{{ route('admin.setting.user.update', $user->id) }}">

                        @csrf
                        @method('PUT')

                        <div class="card-header">
                            <h4 class="card-title">Edit User Record</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="clearfix"></div>

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Username <span class="text-danger">*</span></label>
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
                                        <label for="name">Role <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="role">
                                            <option value="">Please Select</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" {{ $role->name == old('role', $user->role) ? 'selected'  : ''}}>{{$role->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Email <span class="text-danger">*</span> </label>
                                        <input class="form-control @error('email') is-invalid @enderror" type="text" placeholder="e.g admin@synchroweb.com" autocomplete="off" name="email" value="{{ old('email', $user->email) }}" autofocus="" required="" tabindex="1" />
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Password <span class="text-danger">*</span> </label>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror password form-control-merge" id="reset-password-new" name="password" value="{{ old('password	') }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="" tabindex="1" autofocus />
                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                        </div>
                                        <span class="password_strength"> 
                                        

                                        </span>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div> -->

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Permission <span class="text-danger">*</span></label>
                                        <select class="select2 form-control @error('permission') is-invalid @enderror" name="permission">
                                            <option value="">Please Select</option>
                                                <option value="r" {{ 'r' == old('permission', $user->permission) ? 'selected' : '' }}>Read</option>
                                                <option value="rw" {{ 'rw' == old('permission', $user->permission) ? 'selected' : '' }}>Read + Write</option>
                                        </select>
                                        @error('permission')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 mt-2">
                                    <a href="{{ route('admin.setting.user.index') }}">
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

@section('script')


<script>

    function passwordChecker(pwd){

        if(pwd.match(/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])[A-Za-z0-9_@./#&+\-\*]{8,}$/)) return true;
        else return false  
    }

    $(function(){
        
        $('.password').on('keyup', function(){

            let pwd = $(this).val();     
            
            $('.password_strength').html('');

            if(pwd){
                if(passwordChecker(pwd) == false){
                    let det = 'Password must be at least one upper case letter <br> '+
                            'Password must be at least one lower case letter <br>'+
                            'Password must be at least one number <br>'+
                            'Password must be at least 8 characters';
    
                    $('.password_strength').html(det).css('color', 'red').css('fontSize', "10px");
                }
            }
        });

    })
</script>

@endsection