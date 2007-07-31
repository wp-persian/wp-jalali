<?php
/*
Plugin Name: Full Jalali Date & Persian Support Package for Wordpress
Plugin URI: http://www.wp-persian.com/wp-jalali/
Description: Full Jalali Date and Persian(Farsi) Support Package for wordpress,  Full posts' and comments' dates convertion , Jalali Archive , Magic(Jalali/Gregorian) Calendar and Jalali/Gregorian Compaitables Permalinks, TinyMCE RTL/LTR activation, TinyMCE Persian Improvement, Cross browser Perisan keyboard support, Jalali Archive/Calendar widgets and Persian numbers, Great tool for Persian(Iranian) Users of WordPress, part of <a href="http://wp-persian.com" title="پروژه وردپرس فارسی">Persian Wordpress Project</a>.
Version: 3.5
Author: Vali Allah(Mani) Monajjemi
Author URI: http://www.manionline.org/
*/

/*  Copyright 2005-2007  Vali Allah[Mani] Monajjemi  (email : mani.monajjemi@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/* 
Special Thanks to :
*	Farsiweb.info for J2G and G2J Converstion Functions
*	Milad Raastian (miladmovie.com) for JDF (jdf.farsiprojects.com) 
*	Nima Shyanfar (phpmystery.com) for  Fast Farsi Number Conversion Method
*	Gonahkar (gonahkar.com) for WP-Jalali widgets plugin (gonahkar.com/archives/2007/02/26/wp-jalali-widgets-plugin/ )
*	Kaveh Ahmadi (ashoob.net/kaveh) for his valuable Farsi Keyboard Script (ashoob.net/farsitype)
*	Ali Sattari(corelist.net) for great support
*	WP-Persian group members (groups.google.com/group/wp-persian/topics)	
*/

define("MPS_JD_VER","3.5");

if (!function_exists('fetch_rss'))	require_once (ABSPATH . WPINC . '/rss-functions.php');
if (!function_exists('error')) {
	function error ($errormsg, $lvl=E_USER_WARNING) {
	    // append PHP's error message if track_errors enabled
	    if ( $php_errormsg ) { 
	        $errormsg .= " ($php_errormsg)";
	    }
	    if ( MAGPIE_DEBUG ) {
	        trigger_error( $errormsg, $lvl);        
	    }
	    else {
	        error_log( $errormsg, 0);
	    }
	    
	    $notices = E_USER_NOTICE|E_NOTICE;
	    if ( $lvl&$notices ) {
	        $this->WARNING = $errormsg;
	    } else {
	        $this->ERROR = $errormsg;
	    }
	}
}

define("_JDF_USE_PERSIANNUM","0");
define("_JDF_TZhours","0");
define("_JDF_TZminute","0");
define('_JDF_AM_LONG','قبل از ظهر');
define('_JDF_PM_LONG','بعد از ظهر');
define('_JDF_AM_SHORT','ق.ظ');
define('_JDF_PM_SHORT','ب.ظ');
define('_JDF_Sat_LONG','شنبه');
define('_JDF_Sun_LONG','یکشنبه');
define('_JDF_Mon_LONG','دوشنبه');
define('_JDF_Tue_LONG','سه شنبه');
define('_JDF_Wed_LONG','چهارشنبه');
define('_JDF_Thu_LONG','پنجشنبه');
define('_JDF_Fri_LONG','جمعه');
define('_JDF_Sat_SHORT','ش');
define('_JDF_Sun_SHORT','ی');
define('_JDF_Mon_SHORT','د');
define('_JDF_Tue_SHORT','س');
define('_JDF_Wed_SHORT','چ');
define('_JDF_Thu_SHORT','پ');
define('_JDF_Fri_SHORT','ج');
define('_JDF_Suffix','م');
define('_JDF_Far','فروردین');
define('_JDF_Ord','اردیبهشت');
define('_JDF_Kho','خرداد');
define('_JDF_Tir','تیر');
define('_JDF_Mor','مرداد');
define('_JDF_Sha','شهریور');
define('_JDF_Meh','مهر');
define('_JDF_Aba','آبان');
define('_JDF_Aza','آذر');
define('_JDF_Dey','دی');
define('_JDF_Bah','بهمن');
define('_JDF_Esf','اسفند');
define('_JDF_Num0','۰');
define('_JDF_Num1','۱');
define('_JDF_Num2','۲');
define('_JDF_Num3','۳');
define('_JDF_Num4','۴');
define('_JDF_Num5','۵');
define('_JDF_Num6','۶');
define('_JDF_Num7','۷');
define('_JDF_Num8','۸');
define('_JDF_Num9','۹');

$g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
$j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
$j_month_name = array("", "فروردین", "اردیبهشت", "خرداد", "تیر",
"مرداد", "شهریور", "مهر", "آبان", "آذر",
"دی", "بهمن", "اسفند");
$j_day_name = array("یکشنبه","دوشنبه","سه شنبه","چهارشنبه","پنجشنبه","جمعه","شنبه");
$jday_abbrev = array("ی","د","س","چ","پ","ج","ش");

/* Menu Init */

define('MPS_JD_OPTIONS_NAME', "mps_jd_options"."_".MPS_JD_VER);	// Name of the Option stored in the DB

function mps_jd_menu(){
	/* 
		mps_jd_farsinum_XXX :: Toggle Persian numbers in dates
		mps_jd_mcertl :: Toggle TinyMCE Editor RTL / LTR
		mps_jd_jperma :: Toggle Jalali Permalinks on/off (For posts)
		
	*/
	if(function_exists('add_options_page')) {
		//add_options_page("تنظیمات وردپرس فارسی", "وردپرس فارسی", 10, __FILE__,'mps_jd_optionpage');
		add_menu_page("تنظیمات وردپرس فارسی", "وردپرس فارسی", 10, __FILE__,'mps_jd_optionpage');
	}

	$mps_jd_optionsDB = get_option(MPS_JD_OPTIONS_NAME);
	
	if (empty($mps_jd_optionsDB)) { 
		//echo "init";
		$mps_jd_optionsDB['mps_jd_autodate'] = $mps_jd_autodate = true;
		$mps_jd_optionsDB['mps_jd_farsinum_content'] = $mps_jd_farsinum_content = true;
		$mps_jd_optionsDB['mps_jd_farsinum_comment'] = $mps_jd_farsinum_comment = true;
		$mps_jd_optionsDB['mps_jd_farsinum_commentnum'] = $mps_jd_farsinum_commentnum = true;
		$mps_jd_optionsDB['mps_jd_farsinum_title'] = $mps_jd_farsinum_title = true;
		$mps_jd_optionsDB['mps_jd_farsinum_category'] = $mps_jd_farsinum_category = true;
		$mps_jd_optionsDB['mps_jd_farsinum_date'] = $mps_jd_farsinum_date = true;
		$mps_jd_optionsDB['mps_jd_mcertl'] = $mps_jd_mcertl = true;
		$mps_jd_optionsDB['mps_jd_jperma'] = $mps_jd_jperma = true;
		update_option(MPS_JD_OPTIONS_NAME,$mps_jd_optionsDB);	
	}
}

