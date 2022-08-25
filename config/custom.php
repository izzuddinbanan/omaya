<?php

return [

    'omaya_type' => [
        "crowd"         => "crowd",
        "workspace"     => "Workspace",
        "vision"        => "Vision",
    ],
    'venue_type' => [
        'mall'      => 'mall',
        'outlet'    => 'outlet',
        'zone'      => 'zone',
    ],
    'access_point_type' => [
        "huawei" => "Huawei",
        "cambium" => "Cambium",
        "mikrotik" => "Mikrotik",
        "meraki" => "Meraki",
        "ruckus" => "Ruckus",
        "ruijie" => "Ruijie",
        "aruba" => "Aruba",
    ],
    'device_ap' => [
        'Access Point'  => 'Access Point',
        'Kontakt'       => 'Kontakt',
        'Xirrus'        => 'Xirrus',
        'Meraki'        => 'Meraki',
        'Ubiquiti'      => 'Ubiquiti',
        'CMX'           => 'CMX',
        'Cambium'       => 'Cambium',
        'Sundray'       => 'Sundray',
        'Extreme'       => 'Extreme',
        'Ruckus'        => 'Ruckus',
        'Nokia'         => 'Nokia',
        'Aruba'         => 'Aruba',
    ],
    'force_exit' => [
        'time'          => 'Time',
        'no_of_second'  => 'No of Second',
    ],
    'rules' => [
        'type' => [
            "location"      => "Location",
            "venue"         => "Location & Venue",
            "zone"          => "Location & Venue & Zone",
            "device"        => "Device",
            "device_group"  => "Device Group",
        ],
        'event_device' => [
            // "battery_level" => "Battery Level",
            // "button_click"  => "Button Click",
            "enter_check"   => "Enter",
            "exit_check"    => "Exit",
            // "dwell_check"   => "Dwell",
            // "exist"         => "Exist",
            // "absence"       => "Absence",
        ],
        'comparison' => [
            "equal"         => "Equal",
            "not"           => "Not Equal To",
            "less_than"     => "Less Than",
            "higher_than"   => "Higher Than",
            "contain"       => "Contain",
        ],
        'trigger' => [
            "alert" => "Alert [Web Notification]",
            "email" => "Email",
            // "sms"   => "SMS",
        ],
        'event_location' => [
            "temperature"   => "Temperature",
            "humidity"      => "Humidity",
            "count"         => "Count",
        ],
    ],

];
