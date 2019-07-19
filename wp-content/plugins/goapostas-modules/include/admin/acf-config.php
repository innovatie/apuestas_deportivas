<?php

if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(array(
        'page_title'    => 'Ad Locations',
        'menu_title'    => 'Ad Locations',
        'menu_slug'     => 'ad-locations-settings',
        'capability'    => 'edit_posts',
        // 'position'      => 14,
        'redirect'      => false
    ));
}