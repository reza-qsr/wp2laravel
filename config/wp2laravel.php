<?php

return [
    'options_table' => env('WP2L_OPTIONS_TABLE', 'wp_options'),
    'option_cache_ttl' => env('WP2L_OPTION_CACHE_TTL', 0), // ttl cache base on sec (0 mean disable)
];

