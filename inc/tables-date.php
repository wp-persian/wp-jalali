<?php

/* Originally written by Reza Moallemi , www.moallemi.ir */


/*
* Hook into default wordpress date column for posts date
*/
add_filter('post_date_column_time', 'wp_fa_date_columns');
function wp_fa_date_columns($time) {
	$arrtime = split('/', $time);
	if(count($arrtime) == 1)
		return farsi_num($time);
	$gmt = mktime(0,0,0,$arrtime[1],$arrtime[2],$arrtime[0]);
	$time =  jdate('d M Y',$gmt); 
	return $time;
}


/*
* Hook into default wordpress filter in order to filter post by jalali date
*/
add_action('restrict_manage_posts', 'wp_fa_restrict_manage_posts');
function wp_fa_restrict_manage_posts() {
	global $post_type, $wpdb, $wp_locale;

	$months = $wpdb->get_results( $wpdb->prepare( "
		SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month, DAY( post_date ) as day
		FROM $wpdb->posts
		WHERE post_type = %s AND post_status <> 'auto-draft'
		ORDER BY post_date DESC
		", $post_type ) );
	$month_count = count( $months );
	if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
		return;
	$m = isset( $_GET['mfa'] ) ? (int) $_GET['mfa'] : 0;
?>
	<select name='mfa'>
		<option<?php selected( $m, 0 ); ?> value='0'><?php _e( 'Show all dates' ); ?></option>	
<?php
		foreach ( $months as $arc_row ) {
			if ( 0 == $arc_row->year )
				continue;
			$month = zeroise( $arc_row->month, 2 );
			$year = $arc_row->year;
			$gmt = mktime(0 ,0 , 0, $month, $arc_row->day, $year);
			$dateshow = jdate('M Y',$gmt);
			$date = english_num(jdate('Ym',$gmt));
			if($predate != $date)
				printf( "<option %s value='?m=%s'>%s</option>\n",
					selected( $m, $year . $month, false ),
					esc_attr( $date ),
					$dateshow
				);
			$predate = $date;
	}
?>
	</select>
<?php
}

add_action('load-edit.php', 'wp_fa_admin_init');
add_action('admin_init', 'wp_fa_admin_init');

function wp_fa_admin_init() {
	add_filter('posts_where', 'wp_fa_posts_where');
	
	/*
	* Hook into media date column to support jalali date for WP 3.1 and later only
	*/
	add_filter('manage_media_columns', 'wp_fa_new_media_columns');
	add_action('manage_media_custom_column', 'wp_fa_manage_media_columns', 10, 2);
	add_filter('manage_upload_sortable_columns', 'wp_fa_media_date_register_sortable');
	add_filter('request', 'wp_fa_date_fa_column_orderby');
}

function wp_fa_date_fa_column_orderby( $vars ) {
	if (isset($vars['orderby']) && 'date_fa' == $vars['orderby']) {
		$vars = array_merge( $vars, array(
			'orderby' => 'date'
		) );
	}
	return $vars;
}

function wp_fa_media_date_register_sortable( $columns ) {
	$columns['date_fa'] = 'date_fa';
	return $columns;
}

function wp_fa_new_media_columns($default_columns) {
	unset($default_columns['date']);
	$default_columns['date_fa'] = _x('Date', 'column name');
	return $default_columns;
}

function wp_fa_manage_media_columns($column_name, $id) {
	if($column_name == 'date_fa') {
		global $post, $id;
		if ( '0000-00-00 00:00:00' == $post->post_date && 'date' == $column_name ) {
			$t_time = $h_time = __( 'Unpublished' );
		} else {
			$t_time = get_the_time( __( 'Y/m/d g:i:s A' ) );
			$m_time = $post->post_date;
			$time = get_post_time( 'G', true, $post, false );
			if ( ( abs( $t_diff = time() - $time ) ) < 86400 ) {
				if ( $t_diff < 0 )
					$h_time = sprintf( __( '%s from now' ), human_time_diff( $time ) );
				else
					$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
			} else {
				$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
			}
		}
		echo wp_fa_date_columns($h_time);
	}
}

function wp_fa_posts_where($where) {
	global $wpdb, $wp_query;
	if( isset($_GET['mfa']) and $_GET['mfa'] != '0' )
	{
		$wp_query->query_vars['m'] = substr($_GET['mfa'], strpos($_GET['mfa'], '=') + 1);
		$where = mps_jalali_query($where);
	}
	return $where;
}

/*
* Use this hack to hide the default wordpress month filter in edit.php (List off all posts)
*/
add_action('admin_footer', 'wp_fa_admin_footer', 9999);
function wp_fa_admin_footer() {
?>
	<script type="text/javascript" language="javascript">
	jQuery(document).ready(function() {
	  jQuery('select[name="m"]').hide()
	});
	</script>
<?php
}
?>