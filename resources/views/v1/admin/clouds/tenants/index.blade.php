<!-- Description -->
@extends('layouts.main')

@section('title', 'Clouds : Tenants')

@section('page-desc', 'Manage and configure Tenant.')



@section('button-right')

    @if(able_to("cloud", "tenant", "rw"))
    <a href="{{ route('admin.cloud.tenant.create') }}">
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
                                        <th>Tenant ID</th>
                                        <th>TimeZone</th>
                                        <th>Is Active</th>
                                        <th>Expiry Date</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp

                                    @foreach($clouds as $cloud)
                                    <tr>
                                        <td>{{ $i++; }}</td>
                                        <td>{{ $cloud->name }}</td>
                                        <td>{{ $cloud->tenant_id }}</td>
                                        <td>{{ $cloud->timezone }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-{{ $cloud->is_active ? 'success' : 'secondary'}}">{{ $cloud->is_active ? 'Active' : 'Inactive' }}</button>
                                            
                                        </td>
                                        <td>{{ date('d M Y', strtotime($cloud->expired_at)) }}</td>
                                        <td>{{ $cloud->updated_at }}</td>
                                        <td>
                                            @if(able_to("cloud", "tenant", "r"))

                                                <a href="{{ route('admin.cloud.tenant.edit', [$cloud->id]) }}" class="btn btn-icon btn-success btn-sm mr-1 fa fa-pencil" data-bs-toggle="tooltip" title="" data-bs-original-title="Edit"></a>

                                            @endif

                                            @if($cloud->tenant_id != 'default')
                                                @if(able_to("cloud", "tenant", "rw"))
                                                <!-- <a href="{{ route('admin.cloud.tenant.destroy', [$cloud->name]) }}" class="ajaxDeleteButton"> -->
                                                    <!-- <i class="fa fa-trash text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"></i> -->
                                                <!-- </a> -->
                                                @endif
                                                
                                                @if(able_to("cloud", "tenant", "rw"))

                                                <a href="{{ route('admin.cloud.tenant.suspend', [$cloud->name]) }}" class="btn btn-icon btn-success btn-sm mr-1 fa fa-ban ajaxSuspendButton" data-bs-toggle="tooltip" title="" data-bs-original-title="{{ $cloud->is_active ? 'Block' : 'Unblock' }} Account"></a>
                                                
                                                @endif
                                            
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Name</th>
                                        <th>Tenant ID</th>
                                        <th>TimeZone</th>
                                        <th>Is Active</th>
                                        <th>Expiry Date</th>
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
        // var table = $('.datatables-ajax').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     ajax: "{{ route('admin.cloud.tenant.data') }}",
        //     columns: [
        //         { data: 'responsive_id' },
        //         { data: 'DT_RowIndex' },
        //         { data: 'name' },
        //         { data: 'tenant_id' },
        //         { data: 'timezone' },
        //         { data: 'email' },
        //         { data: 'phone' },
        //         { data: 'address' },
        //         { data: 'deleted_at' },
        //         { data: 'updated_at' },
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
        //                     return data['name'];
        //                 }
        //             }),
        //             type: 'column',
        //             renderer: $.fn.dataTable.Responsive.renderer.tableAll({
        //                 tableClass: 'table'
        //             })
        //         }
        //     },
        // });


        $('body').on('click', '.ajaxSuspendButton', function(e){
            e.preventDefault();

            var msg = "This data will not be re-usable!";

            if ($(this).hasClass('ajaxSuspendButton')){
                var msg = "Are you sure to block/unblock this tenant from access Omaya?";
            }

            var url = $(this).attr('href');
            swal({
                title: "Are You Sure?",
                text: msg,
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn btn-success btn-fill",
                confirmButtonText: "Yes, Continue!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            },
            function(isConfirm) {

                if (isConfirm) {
                    $.ajax({
                        url: url,
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: 'post',
                        dataType: 'json',
                        success: function (response) {

                            var success_message = 'Tenant successfully update';
                            var error_message = 'Error while process';
                            
                            if(response.status=='ok'){
                                displayMessage(success_message, 'success', true)
                            }else{
                                displayMessage(error_message.message, 'danger')
                                close_swal();
                            }
                        },
                        error: function(xhr, status, error) {

                            setTimeout(function() {
                                location.reload(true);
                            }, 1000);

                        }
                    });
                }

            });

        });


    });
</script>
@endsection