function mps_jd_optionpage(){
	global $user_level;
	global $j_day_name;
	
	$_wp_version = get_bloginfo("version");
	
	if ( $_wp_version < 2.1 ) {
		get_currentuserinfo();
		$enable_options = ($user_level >= 8);
	} else {
		$enable_options = current_user_can('manage_options');
	}
	
	if (!$enable_options) { //MUST BE ADMIN
		?>
		<div class="wrap">
		<h2>خطا</h2>
		<br /><?php _e("<div style=\"color:#770000;\">شما دارای اختیارات کافی نمی باشید.</div>"); ?><br />
		</div>
		<?php
		return;
	}
	
	if ( (isset($_POST['action'])) && ($_POST['action'] == 'update')) {
		$mps_jd_optionsDB['mps_jd_autodate'] = $mps_jd_autodate = $_POST['mps_jd_autodate'];
		$mps_jd_optionsDB['mps_jd_farsinum_content'] = $mps_jd_farsinum_content = $_POST['mps_jd_farsinum_content'];
		$mps_jd_optionsDB['mps_jd_farsinum_comment'] = $mps_jd_farsinum_comment = $_POST['mps_jd_farsinum_comment'];
		$mps_jd_optionsDB['mps_jd_farsinum_commentnum'] = $mps_jd_farsinum_commentnum = $_POST['mps_jd_farsinum_commentnum'];
		$mps_jd_optionsDB['mps_jd_farsinum_title'] = $mps_jd_farsinum_title = $_POST['mps_jd_farsinum_title'];
		$mps_jd_optionsDB['mps_jd_farsinum_category'] = $mps_jd_farsinum_category = $_POST['mps_jd_farsinum_category'];
		$mps_jd_optionsDB['mps_jd_farsinum_date'] = $mps_jd_farsinum_date = $_POST['mps_jd_farsinum_date'];
		$mps_jd_optionsDB['mps_jd_mcertl'] = $mps_jd_mcertl = $_POST['mps_jd_mcertl'];
		$mps_jd_optionsDB['mps_jd_jperma'] = $mps_jd_jperma = $_POST['mps_jd_jperma'];
		update_option(MPS_JD_OPTIONS_NAME,$mps_jd_optionsDB);
		update_option('gmt_offset',$_POST['gmt_offset']);
		update_option('date_format',$_POST['date_format']);
		update_option('time_format',$_POST['time_format']);
		update_option('start_of_week',$_POST['start_of_week']);
		$mps_ERR = "تغییرات با موفقیت ثبت شد.";
	}

	$mps_jd_optionsDB = get_option(MPS_JD_OPTIONS_NAME);
	$mps_jd_autodate = $mps_jd_optionsDB['mps_jd_autodate'];
	$mps_jd_farsinum_content = $mps_jd_optionsDB['mps_jd_farsinum_content'];
	$mps_jd_farsinum_comment = $mps_jd_optionsDB['mps_jd_farsinum_comment'];
	$mps_jd_farsinum_commentnum = $mps_jd_optionsDB['mps_jd_farsinum_commentnum'];
	$mps_jd_farsinum_title = $mps_jd_optionsDB['mps_jd_farsinum_title'];
	$mps_jd_farsinum_category = $mps_jd_optionsDB['mps_jd_farsinum_category'];
	$mps_jd_farsinum_date = $mps_jd_optionsDB['mps_jd_farsinum_date'];
	$mps_jd_mcertl = $mps_jd_optionsDB['mps_jd_mcertl'];
	$mps_jd_jperma = $mps_jd_optionsDB['mps_jd_jperma'];
	
	if((isset($mps_ERR)) && (!empty($mps_ERR))) {
	?>
		<br clear="all" />
		<div id="message" class="updated fade" style="direction: rtl"><p><strong><?php _e($mps_ERR); ?></strong></p></div>
	<?php
	}
	?>
	
	<?php
	$logo_uri = get_settings('siteurl').'/wp-content/plugins/WP-Jalali/wp-fa-logo.png';
	?>
	
	<div class="wrap" style="direction:rtl ">
	<p style="text-align: center">
		<a href="http://www.wp-persian.com" style="border: none" title="وردپرس فارسی"><img src="<?=$logo_uri?>" alt="Persian Wordpress Logo" width="300" height="70" border="0"/></a>
	</p>
	<h2>اخبار وردپرس فارسی</h2>
	<h3>وبلاگ توسعه وردپرس فارسی</h3>
	
	<?php
		$rss = @fetch_rss('http://www.wp-persian.com/feed/');
		if ( isset($rss->items) && 0 != count($rss->items) ) {
			?>
				<?php
					$rss->items = array_slice($rss->items, 0, 3);
					foreach ($rss->items as $item ) {
					?>
						<h4 dir="rtl" style="color: gray; margin-right: 25px; "><a href='<?php echo wp_filter_kses($item['link']); ?>'><?php echo wp_specialchars($item['title']); ?></a> &#8212; <?php printf(__('%s ago'), human_time_diff(strtotime($item['pubdate'], time() ) ) ); ?></h4>
					<?php
					}
				}
			?>
	<div id="planetnews">		
	<h3>سیاره وردپرس فارسی <a href="http://planet.wp-persian.com/">بیشتر »</a></h3>
	<?php
		$rss = @fetch_rss('http://planet.wp-persian.com/feed/');
		if ( isset($rss->items) && 0 != count($rss->items) ) {
			?>

			<ul>
				<?php
					$rss->items = array_slice($rss->items, 0, 10);
					foreach ($rss->items as $item ) {

					?>
					<li><?php echo wp_specialchars($item['dc']['creator']); ?> : <a href='<?php echo wp_filter_kses($item['link']); ?>'><?php echo wp_specialchars($item['title']); ?></a><?php// printf(__('%s ago'), human_time_diff(strtotime($item['pubdate'], time() ) ) ); ?></li>
					<?php
					}
					?>
					</ul><br style="clear: both;"/>
			<?php
				}
			?>
	
	
	
	<h2>تنظیمات وردپرس فارسی</h2>
			<form method="post">
		<input type="hidden" name="action" value="update" />
	<table width="100%" cellspacing="2" cellpadding="5" class="editform" border="0"> 
	<tr valign="top"> 
        <th width="33%" scope="row">تبدیل خودکار تاریخ نوشته ها و نظرات به تاریخ خورشیدی(شمسی)</th> 
        <td>
        	<select name="mps_jd_autodate" id="mps_jd_autodate">
        		<option value="1" <?=$mps_jd_autodate==true? 'selected=\"selected\"':'' ?>>فعال (پیشنهاد می شود)</option>
        		<option value="0" <?=$mps_jd_autodate==false?'selected=\"selected\"':'' ?>>غیر فعال</option>
        	</select>
        </td> 
      </tr>
	<tr valign="top"> 
        <th width="33%" scope="row">نمایش ارقام فارسی</th> 
        <td>
			<table border="0" cellpadding="2" cellspacing="2">
				<tr>
					<td>متن نوشته ها</td>
					<td><input type="checkbox" name="mps_jd_farsinum_content" <?=$mps_jd_farsinum_content==true? 'checked=\"checked\"':'' ?> /></td>
					<td>متن نظر ها</td>
					<td><input type="checkbox" name="mps_jd_farsinum_comment" <?=$mps_jd_farsinum_comment==true? 'checked=\"checked\"':'' ?> /></td>
					<td>تعداد نظر ها</td>
					<td><input type="checkbox" name="mps_jd_farsinum_commentnum" <?=$mps_jd_farsinum_commentnum==true? 'checked=\"checked\"':'' ?> /></td>
				</tr>
				<tr>
					<td>عنوان نوشته ها</td>
					<td><input type="checkbox" name="mps_jd_farsinum_title" <?=$mps_jd_farsinum_title==true? 'checked=\"checked\"':'' ?> /></td>
					<td>تاریخ ها</td>
					<td><input type="checkbox" name="mps_jd_farsinum_date" <?=$mps_jd_farsinum_date==true? 'checked=\"checked\"':'' ?> /></td>
					<td>فهرست رسته ها</td>
					<td><input type="checkbox" name="mps_jd_farsinum_category" <?=$mps_jd_farsinum_category==true? 'checked=\"checked\"':'' ?> /></td>
				</tr>
			</table>
        	
        </td>
	</tr> 
      <tr valign="top"> 
        <th width="33%" scope="row">جهت ویرایشگر متنی صفحه نوشتن</th> 
        <td>
        	<select name="mps_jd_mcertl" id="mps_jd_mcertl">
        		<option value="1" <?=$mps_jd_mcertl==true? 'selected=\"selected\"':'' ?>>راست به چپ</option>
        		<option value="0" <?=$mps_jd_mcertl==false?'selected=\"selected\"':'' ?>>چپ به راست</option>
        	</select>
        </td> 
      </tr>
	  <tr valign="top"> 
        <th width="33%" scope="row">تبدیل خودکار تاریخ در آدرس (URI) نوشته ها</th> 
        <td>
        	<select name="mps_jd_jperma" id="mps_jd_jperma">
        		<option value="1" <?=$mps_jd_jperma==true? 'selected=\"selected\"':'' ?>>بله</option>
        		<option value="0" <?=$mps_jd_jperma==false?'selected=\"selected\"':'' ?>>خیر</option>
        	</select>
        </td> 
      </tr>
      </table>
      <fieldset class="options"> 
      <legend><?php _e('تنظیمات تاریخ و ساعت') ?></legend> 
	    <table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
      <tr> 
          <th scope="row" width="33%">ساعت به وقت <abbr title="Coordinated Universal Time">UTC</abbr>:</th> 
        <td><code>
        <?php 
        	$m = gmdate('YmdHis'); 
        	$gmt = mktime(substr($m,8,2),substr($m,10,2),substr($m,12,2),substr($m,4,2),substr($m,6,2),substr($m,0,4));
        	
        	
        ?>
        <?php echo jdate('l Y-m-d g:i:s a',$gmt); ?></code></td> 
      </tr>
      <tr>
        <th scope="row">اختلاف ساعت محلی :</th>
        <td><input style="direction:rtl; text-align:left" name="gmt_offset" type="text" id="gmt_offset" size="2" value="<?php form_option('gmt_offset'); ?>" /> 
        ساعت </td>
      </tr>
      <tr>
      	<th scope="row">&nbsp;</th>
      	<td>فرمت زیر مانند <a href="http://php.net/date">تابع <code>date()</code> PHP</a> می باشد. برای نمایش تغییرات این صفحه را به روز کنید.</td>
      	</tr>
      <tr>
      	<th scope="row">فرمت تاریخ پیش فرض</th>
      	<td><input style="direction:rtl; text-align:left" name="date_format" type="text" id="date_format" size="30" value="<?php form_option('date_format'); ?>" /><br />
خروجی : <strong><?php echo jdate(get_settings('date_format'), $gmt + (get_settings('gmt_offset') * 3600)); ?></strong></td>
      	</tr>
      <tr>
        <th scope="row">فرمت زمان پیش فرض</th>
      	<td><input style="direction:rtl; text-align:left" name="time_format" type="text" id="time_format" size="30" value="<?php form_option('time_format'); ?>" /><br />
خروجی : <strong><?php echo jdate(get_settings('time_format'), $gmt + (get_settings('gmt_offset') * 3600)) ; ?></strong></td>
      	</tr> 
      <tr>
        <th scope="row">روز شروع هفته در تقویم</th>
        <td><select name="start_of_week" id="start_of_week">
	<?php
for ($day_index = 0; $day_index <= 6; $day_index++) :
	if ($day_index == get_settings('start_of_week')) $selected = " selected='selected'";
	else $selected = '';
echo "\n\t<option value='$day_index' $selected>$j_day_name[$day_index]</option>";
endfor;
?>
</select></td>
       		</tr>

</table>
	
	
      
		<p class="submit">
      	<input type="submit" name="Submit" value="به روز رسانی &raquo;" />
     </p>
	</form>
	
	<br />
</div><br />&nbsp;
	<div id="wp-bookmarklet" class="wrap" style="direction:rtl; text-align:right">
<h3>پروژه وردپرس فارسی</h3>
	<p>این افزونه، بخشی از <a href="http://www.wp-persian.com/">پروژه وردپرس فارسی</a> می باشد. برای اطلاعات بیشتر در مورد این  پلاگ-این می توانید <a href="http://www.wp-persian.com/wp-jalali/">صفحه مخصوص این پلاگ-این</a> را مشاهده کنید.</p>
	</div>
	
	
	<?php
	
}

