<?php
/**
 * define filters
 * @see http://codex.wordpress.org/Plugin_API/Filter_Reference
 */
//load options
global $ztjalali_option;

if ($ztjalali_option['force_timezone'])
    date_default_timezone_set('Asia/Tehran');

//convert gregorian to jalali filter
if ($ztjalali_option['change_date_to_jalali'])
    add_filter('date_i18n', 'ztjalali_ch_date_i18n', 111, 4);

//jalali link
if ($ztjalali_option['change_url_date_to_jalali']) {
    add_filter("post_link", "ztjalali_permalink_filter_fn", 0, 3);
    add_action('pre_get_posts', 'ztjalali_pre_get_posts_filter_fn');
    add_filter('posts_where', 'ztjalali_posts_where_filter_fn');
}
if ($ztjalali_option['save_changes_in_db']) {
    // change en number to persian number in db
    if ($ztjalali_option['change_title_number_to_persian'])
        add_filter('title_save_pre', 'ztjalali_persian_num');

    if ($ztjalali_option['change_content_number_to_persian'])
        add_filter('content_save_pre', 'ztjalali_persian_num');

    if ($ztjalali_option['change_excerpt_number_to_persian'])
        add_filter('excerpt_save_pre', 'ztjalali_persian_num');

    if ($ztjalali_option['change_comment_number_to_persian']){
        add_filter('comment_save_pre', 'ztjalali_persian_num');
        add_filter('pre_comment_content', 'ztjalali_persian_num');
    }
    
    // change arabic characters
    if ($ztjalali_option['change_arabic_to_persian']) {
        add_filter('content_save_pre', 'ztjalali_ch_arabic_to_persian');
        add_filter('title_save_pre', 'ztjalali_ch_arabic_to_persian');
        add_filter('excerpt_save_pre', 'ztjalali_ch_arabic_to_persian');
        
        add_filter('comment_save_pre', 'ztjalali_ch_arabic_to_persian');
        add_filter('pre_comment_content', 'ztjalali_ch_arabic_to_persian');
    }
} else {
    // change en number to persian number in visit
    if ($ztjalali_option['change_title_number_to_persian'])
        add_filter('the_title', 'ztjalali_persian_num');

    if ($ztjalali_option['change_content_number_to_persian'])
        add_filter('the_content', 'ztjalali_persian_num');

    if ($ztjalali_option['change_excerpt_number_to_persian'])
        add_filter('the_excerpt', 'ztjalali_persian_num');

    if ($ztjalali_option['change_comment_number_to_persian'])
        add_filter('comment_text', 'ztjalali_persian_num');
    
    // change arabic characters
    if ($ztjalali_option['change_arabic_to_persian']) {
        add_filter('the_content', 'ztjalali_ch_arabic_to_persian');
        add_filter('the_title', 'ztjalali_ch_arabic_to_persian');
        add_filter('the_excerpt', 'ztjalali_ch_arabic_to_persian');
        add_filter('comment_text', 'ztjalali_ch_arabic_to_persian');
    }
}

if ($ztjalali_option['change_commentcount_number_to_persian'])
    add_filter('comments_number', 'ztjalali_persian_num');


if ($ztjalali_option['change_category_number_to_persian'])
    add_filter('wp_list_categories', 'ztjalali_persian_num');

if ($ztjalali_option['change_arabic_to_persian']) {
    add_filter('wp_list_categories', 'ztjalali_ch_arabic_to_persian');
}

// change archive title
if ($ztjalali_option['change_archive_title'])
    add_filter('wp_title', 'ztjalali_ch_archive_title', 111, 3);

/* =================================================================== */

/**
 * convert gregorian to jalali filter handler
 * @param string $j          Formatted date string.
 * @param string $req_format Format to display the date.
 * @param int    $i          Unix timestamp.
 * @param bool   $gmt        Whether to convert to GMT for time. Default false.
 * @return string
 */
function ztjalali_ch_date_i18n($j, $req_format, $i, $gmt) {
    global $ztjalali_option;
    if($ztjalali_option['disallow_month_short_name'])
        $req_format = str_replace('M', 'F', $req_format);
    return jdate($req_format, $i);
}

/* =================================================================== */

/**
 * change archive title filter handler
 * @global array $jdate_month_name
 * @global array $wp_query
 * @param string $title
 * @param string $sep
 * @param string $seplocation
 * @return string
 */
