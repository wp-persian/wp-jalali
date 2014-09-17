<?php
/**
 * @see :  http://codex.wordpress.org/Widgets_API#Default_Usage
 */
class ztjalali_calendar extends WP_Widget {

    function __construct() {
        parent::__construct(
                'ztjalali_calendar', __('jalali calendar', 'ztjalali'), array('description' => __('jalali calendar widget', 'ztjalali'),)
        );
    }

    public function form($instance) {
        if (!isset($instance['jcalendar_title']))
            $instance['jcalendar_title'] = __('jalali calendar', 'ztjalali');
        if (!isset($instance['jcalendar_short_weekname']))
            $instance['jcalendar_short_weekname'] = TRUE;
        ?>
        <div dir="rtl" align="justify">
            <p style="text-align:right">
                <label for="<?php echo $this->get_field_id('jcalendar_title'); ?>">
                    <?php _e('title', 'ztjalali') ?>: 
                    <input style="width: 200px;" id="<?php echo $this->get_field_id('jcalendar_title'); ?>" name="<?php echo $this->get_field_name('jcalendar_title'); ?>" type="text" value="<?php echo $instance['jcalendar_title']; ?>" />
                </label>
            </p>
            <p style="text-align:right">
                <label for="<?php echo $this->get_field_id('jcalendar_short_weekname'); ?>">
                    <input name="<?php echo $this->get_field_name('jcalendar_short_weekname'); ?>" type="checkbox" value="1" id="<?php echo $this->get_field_id('jcalendar_short_weekname'); ?>" <?php checked($instance['jcalendar_short_weekname'], TRUE); ?> />
                    <?php _e('jcalendar short name of week', 'ztjalali') ?>
                </label>
            </p>
        </div>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['jcalendar_title'] = strip_tags($new_instance['jcalendar_title']);
        $instance['jcalendar_short_weekname'] = (!empty($new_instance['jcalendar_short_weekname']) ) ? TRUE : FALSE;
        return $instance;
    }

    public function widget($args, $instance) {
        if (!isset($instance['jcalendar_title']))
            $instance['jcalendar_title'] = __('jalali calendar', 'ztjalali');
        if (!isset($instance['jcalendar_short_weekname']))
            $instance['jcalendar_short_weekname'] = TRUE;
        extract($args);
        echo $before_widget;
        echo $before_title . $instance['jcalendar_title'] . $after_title;
        ztjalali_calendar_widget($instance['jcalendar_short_weekname'], TRUE);
        echo $after_widget;
    }

}

/**
 * widget handle action
 */
add_action('widgets_init', 'register_ztjalali_calendar');

function register_ztjalali_calendar() {
    register_widget('ztjalali_calendar');
}

/* =================================================================== */

/**
 * own widget function
 */
