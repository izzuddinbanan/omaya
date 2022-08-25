<?php

return [
    'actions' => ['create', 'update', 'delete', 'view'],
    'action_colors' => ['create'=> '#28a745', 'update'=>'#17a2b8', 'delete'=> '#dc3545', 'view'=>'#007bff'],

    'module_name' => [
        
        //Dashboard
        'DashboardController'           => 'general:dashboard',

        // Manage
        'ManageLocationController'      => 'manage:location',
        'ManageVenueController'         => 'manage:venue',
        'ManageZoneController'          => 'manage:zone',
        'ManageDeviceApController'      => 'manage:device-ap',
        'ManageDeviceTrackerController' => 'manage:device-tracker',
        'ManageEntityController'        => 'manage:entity',
        'ManageGroupController'         => 'manage:group',
        'ManageRuleController'          => 'manage:rule',

        // Monitor
        'MonitorDeviceApController'     => 'monitor:device-ap',
        'MonitorDeviceTrackerController'     => 'monitor:device-tracker',
        'MonitorVenueController'     => 'monitor:venue',
        'MonitorServiceController'      => 'monitor:service',
        'MonitorSchedulerController'    => 'monitor:scheduler',

        // Help Tools
        'HelpToolController'            => 'help:service',
        'deviceBlacklist'               => 'help:device-blacklist',
        'blacklist'                     => 'help:device-blacklist',
        'whitelist'                     => 'help:device-blacklist',

        //Setting
        'SettingRolesController'        => 'setting:role',
        'SettingUserController'         => 'setting:user',
        'SettingConfigController'       => 'setting:config',
        'SettingFilteringController'    => 'setting:filtering',

        //cloud
        'CloudTenantController'     => 'cloud:tenant',
        
        //analytics
        'AnalyticsBenchmarkController'           => 'analytics:benchmark',
        'AnalyticsHeatmapController'             => 'analytics:heatmap',
        'AnalyticsCrossvisitController'          => 'analytics:cross-visit',
        'AnalyticsDwelltimeController'           => 'analytics:dwell-time',
        'AnalyticsLoyaltyDistributionController' => 'analytics:loyalty-distribution',
        'AnalyticsUniqueVisitController'         => 'analytics:unique-visit',
        'AnalyticsVisitController'               => 'analytics:visit',
        'AnalyticsCrossPathController'           => 'analytics:cross-path',
        'AnalyticsEntryExitController'           => 'analytics:enter-exit',
        'AnalyticsVenueMapController'            => 'analytics:venue-map',
    ],
];
