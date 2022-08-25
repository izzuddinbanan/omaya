<!-- Description -->
@extends('layouts.main')



@section('title', 'Manage : Device [ Tracker ]')
@section('page-desc', 'Manage your Device [tracker] data.')



@section('button-right')

    @if(able_to("manage", "device-tracker", "rw"))
        <a href="{{ route('admin.manage.device-tracker.create') }}">
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
                                        <th>Mac Address</th>
                                        <th>is Active</th>
                                        @if(session('omaya_type') == "workspace")
                                        <th>Battery</th>
                                        @endif
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp

                                    @foreach($devices as $device)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $device->name }}</td>
                                            <td>{{ $device->mac_address_separator }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-{{ $device->is_active ? 'success' : 'secondary'}}">{{ $device->is_active ? 'Active' : 'Inactive' }}</button>

                                            </td>
                                                @if(session('omaya_type') == "workspace")
                                                    <td>

                                                        <?php

                                                            $omy_cache          = redisCache();

                                                            $battery = $omy_cache->hGetAll("WORKSPACE:TRACKER:STATUS:".session('tenant_id').":{$device->mac_address}");
                                                            if($battery) {

                                                                echo (isset($battery["battery_level"]) ? ($battery["battery_level"] . "%") : "-");
                                                            }else {

                                                                echo "-";
                                                            }
                                                        ?>
                                                    </td>
                                                @endif

                                            <td>{{ $device->updated_at }}</td>
                                            <td>

                                                @if(able_to("manage", "device-tracker", "r"))

                                                    <a href="{{ route('admin.manage.device-tracker.edit', [$device->device_uid]) }}" class="btn btn-icon btn-success btn-sm mr-1 fa fa-pencil" data-bs-toggle="tooltip" title="" data-bs-original-title="Edit"></a>
                                                 
                                                @endif

                                                @if(able_to("manage", "location", "rw"))

                                                    <a href="{{ route('admin.manage.device-tracker.destroy', [$device->device_uid]) }}" class="ajaxDeleteButton btn btn-icon btn-danger btn-sm mr-1 fa fa-trash" data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"></a>

                                                @endif


                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Name</th>
                                        <th>Mac Address</th>
                                        <th>is Active</th>
                                        @if(session('omaya_type') == "workspace")
                                        <th>Battery</th>
                                        @endif
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

        // $('.datatables-ajax').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     ajax: "{!! route('admin.manage.location.index') !!}",
        //     columns: [
        //         { data : 'responsive_id' },
        //         { data : 'DT_RowIndex',  'orderable' : false, 'searchable' : false },
        //         { data : 'name', name : 'name' },
        //         { data : 'updated_at', name: 'updated_at' },
        //         { data : 'action', name : 'action', 'orderable' : false, 'searchable' : false },
        //     ],
        //     columnDefs: [
        //         {
        //             className: 'control',
        //             orderable: false,
        //             targets: 0
        //         }
        //     ],
        //     order: [[ 3, "desc" ]],
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






        // $('.datatables-ajax').dataTable({
        //     processing: true,
        //     serverSide: true,
        //     dom:
        //         '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        //     ajax: { 
        //         "url" : {!! json_encode(route('admin.manage.location.index')) !!},
        //         "method" : "GET",
                
        //     },
        //     columns: [
        //         { data: 'responsive_id' },
        //         { data: 'raw_count' },
        //         { data: 'name' },
        //         { data: 'updated_at' },
        //         { data: 'action' }
        //     ],
        //     columnDefs: [
        //         {
        //             className: 'control',
        //             orderable: false,
        //             targets: 0
        //         }
        //     ],
        //     // dom: '<"card-header border-bottom p-1"<"head-label"><"dt-action-buttons text-right"B>><"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        //     // orderCellsTop: true,
        //     // buttons: [
        //     //     {
        //     //         extend: 'collection',
        //     //         className: 'btn btn-outline-secondary dropdown-toggle mr-2',
        //     //         text: feather.icons['share'].toSvg({ class: 'font-small-4 mr-50' }) + 'Export',
        //     //         buttons: [
        //     //             {
        //     //               extend: 'print',
        //     //               text: feather.icons['printer'].toSvg({ class: 'font-small-4 mr-50' }) + 'Print',
        //     //               className: 'dropdown-item',
        //     //             },
        //     //             {
        //     //               extend: 'csv',
        //     //               text: feather.icons['file-text'].toSvg({ class: 'font-small-4 mr-50' }) + 'Csv',
        //     //               className: 'dropdown-item',
        //     //             },
        //     //             {
        //     //               extend: 'excel',
        //     //               text: feather.icons['file'].toSvg({ class: 'font-small-4 mr-50' }) + 'Excel',
        //     //               className: 'dropdown-item',
        //     //             },
        //     //             {
        //     //               extend: 'pdf',
        //     //               text: feather.icons['clipboard'].toSvg({ class: 'font-small-4 mr-50' }) + 'Pdf',
        //     //               className: 'dropdown-item',
        //     //             },
        //     //             {
        //     //               extend: 'copy',
        //     //               text: feather.icons['copy'].toSvg({ class: 'font-small-4 mr-50' }) + 'Copy',
        //     //               className: 'dropdown-item',
        //     //             }
        //     //         ],
        //     //         init: function (api, node, config) {
        //     //             $(node).removeClass('btn-secondary');
        //     //             $(node).parent().removeClass('btn-group');
        //     //             setTimeout(function () {
        //     //               $(node).closest('.dt-buttons').removeClass('btn-group').addClass('d-inline-flex');
        //     //             }, 50);
        //     //         }
        //     //     },
        //     //     // {
        //     //     //     text: feather.icons['plus'].toSvg({ class: 'mr-50 font-small-4' }) + 'Add New Record',
        //     //     //     className: 'create-new btn btn-primary btn-create-new-record',
        //     //     //     attr: {
        //     //     //         'data-target': {!! json_encode(url('/')) !!}
        //     //     //     },
        //     //     //     init: function (api, node, config) {
        //     //     //         $(node).removeClass('btn-secondary');
        //     //     //     }
        //     //     // }
        //     // ],
        //     responsive: {
        //         details: {
        //             display: $.fn.dataTable.Responsive.display.modal({
        //                 header: function (row) {
        //                     var data = row.data();
        //                     return 'Details of ' + data['full_name'];
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