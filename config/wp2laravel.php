<?php

return [
    'options_table' => env('WP2L_OPTIONS_TABLE', 'wp_options'),
    'option_cache_ttl' => env('WP2L_OPTION_CACHE_TTL', 0), // ttl cache base on sec (0 mean disable)


    'posts_table' => env('WP2L_POSTS_TABLE', 'wp_posts'),
    'postmeta_table' => env('WP2L_POSTMETA_TABLE', 'wp_postmeta'),

    'term_relationships_table' => env('WP2L_TERM_REL_TABLE', 'wp_term_relationships'),
    'term_taxonomy_table' => env('WP2L_TERM_TAX_TABLE', 'wp_term_taxonomy'),
    'terms_table' => env('WP2L_TERMS_TABLE', 'wp_terms'),

    'permalink_structure' => env('WP2L_PERMALINK', '/%postname%/'),
    'post_repository' => env('WP2L_POST_REPO', 'db'),
];

