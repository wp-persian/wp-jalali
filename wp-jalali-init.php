<?php

/**
 * plugin installer
 */
function ztjalali_installer() {
    $options = get_option('ztjalali_options');
    if (empty($options)) {
        if (!($options = ztjalali_get_old_options()))
            $options = include JALALI_DIR . 'wp-jalali-config.php';
        add_option('ztjalali_options', json_encode($options));
    }else{
        $default_options = include JALALI_DIR . 'wp-jalali-config.php';
        $options =json_decode($options,TRUE);
        $options = array_merge($default_options,$options);
        update_option('ztjalali_options', json_encode($options));
    }
    
    $current_version = ztjalali_get_plugin_version();
    add_option('ztjalali_version',$current_version )
    OR update_option('ztjalali_version', $current_version );
    
    add_option('ztjalali_do_activation', true)
    OR update_option('ztjalali_do_activation', true );
}

/* =================================================================== */

function ztjalali_get_old_options() {
    $old_versions = array('4.1', '4.0', '3.9', '3.8', '3.7', '3.6', '3.5');
    foreach ($old_versions as $old_ver) {
        $mps_jd_optionsDB = get_option('mps_jd_options_' . $old_ver);
        if (!empty($mps_jd_optionsDB))
            break;
    }
    if (empty($mps_jd_optionsDB))
        return false;
    /* ------------------------------------------------------ */
    return array(
        'force_timezone' => FALSE, //doing: test
        'change_date_to_jalali' => ($mps_jd_optionsDB['mps_jd_autodate']) ? TRUE : FALSE,
        'change_jdate_number_to_persian' => ($mps_jd_optionsDB['mps_jd_farsinum_date']) ? TRUE : FALSE,
        'change_url_date_to_jalali' => ($mps_jd_optionsDB['mps_jd_jperma']) ? TRUE : FALSE,
        'afghan_month_name' => ($mps_jd_optionsDB['mps_jd_country'] == 'AF') ? TRUE : FALSE,
        'disallow_month_short_name' => TRUE,
        'change_title_number_to_persian' => ($mps_jd_optionsDB['mps_jd_farsinum_title']) ? TRUE : FALSE,
        'change_content_number_to_persian' => ($mps_jd_optionsDB['mps_jd_farsinum_content']) ? TRUE : FALSE,
        'change_excerpt_number_to_persian' => ($mps_jd_optionsDB['mps_jd_farsinum_content']) ? TRUE : FALSE,
        'change_comment_number_to_persian' => ($mps_jd_optionsDB['mps_jd_farsinum_comment']) ? TRUE : FALSE,
        'change_commentcount_number_to_persian' => ($mps_jd_optionsDB['mps_jd_farsinum_commentnum']) ? TRUE : FALSE,
        'change_category_number_to_persian' => ($mps_jd_optionsDB['mps_jd_farsinum_category']) ? TRUE : FALSE,
        'change_point_to_persian' => TRUE,
        'change_arabic_to_persian' => ($mps_jd_optionsDB['mps_jd_autoyk']) ? TRUE : FALSE,
        'change_archive_title' => ($mps_jd_optionsDB['mps_jd_farsinum_title']) ? TRUE : FALSE,
        'save_changes_in_db' => FALSE,
        'ztjalali_admin_style' => FALSE,
        'persian_planet' => TRUE,
    );
}

add_action('upgrader_process_complete','ztjalali_updater');

/**
 * plugin update
 */
function ztjalali_updater() {
    $current_ver = ztjalali_get_plugin_version();
    if($current_ver != get_option('ztjalali_version')){
        ztjalali_installer();
    }
}


/* =================================================================== */

/**
 * init function
 * @global type $ztjalali_option
 * @global type $jdate_month_name
 * @global array $ztjalali_wpoption
 */
