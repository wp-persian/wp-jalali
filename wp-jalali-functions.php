<?php
/**
 * get plugin version 
 */
function ztjalali_get_plugin_version() {
    if(!function_exists('get_plugin_data')) {
        include(ABSPATH . "wp-admin/includes/plugin.php"); 
    }
    $plugin_data = get_plugin_data(dirname(__FILE__).DIRECTORY_SEPARATOR.'wp-jalali.php', FALSE, FALSE );
    return $plugin_data['Version'];
}

/**
 * preg_replace callback for convert number to farsi
 * @global array $ztjalali_option
 * @param string $matches
 * @return string
 * @since 5.0.0
 * @see wp-jalali 4.5.3 : inc/farsinum-core.php line 5
 */
function ztjalali_convertToFarsi($matches) {
    global $ztjalali_option;
    if ($ztjalali_option['change_point_to_persian'])
        $farsi_array = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "٫");
    else
        $farsi_array = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", ".");

    $english_array = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", ".");

    $out = '';
    if (isset($matches[1])) {
        return str_replace($english_array, $farsi_array, $matches[1]);
    }
    return $matches[0];
}
/* =================================================================== */

/**
 * preg_replace for convert number to farsi
 * @param string $content
 * @return string
 * @since 5.0.0
 * @see wp-jalali 4.5.3 : inc/farsinum-core.php line 23
 */
function ztjalali_persian_num($content) {
//    return preg_replace_callback('/(?:&#\d{2,4};)|((?:\&nbsp\;)*\d+(?:\&nbsp\;)*\d*\.*(?:\&nbsp\;)*\d*(?:\&nbsp\;)*\d*)|(?:[a-z](?:[\x00-\x3B\x3D-\x7F]|<\s*[^>]+>)*)|<\s*[^>]+>/i', 'ztjalali_convertToFarsi', $content);
    return preg_replace_callback('/(?:&#\d{2,4};)|(\d+[\.\d]*)|(?:[a-z](?:[\x20-\x3B\x3D-\x7F]|<\s*[^>]+>)*)|<\s*[^>]+>/i', 'ztjalali_convertToFarsi', $content);
}
/* =================================================================== */

/**
 * convert persian number to latin number
 * @global array $ztjalali_option
 * @param string $str
 * @return string
 * @since 5.0.0
 * @see wp-jalali 4.5.3 : inc/farsinum-core.php line 27
 */
function ztjalali_english_num($str) {
    global $ztjalali_option;
    if ($ztjalali_option['change_point_to_persian'])
        $farsi_array = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "٫");
    else
        $farsi_array = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", ".");

    $english_array = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", ".");

    return str_replace($farsi_array, $english_array, $str);
}
/* =================================================================== */

/**
 * convert latin number to persin number
 * @global array $ztjalali_option
 * @param string $str
 * @return string
 * @since 5.0.0
 * @see wp-jalali 4.5.3 : inc/farsinum-core.php line 27
 */
function ztjalali_persian_num_all($str) {
    global $ztjalali_option;
    if ($ztjalali_option['change_point_to_persian'])
        $farsi_array = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "٫");
    else
        $farsi_array = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", ".");

    $english_array = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", ".");

    return str_replace($english_array, $farsi_array, $str);
}
/* =================================================================== */

/**
 * convert arabic char to persian
 * @param string $content
 * @return string
 * @since 5.0.0
 * @see wp-jalali 4.5.3 inc\yk-core.php 44
 */
function ztjalali_ch_arabic_to_persian($content) {
    return str_replace(array('ي', 'ك', '٤', '٥', '٦', 'ة'), array('ی', 'ک', '۴', '۵', '۶', 'ه'), $content);
}
/* =================================================================== */

/**
 * return week name
 * @param int $gWeek
 * @return string
 * @since 5.0.0
 */
function ztjalali_get_week_name($gWeek = 0) {
    static $jdate_week_name = array('شنبه', 'یکشنبه', 'دوشنبه', 'سه شنبه', 'چهارشنبه', 'پنج شنبه', 'جمعه');
    $jWeek = $gWeek + 1;
    if ($jWeek >= 7)
        $jWeek = 0;
    return $jdate_week_name[$jWeek];
}
/* =================================================================== */

/**
 * return short week name
 * @param int $gWeek
 * @return string
 * @since 5.0.0
 */
function ztjalali_get_short_week_name($gWeek = 0) {
    static $jdate_short_week_name = array("ش", "ی", "د", "س", "چ", "پ", "ج");
    $jWeek = $gWeek + 1;
    if ($jWeek >= 7)
        $jWeek = 0;
    return $jdate_short_week_name[$jWeek];
}
/* =================================================================== */

/**
 * return link of jalali year
 * @param int $year year
 * @return string
 * @since 5.0.0
 * @see wp-includes\link-template.php line 439
 */
function ztjalali_year_link($year) {
    global $ztjalali_option;
    if ($ztjalali_option['change_url_date_to_jalali']) {
        $jdate = gregorian_to_jalali($year, 6, 15);
        return get_year_link($jdate[0]);
    }
    return get_year_link($year);
}
/* =================================================================== */

/**
 * return link of jalali month
 * @param int $year year
 * @param int $month month
 * @return string
 * @since 5.0.0
 * @see wp-includes\link-template.php line 471
 */
function ztjalali_month_link($year, $month) {
    global $ztjalali_option;
    if ($ztjalali_option['change_url_date_to_jalali']) {
        $jdate = gregorian_to_jalali($year, $month, 15);
        return get_month_link($jdate[0], $jdate[1]);
    }
    return get_month_link($year, $month);
}
/* =================================================================== */

/**
 * return link of jalali day
 * @param int $year year
 * @param int $month month
 * @param int $day day
 * @return string
 * @since 5.0.0
 * @see wp-includes\link-template.php line 508
 */
function ztjalali_day_link($year, $month, $day) {
    global $ztjalali_option;
    if ($ztjalali_option['change_url_date_to_jalali']) {
        $jdate = gregorian_to_jalali($year, $month, $day);
        return get_day_link($jdate[0], $jdate[1], $jdate[2]);
    }
    return get_day_link($year, $month, $day);
}
/* =================================================================== */
