<!-- Description -->
@extends('layouts.main')


@section('title', 'Manage : Device [ Access Point ] -> Import')
@section('page-desc', 'Manage your Device [AP] data.')


@section('content')
<!-- Zero configuration table -->
<div class="content-body">
    <section id="input-sizing">

        <div class="row match-height">
            <div class="col-md-12 col-12">
                @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
                @endif

                <div class="card">

                    <form method="POST" action="{{ route('admin.manage.device.insert-import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="clearfix"></div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Select File to Import <span class="text-danger">*<code>Type: csv</code></span></label>
                                        <input class="form-control @error('upload_file') is-invalid @enderror" type="file" autocomplete="off" name="upload_file" value="{{ old('file') }}" accept=".csv" required="" tabindex="6" />
                                        @error('upload_file')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group pt-2">
                                        <a href="#">
                                            <span data-href="{{ route('admin.manage.device.export') }}" id="btn-export" class="btn btn-danger btn-sm" >Download Template</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary  waves-effect waves-float waves-light mt-2">Submit</button>
                                    <a href="{{ route('admin.manage.device-ap.index') }}">
                                        <button type="button" class="btn btn-outline-secondary waves-effect mt-2">Back</button>
                                    </a>
                                </div>
                            </div>

                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    {{ $error }}
                                @endforeach
                            </div>

                            @if(session("import-error"))
                            
                            <div class="alert alert-danger" style="padding: 5px">
                                <p>You have error at: </p>
                                
                                @foreach(session("import-error") as $key_arr => $arr_msg)
                                    Row {{ ($key_arr + 1) }}
                                    
                                    @foreach($arr_msg as $message)
                                        <li>{{ $message[0] }}</li>
                                    @endforeach
                                    <br>

                                @endforeach

                            </div>

                            @endif

                            @if(session()->has('data-success') && session()->get('data-success') > 0)
                                <div class="alert alert-success" style="padding: 5px">
                                    <!-- <p>Successfully import data.</p> -->
                                    <p>Successfully import {{ session()->get('data-success') }} number of row.</p>
                                </div>
                            @endif


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

<script type="text/javascript"></script>

<script>
    $(function(){
        $('#btn-export').on('click', function(){

            window.location.href = $(this).data('href');
        });
    });
</script>
@endsection