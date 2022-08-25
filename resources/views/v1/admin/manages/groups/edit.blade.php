<!-- Description -->
@extends('layouts.main')

@section('title', 'Manage : Group -> Update')
@section('page-desc', 'Manage your group data.')

@section('content')
<!-- Zero configuration table -->
<div class="content-body">
    <section id="input-sizing">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">

                    <form method="POST" action="{{ route('admin.manage.group.update', [$group->group_uid]) }}">

                        @csrf
                        @method('put')

                        <div class="card-header">
                            <h4 class="card-title">Update Group Record</h4>
                        </div>

                        <div class="card-body">
                            <div class="row">


                                <div class="clearfix"></div>
                                <div class="col-md-6 pb-1">
                                    <div class="form-group">
                                        <label for="name">Name <span class="text-danger">* <code>unique</code></span></label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="e.g Technician" autocomplete="off" name="name" value="{{ old('name', $group->name) }}" autofocus="" required="" tabindex="1" />
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12"></div>


                                <div class="col-md-12"></div>

                                <div class="col-md-6">
                                    <div class="form-group pb-1">
                                        <label for="name">Remark</label>
                                        <textarea name="remark" class="form-control @error('remark') is-invalid @enderror" tabindex="3" placeholder="e.g Mall ...">{{ old('remark', $group->remark) }}</textarea>
                                        @error('remark')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12"></div>

                                <div class="col-12">
                                    <a href="{{ route('admin.manage.group.index') }}">
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
</script>
@endsection