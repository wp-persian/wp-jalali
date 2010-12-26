<?php
add_filter('post_date_column_time', 'wp_fa_date_columns');
//add_filter('media_date_column_time', 'wp_fa_date_columns');
function wp_fa_date_columns($time) {
	$arrtime = split('/', $time);
	if(count($arrtime) == 1)
		return farsi_num($time);
	$gmt = mktime(0,0,0,$arrtime[1],$arrtime[2],$arrtime[0]);
	$time =  jdate('d M Y',$gmt); 
	return $time;
}
function wp_fa_media_date_columns($time) {
	//print_r($time);
}

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

add_action('load-edit.php', 'wp_fa_admin_head');
add_action('admin_init', 'wp_fa_admin_head');

function wp_fa_admin_head() {
	add_filter('posts_where', 'wp_fa_posts_where');
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