/* Farsiweb.info Jaladi/Gregorian Convertion Functions */

function div($a, $b)
{
	return (int) ($a / $b);
}

function jalali_to_gregorian($j_y, $j_m, $j_d)
{
	global $g_days_in_month;
	global $j_days_in_month;

	$jy = $j_y-979;
	$jm = $j_m-1;
	$jd = $j_d-1;

	$j_day_no = 365*$jy + div($jy, 33)*8 + div($jy%33+3, 4);
	for ($i=0; $i < $jm; ++$i)
	$j_day_no += $j_days_in_month[$i];

	$j_day_no += $jd;

	$g_day_no = $j_day_no+79;

	$gy = 1600 + 400*div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
	$g_day_no = $g_day_no % 146097;

	$leap = true;
	if ($g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */
	{
		$g_day_no--;
		$gy += 100*div($g_day_no,  36524); /* 36524 = 365*100 + 100/4 - 100/100 */
		$g_day_no = $g_day_no % 36524;

		if ($g_day_no >= 365)
		$g_day_no++;
		else
		$leap = false;
	}

	$gy += 4*div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */
	$g_day_no %= 1461;

	if ($g_day_no >= 366) {
		$leap = false;

		$g_day_no--;
		$gy += div($g_day_no, 365);
		$g_day_no = $g_day_no % 365;
	}

	for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++)
	$g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);
	$gm = $i+1;
	$gd = $g_day_no+1;

	return array($gy, $gm, $gd);
}

function jcheckdate($j_m, $j_d, $j_y)
{
	global $j_days_in_month;

	if ($j_y < 0 || $j_y > 32767 || $j_m < 1 || $j_m > 12 || $j_d < 1 || $j_d >
	($j_days_in_month[$j_m-1] + ($j_m == 12 && !(($j_y-979)%33%4))))
	return false;
	return true;
}

function gregorian_week_day($g_y, $g_m, $g_d)
{
	global $g_days_in_month;

	$gy = $g_y-1600;
	$gm = $g_m-1;
	$gd = $g_d-1;

	$g_day_no = 365*$gy+div($gy+3,4)-div($gy+99,100)+div($gy+399,400);

	for ($i=0; $i < $gm; ++$i)
	$g_day_no += $g_days_in_month[$i];
	if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
	/* leap and after Feb */
	++$g_day_no;
	$g_day_no += $gd;

	return ($g_day_no + 5) % 7 + 1;
}

function jalali_week_day($j_y, $j_m, $j_d)
{
	global $j_days_in_month;

	$jy = $j_y-979;
	$jm = $j_m-1;
	$jd = $j_d-1;

	$j_day_no = 365*$jy + div($jy, 33)*8 + div($jy%33+3, 4);

	for ($i=0; $i < $jm; ++$i)
	$j_day_no += $j_days_in_month[$i];

	$j_day_no += $jd;

	return ($j_day_no + 2) % 7 + 1;
}


function gregorian_to_jalali($g_y, $g_m, $g_d)
{
	global $g_days_in_month;
	global $j_days_in_month;

	$gy = $g_y-1600;
	$gm = $g_m-1;
	$gd = $g_d-1;

	$g_day_no = 365*$gy+div($gy+3,4)-div($gy+99,100)+div($gy+399,400);

	for ($i=0; $i < $gm; ++$i)
	$g_day_no += $g_days_in_month[$i];
	if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
	/* leap and after Feb */
	++$g_day_no;
	$g_day_no += $gd;

	$j_day_no = $g_day_no-79;

	$j_np = div($j_day_no, 12053);
	$j_day_no %= 12053;

	$jy = 979+33*$j_np+4*div($j_day_no,1461);

	$j_day_no %= 1461;

	if ($j_day_no >= 366) {
		$jy += div($j_day_no-1, 365);
		$j_day_no = ($j_day_no-1)%365;
	}

	for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) {
		$j_day_no -= $j_days_in_month[$i];
	}
	$jm = $i+1;
	$jd = $j_day_no+1;


	return array($jy, $jm, $jd);
}

/* These 2 Functions are new Farsi Num convert implemented in ver 3,
Originally written by Nima Shyanfar , www.phpmystery.com 
Thanx Nima ;)
*/

function convertToFarsi($matches) {
	$out = ''; 
	if (isset($matches[1])) {
			for ($i = 0; $i < strlen($matches[1]); $i++)
			if (ereg("([0-9])",$matches[1][$i])) {
				$out .= pack("C*", 0xDB, 0xB0 + $matches[1][$i]);
			} else {
				$out .= $matches[1][$i];
			}

		return $out;
	}
	return $matches[0];
}

function farsi_num($num,$fake = null,$fake2=null) {
	return preg_replace_callback('/(?:&#\d{2,4};)|(\d+[\.\d]*)|<\s*[^>]+>/', 'convertToFarsi', $num);
	
}

/*
Jalali Date function by Milad Rastian (miladmovie AT yahoo DOT com)
jdf.farsiprojects.com
*/