function ztjalali_calendar_widget($shortname = TRUE, $echo = TRUE, $thisyear = 0, $thismonth = 0) {
    global $wpdb, $posts, $wp;
    global $jdate_month_name, $ztjalali_option;
    
    if (isset($wp->query_vars['m'])) {
        $m_year= (int)(substr($wp->query_vars['m'],0, 4));
        $m_month= (int)(substr($wp->query_vars['m'], 4,2));
        if($m_year < 1700){
            list($m_year,$m_month,$tmp_day) = jalali_to_gregorian($m_year, $m_month, 15);
        }
    }
    elseif (isset($wp->query_vars['m']))
            $thisyear =(int)(substr($wp->query_vars['m'],0, 4));
    
    if (empty($thisyear)) {
        if (isset($wp->query_vars['year']))
            $thisyear = (int)$wp->query_vars['year'];
        elseif (isset($m_year))
            $thisyear =$m_year;
        else
            $thisyear = date('Y', time());
    }
    if (empty($thismonth)) {
        if (isset($wp->query_vars['monthnum']))
            $thismonth = (int)$wp->query_vars['monthnum'];
        elseif (isset($m_month))
            $thismonth =$m_month;
        else
            $thismonth = date('m', time());
    }
    

//doing: support $_GET['w']
//  if (isset($_GET['w']))
//    $w = '' . (int)($_GET['w']);
//    if (!empty($w)) {
//// We need to get the month from MySQL
//        $thisyear = '' . (int)(substr($m, 0, 4));
//        $d = (($w - 1) * 7) + 6; //it seems MySQL's weeks disagree with PHP's
//        $thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('{$thisyear}0101', INTERVAL $d DAY) ), '%m')");
//    }

/* doing : cache */
    $cache = array();
    $key = md5( $thismonth . $thisyear);
    if ($cache = wp_cache_get('ztjalali_calendar', 'calendar')) {
        if (is_array($cache) && isset($cache[$key])) {
            if ($echo) {
                /** This filter is documented in wp-includes/general-template.php */
                echo apply_filters('ztjalali_calendar', $cache[$key]);
                return;
            } else {
                /** This filter is documented in wp-includes/general-template.php */
                return apply_filters('ztjalali_calendar', $cache[$key]);
            }
        }
    }

    if (!is_array($cache))
        $cache = array();
// Quick check. If we have no posts at all, abort!
    if (!$posts) {
        $gotsome = $wpdb->get_var("SELECT 1 as test FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' LIMIT 1");
        if (!$gotsome) {
            $cache[$key] = '';
            wp_cache_set('ztjalali_calendar', $cache, 'calendar');
            return;
        }
    }
    
    if($thisyear>1700){
        list($thisyear,$thismonth,$thisday)= gregorian_to_jalali($thisyear, $thismonth, 1);
    }
    $unixmonth = jmktime(0, 0, 0, $thismonth, 1, $thisyear);
    $jthisyear = $thisyear;
    $jthismonth = $thismonth;
    list($thisyear,$thismonth,$jthisday)= jalali_to_gregorian($jthisyear, $jthismonth, 1);
    
    $last_day = jdate('t', $unixmonth,FALSE,FALSE);
    
// Get the next and previous month and year with at least one post
    $startdate = date("Y:m:d", jmktime(0, 0, 0, $jthismonth, 1, $jthisyear));
    $enddate = date("Y:m:d", jmktime(23, 59, 59, $jthismonth, $last_day, $jthisyear));
    
    $previous = $wpdb->get_row("SELECT DAYOFMONTH(post_date) AS `dayofmonth`,MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		WHERE post_date < '$startdate'
		AND post_type = 'post' AND post_status = 'publish'
			ORDER BY post_date DESC
			LIMIT 1");
    $next = $wpdb->get_row("SELECT DAYOFMONTH(post_date) AS `dayofmonth`,MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		WHERE post_date > '$enddate 23:59:59'
		AND post_type = 'post' AND post_status = 'publish'
			ORDER BY post_date ASC
			LIMIT 1");
    /* translators: Calendar caption: 1: month name, 2: 4-digit year */
    $calendar_caption = _x('%1$s %2$s', 'calendar caption');
    $calendar_output = '<table id="wp-calendar" class="widget_calendar">
	<caption>' . sprintf($calendar_caption, $jdate_month_name[(int)$jthismonth], jdate('Y', $unixmonth)) . '</caption>
	<thead>
	<tr>';

    $myweek =$myshortweek = array();
    
    $week_begins = (int)(get_option('start_of_week'));
    

    for ($wdcount = 0; $wdcount <= 6; $wdcount++) {
        $myweek[] = ztjalali_get_week_name(($wdcount + $week_begins) % 7);
        $myshortweek[] = ztjalali_get_short_week_name(($wdcount + $week_begins) % 7);
    }
    
    foreach ($myweek as $k=>$wd) {
        $day_name = (true == $shortname) ? $myshortweek[$k] :$wd;
        $wd = esc_attr($wd);
        $calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
    }

    $calendar_output .= '
	</tr>
	</thead>

	<tfoot>
	<tr>';

    if ($previous) {
        $jprevious=gregorian_to_jalali($previous->year, $previous->month, $previous->dayofmonth);
        if ($ztjalali_option['change_url_date_to_jalali'])
            $calendar_output .= "\n\t\t" . '<td colspan="3" id="prev"><a href="' . get_month_link($jprevious[0],$jprevious[1]) . '" title="' . esc_attr(sprintf(__('View posts for %1$s %2$s','ztjalali'), $jdate_month_name[$jprevious[1]], jdate('Y', mktime(0, 0, 0, $previous->month, 1, $previous->year)))) . '">&laquo; ' . $jdate_month_name[$jprevious[1]] . '</a></td>';
        else
            $calendar_output .= "\n\t\t" . '<td colspan="3" id="prev"><a href="' . get_month_link($previous->year, $previous->month) . '" title="' . esc_attr(sprintf(__('View posts for %1$s %2$s','ztjalali'), $jdate_month_name[$jprevious[1]], jdate('Y', mktime(0, 0, 0, $previous->month, 1, $previous->year)))) . '">&laquo; ' . $jdate_month_name[$jprevious[1]] . '</a></td>';
    } else {
        $calendar_output .= "\n\t\t" . '<td colspan="3" id="prev" class="pad">&nbsp;</td>';
    }

    $calendar_output .= "\n\t\t" . '<td class="pad">&nbsp;</td>';

    if ($next) {
        $jnext=gregorian_to_jalali($next->year, $next->month, $next->dayofmonth);
        if ($ztjalali_option['change_url_date_to_jalali'])
            $calendar_output .= "\n\t\t" . '<td colspan="3" id="next"><a href="' . get_month_link($jnext[0], $jnext[1]) . '" title="' . esc_attr(sprintf(__('View posts for %1$s %2$s','ztjalali'), $jdate_month_name[$jnext[1]], jdate('Y', mktime(0, 0, 0, $next->month, 1, $next->year)))) . '">' . $jdate_month_name[$jnext[1]] . ' &raquo;</a></td>';
        else    
            $calendar_output .= "\n\t\t" . '<td colspan="3" id="next"><a href="' . get_month_link($next->year, $next->month) . '" title="' . esc_attr(sprintf(__('View posts for %1$s %2$s','ztjalali'), $jdate_month_name[$jnext[1]], jdate('Y', mktime(0, 0, 0, $next->month, 1, $next->year)))) . '">' . $jdate_month_name[$jnext[1]] . ' &raquo;</a></td>';
    } else {
        $calendar_output .= "\n\t\t" . '<td colspan="3" id="next" class="pad">&nbsp;</td>';
    }

    $calendar_output .= '
	</tr>
	</tfoot>

	<tbody>
	<tr>';

// Get days with posts
    $dayswithposts = $wpdb->get_results("SELECT DISTINCT post_date
		FROM $wpdb->posts WHERE post_date >= '$startdate 00:00:00'
		AND post_type = 'post' AND post_status = 'publish'
		AND post_date <= '$enddate 23:59:59'", ARRAY_N);
    if ($dayswithposts) {
        foreach ((array) $dayswithposts as $daywith) {
            $jdaywithpost[] = jdate('j',  strtotime($daywith[0]),FALSE,FALSE);
        }
    } else {
        $jdaywithpost = array();
    }

    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'camino') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'safari') !== false)
        $ak_title_separator = "\n";
    else
        $ak_title_separator = ', ';

    $ak_titles_for_day = array();
    $ak_post_titles = $wpdb->get_results("SELECT ID, post_title, post_date as dom "
            . "FROM $wpdb->posts "
            . "WHERE post_date >= '$startdate 00:00:00' "
            . "AND post_date <= '$enddate 23:59:59' "
            . "AND post_type = 'post' AND post_status = 'publish'"
    );
    if ($ak_post_titles) {
        foreach ((array) $ak_post_titles as $ak_post_title) {

            /** This filter is documented in wp-includes/post-template.php */
            $post_title = esc_attr(apply_filters('the_title', $ak_post_title->post_title, $ak_post_title->ID));
            $jdom = $jdaywithpost[] = jdate('j',  strtotime( $ak_post_title->dom),FALSE,FALSE);
            if (empty($ak_titles_for_day['day_' . $jdom]))
                $ak_titles_for_day['day_' . $jdom] = '';
            if (empty($ak_titles_for_day["$jdom"])) // first one
                $ak_titles_for_day["$jdom"] = $post_title;
            else
                $ak_titles_for_day["$jdom"] .= $ak_title_separator . $post_title;
        }
    }
// See how much we should pad in the beginning
    $pad = calendar_week_mod(jdate('w', $unixmonth, false, false)  - $week_begins);
    $pad--;
    if ($pad < 0)
        $pad = 6;
    
    if (0 != $pad)
        $calendar_output .= "\n\t\t" . '<td colspan="' . esc_attr($pad) . '" class="pad">&nbsp;</td>';

    $jdaysinmonth = (int)(jdate('t', $unixmonth,FALSE,FALSE));
    for ($jday = 1; $jday <= $jdaysinmonth; ++$jday) {
        if (isset($newrow) && $newrow)
            $calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t";
        $newrow = false;
        if ($jday == jdate('j', time(),FALSE,FALSE) && $jthismonth == jdate('m', time(),FALSE,FALSE) && $jthisyear == jdate('Y', time(),FALSE,FALSE))
            $calendar_output .= '<td id="today">';
        else
            $calendar_output .= '<td>';

        if (in_array($jday, $jdaywithpost)){ // any posts today?
            $day = jalali_to_gregorian($jthisyear, $jthismonth, $jday);
            if ($ztjalali_option['change_url_date_to_jalali'])
                $calendar_output .= '<a href="' . get_day_link($jthisyear, $jthismonth, $jday) . '" title="' . esc_attr($ak_titles_for_day[$jday]) . "\">$jday</a>";
            else
                $calendar_output .= '<a href="' . get_day_link($day[0], $day[1], $day[2]) . '" title="' . esc_attr($ak_titles_for_day[$jday]) . "\">$jday</a>";
        }else
            $calendar_output .= $jday;
        $calendar_output .= '</td>';
        jdate('w', jmktime(0, 0, 0, $jthismonth, $jday, $jthisyear),FALSE,FALSE);
        if (6 == calendar_week_mod(date('w', jmktime(0, 0, 0, $jthismonth, $jday, $jthisyear)) - $week_begins))
            $newrow = true;
    }

    $pad = 7 - calendar_week_mod(date('w', jmktime(0, 0, 0, $jthismonth, $jday, $jthisyear)) - $week_begins);
    if ($pad != 0 && $pad != 7)
        $calendar_output .= "\n\t\t" . '<td class="pad" colspan="' . esc_attr($pad) . '">&nbsp;</td>';

    $calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";

    $cache[$key] = $calendar_output;
    wp_cache_set('ztjalali_calendar', $cache, 'calendar');

    if ($ztjalali_option['change_jdate_number_to_persian'])
        $calendar_output = ztjalali_persian_num($calendar_output);
    if ($echo) {
        /**
         * Filter the HTML calendar output.
         *
         * @since 3.0.0
         *
         * @param string $calendar_output HTML output of the calendar.
         */
        echo apply_filters('ztjalali_calendar', $calendar_output);
    } else {
        /** This filter is documented in wp-includes/general-template.php */
        return apply_filters('ztjalali_calendar', $calendar_output);
    }
}

?>