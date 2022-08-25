<!-- Description -->
@extends('layouts.main')



@section('title', 'Settings : User')
@section('page-desc', 'Manage your user data.')

@section('button-right')

    @if(able_to("setting", "role", "rw"))
    <a href="{{ route('admin.setting.user.create') }}">
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
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Created</th>
                                        <th>Role</th>
                                        <th>Permission</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp

                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>
                                            <div class="avatar  me-1"><img src="{{ $user->photo ? $user->getThumbnailImageUrlAttribute(session('tenant_id')) : getNoPhotoAvailable() }}" alt="Avatar" height="32" width="32"></div>    
                                            {{ $user->username }}
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->created_at }}</td>
                                            <td>{{ $user->role }}</td>
                                            <td>
                                                @if($user->permission == "r")
                                                    <span class="badge bg-success">Read</span>
                                                @else
                                                    <span class="badge bg-danger">Read + Write</span>
                                                @endif
                                            </td>
                                            <td>

                                                @if(able_to("setting", "user", "r"))

                                                    <a href="{{ route('admin.setting.user.edit', [$user->id]) }}" class="btn btn-icon btn-success btn-sm mr-1 fa fa-pencil" data-bs-toggle="tooltip" title="" data-bs-original-title="Edit"></a>

                                                @endif

                                                @if(able_to("setting", "user", "rw"))

                                                    <a href="{{ route('admin.setting.user.destroy', [$user->id]) }}" class="ajaxDeleteButton btn btn-icon btn-danger btn-sm mr-1 fa fa-trash" data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"></a>

                                                
                                                @endif


                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Created</th>
                                        <th>Role</th>
                                        <th>Permission</th>
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
        // var table = $('.datatables-ajax').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     ajax: "{{ route('admin.setting.user.data') }}",
        //     columns: [
        //         { data: 'responsive_id' },
        //         { data: 'DT_RowIndex' },
        //         { data: 'username' },
        //         { data: 'email' },
        //         { data: 'created_at' },
        //         { data: 'created_at' },
        //         { data: 'role' },
        //         { data: 'action' },
        //     ],
        //     columnDefs: [
        //         {
        //             className: 'control',
        //             orderable: false,
        //             targets: 0
        //         }
        //     ],
        //     orderCellsTop: true,
        //     responsive: {
        //         details: {
        //             display: $.fn.dataTable.Responsive.display.modal({
        //                 header: function (row) {
        //                     var data = row.data();
        //                     return data['username'];
        //                 }
        //             }),
        //             type: 'column',
        //             renderer: $.fn.dataTable.Responsive.renderer.tableAll({
        //                 tableClass: 'table'
        //             })
        //         }
        //     },
        // });


    });
</script>
@endsection