function jdate($type,$maket="now",$forcelatinnums=false)
{
	$result="";
	if($maket=="now"){
		$year=date("Y");
		$month=date("m");
		$day=date("d");
		list( $jyear, $jmonth, $jday ) = gregorian_to_jalali($year, $month, $day);
		$maket=jmaketime(date("h")+_JDF_TZhours,date("i")+_JDF_TZminute,date("s"),$jmonth,$jday,$jyear);
	}else{
		$maket+=_JDF_TZhours*3600+_JDF_TZminute*60;
		$date=date("Y-m-d",$maket);
		list( $year, $month, $day ) = preg_split ( '/-/', $date );

		list( $jyear, $jmonth, $jday ) = gregorian_to_jalali($year, $month, $day);
	}

	$need= $maket;
	$year=date("Y",$need);
	$month=date("m",$need);
	$day=date("d",$need);
	$i=0;
	$skipnext = false;
	while($i<strlen($type))
	{
		$subtype=substr($type,$i,1);
		
		if ($skipnext) {
			$result .= $subtype;
			$skipnext = false;
			$i++;
			continue;
		}	
		
		
		switch ($subtype)
		{
			case "A":
				$result1=date("a",$need);
				if($result1=="pm") 
					$result.=_JDF_PM_LONG;
				else 
					$result.=_JDF_AM_LONG;
				break;

			case "a":
				$result1=date("a",$need);
				if($result1=="pm") 
					$result.=_JDF_PM_SHORT;
				else 
					$result.=_JDF_AM_SHORT;
				break;
			
			case "d":
				list( $jyear, $jmonth, $jday ) = gregorian_to_jalali($year, $month, $day);
				if($jday<10)
					$result1="0".$jday;
				else
					$result1=$jday;
				
				$result.=$result1;
				break;
			
			case "D":
				$result1=date("D",$need);
				if($result1=="Sat") $result1=_JDF_Sat_SHORT;
				else if($result1=="Sun") $result1=_JDF_Sun_SHORT;
				else if($result1=="Mon") $result1=_JDF_Mon_SHORT;
				else if($result1=="Tue") $result1=_JDF_Tue_SHORT;
				else if($result1=="Wed") $result1=_JDF_Wed_SHORT;
				else if($result1=="Thu") $result1=_JDF_Thu_SHORT;
				else if($result1=="Fri") $result1=_JDF_Fri_SHORT;
				$result.=$result1;
				break;
			
			case"F":
				list( $jyear, $jmonth, $jday ) = gregorian_to_jalali($year, $month, $day);
				$result.=monthname($jmonth);
				break;
			
			case "g":
				$result1=date("g",$need);
				$result.=$result1;
				break;
			
			case "G":
				$result1=date("G",$need);
				$result.=$result1;
				break;
			
			case "h":
				$result1=date("h",$need);
				$result.=$result1;
				break;
			
			case "H":
				$result1=date("H",$need);
				$result.=$result1;
				break;	
			
			case "i":
				$result1=date("i",$need);
				$result.=$result1;
				break;
			
			case "j":
				list( $jyear, $jmonth, $jday ) = gregorian_to_jalali($year, $month, $day);
				$result1=$jday;
				$result.=$result1;
				break;
			
			case "l":
				$result1=date("l",$need);
				if($result1=="Saturday") $result1=_JDF_Sat_LONG;
				else if($result1=="Sunday") $result1=_JDF_Sun_LONG;
				else if($result1=="Monday") $result1=_JDF_Mon_LONG;
				else if($result1=="Tuesday") $result1=_JDF_Tue_LONG;
				else if($result1=="Wednesday") $result1=_JDF_Wed_LONG;
				else if($result1=="Thursday") $result1=_JDF_Thu_LONG;
				else if($result1=="Friday") $result1=_JDF_Fri_LONG;
				$result.=$result1;
				break;
			
			case "m":
				list( $jyear, $jmonth, $jday ) = gregorian_to_jalali($year, $month, $day);
				if($jmonth<10) 
					$result1="0".$jmonth;
				else	
					$result1=$jmonth;
				$result.=$result1;
				break;
			
			case "M":
				list( $jyear, $jmonth, $jday ) = gregorian_to_jalali($year, $month, $day);
				$result.=monthname($jmonth);
				break;
			
			case "n":
				list( $jyear, $jmonth, $jday ) = gregorian_to_jalali($year, $month, $day);
				$result1=$jmonth;
				$result.=$result1;
				break;
			
			case "s":
				$result1=date("s",$need);
				$result.=$result1;
				break;
			
			case "S":
				$result.=_JDF_Suffix;
				break;
			
			case "t":
				$result.=lastday ($month,$day,$year);
				break;
			
			case "w":
				$result1=date("w",$need);
				$result.=$result1;
				break;
			
			case "y":
				list( $jyear, $jmonth, $jday ) = gregorian_to_jalali($year, $month, $day);
				$result1=substr($jyear,2,4);
				$result.=$result1;
				break;
			
			case "Y":
				list( $jyear, $jmonth, $jday ) = gregorian_to_jalali($year, $month, $day);
				$result1=$jyear;
				$result.=$result1;
				break;
			
			case "\\":
				$result.='';
				$skipnext = true;
				break;
										
			default:
				$result.=$subtype;
		}
		$i++;
	}
	
	$mps_jd_optionsDB = get_option(MPS_JD_OPTIONS_NAME);
	$mps_jd_farsinum_date = $mps_jd_optionsDB['mps_jd_farsinum_date'];
	
	if ((!$forcelatinnums) && ($mps_jd_farsinum_date))
		$result = farsi_num($result);
	
	return $result;
}



function jmaketime($hour,$minute,$second,$jmonth,$jday,$jyear)
{
	list( $year, $month, $day ) = jalali_to_gregorian($jyear, $jmonth, $jday);
	$i=mktime((int) $hour,(int) $minute,(int) $second, (int) $month, (int) $day, (int) $year, 0);
	return $i;
}


///Find Day Begining Of Month
function mstart($month,$day,$year)
{
	list( $jyear, $jmonth, $jday ) = gregorian_to_jalali($year, $month, $day);
	list( $year, $month, $day ) = jalali_to_gregorian($jyear, $jmonth, "1");
	$timestamp=mktime(0,0,0,$month,$day,$year);
	return date("w",$timestamp);
}

//Find Number Of Days In This Month
function lastday ($month,$day,$year)
{
	$lastdayen=date("d",mktime(0,0,0,$month+1,0,$year));
	list( $jyear, $jmonth, $jday ) = gregorian_to_jalali($year, $month, $day);
	$lastdatep=$jday;
	$jday=$jday2;
	while($jday2!="1")
	{
		if($day<$lastdayen)
		{
			$day++;
			list( $jyear, $jmonth, $jday2 ) = gregorian_to_jalali($year, $month, $day);
			if($jdate2=="1") break;
			if($jdate2!="1") $lastdatep++;
		}
		else
		{
			$day=0;
			$month++;
			if($month==13)
			{
				$month="1";
				$year++;
			}
		}

	}
	return $lastdatep-1;
}

//translate number of month to name of month
function monthname($month)
{
	$month_map = array(1 => _JDF_Far, 2 => _JDF_Ord, 3 => _JDF_Kho, 4 => _JDF_Tir
	, 5 => _JDF_Mor, 6 => _JDF_Sha, 7 => _JDF_Meh, 8 => _JDF_Aba, 9 => _JDF_Aza
	, 10 => _JDF_Dey, 11 => _JDF_Bah, 12 => _JDF_Esf);
	return $month_map[(int) $month];
}


/* Wordpress Convert Functions */

function jmysql2date($dateformatstring, $mysqlstring, $translate = true) {
	global $month, $weekday, $month_abbrev, $weekday_abbrev;
	$m = $mysqlstring;
	if ( empty($m) ) {
		return false;
	}
	$i = mktime(substr($m,11,2),substr($m,14,2),substr($m,17,2),substr($m,5,2),substr($m,8,2),substr($m,0,4));
	
	if ( -1 == $i || false == $i )
		$i = 0;

	$j = @jdate($dateformatstring, $i);
	return $j;
}

function mps_maketimestamp($m) {
	return mktime(substr($m,11,2),substr($m,14,2),substr($m,17,2),substr($m,5,2),substr($m,8,2),substr($m,0,4));
}


function comment_jdate($d='') {
	global $comment;
	$m = $comment->comment_date;
	$timestamp = mps_maketimestamp($m);
	if ('' == $d) {
		echo jdate(get_settings('date_format'), $timestamp);
	} else {
		echo jdate($d, $timestamp);
	}

}

function comment_jtime($d='') {
	global $comment;
	$m = $comment->comment_date;
	$timestamp = mps_maketimestamp($m);
	if ($d == '') {
		echo jdate(get_settings('time_format'), $timestamp);
	} else {
		echo jdate($d, $timestamp);
	}
}

function mps_the_jdate($input,$d='',$before='', $after='') {
	global $id, $post, $day, $previousday, $newday;
	$result = '';
	if ($d == "") $d = get_settings('time_format');
	//if ($day != $previousday) {
	if (strlen($input) > 0) { //Because $previousday is overwritten before reaching here , nice trick ;)
		$m = $post->post_date;
		$timestamp = mps_maketimestamp($m);
		$result .= $before;
		$result .= jdate($d,$timestamp);
		$result .= $after;
		$previousday = $day;
	}
	return $result;
}

function mps_the_jtime($input,$d='') {
	global $id, $post;
	if (!empty($input)){
		if ($d == "") $d = get_settings('time_format');
		$m = $post->post_date;
		$timestamp = mps_maketimestamp($m);
		$the_time = jdate($d, $timestamp);
		return $the_time;
	}

}

