<!-- Description -->
@extends('layouts.main')

@section('title', 'Manage : Entity -> Update')
@section('page-desc', 'Manage your entity [Staff, Visitor, Assets] data.')

@section('content')
<!-- Zero configuration table -->
<div class="content-body">
    <section id="input-sizing">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">

                    <form method="POST" action="{{ route('admin.manage.entity.update', $entity->entity_uid) }}">

                        @csrf
                        @method('put')
                        <div class="card-header">
                            <h4 class="card-title">Update Entity [Staff, Visitor, Assets] Record</h4>
                        </div>

                        <div class="card-body">
                            <div class="row">


                                <div class="clearfix"></div>
                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Name <span class="text-danger">* <code>unique</code></span></label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="e.g Technician" autocomplete="off" name="name" value="{{ old('name', $entity->name) }}" autofocus="" required="" tabindex="1" />
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Type <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="type" id="entity_type"  required="" tabindex="2">
                                            <option value="">Please Select</option>
                                            <option value="asset" {{ old('type', $entity->type) ==  "asset" ? 'selected' : ''  }}>Asset</option>
                                            <option value="staff" {{ old('type', $entity->type) ==  "staff" ? 'selected' : ''  }}>Staff</option>
                                            <option value="visitor" {{ old('type', $entity->type) ==  "visitor" ? 'selected' : ''  }}>Visitor</option>
                                        </select>
                                        <div>
                                            <div class="@error('type') is-invalid @enderror"></div>
                                            @error('type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Device [ Tracker ] </label>
                                        <select class="select2 form-control" name="device" tabindex="2">
                                            <option value="">Please Select</option>
                                            @foreach($devices as $device)
                                                <option value="{{ $device->device_uid }}" {{ old('device', $entity->device_tracker_uid) ==  $device->device_uid ? 'selected' : ''  }}>{{ ucfirst($device->name) }}</option>
                                            @endforeach
                                        </select>
                                        <div>
                                            <div class="@error('device') is-invalid @enderror"></div>
                                            @error('device')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                

                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Group </label>
                                        <select class="select2 form-control" name="group"  required="" tabindex="2">
                                            <option value="">Please Select</option>
                                            @foreach($groups as $group)
                                                <option value="{{ $group->group_uid }}" {{ old('group', $entity->group_uid) ==  $group->group_uid ? 'selected' : ''  }}>{{ ucfirst($group->name) }}</option>
                                            @endforeach
                                        </select>
                                        <div>
                                            <div class="@error('group') is-invalid @enderror"></div>
                                            @error('group')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 pb-1" style="display: none" id="field_meet_with">
                                    <div class="form-group">
                                        <label for="name">Meet With [ Employee ] </label>
                                        <select class="select2 form-control" name="meet_with" tabindex="2">
                                            <option value="">Please Select</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->entity_uid }}" {{ old('meet_with', $entity->meet_entity_uid) ==  $user->entity_uid ? 'selected' : ''  }}>{{ ucfirst($user->name) }}</option>
                                            @endforeach
                                        </select>
                                        <div>
                                            <div class="@error('meet_with') is-invalid @enderror"></div>
                                            @error('meet_with')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12"></div>


                                <div class="col-md-12"></div>

                                <div class="col-md-6">
                                    <div class="form-group pb-1">
                                        <label for="name">Remark</label>
                                        <textarea name="remark" class="form-control @error('remark') is-invalid @enderror" tabindex="3" placeholder="e.g Visitor - IT Technician ...">{{ old('remark', $entity->remarks) }}</textarea>
                                        @error('remark')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12"></div>

                                <div class="col-12">
                                    <a href="{{ route('admin.manage.entity.index') }}">
                                        <button type="button" class="btn btn-outline-secondary waves-effect" tabindex="5">Back</button>
                                    </a>
                                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light" tabindex="4">Update</button>
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

    onchangeEntityType();
    $(document).ready(function(){


        $("#entity_type").change(function() {
            
            onchangeEntityType()

        })


    })

    function onchangeEntityType() {

        let type = $("#entity_type").val();

        $("#field_meet_with").hide();
        if(type == "visitor") {
            $("#field_meet_with").show();
        }

    }
</script>
@endsection