function ztjalali_ch_archive_title($title, $sep, $seplocation) {
    global $jdate_month_name, $wp_query;
    $query = $wp_query->query;
    $new_title='';
    if (is_archive()) {
        if (isset($query['monthnum'])) {
            if ($query['year'] < 1700) {
                $new_title = $jdate_month_name[(int) $query['monthnum']] . ' ' . $query['year'];
            }
        } elseif (isset($query['year'])) {
            $new_title = $query['year'];
        } elseif (isset($query['m'])) {
            $year = substr($query['m'], 0, 4);
            if ($year < 1700) {
                $monthnum = (int) substr($query['m'], 4, 2);
                $new_title = $jdate_month_name[$monthnum] . ' ' . $year;
            }
        }
        if (!empty($new_title)) {
//            if ($seplocation == 'right')
//                $new_title.= $sep . ' ' . get_bloginfo('name');
//            else
//                $new_title .= get_bloginfo('name') . ' ' . $sep;
            $new_title .= ' - '. get_bloginfo('name');
            return ztjalali_persian_num($new_title);
        }
    }
    return $title;
}

/* =================================================================== */

/**
 * posts where filter handler
 * @see wp-includes\query.php 2353
 */
function ztjalali_posts_where_filter_fn($where) {
    global $wp_query, $wpdb, $pagenow;
    if (empty($wp_query->query_vars))
        return $where;
    $m = $hour = $minute = $second = $year = $month = $day = "";
    if (isset($wp_query->query_vars['m']))
        $m = $wp_query->query_vars['m'];
    if (isset($wp_query->query_vars['hour']))
        $hour = $wp_query->query_vars['hour'];
    if (isset($wp_query->query_vars['minute']))
        $minute = $wp_query->query_vars['minute'];
    if (isset($wp_query->query_vars['second']))
        $second = $wp_query->query_vars['second'];
    if (isset($wp_query->query_vars['year']))
        $year = $wp_query->query_vars['year'];
    if (isset($wp_query->query_vars['monthnum']))
        $month = $wp_query->query_vars['monthnum'];
    if (isset($wp_query->query_vars['day']))
        $day = $wp_query->query_vars['day'];

    if (!empty($m)) {
        $len = strlen($m);
        $year = substr($m, 0, 4);
        if ($len > 5)
            $month = substr($m, 4, 2);
        if ($len > 7)
            $day = substr($m, 6, 2);
        if ($len > 9)
            $hour = substr($m, 8, 2);
        if ($len > 11)
            $minute = substr($m, 10, 2);
        if ($len > 13)
            $second = substr($m, 12, 2);
    }
    if (empty($year) || $year > 1700)
        return $where;

    $start_month = $start_day = $end_month = $end_day = 1;
    $start_hour = $start_min = $start_sec = $end_hour = $end_min = $end_sec = '00';
    $start_year = $year;
    $end_year = $year + 1;

    if (!empty($month)) {
        $start_month = $month;
        if ($month == 12)
            $end_month = 1;
        else
            $end_month = $month + 1;

        if ($end_month == 1)
            $end_year = $start_year + 1;
        else
            $end_year = $start_year;
    }
    $jday_count = array(NULL, 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

    if (!empty($day)) {
        $start_day = $day;
        if ($day == $jday_count[$month])
            $end_day = 1;
        else
            $end_day = $day + 1;

        if ($end_day == 1)
            $end_month = $start_month + 1;
        else
            $end_month = $start_month;
    }

    if (!empty($hour)) {
        $start_hour = $hour;
        if ($hour == 24)
            $end_hour = '00';
        else
            $end_hour = $hour + 1;

        if ($end_hour == '00')
            $end_day = $start_day + 1;
        else
            $end_day = $start_day;
    }

    if (!empty($minute)) {
        $start_min = $minute;
        if ($minute == 59)
            $end_min = '00';
        else
            $end_min = $minute + 1;
        if ($end_min == '00')
            $end_hour = $start_hour + 1;
        else
            $end_hour = $start_hour;
    }

    if (!empty($second)) {
        $start_sec = $second;
        if ($second == 59)
            $end_sec = '00';
        else
            $end_sec = $second + 1;
        if ($end_sec == '00')
            $end_min = $start_min + 1;
        else
            $end_min = $start_min;
    }

    $start_date = jalali_to_gregorian($start_year, $start_month, $start_day);
    $start_date.=" $start_hour:$start_min:$start_sec";
    $end_date = jalali_to_gregorian($end_year, $end_month, $end_day);
    $end_date.=" $end_hour:$end_min:$end_sec";
    $paterns = array('/YEAR\((.*?)post_date\s*\)\s*=\s*[0-9\']*/',
        '/DAYOFMONTH\((.*?)post_date\s*\)\s*=\s*[0-9\']*/',
        '/MONTH\((.*?)post_date\s*\)\s*=\s*[0-9\']*/',
        '/HOUR\((.*?)post_date\s*\)\s*=\s*[0-9\']*/',
        '/MINUTE\((.*?)post_date\s*\)\s*=\s*[0-9\']*/',
        '/SECOND\((.*?)post_date\s*\)\s*=\s*[0-9\']*/');
    foreach ($paterns as $ptn) {
        $where = preg_replace($ptn, '1=1', $where);
    }
    $prefixp = "{$wpdb->posts}.";
    $prefixp = (strpos($where, $prefixp) === false) ? '' : $prefixp;
    $where .= " AND {$prefixp}post_date >= '$start_date' AND {$prefixp}post_date < '$end_date' ";
    return $where;
}

/* =================================================================== */

/**
 * pre get posts filter handler
 * @param object $post
 * @see wp-includes\query.php 2353
 */
function ztjalali_pre_get_posts_filter_fn($query) {
    global $wpdb;
    $query_vars = $query->query;
    $year = $monthnum = $day = "";
    if (isset($query_vars['m']) AND !empty($query_vars['m'])){
        $year= (int)(substr($query_vars['m'],0, 4));
        if($year < 1700){
            $monthnum= (int)(substr($query_vars['m'], 4,2));
            if(empty($monthnum)){
                $start_date = jalali_to_gregorian($year, 1, 1);
                $end_date = jalali_to_gregorian($year, 12, jday_of_month($year, 12));
            }else{
                $day= (int)(substr($query_vars['m'], 6,2));
                if(empty($day)){
                    $start_date = jalali_to_gregorian($year, $monthnum, 1);
                    $end_date = jalali_to_gregorian($year, $monthnum, jday_of_month($year, $monthnum));
                }else{
                    $end_date = $start_date = jalali_to_gregorian($year, $monthnum,$day);
                }
            }
        
            $date_query = array(
                array(
                    'after' => array(
                        'year' => $start_date[0],
                        'month' => $start_date[1],
                        'day' => $start_date[2],
                    ),
                    'before' => array(
                        'year' => $end_date[0],
                        'month' => $end_date[1],
                        'day' => $end_date[2],
                    ),
                    'inclusive' => TRUE,
                ),
            );
            $query->set('date_query', $date_query);
            $query->set('m', '');
        }
        return $query;
    }

    /* ------------------------------------------------------ */

    if (isset($query_vars['year']))
        $year = $query_vars['year'];
    if (isset($query_vars['monthnum']))
        $monthnum = $query_vars['monthnum'];
    if (isset($query_vars['day']))
        $day = $query_vars['day'];
    if($year > 1700)
        return $query;
    if (isset($query_vars['name'])) {
        $post_date = $wpdb->get_var($wpdb->prepare("select post_date from {$wpdb->posts} where post_name=%s order by ID", $query_vars['name']));
        $Date = explode('-', date('Y-m-d', strtotime($post_date)));
        $jDate = gregorian_to_jalali($Date[0], $Date[1], $Date[2]);

        if ($year == $jDate[0])
            $query->set('year', $Date[0]);

        if ($monthnum == $jDate[1])
            $query->set('monthnum', $Date[1]);

        if ($day == $jDate[2])
            $query->set('day', $Date[2]);

        return $query;
    }

    if (isset($query_vars['post_id'])) {
        $post_date = $wpdb->get_var($wpdb->prepare("select post_date from {$wpdb->posts} where ID=%d", $query_vars['post_id']));
        $cDate = getdate(strtotime($post_date));
        if (!empty($year))
            $query->set('year', $cDate['year']);

        if (!empty($monthnum))
            $query->set('monthnum', $cDate['mon']);

        if (!empty($day))
            $query->set('day', $cDate['mday']);

        return $query;
    }

    if (!empty($year) and ! empty($monthnum) and ! empty($day)) {
        $post_date = jalali_to_gregorian($year, $monthnum, $day);
        $query->set('year', $post_date[0]);
        $query->set('monthnum', $post_date[1]);
        $query->set('day', $post_date[2]);
        return $query;
    }

    if (!empty($year) and ! empty($monthnum)) {
        $start_date = jalali_to_gregorian($year, $monthnum, 1);
        $end_date = jalali_to_gregorian($year, $monthnum, jday_of_month($year, $monthnum));

        $date_query = array(
            array(
                'after' => array(
                    'year' => $start_date[0],
                    'month' => $start_date[1],
                    'day' => $start_date[2],
                ),
                'before' => array(
                    'year' => $end_date[0],
                    'month' => $end_date[1],
                    'day' => $end_date[2],
                ),
                'inclusive' => TRUE,
            ),
        );
        $query->set('date_query', $date_query);
        $query->set('year', '');
        $query->set('monthnum', '');

//        $post_date = jalali_to_gregorian($year, $monthnum, 15);
//        $query->set('year', $post_date[0]);
//        $query->set('monthnum', $post_date[1]);
        return $query;
    }

    if (!empty($year)) {
        $start_date = jalali_to_gregorian($year, 1, 1);
        if (is_jalali_leap_year($year))
            $end_date = jalali_to_gregorian($year, 12, 30);
        else
            $end_date = jalali_to_gregorian($year, 12, 29);
        $date_query = array(
            array(
                'after' => array(
                    'year' => $start_date[0],
                    'month' => $start_date[1],
                    'day' => $start_date[2],
                ),
                'before' => array(
                    'year' => $end_date[0],
                    'month' => $end_date[1],
                    'day' => $end_date[2],
                ),
                'inclusive' => TRUE,
            ),
        );
        $query->set('date_query', $date_query);
        $query->set('year', '');
        return $query;
    }

    return $query;
}

/* =================================================================== */

/**
 * jalali link filter handlers
 * @param string $perma
 * @param string $post
 * @param string $leavename
 * @see wp-includes\link-template.php line 112
 */
function ztjalali_permalink_filter_fn($perma, $post, $leavename = false) {
    $permalink = get_option('permalink_structure');
    if (empty($permalink))
        return $perma;
    if(!(preg_match('/%year%|%monthnum%|%day%/', $permalink)))
        return $perma;
    /* ------------------------------------------------------ */
    if (empty($post->ID))
        return $perma;
    /* ------------------------------------------------------ */
    if (in_array($post->post_status, array('draft', 'pending', 'auto-draft')))
        return $perma;
    /* ------------------------------------------------------ */
    $illegal_post_types = get_post_types(array('_builtin' => false));
    $illegal_post_types[] ='page';
    $illegal_post_types[] ='attachment';
    if (in_array($post->post_type,$illegal_post_types))
        return $perma;
    /* ------------------------------------------------------ */
    $rewritecode = array(
        '%year%',
        '%monthnum%',
        '%day%',
        '%hour%',
        '%minute%',
        '%second%',
        $leavename ? '' : '%postname%',
        '%post_id%',
        '%category%',
        '%author%',
        $leavename ? '' : '%pagename%',
    );

    $unixtime = strtotime($post->post_date);
    $category = "";
    if (strpos($permalink, '%category%') !== false) {
        $cats = get_the_category($post->ID);
        if ($cats) {
            usort($cats, '_usort_terms_by_ID'); // order by ID
            $category_object = get_term(reset($cats), 'category');
            $category = $category_object->slug;
            if ($parent = $category_object->parent)
                $category = get_category_parents($parent, false, '/', true) . $category;
        }
        if (empty($category)) {
            $default_category = get_term(get_option('default_category'), 'category');
            $category = is_wp_error($default_category) ? '' : $default_category->slug;
        }
    }

    $author = "";
    if (strpos($permalink, '%author%') !== false) {
        $authordata = get_user_by('id', $post->post_author);
        if(!empty($authordata))
            $author = $authordata->user_nicename;
    }

    $date = explode("-", date('Y-m-d-H-i-s', $unixtime));
    $jdate = gregorian_to_jalali($date[0], $date[1], $date[2]);
    $rewritereplace = array(
        $jdate[0],
        sprintf('%02d', $jdate[1]), // add leading zero if necessary
        sprintf('%02d', $jdate[2]),
        $date[3],
        $date[4],
        $date[5],
        $post->post_name,
        $post->ID,
        $category,
        $author,
        $post->post_name,
    );
    $permalink = home_url(str_replace($rewritecode, $rewritereplace, $permalink));
    return user_trailingslashit($permalink, 'single');
}

/* =================================================================== */