function mps_comment_jdate($input, $d = '') {
	global $comment;
	$result = '';
	$m = $comment->comment_date;
	$timestamp = mps_maketimestamp($m);
	if ( '' == $d )
		$result = jdate(get_settings('date_format'), $timestamp);
	else
		$result = jdate($d, $timestamp);
	return $result;
}

function mps_comment_jtime($input, $d = '') {
	global $comment;
	$result = '';
	$m = $comment->comment_date;
	$timestamp = mps_maketimestamp($m);
	if ( '' == $d )
		$result = jdate(get_settings('time_format'), $timestamp);
	else
		$result = jdate($d, $timestamp);
	return  $result;
}

function mps_the_jweekday($input) {
	global $weekday, $id, $post;
	$m = $post->post_date;
	$timestamp = mps_maketimestamp($m);
	$the_weekday = jdate('w', $timestamp);
	return $the_weekday;
}


function mps_the_jweekday_date($input, $before, $after) {
	global $weekday, $id, $post, $day, $previousweekday;
	$the_weekday_date = '';
	$m = $post->post_date;
	$timestamp = mps_maketimestamp($m);
	if (strlen($input) > 0) {
		$the_weekday_date .= $before;
		$the_weekday_date .= jdate('w', $timestamp);
		$the_weekday_date .= $after;
	}
	return  $the_weekday_date;
}


function mps_jalali_query($where) {

	/* Wordpress 1.6+ */
	global $wp_query;
	global $j_days_in_month;

	$m = $wp_query->query_vars['m'];
	$hour = $wp_query->query_vars['hour'];
	$minute = $wp_query->query_vars['minute'];
	$second = $wp_query->query_vars['second'];
	$year = $wp_query->query_vars['year'];
	$monthnum = $wp_query->query_vars['monthnum'];
	$day = $wp_query->query_vars['day'];
	
	$j_monthnum = 1;
	$j_day = 1;
	$j_hour = 0;
	$j_minute = 0;
	$j_second = 0;
	$j_monthnum_next = 1;
	$j_day_next = 1;
	$j_hour_next = 0;
	$j_minute_next = 0;
	$j_second_next = 0;
	$j_doit = false;

	if ($m != '') {
		$m = '' . preg_replace('|[^0-9]|', '', $m);
		$j_year = substr($m,0,4);
		if ($j_year < 1700) { // Wow ! It's a Jalali Date!
		$j_doit = true;
		$j_year_next = $j_year + 1;

		if (strlen($m)>5) {
			$j_monthnum = substr($m, 4, 2);
			$j_year_next = $j_year;
			$j_monthnum_next = $j_monthnum + 1;
		}

		if (strlen($m)>7) {
			$j_day = substr($m, 6, 2);
			$j_monthnum_next = $j_monthnum;
			$j_day_next = $j_day + 1;
		}
		if (strlen($m)>9) {
			$j_hour = substr($m, 8, 2);
			$j_day_next = $j_day;
			$j_hour_next = $j_hour + 1;
		}
		if (strlen($m)>11) {
			$j_minute = substr($m, 10, 2);
			$j_hour_next = $j_hour;
			$j_minute_next = $j_minute + 1;
		}
		if (strlen($m)>13) {
			$j_second = substr($m, 12, 2);
			$j_minute_next = $j_minute;
			$j_second_next = $j_second + 1;
		}
		}
	} else if (($year != '') && ($year < 1700)) {
		$j_doit = true;
		$j_year = $year;
		$j_year_next = $j_year + 1;

		if ($monthnum != '') {
			$j_monthnum = $monthnum;
			$j_year_next = $j_year;
			$j_monthnum_next = $j_monthnum + 1;
		}
		if ($day != '') {
			$j_day = $day;
			$j_monthnum_next = $j_monthnum;
			$j_day_next = $j_day + 1;
		}
		if ($hour != '') {
			$j_hour = $hour;
			$j_day_next = $j_day;
			$j_hour_next = $j_hour + 1;
		}
		if ($minute != '') {
			$j_minute = $minute;
			$j_hour_next = $j_hour;
			$j_minute_next = $j_minute + 1;
		}
		if ($second != '') {
			$j_second = $second;
			$j_minute_next = $j_minute;
			$j_second_next = $j_second + 1;
		}
	}
	
	if ($j_doit) {
		/* WP 1.5+ NEEDS THIS :: CLEANING PREV. TIMINGS*/
		$patterns =  array("YEAR\(post_date\)='*[0-9]{4}'*","DAYOFMONTH\(post_date\)='*[0-9]{1,}'*"
		,"MONTH\(post_date\)='*[0-9]{1,}'*","HOUR\(post_date\)='*[0-9]{1,}'*",
		"MINUTE\(post_date\)='*[0-9]{1,}'*","SECOND\(post_date\)='*[0-9]{1,}'*");
		foreach ($patterns as $pattern){
			$where = ereg_replace($pattern,"1=1",$where); // :D good idea ! isn't it ?
		}
		if ($j_second_next > 59) {
			$j_second_next = 0;
			$j_minute_next++;
		}
		if ($j_minute_next > 59) {
			$j_minute_next = 0;
			$j_hour_next++;
		}
		if ($j_hour_next > 23) {
			$j_hour_next = 0;
			$j_day_next++;
		}
		if ($j_day_next > $j_days_in_month[$j_monthnum-1]){
			$j_day_next = 1;
			$j_monthnum_next++;
		}
		if ($j_monthnum_next > 12) {
			$j_monthnum_next = 1;
			$j_year_next++;
		}

		$g_startdate = date("Y:m:d 00:00:00",jmaketime($j_hour,$j_minute,$j_second,$j_monthnum,$j_day,$j_year));
		$g_enddate = date("Y:m:d 00:00:00",jmaketime($j_hour_next,$j_minute_next,$j_second_next,$j_monthnum_next,$j_day_next,$j_year_next));
		
		$where .= " AND post_date >= '$g_startdate' AND post_date < '$g_enddate' ";
	}
	return  $where;
}

