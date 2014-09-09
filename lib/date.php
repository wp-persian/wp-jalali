<?php

# 2014 Zakrot Web Solutions
# 
# changes:
#   function names changed
#   added timesone
#   added jmaketime
# 
# 2009-2013 Vahid Sohrablou (IranPHP.org)
# 2000 Roozbeh Pournader and Mohammad Tou'si  
# 
# This program is free software; you can redistribute it and/or 
# modify it under the terms of the GNU General Public License 
# as published by the Free Software Foundation; either version 2 
# of the License, or (at your option) any later version. 
# 
# This program is distributed in the hope that it will be useful, 
# but WITHOUT ANY WARRANTY; without even the implied warranty of 
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
# GNU General Public License for more details. 
# 
# A copy of the GNU General Public License is available from: 
# 
# <a href="http://gnu.org/copyleft/gpl.html" target="_blank">http://gnu.org/copyleft/gpl.html</a> 
# Version 1.2.9


/**
 * The format of the outputted date string (jalali equivalent of php date() function)
 * @global array $jdate_month_name
 * @global array $ztjalali_option
 * @param string $format for example 'Y-m-d H:i:s'
 * @param timestamp $timestamp [optional]
 * @param bool $timezone [optional]
 * @param bool $fanum [optional]<br/>convert number to persian ?<br/>
 *      default : get from plugin option
 * @return string
 * @since 5.0.0
 */
