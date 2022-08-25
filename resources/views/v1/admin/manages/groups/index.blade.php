<!-- Description -->
@extends('layouts.main')



@section('title', 'Manage : Group')
@section('page-desc', 'Manage your group for entity data.')

@section('button-right')

    @if(able_to("manage", "group", "rw"))
        <a href="{{ route('admin.manage.group.create') }}">
            <button type="button" class="btn btn-primary btn-sm">
                <i data-feather="plus" class="mr-25"></i>
                <span>Add New</span>
            </button>
        </a>
    @endif
@endsection

@section('vendor-css')
    @include('layouts.components.datatables.css')
@endsection

@section('content')
<!-- Zero configuration table -->
<div class="content-body">

    <!-- Ajax Sourced Server-side -->
    <section id="ajax-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-datatable">

                            <table class="datatables-ajax table">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Name</th>
                                        <th>Remark</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp

                                    @foreach($groups as $group)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $group->name }}</td>
                                            <td>{{ $group->remark }}</td>
                                            <td>{{ $group->updated_at }}</td>
                                            <td>

                                                @if(able_to("manage", "group", "r"))

                                                    <a href="{{ route('admin.manage.group.edit', [$group->group_uid]) }}" class="btn btn-icon btn-success btn-sm mr-1 fa fa-pencil" data-bs-toggle="tooltip" title="" data-bs-original-title="Edit"></a>

                                                   
                                                @endif

                                                @if(able_to("manage", "group", "rw"))

                                                    <a href="{{ route('admin.manage.group.destroy', [$group->group_uid]) }}" class="ajaxDeleteButton btn btn-icon btn-danger btn-sm mr-1 fa fa-trash" data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"></a>

                                                @endif


                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Remark</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
<style type="text/css">
    .dt-buttons {
        float: right !important;
    }
</style>

@endsection

@section('vendor-js')
@include('layouts.components.datatables.js')
@endsection

@section('script')


<script type="text/javascript">


    $(document).ready(function() {

        $('.datatables-ajax').DataTable();


    });
</script>
@endsection