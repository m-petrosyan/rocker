<?php

return [
    /*
     * Facebook App credentials for Graph API.
     * Create an app at https://developers.facebook.com/
     */
    'app_id' => env('FACEBOOK_APP_ID'),
    'app_secret' => env('FACEBOOK_APP_SECRET'),

    /*
     * Graph API version
     */
    'api_version' => env('FACEBOOK_API_VERSION', 'v22.0'),

    /*
     * Base URL for Graph API
     */
    'api_base_url' => 'https://graph.facebook.com',

    /*
     * Default country/city for imported events (can be overridden per user)
     */
    'default_country' => env('FACEBOOK_DEFAULT_COUNTRY', 'am'),
    'default_city' => env('FACEBOOK_DEFAULT_CITY', 'yerevan'),
];
