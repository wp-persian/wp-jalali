<?php
function jalali_touch_time( $edit = 1, $for_post = 1 ) {
	global $wp_locale, $post, $comment, $j_month_name;
	
	if ( $for_post )
	$edit = ( ('draft' == $post->post_status ) && (!$post->post_date || '0000-00-00 00:00:00' == $post->post_date ) ) ? false : true;
 
	$time_adj = time() + (get_option( 'gmt_offset' ) * 3600 );
	$post_date = ($for_post) ? $post->post_date : $comment->comment_date;
	$jj = ($edit) ? mysql2date( 'd', $post_date ) : gmdate( 'd', $time_adj );
	$mm = ($edit) ? mysql2date( 'm', $post_date ) : gmdate( 'm', $time_adj );
	$aa = ($edit) ? mysql2date( 'Y', $post_date ) : gmdate( 'Y', $time_adj );
	$hh = ($edit) ? mysql2date( 'H', $post_date ) : gmdate( 'H', $time_adj );
	$mn = ($edit) ? mysql2date( 'i', $post_date ) : gmdate( 'i', $time_adj );
	$ss = ($edit) ? mysql2date( 's', $post_date ) : gmdate( 's', $time_adj );
	
	// making jalali date from post time/current time
	$jalali_time = gregorian_to_jalali($aa,$mm,$jj);
	$jy = $jalali_time[0];
	$jm = $jalali_time[1];
	$jd = $jalali_time[2];
	
	echo "\n<script type=\"text/javascript\" src=\"" . get_option('siteurl') ."/wp-content/plugins/wp-jalali/inc/js/editjalali.js\"></script>\n";
?>

<style>#jmonths {direction:rtl;text-align: right}</style>

<script type="text/javascript">

	function jalali_timestamp_func() {
		var jd = jQuery('#jd').attr('value');
		var jy = jQuery('#jy').attr('value');
		var jm = jQuery('select#jm_select > option:selected').attr('value');
		if(!jd) jd = <?php echo $jd; ?>;
		if(!jm) jm = <?php echo $jm; ?>;
		if(!jy) jy = <?php echo $jy; ?>;
		var j_time_array = new Array(jy,jm,jd);
		var j2g = jalali_to_gregorian(j_time_array);
		var gy = j2g[0];
		var gm = j2g[1];
		var gd = j2g[2];
		jQuery('#jj').attr('value',gd);
		jQuery('#aa').attr('value',gy);
		if(gm<10) gm = "0"+gm;
		jQuery('select[@name=mm] > option[value='+gm+']').attr('selected','selected');
	}
	
	function inject_jalali_div() { // use for injecting jalali input boxes and month list under the default georgian date place
		jQuery("#timestampdiv *").hide();
		jQuery("#timestampdiv").append('<div id="jalalitimestamp"></div>');
		jQuery("#jalalitimestamp").append('<select tabindex="501" onchange="jalali_timestamp_func()" name="jm" id="jm_select"></select> ');
		<?php
		for ( $i = 1; $i < 13; $i = $i +1 ) {
			if($i == $jm) $selected_month = 'selected="selected"';
			echo "\tjQuery(\"#jm_select\").append('<option $selected_month value=\"$i\" id=\"jm_$i\">$j_month_name[$i]</option>');\n";
			$selected_month = '';
		}
		?>
		jQuery("#jalalitimestamp").append('<input tabindex="502" type="text"  id="jd" name="jd" value="<?php echo $jd; ?>" size="2" maxlength="2" onchange="jalali_timestamp_func()"/> ');
		jQuery("#jalalitimestamp").append('<input tabindex="500" type="text" id="jy" name="jy" value="<?php echo $jy ?>" size="4" maxlength="5" onchange="jalali_timestamp_func()" />');
		jQuery("#jalalitimestamp").append('<input type="hidden" id="ss" name="ss" value="<?php echo $ss ?>" size="2" maxlength="2" onchange="jalali_timestamp_func()" />');
		jQuery("#jalalitimestamp").append('<br />ساعت: <input tabindex="503" type="text" id="hh" name="hh" value="<?php echo $hh ?>" size="2" maxlength="2" onchange="jalali_timestamp_func()" /> : <input tabindex="504" type="text" id="mn" name="mn" value="<?php echo $mn ?>" size="2" maxlength="2" onchange="jalali_timestamp_func()" /> دقیقه');
		
		jQuery("select#mm, input#jj, input#aa").attr("onchange","georgian_timestamp_func()");
		<?php
		if ( $edit ) {
			echo "jQuery(\"a.edit-timestamp\").before('" . farsi_num(sprintf( __('<br /> %2$s %1$s / %3$s در %4$s:%5$s' ), $j_month_name[$jm], $jd, $jy, $hh, $mn )) . " <br />');\n";
		}
		?>
	}
	inject_jalali_div(); // inject!
</script>
	<?php

}

function jalali_timestamp_admin() {
	if ( current_user_can('edit_posts') ) jalali_touch_time(($action == 'edit'));
}
?>