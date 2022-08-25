<!-- Description -->
@extends('layouts.main')

@section('title', 'Settings : Role -> Create')

@section('page-desc', 'Manage your role data.')


@section('content')
<!-- Zero configuration table -->
<div class="content-body">
    <section id="input-sizing">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">

                    <form method="POST" action="{{ route('admin.setting.role.store') }}">

                        @csrf

                        <div class="card-header">
                            <h4 class="card-title">Add New Role Record</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="clearfix"></div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name <span class="text-danger">*</span></label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="e.g Operator" autocomplete="off" name="name" value="{{ old('name') }}" autofocus="" required="" tabindex="1" />
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12"></div>

                               <!--  <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Allowed Venue</label>
                                        <select class="form-control select2" multiple name="allowed_venue_id[]">
                                            @foreach($venues as $venue)
                                            <option value="{{ $venue->venue_uid }}">{{ $venue->venue_name }}</option>
                                            @endforeach

                                        </select>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div> -->

                                <div class="col-md-12"></div>

                                <div class="col-md-6">
                                    <label for="name">Allow Access</label>
                                    <input class="form-control filter" type="text" placeholder="Filter" autocomplete="off" value=""tabindex="1" />
                                </div>

                                <div class="col-md-6 mt-2">
                                    <button type="button" class="btn btn-primary mr-1 waves-effect waves-float waves-light select-all">Select All</button>
                                    <button type="button" class="btn btn-danger mr-1 waves-effect waves-float waves-light clear-all">Clear All</button>
                                </div>
                                
                                <div class="col-md-12"></div>

                                <div class="col-md-6 all-module">
                                    @foreach($modules as $module)
                                        <div class="custom-control custom-checkbox mt-1 group-name field-{{ $module->group }}" >
                                            <input type="checkbox" class="custom-control-input check-all-module" data-module="{{ $module->group }}" id="{{ $module->group }}" name="module"/>
                                            <label class="custom-control-label" for="{{ $module->group }}"><span style="font-size: 16px; font-weight:bold">{{ strtoupper($module->group) }}</span></label>
                                        </div>

                                        @foreach($submodules as $submodule)
                                            @if($submodule->group == $module->group)
                                                <div class="custom-control custom-checkbox role-name field-{{ str_replace(':','-', $submodule->name) }}" >
                                                    <input type="checkbox" class="custom-control-input {{ $module->group }}" name="submodule[]" value="{{$submodule->name}}" id="{{$submodule->name}}" />
                                                    <label class="custom-control-label" for="{{$submodule->name}}">{{ ucwords(str_replace($module->group.':','', $submodule->name )) }}</label>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </div>

                                <div class="col-12 mt-2">
                                    <a href="{{ route('admin.setting.role.index') }}">
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

<script type="text/javascript">


    $(function(){
        
        $('body').on('change','.check-all-module', function(){
            var module = $(this).data('module');
            
            if($(this).is(':checked')) $('.'+ module).prop('checked', true);
            else $('.'+ module).prop('checked', false);
        });

        $('.select-all').on('click', function(){
            $('input[type="checkbox"]').prop('checked', true);
        });

        $('.clear-all').on('click', function(){
            $('input[type="checkbox"]').prop('checked', false);
        });

        $('.filter').on('keyup keyin', function(){
            var filter = $(this).val();

            $.ajax({
                url: {!! json_encode(route('admin.setting.role.ajax_filter')) !!},
                data: {'filter': filter,  '_token':' {!! csrf_token() !!}' }, 
                type: "GET",
                success: function(res){
                    
                    $('.group-name').hide();
                    $('.role-name').hide();

                    res.module.forEach(module => {
                        console.log(module);
                        $('.field-'+ module.group).show();
                    });

                    res.sub.forEach(submodule => {
                        console.log(submodule);

                        var name = submodule.name.replace(':','-')
                        $('.field-'+ name).show();
                    })
                }
            });
        })
    });

</script>
@endsection