function jdate($format, $timestamp = NULL, $timezone = false, $fanum = NULL) {
    global $jdate_month_name, $ztjalali_option;
    static $jdate_month_days = array(0, 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
    static $jdate_week_name = array('شنبه', 'یکشنبه', 'دوشنبه', 'سه شنبه', 'چهارشنبه', 'پنج شنبه', 'جمعه');
    
    if (!$timestamp)
        $timestamp = time();
    elseif (!is_numeric($timestamp))
        $timestamp = strtotime($timestamp);
    elseif (!is_integer($timestamp))
        $timestamp = intval($timestamp);

    /* =================================================================== */
    if ($timezone === 'local' OR $timezone === FALSE) {
        //do noting
    } elseif ( $timezone === TRUE) {
        $fanum = FALSE;// support jdate older version 
    } elseif ($timezone === 'current') {
        $time_zone = 'Asia/Tehran';
        function_exists('get_option') AND $time_zone = get_option('timezone_string');
        $dtz = new DateTimeZone($time_zone);
        $time_obj = new DateTime('now', $dtz);
        $deff_time = $dtz->getOffset($time_obj);
        $timestamp += $deff_time;
    } elseif (is_numeric($time_zone)) {
        $timestamp += (int) $time_zone;
    } elseif (is_string($time_zone)) {
        $dtz = new DateTimeZone($time_zone);
        $time_obj = new DateTime('now', $dtz);
        $deff_time = $dtz->getOffset($time_obj);
        $timestamp += $deff_time;
    }
    /* =================================================================== */
    if ($fanum === NULL AND ! empty($ztjalali_option['change_jdate_number_to_persian']) AND $ztjalali_option['change_jdate_number_to_persian']) {
        $fanum = TRUE;
    }
    /* =================================================================== */
    # Create need date parametrs
    list($gYear, $gMonth, $gDay, $gWeek) = explode('-', date('Y-m-d-w', $timestamp));
    list($pYear, $pMonth, $pDay) = gregorian_to_jalali($gYear, $gMonth, $gDay);
    $pWeek = ($gWeek + 1);

    if ($pWeek >= 7) {
        $pWeek = 0;
    }

    if ($format == '\\') {
        $format = '//';
    }

    $lenghFormat = strlen($format);
    $i = 0;
    $result = '';

    while ($i < $lenghFormat) {
        $par = $format{$i};
        if ($par == '\\') {
            $result .= $format{ ++$i};
            $i ++;
            continue;
        }
        switch ($par) {
            # Day
            case 'd':
                $result .= (($pDay < 10) ? ('0' . $pDay) : $pDay);
                break;

            case 'D':
                $result .= substr($jdate_week_name[$pWeek], 0, 2);
                break;

            case 'j':
                $result .= $pDay;
                break;

            case 'l':
                $result .= $jdate_week_name[$pWeek];
                break;

            case 'N':
                $result .= $pWeek + 1;
                break;

            case 'w':
                $result .= $pWeek;
                break;

            case 'z':
                $result .= jday_of_year($pMonth, $pDay);
                break;

            case 'S':
                $result .= 'ام';
                break;

            # Week
            case 'W':
                $result .= ceil(jday_of_year($pMonth, $pDay) / 7);
                break;

            # Month
            case 'F':
                $result .= $jdate_month_name[$pMonth];
                break;

            case 'm':
                $result .= (($pMonth < 10) ? ('0' . $pMonth) : $pMonth);
                break;

            case 'M':
                $result .= substr($jdate_month_name[$pMonth], 0, 6);
                break;

            case 'n':
                $result .= $pMonth;
                break;

            case 't':
                $result .= jday_of_month($pYear,$pMonth);
                break;

            # Years
            case 'L':
                $result .= (int) is_jalali_leap_year($pYear);
                break;

            case 'Y':
            case 'o':
                $result .= $pYear;
                break;

            case 'y':
                $result .= substr($pYear, 2);
                break;

            # Time
            case 'a':
            case 'A':
                if (date('a', $timestamp) == 'am') {
                    $result .= (($par == 'a') ? 'ق.ظ' : 'قبل از ظهر');
                } else {
                    $result .= (($par == 'a') ? 'ب.ظ' : 'بعد از ظهر');
                }
                break;

            case 'B':
            case 'g':
            case 'G':
            case 'h':
            case 'H':
            case 's':
            case 'u':
            case 'i':
            # Timezone
            case 'e':
            case 'I':
            case 'O':
            case 'P':
            case 'T':
            case 'Z':
                $result .= date($par, $timestamp);
                break;

            # Full Date/Time
            case 'c':
                $result .= ($pYear . '-' . $pMonth . '-' . $pDay . ' ' . date('H:i:s P', $timestamp));
                break;

            case 'r':
                $result .= (substr($jdate_week_name[$pWeek], 0, 2) . '، ' . $pDay . ' ' . substr($jdate_month_name[$pMonth], 0, 6) . ' ' . $pYear . ' ' . date('H::i:s P', $timestamp));
                break;

            case 'U':
                $result .= $timestamp;
                break;

            default:
                $result .= $par;
        }
        $i ++;
    }
    if ($fanum)
        return ztjalali_persian_num($result);
    return $result;
}
/* =================================================================== */

/**
 * Format a local time/date according to locale settings (jalali equivalent of php strftime() function)
 * @global array $jdate_month_name
 * @param string $format for example 'Y-m-d H:i:s'
 * @param timestamp $timestamp [optional]
 * @param bool $fanum [optional]<br/>convert number to persian ?<br/>
 *      default : get from plugin option
 * @return type
 * @since 5.0.0
 */
function jstrftime($format, $timestamp = NULL, $fanum = false) {
    global $jdate_month_name;
    static $jdate_month_days = array(0, 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
    static $jdate_week_name = array('شنبه', 'یکشنبه', 'دوشنبه', 'سه شنبه', 'چهارشنبه', 'پنج شنبه', 'جمعه');
    if (!$timestamp) {
        $timestamp = time();
    }

    # Create need date parametrs
    list($gYear, $gMonth, $gDay, $gWeek) = explode('-', date('Y-m-d-w', $timestamp));
    list($pYear, $pMonth, $pDay) = gregorian_to_jalali($gYear, $gMonth, $gDay);
    $pWeek = $gWeek + 1;

    if ($pWeek >= 7) {
        $pWeek = 0;
    }

    $lenghFormat = strlen($format);
    $i = 0;
    $result = '';

    while ($i < $lenghFormat) {
        $par = $format{$i};
        if ($par == '%') {
            $type = $format{ ++$i};
            switch ($type) {
                # Day
                case 'a':
                    $result .= substr($jdate_week_name[$pWeek], 0, 2);
                    break;

                case 'A':
                    $result .= $jdate_week_name[$pWeek];
                    break;

                case 'd':
                    $result .= (($pDay < 10) ? '0' . $pDay : $pDay);
                    break;

                case 'e':
                    $result.= $pDay;
                    break;

                case 'j':
                    $dayinM = jday_of_year($pMonth, $pDay);
                    $result .= (($dayinM < 10) ? '00' . $dayinM : ($dayinM < 100) ? '0' . $dayinM : $dayinM);
                    break;

                case 'u':
                    $result .= $pWeek + 1;
                    break;

                case 'w':
                    $result .= $pWeek;
                    break;

                # Week
                case 'U':
                    $result .= floor(jday_of_year($pMonth, $pDay) / 7);
                    break;

                case 'V':
                case 'W':
                    $result .= ceil(jday_of_year($pMonth, $pDay) / 7);
                    break;

                # Month
                case 'b':
                case 'h':
                    $result .= substr($jdate_month_name[$pMonth], 0, 6);
                    break;

                case 'B':
                    $result .= $jdate_month_name[$pMonth];
                    break;

                case 'm':
                    $result .= (($pMonth < 10) ? '0' . $pMonth : $pMonth);
                    break;

                # Year
                case 'C':
                    $result .= ceil($pYear / 100);
                    break;

                case 'g':
                case 'y':
                    $result .= substr($pYear, 2);
                    break;

                case 'G':
                case 'Y':
                    $result .= $pYear;
                    break;

                # Time
                case 'H':
                case 'I':
                case 'l':
                case 'M':
                case 'R':
                case 'S':
                case 'T':
                case 'X':
                case 'z':
                case 'Z':
                    $result .= strftime('%' . $type, $timestamp);
                    break;

                case 'p':
                case 'P':
                case 'r':
                    if (date('a', $timestamp) == 'am') {
                        $result .= (($type == 'p') ? 'ق.ظ' : ($type == 'P') ? 'قبل از ظهر' : strftime("%I:%M:%S قبل از ظهر", $timestamp));
                    } else {
                        $result .= (($type == 'p') ? 'ب.ظ' : ($type == 'P') ? 'بعد از ظهر' : strftime("%I:%M:%S بعد از ظهر", $timestamp));
                    }
                    break;

                # Time and Date Stamps
                case 'c':
                    $result .= (substr($jdate_week_name[$pWeek], 0, 2) . ' ' . substr($jdate_month_name[$pMonth], 0, 6) . ' ' . $pDay . ' ' . strftime("%T", $timestamp) . ' ' . $pYear);
                    break;

                case 'D':
                case 'x':
                    $result .= ((($pMonth < 10) ? '0' . $pMonth : $pMonth) . '-' . (($pDay < 10) ? '0' . $pDay : $pDay) . '-' . substr($pYear, 2));
                    break;

                case 'F':
                    $result .= ($pYear . '-' . (($pMonth < 10) ? '0' . $pMonth : $pMonth) . '-' . (($pDay < 10) ? '0' . $pDay : $pDay));
                    break;

                case 's':
                    $result .= $timestamp;
                    break;

                # Miscellaneous
                case 'n':
                    $result .= "\n";
                    break;

                case 't':
                    $result .= "\t";
                    break;

                case '%':
                    $result .= '%';
                    break;

                default:
                    $result .= '%' . $type;
            }
        } else {
            $result .= $par;
        }
        $i ++;
    }
    if ($fanum)
        return ztjalali_persian_num($result);
    return $result;
}
/* =================================================================== */

/**
 * return Unix timestamp for a date (jalali equivalent of php mktime() function)
 * @param int $hour [optional] max : 23
 * @param int $minute [optional] max : 59
 * @param int $second [optional] max: 59
 * @param int $month [optional] max: 12
 * @param int $day [optional] max: 31
 * @param int $year [optional]
 * @param int $is_dst [optional]
 * @return timestamp
 * @since 5.0.0
 */
function jmktime($hour = 0, $minute = 0, $second = 0, $month = 0, $day = 0, $year = 0, $is_dst = -1) {
    if (($hour == 0) && ($minute == 0) && ($second == 0) && ($month == 0) && ($day == 0) && ($year == 0)) {
        return time();
    }

    list($year, $month, $day) = jalali_to_gregorian($year, $month, $day);
    return mktime($hour, $minute, $second, $month, $day, $year, $is_dst);
}
/* =================================================================== */

/**
 * validate a jalali date (jalali equivalent of php checkdate() function)
 * @param int $month
 * @param int $day
 * @param int $year
 * @return int
 * @since 5.0.0
 */
function jcheckdate($month, $day, $year) {
    if (($month < 1) || ($month > 12) || ($year < 1) || ($year > 32767) || ($day < 1)) {
        return 0;
    }

    static $jdate_month_days = array(0, 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

    if ($day > $jdate_month_days[$month]) {
        if (($month != 12) || ($day != 30) || !is_jalali_leap_year($year)) {
            return 0;
        }
    }

    return 1;
}
/* =================================================================== */

/**
 * Get date/time information (jalali equivalent of php getdate() function)
 * @param timestamp $timestamp
 * @return array
 * @since 5.0.0
 */
function jgetdate($timestamp = NULL) {
    if (!$timestamp) {
        $timestamp = mktime();
    }

    list($seconds, $minutes, $hours, $mday, $wday, $mon, $year, $yday, $weekday, $month) = explode('-', jdate('s-i-G-j-w-n-Y-z-l-F', $timestamp, false, false));
    return array(0 => $timestamp, 'seconds' => $seconds, 'minutes' => $minutes, 'hours' => $hours, 'mday' => $mday, 'wday' => $wday, 'mon' => $mon, 'year' => $year, 'yday' => $yday, 'weekday' => $weekday, 'month' => $month);
}
/* =================================================================== */

/**
 * gregorian to jalali convertion
 * @staticvar array $g_days_in_month
 * @staticvar array $j_days_in_month
 * @param int $g_y
 * @param int $g_m
 * @param int $g_d
 * @return array
 * @since 5.0.0
 */
function gregorian_to_jalali($g_y, $g_m, $g_d) {
    static $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    static $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
    $gy = $g_y - 1600;
    $gm = $g_m - 1;
    $g_day_no = (365 * $gy + int_div($gy + 3, 4) - int_div($gy + 99, 100) + int_div($gy + 399, 400));

    for ($i = 0; $i < $gm; ++$i) {
        $g_day_no += $g_days_in_month[$i];
    }

    if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0)))
    # leap and after Feb
        $g_day_no ++;
    $g_day_no += $g_d - 1;
    $j_day_no = $g_day_no - 79;
    $j_np = int_div($j_day_no, 12053); # 12053 = (365 * 33 + 32 / 4)
    $j_day_no = $j_day_no % 12053;
    $jy = (979 + 33 * $j_np + 4 * int_div($j_day_no, 1461)); # 1461 = (365 * 4 + 4 / 4)
    $j_day_no %= 1461;

    if ($j_day_no >= 366) {
        $jy += int_div($j_day_no - 1, 365);
        $j_day_no = ($j_day_no - 1) % 365;
    }

    for ($i = 0; ($i < 11 && $j_day_no >= $j_days_in_month[$i]); ++$i) {
        $j_day_no -= $j_days_in_month[$i];
    }

    return array($jy, $i + 1, $j_day_no + 1);
}
/* =================================================================== */

