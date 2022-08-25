<!-- Description -->
@extends('layouts.main')

@section('vendor-css')
@endsection

@section('title', 'Settings :  Configuration')

@section('page-desc', 'Global configuration for omaya app')

@section('content')
<!-- Zero configuration table -->
<div class="content-body">

    <section id="nav-filled">
        <div class="row match-height">
            <!-- Filled Tabs starts -->
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ $active_tab == 'license' ? 'active' : '' }}" id="home-tab-fill" data-bs-toggle="tab" href="#license-tab" role="tab" aria-controls="home-fill" aria-selected="true">License Key</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $active_tab == 'timezone' ? 'active' : '' }}" id="profile-tab-fill" data-bs-toggle="tab" href="#timezone-tab" role="tab" aria-controls="profile-fill" aria-selected="false">General</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ $active_tab == 'smtp' ? 'active' : '' }}" id="smtp-tab-fill" data-bs-toggle="tab" href="#smtp-tab" role="tab" aria-controls="smtp-fill" aria-selected="false">SMTP</a>
                            </li>
                            <!-- <li class="nav-item">
                                <a class="nav-link {{ $active_tab == 'mall' ? 'active' : '' }}" id="messages-tab-fill" data-bs-toggle="tab" href="#mall-tab" role="tab" aria-controls="messages-fill" aria-selected="false">Mall Module</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $active_tab == 'dwell' ? 'active' : '' }}" id="settings-tab-fill" data-bs-toggle="tab" href="#dwell-tab" role="tab" aria-controls="settings-fill" aria-selected="false">Dwell Time Option</a>
                            </li> -->
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content pt-1">
                            
                            <div class="tab-pane {{ $active_tab == 'license' ? 'active' : '' }}" id="license-tab" role="tabpanel" aria-labelledby="home-tab-fill">
                                
                                <!-- <div class="col-md-3">
                                    <div class="row">
                                        <button type="button" class="btn btn-inline btn-primary btn-renew">
                                            <i class="fa fa-credit-card"></i> Renew License
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-12 pt-2 form-renew">
                                    <form action="" method="POST" >
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name">Serial Key</label>
                                                    <textarea class="form-control"></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-12 pt-2" >
                                                <button type="submit" class="btn btn-primary pull-right">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                 -->
                                <div class="d-none d-lg-block" style="bottom:0; right:0; position:absolute"> 
                                    <img class="img-fluid" src="{{ url('images/illustration.png') }}" alt="Login V2" style="width: 350px;"/>
                                </div>
                                <div class="col-lg-12 col-md-12 col-xs-12"> 
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-2" style="font-weight: 700;">Organisation/Brand Name</div>
                                        <div class="col-lg-1"><span class="fa fa-arrow-circle-right text-info" style="color: red;"></span></div>
                                        <div class="col-lg-9"> {{ session('name') }}</div>
                                    </div>   
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-2" style="font-weight: 700;">Tenant ID</div>
                                        <div class="col-lg-1"><span class="fa fa-arrow-circle-right text-info" style="color: red;"></span></div>
                                        <div class="col-lg-9"> {{ session('tenant_id') }}</div>
                                    </div>    

                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-2" style="font-weight: 700;">Omaya Type</div>
                                        <div class="col-lg-1"><span class="fa fa-arrow-circle-right text-info" style="color: red;"></span></div>
                                        <div class="col-lg-9"> {{ session('omaya_type') }}</div>
                                    </div>    

                                    <hr>
                                    <div class="row">
                                        <div class="col-md-2" style="font-weight: 700;">License Status</div>
                                        <div class="col-md-1"><span class="fa fa-arrow-circle-right text-info" style="color: red;"></span></div>
                                        <div class="col-md-9"> <div class="spinner-grow spinner-grow-sm text-{{ $license_validity == 'Valid' ? 'success' : 'danger' }}" role="status"></div> &nbsp; {{ $license_validity }}</div>
                                    </div>    

                                    <hr>
                                    <div class="row">
                                        <div class="col-md-2" style="font-weight: 700;">Issue Date</div>
                                        <div class="col-md-1"><span class="fa fa-arrow-circle-right text-info" style="color: red;"></span></div>
                                        <div class="col-md-9"> {{ date('d M Y', session('generate_on')) }}</div>
                                    </div>    

                                    <hr>
                                    <div class="row">
                                        <div class="col-md-2" style="font-weight: 700;">Expired Date</div>
                                        <div class="col-md-1"><span class="fa fa-arrow-circle-right text-info" style="color: red;"></span></div>
                                        <div class="col-md-9"> {{ date('d M Y', session('expire_on')) }} ( {{$intervalstring}} ) </div>
                                    </div>    

                                    <hr>
                                    <div class="row">
                                        <div class="col-md-2" style="font-weight: 700;">Total Allowed NAS / AP</div>
                                        <div class="col-md-1"><span class="fa fa-arrow-circle-right text-info" style="color: red;"></span></div>
                                        <div class="col-md-9"> {{ session('device_limit') }}</div>
                                    </div>    

                                    <hr>
                                    <div class="row">
                                        <div class="col-md-2" style="font-weight: 700;">Heatmap Status</div>
                                        <div class="col-md-1"><span class="fa fa-arrow-circle-right text-info" style="color: red;"></span></div>
                                        <div class="col-md-9"> Enabled</div>
                                    </div>    
                                    
                                </div>
                                
                                
                            </div>
                            <div class="tab-pane {{ $active_tab == 'timezone' ? 'active' : '' }}" id="timezone-tab" role="tabpanel" aria-labelledby="profile-tab-fill">
                                <form action="{{ route('admin.setting.config.timezone') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="clearfix"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">Timezone Setting </label>
                                                <select class="form-control select2" name="timezone">
                                                    <option value="">Please Select</option>
                                                    @foreach($tzlist as $list)
                                                    <option value="{{ $list }}" {{ $cloud->timezone == $list ? 'selected' : '' }}>{{ $list }}</option>
                                                    @endforeach
                                                </select>
                                                @error('timezone')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row pt-2">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">How Long to Keep Log Files / Backup </label><code>Days</code>
                                                <input type="number" name="keep_log" value="{{ $cloud->delete_log }}" required placeholder="e.g 10" class="form-control @error('keep_log') is-invalid @enderror"> 
                                                @error('keep_log')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 pt-2" >
                                            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Submit</button>
                                        </div>
                                    </div>    

                                </form>
                            </div>

                            <div class="tab-pane {{ $active_tab == 'smtp' ? 'active' : '' }}" id="smtp-tab" role="tabpanel" aria-labelledby="smtp-tab-fill">
                                <form action="{{ route('admin.setting.config.smtp') }}" method="POST">
                                    @csrf
                                    <div class="row">

                                        <div class="col-md-6 pb-1">
                                            <label class="form-check-label mb-50" for="customSwitch11">Is Active</label>
                                            <div class="form-check form-switch form-check-success">
                                                <input type="checkbox" class="form-check-input" id="customSwitch11" {{ old('is_active', $cloud->smtp_is_active) ? 'checked' : '' }} name="is_active" value="1" />
                                                <label class="form-check-label" for="customSwitch11">
                                                    <span class="switch-icon-left"><i data-feather="check"></i></span>
                                                    <span class="switch-icon-right"><i data-feather="x"></i></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>

                                        <div class="col-md-6 pb-1">
                                            <div class="form-group">
                                                <label for="name">SMTP Host </label>
                                                <input class="form-control @error('smtp_host') is-invalid @enderror" type="text" placeholder="e.g mail.domain.com" autocomplete="off" name="smtp_host" value="{{ old('smtp_host', $cloud->smtp_host) }}" autofocus=""  tabindex="1" />
                                                @error('smtp_host')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 pb-1">
                                            <div class="form-group">
                                                <label for="name">SMTP Port </label>
                                                <input class="form-control @error('smtp_host') is-invalid @enderror" type="text" placeholder="e.g 25" autocomplete="off" name="smtp_port" value="{{ old('smtp_port', $cloud->smtp_port) }}" autofocus=""  tabindex="1" />
                                                @error('smtp_port')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 pb-1">
                                            <div class="form-group">
                                                <label for="name">Authentication </label>
                                                <select class="form-control select2" name="smtp_authentication">
                                                    <option value="">Please Select</option>
                                                    <option value="none" {{ old('smtp_authentication', $cloud->smtp_auth) == "none" ? "selected" : "" }}>None</option>
                                                    <option value="tls" {{ old('smtp_authentication', $cloud->smtp_auth) == "tls" ? "selected" : "" }}>Yes: TLS</option>
                                                    <option value="ssl" {{ old('smtp_authentication', $cloud->smtp_auth) == "ssl" ? "selected" : "" }}>Yes: SSL</option>
                                                </select>
                                                @error('smtp_authentication')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>

                                        <div class="col-md-6 pb-1">
                                            <div class="form-group">
                                                <label for="name">username </label>
                                                <input class="form-control @error('smtp_username') is-invalid @enderror" type="text" placeholder="e.g user" autocomplete="off" name="smtp_username" value="{{ old('smtp_username', $cloud->smtp_username) }}" autofocus=""  tabindex="1" />
                                                @error('smtp_username')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 pb-1">
                                            <div class="form-group">
                                                <label for="name">Password </label>
                                                <input class="form-control @error('smtp_password') is-invalid @enderror" type="password" placeholder="e.g password" autocomplete="off" name="smtp_password" value="{{ old('smtp_password', $cloud->smtp_password) }}" autofocus=""  tabindex="1" />
                                                @error('smtp_password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-md-6 pb-1">
                                            <div class="form-group">
                                                <label for="name">From Email Address </label>
                                                <input class="form-control @error('smtp_from_email_address') is-invalid @enderror" type="text" placeholder="e.g no-reply@mail.com" autocomplete="off" name="smtp_from_email_address" value="{{ old('smtp_from_email_address', $cloud->smtp_from_email) }}" autofocus=""  tabindex="1" />
                                                @error('smtp_from_email_address')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 pb-1">
                                            <div class="form-group">
                                                <label for="name">From Name </label>
                                                <input class="form-control @error('smtp_from_name') is-invalid @enderror" type="text" placeholder="e.g Omaya" autocomplete="off" name="smtp_from_name" value="{{ old('smtp_from_name', $cloud->smtp_from_name) }}" autofocus=""  tabindex="1" />
                                                @error('smtp_from_name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row pt-2">
                                

                                        <div class="col-12 pt-2" >
                                            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Submit</button>

                                            <button type="Button" class="btn btn-info mr-1 waves-effect waves-float waves-light btn-test-smtp">Test SMTP Connection</button>
                                        </div>
                                    </div>    

                                </form>
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
            <!-- Filled Tabs ends -->
        </div>
    </section>    

</div>

<!--
|--------------------------------------------------------------------------
|                START MODAL NOTIFICATION ON TRIGGER EVENT
|--------------------------------------------------------------------------

-->





<div class="modal-size-large d-inline-block">
    <div class="modal fade text-start modal-primary" id="modal-test-smtp" tabindex="-1" aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Test SMTP Connection</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <div class="modal-body this-body">
            

                        <div class="card">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="text" name="recipient_mail" value="" class="form-control"   placeholder="e.g user@mail.com, admin@email.com" autocomplete="off">
                                    </div>

                                    <div class="col-md-12">
                                        <button class="btn btn-sm btn-success smtp-send-email" type="button">Send Email</button>
                                    </div>
                                </div>
                            </div>


                            <div class="card-body">

                                <div class="row">
                                    <div class="col-12 col-sm-12">
                                            
                                        <div class="smtp-result-space"></div>


                                    </div>
                                </div>
                            </div>


                                    
                        </div>

                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
    </div>
</div>


<!-- 

|--------------------------------------------------------------------------
|                END MODAL NOTIFICATION ON TRIGGER EVENT
|--------------------------------------------------------------------------

-->

@endsection

@section('vendor-js')

@endsection

@section('script')

<script type="text/javascript">

    $(function(){

        //START TAB LICENSE KEY
        $('.form-renew').hide();
        $('.btn-renew').on('click', function(){

            if(!$('.form-renew').is(":visible")){
                $('.form-renew').slideDown();
            }
            else{
                $('.form-renew').slideUp();
            }
           
        });
        //END TAB LICENSE KEY



        $(".btn-test-smtp").on("click", function (e) {

            $(".smtp-result-space").html("");
            $('#modal-test-smtp').modal('show');


        });


        $(".smtp-send-email").on("click", function (e) {

            let recipient = $("input[name=recipient_mail]").val();
            $(".smtp-result-space").html("");

            if (recipient.length > 5) {


                $(".smtp-result-space").html("<div class=\"progress progress-bar-primary progress-lg\">\n" +
                    "<div class=\"progress-bar progress-bar-striped progress-bar-animated\" role=\"progressbar\" aria-valuenow=\"100\" aria-valuemin=\"100\" aria-valuemax=\"100\" style=\"width:100%\"></div>\n" +
                    "</div>");


                $.ajax({
                    url :"{{ route('admin.setting.config.smtp-test') }}",
                    type:'POST',
                    data: {
                        'recipient'      : recipient,
                    },
                    success:function(response){



                        if(response["status"] == "success" || response["status"] == "info") {


                            $(".smtp-result-space").html(response["message"]);

                            displayMessage("Complete sending email process. Please check the result given", "info")

                        }else {
                            
                            displayMessage(response["message"], response["status"])


                        }


                    }
                });




            }else{


                displayMessage('Please enter recipient_mail', 'danger')



            }

    
        });
    });
    
</script>
@endsection