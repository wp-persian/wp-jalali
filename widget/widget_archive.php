<?php
/**
 * @see :  http://codex.wordpress.org/Widgets_API#Default_Usage
 */
class ztjalali_archive extends WP_Widget {

    function __construct() {
        parent::__construct(
                'ztjalali_archive', __('jalali archive', 'ztjalali'), array('description' => __('jalali archive widget', 'ztjalali'),)
        );
    }

    public function form($instance) {

        if (!isset($instance['jarchive_title']))
            $instance['jarchive_title'] = __('jalali archive', 'ztjalali');

        if (!isset($instance['jarchive_type']))
            $instance['jarchive_type'] = 'monthly';

        if (!isset($instance['jarchive_link_count']))
            $instance['jarchive_link_count'] = 12;
        
        if (!isset($instance['jarchive_show_post_count']))
            $instance['jarchive_show_post_count'] = TRUE;

        if (!isset($instance['jarchive_dropdown']))
            $instance['jarchive_dropdown'] = FALSE;
        ?>
        <div dir="rtl" align="justify">
            <p style="text-align:right">
                <label for="<?php echo $this->get_field_id('jarchive_title'); ?>">
                    <?php _e('title', 'ztjalali') ?>: 
                    <input style="width: 200px;" id="<?php echo $this->get_field_id('jarchive_title'); ?>" name="<?php echo $this->get_field_name('jarchive_title'); ?>" type="text" value="<?php echo $instance['jarchive_title']; ?>" />
                </label>
            </p>
            <p style="text-align:right">
                <label for="<?php echo $this->get_field_id('yearly'); ?>">
                    <input name="<?php echo $this->get_field_name('jarchive_type'); ?>" id="<?php echo $this->get_field_id('yearly'); ?>" type="radio" value="yearly" <?php checked($instance['jarchive_type'], 'yearly'); ?> />
                    <?php _e('yearly', 'ztjalali') ?>
                </label>
                <br/>
                <label for="<?php echo $this->get_field_id('monthly'); ?>">
                    <input name="<?php echo $this->get_field_name('jarchive_type'); ?>" id="<?php echo $this->get_field_id('monthly'); ?>" type="radio" value="monthly" <?php checked($instance['jarchive_type'], 'monthly'); ?> />
                    <?php _e('monthly', 'ztjalali') ?>
                </label>
                <br/>
                <label for="<?php echo $this->get_field_id('daily'); ?>">
                    <input name="<?php echo $this->get_field_name('jarchive_type'); ?>" id="<?php echo $this->get_field_id('daily'); ?>" type="radio" value="daily" <?php checked($instance['jarchive_type'], 'daily'); ?> />
                    <?php _e('daily', 'ztjalali') ?>
                </label>
                <br/>
                <label for="<?php echo $this->get_field_id('postbypost'); ?>">
                    <input name="<?php echo $this->get_field_name('jarchive_type'); ?>" id="<?php echo $this->get_field_id('postbypost'); ?>" type="radio" value="postbypost" <?php checked($instance['jarchive_type'], 'postbypost'); ?> />
                    <?php _e('post by post', 'ztjalali') ?>
                </label>
            </p>
            <p style="text-align:right">
                <label for="<?php echo $this->get_field_id('jarchive_link_count'); ?>">
                    <?php _e('link count(zero to unlimit)', 'ztjalali') ?>: 
                    <input style="width: 200px;" id="<?php echo $this->get_field_id('jarchive_link_count'); ?>" name="<?php echo $this->get_field_name('jarchive_link_count'); ?>" type="text" value="<?php echo $instance['jarchive_link_count']; ?>" />
                </label>
            </p>
            <p style="text-align:right">
                <label for="<?php echo $this->get_field_id('jarchive_show_post_count'); ?>">
                    <input name="<?php echo $this->get_field_name('jarchive_show_post_count'); ?>" type="checkbox" value="1" id="<?php echo $this->get_field_id('jarchive_show_post_count'); ?>" <?php checked($instance['jarchive_show_post_count'], TRUE); ?> />
                    <?php _e('show count of post (not work in post by post)', 'ztjalali') ?>
                </label>
            </p>
            <p style="text-align:right">
                <label for="<?php echo $this->get_field_id('dropdown'); ?>">
                    <input name="<?php echo $this->get_field_name('jarchive_dropdown'); ?>" type="checkbox" value="1" id="<?php echo $this->get_field_id('dropdown'); ?>" <?php checked($instance['jarchive_dropdown'], TRUE); ?> />
                    <?php _e('show dropdown list (not work in post by post)', 'ztjalali') ?>
                </label>
            </p>
        </div>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['jarchive_title'] = strip_tags($new_instance['jarchive_title']);
        $instance['jarchive_type'] = strip_tags($new_instance['jarchive_type']);
        $instance['jarchive_link_count'] = strip_tags($new_instance['jarchive_link_count']);
        $instance['jarchive_show_post_count'] = (!empty($new_instance['jarchive_show_post_count']) ) ? TRUE : FALSE;
        $instance['jarchive_dropdown'] = (!empty($new_instance['jarchive_dropdown']) ) ? TRUE : FALSE;
        return $instance;
    }