function ztjalali_init() {
    /**
     * define global variables 
     */
    global $ztjalali_option;
    global $jdate_month_name;

    /**
     * load options
     */
    $OPT = get_option('ztjalali_options');
    $ztjalali_option = json_decode($OPT, TRUE);
    /* =================================================================== */

    /**
     * set global variables value
     */
    if (isset($ztjalali_option['afghan_month_name']) && $ztjalali_option['afghan_month_name'])
        $jdate_month_name = array("", "حمل", "ثور", "جوزا", "سرطان", "اسد", "سنبله", "میزان", "عقرب", "قوس", "جدی", "دلو", "حوت");
    else
        $jdate_month_name = array('', 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند');
}

/* =================================================================== */

/**
 * Setup language text domain
 */
load_plugin_textdomain('ztjalali', false, basename(dirname(__FILE__)).'/languages');
/* =================================================================== */


/**
 * Setup plugin page option link
 */
function ztjalali_add_settings_link( $links ) {
    $settings_link = '<a href="'.menu_page_url('ztjalali_admin_page',FALSE).'">'.__('setting','ztjalali').'</a>';
    Array_unshift( $links, $settings_link );
    return $links;
}

/* =================================================================== */



/**
 * Enqueue styles & scripts
 * @see http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
 */
// site -------------------------
//add_action('wp_enqueue_scripts', 'ztjalali_reg_css_and_js');
//function ztjalali_reg_css_and_js() {
//    wp_register_style('ztjalali_reg_style', plugins_url('assets/css/style.css', __FILE__));
//    wp_enqueue_style('ztjalali_reg_style');
//    wp_enqueue_script('ztjalali_reg_js', plugins_url('assets/js/js.js', __FILE__), array('jquery'));
//}
//admin -------------------------
add_action('admin_enqueue_scripts', 'ztjalali_reg_admin_css_and_js');

function ztjalali_reg_admin_css_and_js() {
    global $ztjalali_option;
    wp_register_style('ztjalali_reg_admin_style', plugins_url('assets/css/admin.css', __FILE__));
    wp_enqueue_style('ztjalali_reg_admin_style');
    
    if (isset($ztjalali_option['ztjalali_admin_style']) && $ztjalali_option['ztjalali_admin_style']){
        wp_register_style('ztjalali_reg_custom_admin_style', plugins_url('assets/css/admin_style.css', __FILE__));
        wp_enqueue_style('ztjalali_reg_custom_admin_style');
    }
    
    add_editor_style(plugins_url('assets/css/wysiwyg.css', __FILE__));
    wp_enqueue_script('ztjalali_reg_date_js', plugins_url('assets/js/date.js', __FILE__));
    if (isset($ztjalali_option['afghan_month_name']) && $ztjalali_option['afghan_month_name'])
        wp_enqueue_script('ztjalali_reg_admin_js', plugins_url('assets/js/admin-af.js', __FILE__), array('jquery'));
    else
        wp_enqueue_script('ztjalali_reg_admin_js', plugins_url('assets/js/admin-ir.js', __FILE__), array('jquery'));
}

//theme editiong style -----------------
add_action('admin_print_styles-plugin-editor.php', 'ztjalali_reg_theme_editor_css_and_js', 11);
add_action('admin_print_styles-theme-editor.php', 'ztjalali_reg_theme_editor_css_and_js', 11);

function ztjalali_reg_theme_editor_css_and_js() {
    wp_register_style('ztjalali_reg_theme_editor_style', plugins_url('assets/css/theme_editing.css', __FILE__));
    wp_enqueue_style('ztjalali_reg_theme_editor_style');
}

/* =================================================================== */

/**
 * Login Form modifiers 
 * @see http://codex.wordpress.org/Plugin_API/Filter_Reference/login_headerurl
 */
//add_filter('login_headerurl', 'ztjalali_login_url', 111);
add_filter('login_headertitle', 'ztjalali_login_text', 111);
//add_action('login_head', 'ztjalali_login_img', 111);

function ztjalali_login_url() {
    return 'http://wp-persian.com';
}

function ztjalali_login_text() {
    return __('Powered BY persian wordpress', 'ztjalali');
}

function ztjalali_login_img() {
    echo '<style>#login h1 a {background: transparent url(' . plugins_url('assets/img/wp-jalali-80x80.png', __FILE__) . ') no-repeat scroll center top}</style>';
}

/* =================================================================== */
