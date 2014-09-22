<?php

/**
 * use <b>jmktime()</b> instead jmaketime()
 * @deprecated since 5.0.0
 */
function jmaketime($hour = 0, $minute = 0, $second = 0, $month = 0, $day = 0, $year = 0, $is_dst = -1) {
    return jmktime($hour, $minute, $second, $month, $day, $year, $is_dst);
}

/**
 * @deprecated since 5.0.0
 */
function gregorian_week_day($g_y, $g_m, $g_d) {
    global $g_days_in_month;

    $gy = $g_y - 1600;
    $gm = $g_m - 1;
    $gd = $g_d - 1;

    $g_day_no = 365 * $gy + div($gy + 3, 4) - div($gy + 99, 100) + div($gy + 399, 400);

    for ($i = 0; $i < $gm; ++$i)
        $g_day_no += $g_days_in_month[$i];
    if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0)))
    /* leap and after Feb */
        ++$g_day_no;
    $g_day_no += $gd;

    return ($g_day_no + 5) % 7 + 1;
}

/**
 * 
 * @deprecated since 5.0.0
 */
function jalali_week_day($j_y, $j_m, $j_d) {
    global $j_days_in_month;

    $jy = $j_y - 979;
    $jm = $j_m - 1;
    $jd = $j_d - 1;

    $j_day_no = 365 * $jy + div($jy, 33) * 8 + div($jy % 33 + 3, 4);

    for ($i = 0; $i < $jm; ++$i)
        $j_day_no += $j_days_in_month[$i];

    $j_day_no += $jd;

    return ($j_day_no + 2) % 7 + 1;
}

/**
 * Find Day Begining Of Month
 * 
 * @deprecated since 5.0.0
 */
function mstart($month, $day, $year) {
    list( $jyear, $jmonth, $jday ) = gregorian_to_jalali($year, $month, $day);
    list( $year, $month, $day ) = jalali_to_gregorian($jyear, $jmonth, "1");
    $timestamp = mktime(0, 0, 0, $month, $day, $year);
    return date("w", $timestamp);
}

/**
 * Find Number Of Days In This Month<br/>
 * use <b>jday_of_month()</b> instead lastday()
 * @deprecated since 5.0.0
 */
function lastday($month, $day, $year) {
    $lastdayen = date("d", mktime(0, 0, 0, $month + 1, 0, $year));
    list( $jyear, $jmonth, $jday ) = gregorian_to_jalali($year, $month, $day);
    $lastdatep = $jday;
    $jday = $jday2;
    while ($jday2 != "1") {
        if ($day < $lastdayen) {
            $day++;
            list( $jyear, $jmonth, $jday2 ) = gregorian_to_jalali($year, $month, $day);
            if ($jdate2 == "1")
                break;
            if ($jdate2 != "1")
                $lastdatep++;
        }
        else {
            $day = 0;
            $month++;
            if ($month == 13) {
                $month = "1";
                $year++;
            }
        }
    }
    return $lastdatep - 1;
}

/**
 * use <b>int_div()</b> instead div()
 * @deprecated since 5.0.0
 */
function div($a, $b) {
    return (int) ($a / $b);
}

/**
 * use <b>ztjalali_convertToFarsi()</b> instead convertToFarsi()
 * @deprecated since 5.0.0
 */
function convertToFarsi($matches) {
    return ztjalali_convertToFarsi($matches);
}

/**
 * use <b>ztjalali_persian_num()</b> OR  <b>ztjalali_persian_num_all()</b> instead farsi_num()
 * @deprecated since 5.0.0
 */
function farsi_num($str, $fake = null, $fake2 = null) {
    return ztjalali_persian_num($str);
}

/**
 * use <b>ztjalali_english_num()</b> instead english_num()
 * @deprecated since 5.0.0
 */
function english_num($str) {
    return ztjalali_english_num($str);
}

/**
 * use <b>ztjalali_archive_widget()</b> instead wp_get_jarchives()
 * @deprecated since 5.0.0
 */
function wp_get_jarchives($args = '') {
    parse_str($args, $r);
    if (!isset($r['type'])) $r['type'] = '';
    if (!isset($r['limit'])) $r['limit'] = '';
    if (!isset($r['format'])) $r['format'] = 'html';
    if (!isset($r['before'])) $r['before'] = '';
    if (!isset($r['after'])) $r['after'] = '';
    if (!isset($r['show_post_count'])) $r['show_post_count'] = false;
    return ztjalali_archive_widget($r['type'],$r['format'], $r['show_post_count'],$r['limit'],$r['before'],$r['after']);
}

/**
 * use <b>ztjalali_calendar_widget()</b> instead get_jcalendar()
 * @deprecated since 5.0.0
 */
function get_jcalendar() {
    return ztjalali_calendar_widget();
}