    public function widget($args, $instance) {
        if (!isset($instance['jarchive_title']))
            $instance['jarchive_title'] = __('jalali archive', 'ztjalali');

        if (!isset($instance['jarchive_type']))
            $instance['jarchive_type'] = 'monthly';
        
        if (!isset($instance['jarchive_link_count']))
            $instance['jarchive_link_count'] = 12;

        if (!isset($instance['jarchive_show_post_count']))
            $instance['jarchive_show_post_count'] = TRUE;

        if (!isset($instance['jarchive_dropdown']))
            $instance['jarchive_dropdown'] = FALSE;


        extract($args);
        echo $before_widget;
        echo $before_title . $instance['jarchive_title'] . $after_title;
        if ($instance['jarchive_dropdown']) {
            echo "<select name=\"jarchive-dropdown\" onchange='document.location.href=this.options[this.selectedIndex].value;'> <option value=\"\">" . esc_attr($instance['jarchive_title']) . "</option>";
            ztjalali_archive_widget($instance['jarchive_type'],'option',$instance['jarchive_show_post_count'],$instance['jarchive_link_count']);
            echo "</select>";
        } else {
            echo '<ul>';
            ztjalali_archive_widget($instance['jarchive_type'],'html',$instance['jarchive_show_post_count'],$instance['jarchive_link_count']);
            echo '</ul>';
        }

        echo $after_widget;
    }

}

/**
 * widget handle action
 */
add_action('widgets_init', 'register_ztjalali_archive');

function register_ztjalali_archive() {
    register_widget('ztjalali_archive');
}

/* =================================================================== */
/**
 * own widget function
 */
