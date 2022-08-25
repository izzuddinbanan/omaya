<!-- BEGIN: Vendor CSS-->
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/vendors.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/extensions/toastr.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/forms/select/select2.min.css') }}">
<!-- END: Vendor CSS-->

<!-- BEGIN: Theme CSS-->
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/bootstrap.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/bootstrap-extended.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/colors.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/components.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/themes/dark-layout.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/themes/bordered-layout.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/themes/semi-dark-layout.css') }}">

<!-- BEGIN: Page CSS-->
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/plugins/extensions/ext-component-toastr.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/pages/ui-feather.css') }}">
    

@yield('vendor-css')

<!-- CUSTOM : CSS -->
<link href="{{ url('icons/fontawesome/styles.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('plugins/sweetalert/sweetalert.css') }}" rel="stylesheet">

<!-- DROPIFY -->
<link href="{{ url('plugins/dropify/dist/css/dropify.min.css') }}"    rel="stylesheet" type="text/css" />


<!-- END: Page CSS-->

<!-- BEGIN: Custom CSS-->
<link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/assets/css/style.css') }}">
<!-- END: Custom CSS-->




<style type="text/css">
    body {
       /* -moz-transform: scale(0.8, 0.8); 
        zoom: 0.8; 
        zoom: 80%; */
    }
    
    .loader-spinner {
        background-color: rgb(0 0 0 / 0.4);
        /*background: #f4f3ef;*/
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        bottom: 0;
        z-index: 99;
        overflow: hidden !important;
        animation: rotation 10s infinite linear;
    }

    .loader-spinner img {
        width: 100px;
        height: 100px;
        position: sticky;
        top: 40%;
        left: 50%;
        margin-left: -50px;
        margin-right: -50px;
        -webkit-animation: spin 1s linear infinite;
        animation: spin 1s linear infinite;
        background-color: transparent;
    }

    .file-icon p {
        font-size: 20px !important;
    }


    table.dataTable>thead .sorting:before, table.dataTable>thead .sorting_asc:before, table.dataTable>thead .sorting_desc:before, table.dataTable>thead .sorting_asc_disabled:before, table.dataTable>thead .sorting_desc_disabled:before {
        right: 0px;
        content: "";
    }

    table.dataTable>thead .sorting:after, table.dataTable>thead .sorting_asc:after, table.dataTable>thead .sorting_desc:after, table.dataTable>thead .sorting_asc_disabled:after, table.dataTable>thead .sorting_desc_disabled:after {
        right: 0px;
        content: "";
    }


</style>


@yield('style')
