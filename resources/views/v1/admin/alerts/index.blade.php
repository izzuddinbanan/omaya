<!-- Description -->
@extends('layouts.main')



@section('title', 'Alert')
@section('page-desc', 'View alert data.')


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
                                        <th>Location</th>
                                        <th>Trigger At</th>
                                        <th>Status</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp

                                    @foreach($notifications as $notification)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $notification->rule->name }}</td>
                                            <td>{{ $notification->location->name }} -> {{ $notification->zone->name }} -> {{ $notification->zone->name }}</td>
                                            <td>{{ getDateLocal($notification->trigger_at, session('timezone'), "d M Y h:ia") }}</td>
                                            <td>{{ $notification->updated_at }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-{{ $notification->status == 'new' ? 'danger' : 'secondary'}}">{{ ucfirst($notification->status) }}</button>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-icon btn-success btn-sm mr-1 fa fa-eye" data-bs-toggle="tooltip" title="" data-bs-original-title="View History"></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Name</th>
                                        <th>Trigger At</th>
                                        <th>Location</th>
                                        <th>Status</th>
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