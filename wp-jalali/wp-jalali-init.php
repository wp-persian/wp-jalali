<?php
/**
 * plugin installer
 */
function ztjalali_installer() {
    $options = get_option('ztjalali_options');
    if (empty($options)) {
        $options = include JALALI_DIR . 'wp-jalali-config.php';
        add_option('ztjalali_options', json_encode($options));
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
    if ($ztjalali_option['afghan_month_name'])
        $jdate_month_name = array("", "حمل", "ثور", "جوزا", "سرطان", "اسد", "سنبله", "میزان", "عقرب", "قوس", "جدی", "دلو", "حوت");
    else
        $jdate_month_name = array('', 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند');

}
/* =================================================================== */

/**
 * Setup language text domain
 */
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain('ztjalali', false, $plugin_dir);
/* =================================================================== */

/**
 * Enqueue styles & scripts
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
    add_editor_style(plugins_url('assets/css/wysiwyg.css', __FILE__));
    wp_enqueue_script('ztjalali_reg_date_js', plugins_url('assets/js/date.js', __FILE__));
    if ($ztjalali_option['afghan_month_name'])
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
 */
add_filter('login_headerurl', 'ztjalali_login_url', 111);
add_filter('login_headertitle', 'ztjalali_login_text', 111);
add_action('login_head', 'ztjalali_login_img', 111);

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
