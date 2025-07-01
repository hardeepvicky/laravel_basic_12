<?php

return [
    'path' => [
        'temp' => 'user_files/temp/',
    ],

    'font_awesome_icon' => [
        'summary' => 'fas fa-table',
        'create' => 'fas fa-plus-circle',
    ],

    'yes_no_list' => [
        1 => "Yes",
        0 => "No",
    ],

    'success_fail_list' => [
        1 => "Success",
        0 => "Fail",
    ],

    "cache_time" => [
        "permission" => 60 * 24 * 365,
        "menu" => 60 * 24 * 365,
        "model" => 60 * 24,
        "summary_search" => 60 * 24 * 30
    ],

    'msg_type' => [
        'otp' => 'otp',
        'registration' => 'registration',
        'forgot_password' => 'forgot_password',

        'list' => [
            'otp' => 'OTP',
            'registration' => 'User Registration',
            'forgot_password' => 'Forgot Password',
        ]
    ],
];
