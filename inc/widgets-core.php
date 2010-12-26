<?php
function widget_mps_calendar_init() {
	if ( !function_exists('mps_calendar') )
		return;

	if ( !function_exists('wp_register_sidebar_widget') )
		return;
		
	function mps_calendar_widget($args) {
		extract($args);
		$options = get_option('mps_calendar_widget');
		$title = $options['title'];
		echo $before_widget;
		echo $before_title . $title . $after_title;
		mps_calendar();
		echo $after_widget;
	}
	
	function widget_mps_calendar_control() {

		$options = get_option('mps_calendar_widget');
		if ( !is_array($options) )
			$options = array('title'=>'');
		if ( $_POST['mps_calendar_submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['mps_calendar_title']));
			update_option('mps_calendar_widget', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		?>
		<p style="text-align:right; direction:rtl"><label for="mps_calendar_title">عنوان: <input style="width: 200px;" id="mps_calendar_title" name="mps_calendar_title" type="text" value="<?php echo $title; ?>" /></label></p>
		<input type="hidden" id="mps_calendar_submit" name="mps_calendar_submit" value="1" />
		<?php
	}
	
	wp_register_sidebar_widget(1000, 'Jalali Calendar','mps_calendar_widget');
	wp_register_widget_control(1000, 'Jalali Calendar', 'widget_mps_calendar_control', 250, 100);
}

function widget_jarchive_init() {
	if ( !function_exists('wp_get_jarchives') )
		return;

	if ( !function_exists('wp_register_sidebar_widget') )
		return;
		
	function jarchive_widget($args) {
		extract($args);
		$options = get_option('jarchive_widget');
		$title = $options['title'];
		if (!isset($options['type'])) {
			$type="monthly";
		} else {
			$type = $options['type'];
		}
		$show_post_count = ($options['show_post_count'] == '1') ? "1" : "0"; // More Safer Way
		$dropdown = ($options['dropdown'] == '1') ? "1" : "0";
		echo $before_widget;
		echo $before_title . $title . $after_title;
		if ($dropdown) {
			echo "<select name=\"jarchive-dropdown\" onchange='document.location.href=this.options[this.selectedIndex].value;'> <option value=\"\">".attribute_escape($title)."</option>";
			wp_get_jarchives("type=$type&format=option&show_post_count=$show_post_count");
			echo "</select>";
		} else {
			echo '<ul>';
			wp_get_jarchives("type=$type"."&show_post_count=".$show_post_count);
			echo '</ul>';
		}
	
		echo $after_widget;
	}
	
	function widget_jarchive_control() {

		$options = get_option('jarchive_widget');
		if ( !is_array($options) )
			$options = array('title'=>'');
		if ( $_POST['jarchive_submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['jarchive_title']));
			$options['type'] = strip_tags(stripslashes($_POST['jarchive_type']));
			$options['show_post_count'] = strip_tags(stripslashes($_POST['jarchive_show_post_count']));
			$options['dropdown'] = isset($_POST['jarchives_dropdown']);
			update_option('jarchive_widget', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$type = htmlspecialchars($options['type'], ENT_QUOTES);
		
		if (empty($options['type']))
			$options['type'] = 'monthly';
		?>
		<div dir="rtl" align="justify">
		<p style="text-align:right"><label for="jarchive_title">عنوان: <input style="width: 200px;" id="jarchive_title" name="jarchive_title" type="text" value="<?php echo $title; ?>" /></label></p>
        <input name="jarchive_type" type="radio" value="yearly" id="yearly" <?php echo $options['type']=='yearly' ? 'checked="checked"':'' ?> /> <label for="yearly">سالیانه</label><br />
		<input name="jarchive_type" type="radio" value="monthly" id="monthly" <?php echo $options['type']=='monthly' ? 'checked="checked"':'' ?> /> <label for="monthly">ماهیانه</label><br />
		<input name="jarchive_type" type="radio" value="daily" id="daily" <?php echo $options['type']=='daily' ? 'checked="checked"':'' ?> /> <label for="daily">روزانه</label><br />
		<input name="jarchive_type" type="radio" value="postbypost" id="postbypost" <?php echo $options['type']=='postbypost' ? 'checked="checked"':'' ?> /> <label for="postbypost">نوشته به نوشته</label><br /><br />
		<input name="jarchive_show_post_count" type="checkbox" value="1" id="show_post_count" <?php echo $options['show_post_count']=='1' ? 'checked="checked"':'' ?> /> <label for="show_post_count">نمایش تعداد نوشته ها (فقط برای بایگانی ماهیانه و سالیانه)</label><br />
		<input name="jarchives_dropdown" type="checkbox" value="1" id="dropdown" <?php echo $options['dropdown']=='1' ? 'checked="checked"':'' ?> /> <label for="dropdown">نمایش به صورت لیست بازشو (فقط برای بایگانی ماهیانه و سالیانه)</label>	
		<input type="hidden" id="jarchive_submit" name="jarchive_submit" value="1" />
		</div>
		<?php
	}
	
	wp_register_sidebar_widget(1001, 'Jalali Archive','jarchive_widget');
	if(get_bloginfo('version') < 2.5)
		wp_register_widget_control(1001, 'Jalali Archive', 'widget_jarchive_control', 300, 150);
	else
		wp_register_widget_control(1001,'Jalali Archive', 'widget_jarchive_control');
}
?>