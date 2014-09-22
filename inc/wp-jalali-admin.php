<?php

/**
 * register admin menu
 * @see http://codex.wordpress.org/Administration_Menus
 */
add_action('admin_menu', 'ztjalali_reg_admin_meun_fn');

function ztjalali_reg_admin_meun_fn() {
    global $ztjalali_admin_page;
    $ztjalali_admin_page = add_menu_page(
            __('WP Jalali Options', 'ztjalali'), // page title 
            __('WP Jalali', 'ztjalali'), // menu title
            'manage_options', // user access capability
            'ztjalali_admin_page', // menu slug
            'ztjalali_admin_page_fn', //menu content function
//            plugins_url('/assets/img/wp-jalali-16x16.png', dirname(__FILE__)), // menu icon
            'dashicons-ztjalali', // menu icon
            82 // menu position
    );
    add_submenu_page('ztjalali_admin_page', __('WP Jalali About', 'ztjalali'), __('About', 'ztjalali'), 'manage_options', 'ztjalali_help_page', 'ztjalali_help_page_fn');
    add_action('load-' . $ztjalali_admin_page, 'ztjalali_admin_save_option_page_fn');
}

function ztjalali_admin_page_fn() {
    include JALALI_DIR . 'inc' . DIRECTORY_SEPARATOR . 'wp-jalali-admin-option.php';
}

function ztjalali_help_page_fn() {
//    wp_enqueue_style( 'wp-pointer' );
//    wp_enqueue_script( 'wp-pointer' );
    include JALALI_DIR . 'inc' . DIRECTORY_SEPARATOR . 'wp-jalali-help-page.php';
}

function ztjalali_admin_save_option_page_fn() {
    global $ztjalali_admin_page;
    $screen = get_current_screen();
    if ($screen->id != $ztjalali_admin_page)
        return;
    
    //remove admin notices in first check options
    delete_option('ztjalali_do_activation');
    remove_action('admin_notices', 'ztjalali_admin_message');
    
    if (isset($_POST['save_wper_options'])) {
        global $ztjalali_option;
        check_admin_referer('jalali_save_options');
        $ztjalali_option = array(
            'force_timezone' => !empty($_POST['force_timezone']),
            'change_date_to_jalali' => !empty($_POST['change_date_to_jalali']),
            'change_jdate_number_to_persian' => !empty($_POST['change_jdate_number_to_persian']),
            'change_url_date_to_jalali' => !empty($_POST['change_url_date_to_jalali']),
            'afghan_month_name' => !empty($_POST['afghan_month_name']),
            'disallow_month_short_name' => !empty($_POST['disallow_month_short_name']),
            'change_title_number_to_persian' => !empty($_POST['change_title_number_to_persian']),
            'change_content_number_to_persian' => !empty($_POST['change_content_number_to_persian']),
            'change_excerpt_number_to_persian' => !empty($_POST['change_excerpt_number_to_persian']),
            'change_comment_number_to_persian' => !empty($_POST['change_comment_number_to_persian']),
            'change_commentcount_number_to_persian' => !empty($_POST['change_commentcount_number_to_persian']),
            'change_category_number_to_persian' => !empty($_POST['change_category_number_to_persian']),
            'change_point_to_persian' => !empty($_POST['change_point_to_persian']),
            'change_arabic_to_persian' => !empty($_POST['change_arabic_to_persian']),
            'change_archive_title' => !empty($_POST['change_archive_title']),
            'save_changes_in_db' => !empty($_POST['save_changes_in_db']),
            'ztjalali_admin_style' => !empty($_POST['ztjalali_admin_style']),
            'persian_planet' => !empty($_POST['persian_planet']),
        );
        update_option('ztjalali_options', json_encode($ztjalali_option))
                OR add_option('ztjalali_options', json_encode($ztjalali_option));
    }
}

/* =================================================================== */

/**
 * after install actions
 */
add_action('admin_init', 'ztjalali_after_install_actions');

function ztjalali_after_install_actions() {
    $active = get_option('ztjalali_do_activation');
    if ($active) {
        add_action('admin_notices', 'ztjalali_admin_message');
//        delete_option('ztjalali_do_activation');
//        $help_page = menu_page_url('ztjalali_help_page', FALSE);
//        header('Location: '.$help_page);
//        wp_redirect();
//        die('');
    }
}

function ztjalali_admin_message(){
    $Message=  sprintf(
                __('WP Jalali successful installed. please check %soptions%s','ztjalali')
                ,'<a href="'.menu_page_url('ztjalali_admin_page',FALSE).'">', '</a>'          
            );
    echo '<div class="updated"><p>' . $Message . '</p></div>';
//    echo '<div class="error"><p>' . $Message . '</p></div>';
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
        'title' => __('WordPress Development Blog', 'ztjalali')
    ),
    2 => array(
        'link' => 'http://wp-persian.com/',
        'feed' => 'http://wp-persian.com/feed/',
        'title' => __('wp-persian news Blog', 'ztjalali')
    )
);
$secondary_replacement = array(
    1 => array(
        'link' => 'http://planet.wordpress.org/',
        'feed' => 'http://planet.wordpress.org/feed/',
        'title' => __('Other WordPress News', 'ztjalali')
    ),
    2 => array(
        'link' => 'http://planet.wp-persian.com/',
        'feed' => 'http://planet.wp-persian.com/feed/',
        'title' => __('wp-persian planet', 'ztjalali')
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