/**
 * jalali to gregorian convertion
 * @staticvar array $g_days_in_month
 * @staticvar array $j_days_in_month
 * @param int $j_y
 * @param int $j_m
 * @param int $j_d
 * @return array
 * @since 5.0.0
 */
function jalali_to_gregorian($j_y, $j_m, $j_d) {
    static $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    static $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
    $jy = $j_y - 979;
    $jm = $j_m - 1;
    $j_day_no = (365 * $jy + int_div($jy, 33) * 8 + int_div($jy % 33 + 3, 4));

    for ($i = 0; $i < $jm; ++$i) {
        $j_day_no += $j_days_in_month[$i];
    }

    $j_day_no += $j_d - 1;
    $g_day_no = $j_day_no + 79;
    $gy = (1600 + 400 * int_div($g_day_no, 146097)); # 146097 = (365 * 400 + 400 / 4 - 400 / 100 + 400 / 400)
    $g_day_no = $g_day_no % 146097;
    $leap = 1;

    if ($g_day_no >= 36525) { # 36525 = (365 * 100 + 100 / 4)
        $g_day_no --;
        $gy += (100 * int_div($g_day_no, 36524)); # 36524 = (365 * 100 + 100 / 4 - 100 / 100)
        $g_day_no = $g_day_no % 36524;
        if ($g_day_no >= 365) {
            $g_day_no ++;
        } else {
            $leap = 0;
        }
    }

    $gy += (4 * int_div($g_day_no, 1461)); # 1461 = (365 * 4 + 4 / 4)
    $g_day_no %= 1461;

    if ($g_day_no >= 366) {
        $leap = 0;
        $g_day_no --;
        $gy += int_div($g_day_no, 365);
        $g_day_no = ($g_day_no % 365);
    }

    for ($i = 0; $g_day_no >= ($g_days_in_month[$i] + ($i == 1 && $leap)); $i ++) {
        $g_day_no -= ($g_days_in_month[$i] + ($i == 1 && $leap));
    }

    return array($gy, $i + 1, $g_day_no + 1);
}
/* =================================================================== */