function mps_get_jarchives($type='', $limit='', $format='html', $before = '', $after = '', $show_post_count = false) {
	//Added in 3.5 for backward compability
	$_wp_version = get_bloginfo("version");
	if ($_wp_version >= 2.1) {
		$_query_add = " post_type='post' ";
	} else {
		$_query_add = " 1 = 1 "; // =)) 11-5-2007 0:38
	}
	global $month, $wpdb;
	global $j_month_name;

	if ('' == $type) {
		$type = 'monthly';
	}

	if ('' != $limit) {
		$limit = (int) $limit;
		$limit = ' LIMIT '.$limit;
	}
	// this is what will separate dates on weekly archive links
	$archive_week_separator = '&#8211;';

	// archive link url
	$archive_link_m = get_settings('siteurl') . '/?m=';    # monthly archive;
	$archive_link_p = get_settings('siteurl') . '/?p=';    # post-by-post archive;

	// over-ride general date format ? 0 = no: use the date format set in Options, 1 = yes: over-ride
	$archive_date_format_over_ride = 0;

	// options for daily archive (only if you over-ride the general date format)
	$archive_day_date_format = 'Y/m/d';

	// options for weekly archive (only if you over-ride the general date format)
	$archive_week_start_date_format = 'Y/m/d';
	$archive_week_end_date_format   = 'Y/m/d';

	if (!$archive_date_format_over_ride) {
		$archive_day_date_format = get_settings('date_format');
		$archive_week_start_date_format = get_settings('date_format');
		$archive_week_end_date_format = get_settings('date_format');
	}

	$add_hours = intval(get_settings('gmt_offset'));
	$add_minutes = intval(60 * (get_settings('gmt_offset') - $add_hours));

	$now = current_time('mysql');

	if ("monthly" == $type) {
		$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) as 'day',count(ID) as 'posts' FROM $wpdb->posts WHERE ".$_query_add." AND post_date < '$now' AND post_status = 'publish' GROUP BY YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) ORDER BY post_date DESC " . $limit);
		if ($arcresults) {
			$afterafter = $after;
			$len = count($arcresults)-1;
		
			list($jal_startyear,$jal_startmonth,$jal_startday) = gregorian_to_jalali($arcresults[$len]->year,$arcresults[$len]->month,$arcresults[$len]->day);
			list($jal_endyear,$jal_endmonth,$jal_endday) = gregorian_to_jalali($arcresults[0]->year,$arcresults[0]->month,$arcresults[0]->day);
			//$jal_year = $jal_startyear;
			//$jal_month = $jal_startmonth;
			$jal_year = $jal_endyear;
			$jal_month = $jal_endmonth + 1;
			if ($jal_month > 12) {
				$jal_month = 1;
				$jal_year++;
			}
			while (jmaketime(0,0,0,$jal_month,1,$jal_year) >= jmaketime(0,0,0,$jal_startmonth,1,$jal_startyear)){
				$jal_nextmonth = $jal_month-1;
				$jal_nextyear = $jal_year;
				if ($jal_nextmonth < 1) {
					$jal_nextmonth = 12;
					$jal_nextyear--;
				}
				$gre_end = date("Y:m:d H:i:s",jmaketime(0,0,0,$jal_month,1,$jal_year));
				$gre_start = date("Y:m:d H:i:s",jmaketime(0,0,0,$jal_nextmonth,1,$jal_nextyear));
				
				$jal_post_count = $wpdb->get_results("SELECT COUNT(id) as 'post_count' FROM $wpdb->posts WHERE ".$_query_add." AND post_date < '$now' AND post_status = 'publish' AND post_date >= '$gre_start' AND post_date < '$gre_end'");
				$jal_posts = $jal_post_count[0]->post_count;
				if ($jal_posts > 0){
					
					$url  = get_month_link($jal_nextyear, $jal_nextmonth);
					
					$mps_jd_optionsDB = get_option(MPS_JD_OPTIONS_NAME);
					$mps_jd_farsinum_date = $mps_jd_optionsDB['mps_jd_farsinum_date'];
					
					if ($mps_jd_farsinum_date) {
						$jal_year_text = farsi_num($jal_nextyear);
						$jal_posts = farsi_num($jal_posts);
					} else {
						$jal_year_text = $jal_nextyear;
					}
						
					if ($show_post_count) {
						$text = sprintf('%s %s', $j_month_name[$jal_nextmonth], $jal_year_text);
						$after = '&nbsp;('.$jal_posts.')' . $afterafter;
					} else {
						$text = sprintf('%s %s', $j_month_name[$jal_nextmonth], $jal_year_text);
					}
					echo get_archives_link($url, $text, $format, $before, $after);
				}
				$jal_month = $jal_nextmonth;
				$jal_year = $jal_nextyear;
			}
		}
	} else if ("daily" == $type) {
		$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth` FROM $wpdb->posts WHERE ".$_query_add." AND post_date < '$now' AND post_status = 'publish' ORDER BY post_date DESC" . $limit);
		if ($arcresults) {
			foreach ($arcresults as $arcresult) {
				list($jal_year,$jal_month,$jal_dayofmonth) = gregorian_to_jalali($arcresult->year,$arcresult->month,$arcresult->dayofmonth);
				$url  = get_day_link($jal_year, $jal_month, $jal_dayofmonth);
				//$date = sprintf("%d-%02d-%02d 00:00:00", $jal_year, $jal_month, $jal_dayofmonth);
				//$text = mysql2date($archive_day_date_format, $date);
				$date = jmaketime(0,0,0,$jal_month,$jal_dayofmonth,$jal_year);
				$text = jdate($archive_day_date_format,$date);
				echo get_archives_link($url, $text, $format, $before, $after);
			}
		}
	} elseif ('postbypost' == $type) {
		$arcresults = $wpdb->get_results("SELECT ID, post_date, post_title FROM $wpdb->posts WHERE ".$_query_add." AND post_date < '$now' AND post_status = 'publish' ORDER BY post_date DESC" . $limit);
		if ($arcresults) {
			foreach ($arcresults as $arcresult) {
				if ($arcresult->post_date != '0000-00-00 00:00:00') {
					$url  = get_permalink($arcresult->ID);
					$arc_title = $arcresult->post_title;
					if ($arc_title) {
						$text = strip_tags($arc_title);
					} else {
						$text = $arcresult->ID;
					}
					echo get_archives_link($url, $text, $format, $before, $after);
				}
			}
		}
	}
}

function wp_get_jarchives($args = '') {
	parse_str($args, $r);
	if (!isset($r['type'])) $r['type'] = '';
	if (!isset($r['limit'])) $r['limit'] = '';
	if (!isset($r['format'])) $r['format'] = 'html';
	if (!isset($r['before'])) $r['before'] = '';
	if (!isset($r['after'])) $r['after'] = '';
	if (!isset($r['show_post_count'])) $r['show_post_count'] = false;
	mps_get_jarchives($r['type'], $r['limit'], $r['format'], $r['before'], $r['after'], $r['show_post_count']);
}


function get_jcalendar() {
	global $wpdb, $m, $monthnum, $year, $timedifference, $month, $day,  $posts;
	global $j_month_name , $j_day_name , $jday_abbrev;

	if (!$posts) {
		$gotsome = $wpdb->get_var("SELECT ID from $wpdb->posts WHERE post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
		if (!$gotsome)
		return;
	}

	$week_begins = intval(get_settings('start_of_week'));
	$add_hours = intval(get_settings('gmt_offset'));
	$add_minutes = intval(60 * (get_settings('gmt_offset') - $add_hours));

	$input_is_gregorian = false;

	if (!empty($monthnum) && !empty($year)) {
		$thismonth = ''.zeroise(intval($monthnum), 2);
		$thisyear = ''.intval($year);
	}  elseif (!empty($m)) {
		$calendar = substr($m, 0, 6);
		$thisyear = ''.intval(substr($m, 0, 4));
		if (strlen($m) < 6) {
			$thismonth = '01';
		} else {
			$thismonth = ''.zeroise(intval(substr($m, 4, 2)), 2);
		}
	} else {
		$input_is_gregorian = true;
		$thisyear = gmdate('Y', current_time('timestamp') + get_settings('gmt_offset') * 3600);
		$thismonth = gmdate('m', current_time('timestamp') + get_settings('gmt_offset') * 3600);
		$thisday = gmdate('d', current_time('timestamp') + get_settings('gmt_offset') * 3600);
	}

	if ($input_is_gregorian) {
		list($jthisyear,$jthismonth,$jthisday) = gregorian_to_jalali($thisyear,$thismonth,$thisday);
		$unixmonth = jmaketime(0, 0 , 0, $jthismonth, 1, $jthisyear);
	} else {
		$unixmonth = jmaketime(0, 0 , 0, $thismonth, 1, $thisyear);
		$jthisyear = $thisyear;
		$jthismonth = $thismonth;

	}



	$jnextmonth = $jthismonth + 1;
	$jnextyear = $jthisyear;
	if ($jnextmonth > 12) {
		$jnextmonth = 1;
		$jnextyear++;
	}
	//This is so important to change the table dir to RTL and keep it
	echo '<table id="wp-calendar" style="direction: rtl">
    <caption>' . $j_month_name[(int) $jthismonth ] . ' ' . jdate('Y', $unixmonth) . '</caption>
    <thead>
    <tr>';

	$day_abbrev = $weekday_initial;

	$myweek = array();

	for ($wdcount=0; $wdcount<=6; $wdcount++) {
		$myweek[]=$jday_abbrev[($wdcount+$week_begins)%7];
	}

	foreach ($myweek as $wd) {
		echo "\n\t\t<th abbr=\"$wd\" scope=\"col\" title=\"$wd\">" . $wd . '</th>';
	}

	echo '
    </tr>
    </thead>
	
    <tfoot>
    <tr>';
	$g_startdate = date("Y:m:d H:i:s",jmaketime(0,0,0,$jthismonth,1,$jthisyear));
	$g_enddate = date("Y:m:d H:i:s",jmaketime(0,0,0,$jnextmonth,1,$jnextyear));
	$prev = $wpdb->get_results("SELECT count(id) AS prev FROM $wpdb->posts
    								WHERE post_date < '$g_startdate'
    								AND post_status = 'publish'
    								AND post_date < '" . current_time('mysql') . '\'', ARRAY_N);

	$next = $wpdb->get_results("SELECT count(id) AS next FROM $wpdb->posts
    								WHERE post_date > '$g_enddate'
    								AND post_status = 'publish'
    								AND post_date < '" . current_time('mysql') . '\'', ARRAY_N);
	if ($prev[0][0] != 0) $is_prev = true; else $is_prev = false;
	if ($next[0][0] != 0) $is_next = true; else $is_next = false;

	if ($is_prev) {
		$previous_month = $jthismonth - 1;
		$previous_year = $jthisyear;
		if ($previous_month == 0) {
			$previous_month = 12;
			$previous_year --;
		}
	}
	if ($is_next) {
		$next_month = $jthismonth + 1;
		$next_year = $jthisyear;
		if ($next_month == 13) {
			$next_month = 1;
			$next_year ++;
		}
	}

	if ($is_prev) {
		echo "\n\t\t".'<td abbr="' . $j_month_name[previous_month] . '" colspan="3" id="prev"><a href="' .
		get_month_link($previous_year, $previous_month) . '" title="' . sprintf(__('View posts for %1$s %2$s'), $j_month_name[$previous_month], jdate('Y', jmaketime(0, 0 , 0, $previous_month, 1, $previous_year))) . '">&laquo; ' . $j_month_name[$previous_month] . '</a></td>';
	} else {
		echo "\n\t\t".'<td colspan="3" id="prev" class="pad">&nbsp;</td>';
	}


	echo "\n\t\t".'<td class="pad">&nbsp;</td>';

	if ($is_next) {
		echo "\n\t\t".'<td abbr="' . $j_month_name[$next_month] . '" colspan="3" id="next"><a href="' .
		get_month_link($next_year, $next_month) . '" title="View posts for ' . $j_month_name[$next_month] . ' ' .
		jdate('Y', jmaketime(0, 0 , 0, $next_month, 1, $next_year)) . '">' . $j_month_name[$next_month] . ' &raquo;</a></td>';
	} else {
		echo "\n\t\t".'<td colspan="3" id="next" class="pad">&nbsp;</td>';
	}

	echo '
    </tr>
    </tfoot>

    <tbody>
    <tr>';

	$dayswithposts = $wpdb->get_results("SELECT DISTINCT DAYOFMONTH(post_date),MONTH(post_date),YEAR(post_date)
            FROM $wpdb->posts WHERE 1=1
            AND post_date > '$g_startdate' AND post_date < '$g_enddate'
            AND post_status = 'publish'
            AND post_date < '" . current_time('mysql') . '\'', ARRAY_N);
	if ($dayswithposts) {
		foreach ($dayswithposts as $daywith) {
			//$daywithpost[] = $daywith[0];
			$daywithpost[] = jdate("j",mktime(0,0,0,$daywith[1],$daywith[0],$daywith[2]),true);
		}
	} else {
		$daywithpost = array();
	}

	if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE') ||
	strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'camino') ||
	strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'safari')) {
		$ak_title_separator = "\n";
	} else {
		$ak_title_separator = ', ';
	}

	$ak_titles_for_day = array();
	$ak_post_titles = $wpdb->get_results("SELECT post_title, DAYOFMONTH(post_date) as dom, MONTH(post_date) as month, YEAR(post_date) as year "
	."FROM $wpdb->posts "
	."WHERE post_date > '$g_startdate' AND post_date < '$g_enddate' "
	."AND 1=1 "
	."AND post_date < '".current_time('mysql')."' "
	."AND post_status = 'publish'"
	);



	if ($ak_post_titles) {
		$i = 0;
		while ($ak_post_titles[$i]){
			$ak_post_titles[$i] -> dom = jdate("j",mktime(0,0,0,$ak_post_titles[$i] ->month,$ak_post_titles[$i] ->dom,$ak_post_titles[$i] ->year),true);
			$i++;
		}
		foreach ($ak_post_titles as $ak_post_title) {
			if (empty($ak_titles_for_day['day_'.$ak_post_title->dom])) {
				$ak_titles_for_day['day_'.$ak_post_title->dom] = '';
			}
			if (empty($ak_titles_for_day["$ak_post_title->dom"])) { // first one
			$ak_titles_for_day["$ak_post_title->dom"] = str_replace('"', '&quot;', wptexturize($ak_post_title->post_title));
			} else {
				$ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . str_replace('"', '&quot;', wptexturize($ak_post_title->post_title));
			}
		}
	}

	// See how much we should pad in the beginning
	$pad = calendar_week_mod(jdate('w', $unixmonth,true)-$week_begins);
	if (0 != $pad) echo "\n\t\t".'<td colspan="'.$pad.'" class="pad">&nbsp;</td>';

	$daysinmonth = intval(jdate('t', $unixmonth,true));
	for ($day = 1; $day <= $daysinmonth; ++$day) {
		list($thiyear,$thismonth,$thisday) = jalali_to_gregorian($jthisyear,$jthismonth,$day);
		if (isset($newrow) && $newrow)
		echo "\n\t</tr>\n\t<tr>\n\t\t";
		$newrow = false;
		if ($thisday == gmdate('j', (time() + (get_settings('gmt_offset') * 3600))) && $thismonth == gmdate('m', time()+(get_settings('gmt_offset') * 3600)) && $thisyear == gmdate('Y', time()+(get_settings('gmt_offset') * 3600)))
		echo '<td id="today">';
		else
		echo '<td>';
		
		$mps_jd_optionsDB = get_option(MPS_JD_OPTIONS_NAME);
		$mps_jd_farsinum_date = $mps_jd_optionsDB['mps_jd_farsinum_date'];
					
		if ($mps_jd_farsinum_date)
			$day_text = farsi_num($day);
		else
			$day_text = $day;
				
		if (in_array($day, $daywithpost)) { // any posts today?
		echo '<a href="' . get_day_link($jthisyear, $jthismonth, $day) . "\" title=\"$ak_titles_for_day[$day]\">$day_text</a>";
		} else {
			echo $day_text;
		}
		echo '</td>';

		if (6 == calendar_week_mod(date('w', jmaketime(0, 0 , 0, $jthismonth, $day, $jthisyear))-$week_begins))
		$newrow = true;
	}

	$pad = 7 - calendar_week_mod(date('w', jmaketime(0, 0 , 0, $jthismonth, $day, $jthisyear))-$week_begins);
	if ($pad != 0 && $pad != 7)
	echo "\n\t\t".'<td class="pad" colspan="'.$pad.'">&nbsp;</td>';

	echo "\n\t</tr>\n\t</tbody>\n\t</table>";

}

function mps_calendar() {
	global $m,$year;

	if ($m != '') {
		$m = '' . preg_replace('|[^0-9]|', '', $m);
		$i_year = substr($m,0,4);
	} else if ($year != '') {
		$i_year = $year;
	}

	if ($i_year > 1700) get_calendar(); else get_jcalendar();

}

function _get_permalink($id = 0) {
	$rewritecode = array(
		'%year%',
		'%monthnum%',
		'%day%',
		'%hour%',
		'%minute%',
		'%second%',
		'%postname%',
		'%post_id%',
		'%category%',
		'%author%',
		'%pagename%'
	);

	$post = &get_post($id);
	if ( $post->post_status == 'static' )
		return get_page_link($post->ID);
	elseif ($post->post_status == 'object')
		return get_subpost_link($post->ID);

	$permalink = get_settings('permalink_structure');

	if ( '' != $permalink && 'draft' != $post->post_status ) {
		$unixtime = strtotime($post->post_date);

		$category = '';
		if ( strstr($permalink, '%category%') ) {
			$cats = get_the_category($post->ID);
			$category = $cats[0]->category_nicename;
			if ( $parent=$cats[0]->category_parent )
				$category = get_category_parents($parent, FALSE, '/', TRUE) . $category;
		}

		$authordata = get_userdata($post->post_author);
		$author = $authordata->user_nicename;
		$rewritereplace = 
		array(
			date('Y', $unixtime),
			date('m', $unixtime),
			date('d', $unixtime),
			date('H', $unixtime),
			date('i', $unixtime),
			date('s', $unixtime),
			$post->post_name,
			$post->ID,
			$category,
			$author,
			$post->post_name,
		);
		return apply_filters('post_link', get_settings('home') . str_replace($rewritecode, $rewritereplace, $permalink), $post);
	} else { // if they're not using the fancy permalink option
		$permalink = get_settings('home') . '/?p=' . $post->ID;
		return apply_filters('post_link', $permalink, $post);
	}
}

function get_jpermalink($old_perma,$post) {
	global $wpdb;

	$rewritecode = array(
	'%year%',
	'%monthnum%',
	'%day%',
	'%hour%',
	'%minute%',
	'%second%',
	'%postname%',
	'%post_id%',
	'%category%',
	'%author%',
	'%pagename%'
	);

	if ( $post->post_status == 'static' )
		return get_page_link($post->ID);
	elseif ($post->post_status == 'object')
		return get_subpost_link($post->ID);
	
	
	$permalink = get_settings('permalink_structure');

	if ( '' != $permalink && 'draft' != $post->post_status ) {
		$unixtime = strtotime($post->post_date);

		$category = '';
		if ( strstr($permalink, '%category%') ) {
			$cats = get_the_category($post->ID);
			$category = $cats[0]->category_nicename;
			if ( $parent=$cats[0]->category_parent )
				$category = get_category_parents($parent, FALSE, '/', TRUE) . $category;
		}

		$authordata = get_userdata($post->post_author);
		$author = $authordata->user_nicename;
		$rewritereplace = 
		array(
			jdate('Y', $unixtime,true),
			jdate('m', $unixtime,true),
			jdate('d', $unixtime,true),
			jdate('H', $unixtime,true),
			jdate('i', $unixtime,true),
			jdate('s', $unixtime,true),
			$post->post_name,
			$post->ID,
			$category,
			$author,
			$post->post_name,
		);
		return get_settings('home') . str_replace($rewritecode, $rewritereplace, $permalink);
	} else { // if they're not using the fancy permalink option
		$permalink = get_settings('home') . '/?p=' . $post->ID;
	return  $permalink;
	}
}

function mps_comments_number($input){
	$input = farsi_num($input);
	return $input;
}

function mps_fixmonthnames() {
	global $month;
	$month['01'] = "فروردین";
	$month['02'] = "اردیبهشت";
	$month['03'] = "خرداد";
	$month['04'] = "تیر";	
	$month['05'] = "مرداد";
	$month['06'] = "شهریور";	
	$month['07'] = "مهر";
	$month['08'] = "آبان";
	$month['09'] = "آذر";
	$month['10'] = "دی";
	$month['11'] = "بهمن";
	$month['12'] = "اسفند";

}

function mps_fixmonthnames_restore() {
	global $month;
	$month['01'] = __('January');
	$month['02'] = __('February');
	$month['03'] = __('March');
	$month['04'] = __('April');
	$month['05'] = __('May');
	$month['06'] = __('June');
	$month['07'] = __('July');
	$month['08'] = __('August');
	$month['09'] = __('September');
	$month['10'] = __('October');
	$month['11'] = __('November');
	$month['12'] = __('December');
}

function mps_fixtitle($title, $sep=null){
	global $month,$j_month_name;
	global $wp_query;
	
	$m = $wp_query->query_vars['m'];
	
	if ($m != '') {
		$m = '' . preg_replace('|[^0-9]|', '', $m);
		$year = substr($m,0,4);
	} else { 
		$year = $wp_query->query_vars['year'];
	}
	
	if ((isset($year)) && ($year < 1700) && (is_archive())) {
		unset($j_month_name[0]);
		$g_months_name = array();
		foreach ($month as $key=>$val){
			$g_month_name[(int) $key] = $val;
		}
	
		$title = str_replace($g_month_name, $j_month_name, $title);
	}
		
	return $title;
}

function mps_fixMCEdir(){
	echo "directionality : \"rtl\" ,";
}

function mps_mce_pretext($input=null){
	$_nbsp = "&nbsp;";
	$_p = "<p dir=\"rtl\">".$_nbsp."</p>";
	return (strlen($input)==0?$_p:$input);
}

function mps_mce_plugins($input){
	$input[] = "directionality";
	return $input;
}

function mps_mce_buttons($input){
	$new_buttons = array();
	if (!in_array("rtl",$input)) {
		$new_buttons = array("separator","ltr","rtl");
	}
	return array_merge($input,$new_buttons);
}

function widget_mps_calendar_init() {
	if ( !function_exists('mps_calendar') )
		return;

	if ( !function_exists('register_sidebar_widget') )
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
	
	register_sidebar_widget('Jalali Calendar','mps_calendar_widget');
	register_widget_control('Jalali Calendar', 'widget_mps_calendar_control', 250, 100);
}

function widget_jarchive_init() {
	if ( !function_exists('wp_get_jarchives') )
		return;

	if ( !function_exists('register_sidebar_widget') )
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
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo '<ul>';
		wp_get_jarchives("type=$type"."&show_post_count=".$show_post_count);
		echo '</ul>';
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
			update_option('jarchive_widget', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$type = htmlspecialchars($options['type'], ENT_QUOTES);
		
		if (empty($options['type']))
			$options['type'] = 'monthly';
		?>
		<div dir="rtl" align="justify">
		<p style="text-align:right"><label for="jarchive_title">عنوان: <input style="width: 200px;" id="jarchive_title" name="jarchive_title" type="text" value="<?php echo $title; ?>" /></label></p>
		<input name="jarchive_type" type="radio" value="monthly" id="monthly" <?=$options['type']=='monthly' ? 'checked=\"checked\"':'' ?> /> <label for="monthly">ماهیانه</label><br />
		<input name="jarchive_type" type="radio" value="daily" id="daily" <?=$options['type']=='daily' ? 'checked=\"checked\"':'' ?> /> <label for="daily">روزانه</label><br />
		<input name="jarchive_type" type="radio" value="postbypost" id="postbypost" <?=$options['type']=='postbypost' ? 'checked=\"checked\"':'' ?> /> <label for="postbypost">نوشته به نوشته</label><br /><br />
		<input name="jarchive_show_post_count" type="checkbox" value="1" id="show_post_count" <?=$options['show_post_count']=='1' ? 'checked=\"checked\"':'' ?> /> <label for="show_post_count">نمایش تعداد نوشته ها (فقط برای بایگانی ماهیانه)</label>
		<input type="hidden" id="jarchive_submit" name="jarchive_submit" value="1" />
		</div>
		<?php
	}
	
	register_sidebar_widget('Jalali Archive','jarchive_widget');
	register_widget_control('Jalali Archive', 'widget_jarchive_control', 300, 150);
}

function mps_farsikeyboard() {
	/* Simple API for adding farsitype.js to themes */
	if (!file_exists(dirname(__FILE__) . '/farsitype.js') ) return;
	$script_uri = get_settings('siteurl').'/wp-content/plugins/WP-Jalali/farsitype.js';
	echo "<script language=\"javascript\" src=\"$script_uri\" type=\"text/javascript\"></script>";

}


$_wp_version = get_bloginfo("version");

add_action('admin_menu', 'mps_jd_menu');

if ($_wp_version < 2) {
	add_action('init', 'mps_fixmonthnames');
	add_action('wp_head', 'mps_fixmonthnames_restore');
} else {
	add_filter('wp_title', 'mps_fixtitle',2);
	
	//$richedit = ( 'true' != get_user_option('rich_editing') ) ? false : true;
	$richedit = true;
	
	if ($richedit) {
		add_filter("mce_plugins","mps_mce_plugins");
		add_filter("mce_buttons","mps_mce_buttons");
	
		$mps_jd_optionsDB = get_option(MPS_JD_OPTIONS_NAME);
		$mps_jd_mcertl = $mps_jd_optionsDB['mps_jd_mcertl'];
		if ((isset($mps_jd_mcertl) && ($mps_jd_mcertl == true))) {
			add_action('mce_options','mps_fixMCEdir');
			//add_filter("richedit_pre","mps_mce_pretext");
			/* The above line commented because of unknown bug of TinyMCE in FireFox */
		}
	}
}

add_filter("posts_where","mps_jalali_query");

$mps_jd_optionsDB = get_option(MPS_JD_OPTIONS_NAME);

$mps_jd_autodate = $mps_jd_optionsDB['mps_jd_autodate'];
$mps_jd_farsinum_content = $mps_jd_optionsDB['mps_jd_farsinum_content'];
$mps_jd_farsinum_comment = $mps_jd_optionsDB['mps_jd_farsinum_comment'];
$mps_jd_farsinum_commentnum = $mps_jd_optionsDB['mps_jd_farsinum_commentnum'];
$mps_jd_farsinum_title = $mps_jd_optionsDB['mps_jd_farsinum_title'];
$mps_jd_farsinum_category = $mps_jd_optionsDB['mps_jd_farsinum_category'];
$mps_jd_jperma = $mps_jd_optionsDB['mps_jd_jperma'];

if ($mps_jd_autodate) {
	add_filter("the_date","mps_the_jdate",10,4);
	add_filter("the_time","mps_the_jtime",10,4);
	add_filter("get_comment_date","mps_comment_jdate",10,2); //works only in wp > 1.5.1
	add_filter("get_comment_time","mps_comment_jtime",10,2); //works only in wp > 1.5.1
	add_filter("the_weekday","mps_the_jweekday");
	add_filter("the_weekday_date","mps_the_jweekday_date",10,3);
}

if ($mps_jd_farsinum_content) add_filter("the_content","farsi_num",10);
if ($mps_jd_farsinum_comment) add_filter("comment_text","farsi_num",10);
if ($mps_jd_farsinum_commentnum) add_filter("comments_number","farsi_num",10);
if ($mps_jd_farsinum_title) add_filter("the_title","farsi_num",10,3);	
if ($mps_jd_farsinum_category) add_filter("wp_list_categories","farsi_num",10,1);

if ($mps_jd_jperma) add_filter("post_link","get_jpermalink",10,2);

add_action('widgets_init', 'widget_jarchive_init');
add_action('widgets_init', 'widget_mps_calendar_init');
?>