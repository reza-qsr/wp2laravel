<?php

return [
    'options_table' => env('WP2L_OPTIONS_TABLE', 'wp_options'),
    'posts_table' => env('WP2L_POSTS_TABLE', 'wp_posts'),
    'postmeta_table' => env('WP2L_POSTMETA_TABLE', 'wp_postmeta'),
    'terms_table' => env('WP2L_TERMS_TABLE', 'wp_terms'),
    'term_relationships_table' => env('WP2L_TERM_RELATIONSHIPS_TABLE', 'wp_term_relationships'),
    'term_taxonomy_table' => env('WP2L_TERM_TAXONOMY_TABLE', 'wp_term_taxonomy'),
    'users_table' => env('WP2L_USER_TABLE', 'wp_users'),
    'usermeta_table' => env('WP2L_USERMETA_TABLE', 'wp_usermeta'),
];

