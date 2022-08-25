<?php

return [

    'tenant_name' 			=> 'default',
    'multi_tenant' 			=> env('MULTI_TENANT', false),
    'random_uid_length' 	=> env('RANDOM_UID_LENGTH', 8),
    'date_format'           => env('DATE_FORMAT', "d M Y"),

];
