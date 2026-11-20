<?php

return [

    'email' => [
        'is_test_mode' => env('EMAIL_IS_TEST_MODE', false),
        'test_email_from' => env('TEST_EMAIL_FROM'),
        'test_email_to' => env('TEST_EMAIL_TO')
    ],

];