function ztjalali_archive_widget($type ='monthly',$format='html',$show_post_count=false,$limit=12,$before='',$after='') {
    global $wpdb, $jdate_month_name, $ztjalali_option;
    if ($type === "yearly") {
        $YearlyQry = $wpdb->get_results(
                "SELECT DATE_FORMAT( post_date ,'%Y-%m-%d' ) as date,
                        count(ID) as count,
                        YEAR(post_date) AS `year`, 
                        MONTH(post_date) AS `month`, 
                        DAYOFMONTH(post_date) AS `dayofmonth` 
                FROM $wpdb->posts 
                WHERE post_date < NOW() 
                  AND post_type = 'post' 
                  AND post_status = 'publish' 
                GROUP BY date ORDER BY post_date DESC");
        
        if (!empty($YearlyQry)) {
            $jYears = array();
            $i = 1;
            foreach ($YearlyQry as $res) {
                $jalali_year = gregorian_to_jalali($res->year, $res->month, $res->dayofmonth);
                $jYears[$jalali_year[0]]['year'] =$res->year ;
                if (!array_key_exists('count', $jYears[$jalali_year[0]])) {
                    $jYears[$jalali_year[0]]['count'] = 0;
                }
                $jYears[$jalali_year[0]]['count'] +=$res->count ;
            }
            foreach ($jYears as $jYear =>$data) {
                if ($ztjalali_option['change_url_date_to_jalali'])
                    $url = get_year_link($jYear);
                else
                    $url = get_year_link($data['year']);
                
                $jYear = ztjalali_persian_num($jYear);
                $c_after = $show_post_count ? '&nbsp;(' . ztjalali_persian_num($data['count']) . ')' . $after : '';
                echo get_archives_link($url, $jYear, $format, $before, $c_after);
                if ($i == $limit)
                    break;
                $i++;
            }
        }
    } elseif ("monthly" == $type OR empty ($type)) {
        $MonthlyQry = $wpdb->get_results(
                "SELECT DATE_FORMAT( post_date ,'%Y-%m-%d' ) as date,
                        count(ID) as count,
                        YEAR(post_date) AS `year`, 
                        MONTH(post_date) AS `month`, 
                        DAYOFMONTH(post_date) AS `dayofmonth` 
                FROM $wpdb->posts 
                WHERE post_date < NOW() 
                  AND post_type = 'post' 
                  AND post_status = 'publish' 
                GROUP BY date ORDER BY post_date DESC");
        
        if (!empty($MonthlyQry)) {
            $jMonths = array();
            foreach ($MonthlyQry as $res) {
                $jalali_month = gregorian_to_jalali($res->year, $res->month, $res->dayofmonth);
                $jMonths[$jalali_month[0].'-'.$jalali_month[1]]['year'] =$res->year ;
                $jMonths[$jalali_month[0].'-'.$jalali_month[1]]['month'] =$res->month ;
                if (!array_key_exists('count', $jMonths[$jalali_month[0].'-'.$jalali_month[1]])) {
                    $jMonths[$jalali_month[0].'-'.$jalali_month[1]]['count'] = 0;
                }
                $jMonths[$jalali_month[0].'-'.$jalali_month[1]]['count'] +=$res->count ;
            }
            $i = 1;
            foreach ($jMonths as $jMonth =>$data) {
                list($jY,$jM)= explode('-', $jMonth);
                if ($ztjalali_option['change_url_date_to_jalali'])
                    $url = get_month_link($jY,$jM);
                else
                    $url = get_month_link($data['year'],$data['month']);
                
                $jY = ztjalali_persian_num($jY);
                $jM = $jdate_month_name[$jM];
                $c_after = $show_post_count ? '&nbsp;(' . ztjalali_persian_num($data['count']) . ')' . $after : '';
                echo get_archives_link($url, $jM.' '.$jY, $format, $before, $c_after);
                if ($i == $limit)
                    break;
                $i++;
            }
        }
    } elseif ("daily" == $type) {
        $limStr = '';
        if (!empty($limit)) {            
            $limit = (int) $limit;
            $limStr = ' LIMIT ' . $limit;
        }
        $DailyQry = $wpdb->get_results(
                "SELECT DATE_FORMAT( post_date ,'%Y-%m-%d' ) as date,
                        count(ID) as count,
                        YEAR(post_date) AS `year`, 
                        MONTH(post_date) AS `month`, 
                        DAYOFMONTH(post_date) AS `dayofmonth` 
                FROM $wpdb->posts 
                WHERE post_date < NOW() 
                  AND post_type = 'post' 
                  AND post_status = 'publish' 
                GROUP BY date ORDER BY post_date DESC ".$limStr);
        
        if (!empty($DailyQry)) {
            foreach ($DailyQry as $Daily) {
                list($jY, $jM, $jD) = gregorian_to_jalali($Daily->year, $Daily->month, $Daily->dayofmonth);
                if ($ztjalali_option['change_url_date_to_jalali'])
                    $url = get_day_link($jY, $jM, $jD);
                else
                    $url = get_day_link($Daily->year, $Daily->month, $Daily->dayofmonth);
                
                $date = mktime(0, 0, 0, $Daily->month, $Daily->dayofmonth, $Daily->year);
                $text = jdate(get_option('date_format'), $date);
                if ($show_post_count)
                    $c_after = '&nbsp;(' . ztjalali_persian_num($Daily->count) . ')' . $after;
                echo get_archives_link($url, $text, $format, $before, $c_after);
            }
        }
    } elseif ('postbypost' == $type) {
        $limStr = '';
        if (!empty($limit)) {            
            $limit = (int) $limit;
            $limStr = ' LIMIT ' . $limit;
        }
        $byPosts = $wpdb->get_results("SELECT ID, post_date, post_title FROM $wpdb->posts WHERE  post_type='post'  AND post_date < NOW() AND post_status = 'publish' ORDER BY post_date DESC" . $limStr);
        if (!empty($byPosts)) {
            foreach ($byPosts as $aPost) {
                if ($aPost->post_date != '0000-00-00 00:00:00') {
                    $url = get_permalink($aPost->ID);
                    echo get_archives_link($url, strip_tags($aPost->post_title), $format, $before, $after);
                }
            }
        }
    }
}
?>