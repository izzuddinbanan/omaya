<!-- Description -->
@extends('layouts.main')



@section('title', 'Manage : Rule')
@section('page-desc', 'Manage your rule data.')

@section('button-right')

    @if(able_to("manage", "rule", "rw"))
        <a href="{{ route('admin.manage.rule.create') }}">
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
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Event</th>
                                        <th>Start/End Time</th>
                                        <th>Status</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Event</th>
                                        <th>Start/End Time</th>
                                        <th>Status</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp

                                    @foreach($rules as $rule)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $rule->name }}</td>
                                            
                                            <td>{{ ucwords(str_replace("_", " ", $rule->event)) }}</td>
                                            <td>{{ ucwords($rule->start_time_action) }} - {{ ucwords($rule->stop_time_action) }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-{{ $rule->is_active ? 'success' : 'secondary'}}">{{ $rule->is_active ? 'Active' : 'Inactive' }}</button>
                                            </td>
                                            <td>{{ $rule->updated_at }}</td>
                                            <td>

                                                @if(able_to("manage", "location", "r"))
                                                    <a href="{{ route('admin.manage.rule.edit', [$rule->rule_uid]) }}" class="btn btn-icon btn-success btn-sm mr-1 fa fa-pencil" data-bs-toggle="tooltip" title="" data-bs-original-title="Edit"></a>
                                                @endif


                                                @if($rule->is_default == false)
                                                    @if(able_to("manage", "location", "rw"))

                                                        <a href="{{ route('admin.manage.rule.destroy', [$rule->rule_uid]) }}" class="ajaxDeleteButton btn btn-icon btn-danger btn-sm mr-1 fa fa-trash" data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"></a>

                                                    @endif
                                                @endif

                                


                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            
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

    //     $('.datatables-ajax').DataTable({
    //         processing: true,
    //         serverSide: true,
    //         ajax: "{!! route('admin.manage.venue.index') !!}",
    //         columns: [
    //             { data : 'responsive_id' },
    //             { data : 'DT_RowIndex',  'orderable' : false, 'searchable' : false },
    //             { data : 'name', name : 'name' },
    //             { data : 'location.name', name : 'location.name' },
    //             { data : 'image', name: 'image', 'orderable' : false, 'searchable' : false },
    //             { data : 'updated_at', name: 'updated_at' },
    //             { data : 'action', name : 'action', 'orderable' : false, 'searchable' : false },
    //         ],
    //         columnDefs: [
    //             {
    //                 className: 'control',
    //                 orderable: false,
    //                 targets: 0
    //             }
    //         ],
    //         order: [[ 5, "desc" ]],
    //         orderCellsTop: true,
    //         responsive: {
    //             details: {
    //                 display: $.fn.dataTable.Responsive.display.modal({
    //                     header: function (row) {
    //                         var data = row.data();
    //                         return data['name'];
    //                     }
    //                 }),
    //                 type: 'column',
    //                 renderer: $.fn.dataTable.Responsive.renderer.tableAll({
    //                     tableClass: 'table'
    //                 })
    //             }
    //         },
    // });


    });
</script>
@endsection