/**
 * integer division
 * @param int $a
 * @param int $b
 * @return type
 * @since 5.0.0
 */
function int_div($a, $b) {
    return (int) ($a / $b);
}
/* =================================================================== */

/**
 * return day number from first day of year
 * @param int $pMonth
 * @param int $pDay
 * @return type
 * @since 5.0.0
 */
function jday_of_year($pMonth, $pDay) {
    static $jdate_month_days = array(0, 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
    $days = 0;
    
    for ($i = 1; $i < $pMonth; $i ++) {
        $days += $jdate_month_days[$i];
    }

    return ($days + $pDay);
}
/* =================================================================== */

/**
 * check jalali year is leap(kabise)
 * @param int $year
 * @return int
 * @since 5.0.0
 */
function is_jalali_leap_year($year) {
    $mod = ($year % 33);

    if (($mod == 1) or ( $mod == 5) or ( $mod == 9) or ( $mod == 13) or ( $mod == 17) or ( $mod == 22) or ( $mod == 26) or ( $mod == 30)) {
        return 1;
    }

    return 0;
}
/* =================================================================== */

/**
 * return last day of month
 * @param int $year
 * @param int $month
 * @return int number of day in month
 * @since 5.0.0
 */
function jday_of_month($year,$month) {
    static $jdate_month_days = array(0, 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
    if(is_jalali_leap_year($year) && ($month == 12))
        return 30;
    $month = (int)$month;
    return $jdate_month_days[$month];
}
/* =================================================================== */

/**
 * return jalali name of month from month number
 * @global array $jdate_month_name
 * @param int $month
 * @return string
 * @since 5.0.0
 */
function monthname($month) {
    global $jdate_month_name;
    $month = (int)$month;
    return $jdate_month_name[$month];
}
/* =================================================================== */

