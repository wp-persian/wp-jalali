<?php
/**
 * register admin menu
 */
add_action('admin_menu', 'ztjalali_reg_admin_meun_fn');

function ztjalali_reg_admin_meun_fn() {
    add_menu_page(
            __('wp-jalali options', 'ztjalali'), // page title 
            __('wp-jalali', 'ztjalali'), // menu title
            'manage_options', // user access capability
            'ztjalali_admin_page', // menu slug
            'ztjalali_admin_page_fn', //menu content function
//            plugins_url('/assets/img/wp-jalali-16x16.png', dirname(__FILE__)), // menu icon
            'dashicons-ztjalali', // menu icon
            82 // menu position
        );
}
function ztjalali_admin_page_fn() {
    include JALALI_DIR .'inc'.DIRECTORY_SEPARATOR .'wp-jalali-admin-option.php';
}
/* =================================================================== */

/**
 * wp planet options
 */
add_filter('dashboard_primary_link', 'ztjalali_dashboard_primary_link', 111, 1);
add_filter('dashboard_primary_feed', 'ztjalali_dashboard_primary_feed', 111, 1);
add_filter('dashboard_primary_title', 'ztjalali_dashboard_primary_title', 111, 1);

add_filter('dashboard_secondary_link', 'ztjalali_dashboard_secondary_link', 111, 1);
add_filter('dashboard_secondary_feed', 'ztjalali_dashboard_secondary_feed', 111, 1);
add_filter('dashboard_secondary_title', 'ztjalali_dashboard_secondary_title', 111, 1);

global $primary_replacement, $secondary_replacement;
$primary_replacement = array(
    1 => array(
        'link' => 'http://wordpress.org/development/',
        'feed' => 'http://wordpress.org/development/feed/',
        'title' => __('WordPress Development Blog','ztjalali')
    ),
    2 => array(
        'link' => 'http://wp-persian.com/',
        'feed' => 'http://wp-persian.com/feed/',
        'title' =>  __('wp-persian news Blog','ztjalali')
    )
);
$secondary_replacement = array(
    1 => array(
        'link' => 'http://planet.wordpress.org/',
        'feed' => 'http://planet.wordpress.org/feed/',
        'title' =>  __('Other WordPress News','ztjalali')
    ),
    2 => array(
        'link' => 'http://planet.wp-persian.com/',
        'feed' => 'http://planet.wp-persian.com/feed/',
        'title' =>  __('wp-persian planet','ztjalali')
    )
);

function ztjalali_dashboard_primary_link($value) {
    global $primary_replacement, $ztjalali_option;
    if ($ztjalali_option['persian_planet']) {
        return $primary_replacement[2]['link'];
    }
    return $value;
}

function ztjalali_dashboard_primary_feed($value) {
    global $primary_replacement, $ztjalali_option;
    if ($ztjalali_option['persian_planet']) {
        return $primary_replacement[2]['feed'];
    }
    return $value;
}

function ztjalali_dashboard_primary_title($value) {
    global $primary_replacement, $ztjalali_option;
    if ($ztjalali_option['persian_planet']) {
        return $primary_replacement[2]['title'];
    }
    return $value;
}

function ztjalali_dashboard_secondary_link($value) {
    global $secondary_replacement, $ztjalali_option;
    if ($ztjalali_option['persian_planet']) {
        return $secondary_replacement[2]['link'];
    }
    return $value;
}

function ztjalali_dashboard_secondary_feed($value) {
    global $secondary_replacement, $ztjalali_option;
    if ($ztjalali_option['persian_planet']) {
        return $secondary_replacement[2]['feed'];
    }
    return $value;
}

function ztjalali_dashboard_secondary_title($value) {
    global $secondary_replacement, $ztjalali_option;
    if ($ztjalali_option['persian_planet']) {
        return $secondary_replacement[2]['title'];
    }
    return $value;
}
/* =================================================================== */

