<?php
/*
Plugin Name: wp-jalali
Plugin URI: http://wp-persian.com/plugins/wp-jalali/
Description: Full Jalali Date and Persian(Farsi) Support Package for wordpress,  Full posts' and comments' dates convertion , Jalali Archive , Magic(Jalali/Gregorian) Calendar and Jalali/Gregorian Compaitables Permalinks, TinyMCE RTL/LTR activation, TinyMCE Persian Improvement, Cross browser Perisan keyboard support, Jalali Archive/Calendar widgets and Persian numbers, Great tool for Persian(Iranian) Users of WordPress, part of <a href="http://wp-persian.com" title="پروژه وردپرس فارسی">Persian Wordpress Project</a>.
Version: 4.5.1
Author: Mani Monajjemi
Author URI: http://www.manionline.org/
*/

/*  Copyright 2005-2013  Wordpress Persian Project  (email : info@wp-persian.com)

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
* 	Wordpress Persian Team members (wp-persian.com)
*	Farsiweb.info for J2G and G2J Converstion Functions
*	Milad Raastian (miladmovie.com) for JDF (jdf.farsiprojects.com) 
*	Nima Shyanfar (phpmystery.com) for  Fast Farsi Number Conversion Method
*	Gonahkar (gonahkar.com) for WP-Jalali widgets plugin (gonahkar.com/archives/2007/02/26/wp-jalali-widgets-plugin/ ), edit jalali timestamp in write/edit panel and [arabic] writing shortcode.
*	Kaveh Ahmadi (ashoob.net/kaveh) for his valuable Farsi Keyboard Script (ashoob.net/farsitype)
*	Ali Sattari(corelist.net) for great support
* 	Ali Farhadi (farhadi.ir) for improving Farsi Number Convertor.
*   Reza Moallemi (moallemi.ir) for Jalali date in tables.
*/

define("MPS_JD_VER","4.1");
define('MPS_JD_OPTIONS_NAME', "mps_jd_options"."_".MPS_JD_VER);	// Name of the Option stored in the DB
define('MPS_JD_DIR', dirname(__FILE__));
define('MPS_JD_URI', get_option('siteurl').'/wp-content/plugins/wp-jalali');

require_once(MPS_JD_DIR.'/inc/jalali-core.php');
require_once(MPS_JD_DIR.'/inc/deprecated.php');
require_once(MPS_JD_DIR.'/inc/yk-core.php');
require_once(MPS_JD_DIR.'/inc/farsinum-core.php');
require_once(MPS_JD_DIR.'/inc/dashboard-core.php');
require_once(MPS_JD_DIR.'/inc/widgets-core.php');
require_once(MPS_JD_DIR.'/inc/editjalali-core.php');
require_once(MPS_JD_DIR.'/inc/tables-date.php');
//require_once(MPS_JD_DIR.'/inc/tinymce-button.php');

/* Menu Init */

function mps_jd_menu(){
	/* 
		mps_jd_farsinum_XXX :: Toggle Persian numbers in dates
		mps_jd_mcertl :: Toggle TinyMCE Editor RTL / LTR
		mps_jd_jperma :: Toggle Jalali Permalinks on/off (For posts)
		
	*/
	if(function_exists('add_options_page')) {
		//add_options_page("تنظیمات وردپرس فارسی", "وردپرس فارسی", 10, __FILE__,'mps_jd_optionpage');
		add_menu_page("تنظیمات وردپرس فارسی", "وردپرس فارسی", 10, 'wp-jalali','mps_jd_optionpage', plugins_url('wp-jalali/images/logo.png'));
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
		$mps_jd_optionsDB['mps_jd_decimal'] = $mps_jd_decimal = true;
		$mps_jd_optionsDB['mps_jd_mcertl'] = $mps_jd_mcertl = true;
		$mps_jd_optionsDB['mps_jd_jperma'] = $mps_jd_jperma = true;
		$mps_jd_optionsDB['mps_jd_autoyk'] = $mps_jd_autoyk = true;
		$mps_jd_optionsDB['mps_jd_editjalali'] = $mps_jd_editjalali = true;
		$mps_jd_optionsDB['mps_jd_dashboard'] = $mps_jd_dashboard = 0;
		$mps_jd_optionsDB['mps_jd_country'] = $mps_jd_country = 'IR';
		update_option(MPS_JD_OPTIONS_NAME,$mps_jd_optionsDB);	
	}
}

function mps_jd_optionpage(){
	global $user_level;
	global $j_day_name;
	
	$_wp_version = get_bloginfo("version");
	
	if ( version_compare($_wp_version, 2.1, '<' )) {
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
		$mps_jd_optionsDB['mps_jd_decimal'] = $mps_jd_decimal = $_POST['mps_jd_decimal'];
		$mps_jd_optionsDB['mps_jd_mcertl'] = $mps_jd_mcertl = $_POST['mps_jd_mcertl'];
		$mps_jd_optionsDB['mps_jd_jperma'] = $mps_jd_jperma = $_POST['mps_jd_jperma'];
		$mps_jd_optionsDB['mps_jd_autoyk'] = $mps_jd_autoyk = $_POST['mps_jd_autoyk'];
		$mps_jd_optionsDB['mps_jd_editjalali'] = $mps_jd_editjalali = $_POST['mps_jd_editjalali'];
		$mps_jd_optionsDB['mps_jd_country'] = $mps_jd_country = $_POST['mps_jd_country'];
		$old_options = get_option(MPS_JD_OPTIONS_NAME);
		if ($old_options['mps_jd_dashboard'] != $_POST['mps_jd_dashboard']) {
			// Dashboard wigdets updating ... Needs to reset some terms
			if ( $widget_options = get_option( 'dashboard_widget_options' ) ) {
				unset($widget_options['dashboard_primary']);
				unset($widget_options['dashboard_secondary']);
				update_option( 'dashboard_widget_options', $widget_options );
			}
		}
		$mps_jd_optionsDB['mps_jd_dashboard'] = $mps_jd_dashboard = $_POST['mps_jd_dashboard'];
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
	$mps_jd_decimal = $mps_jd_optionsDB['mps_jd_decimal'];
	$mps_jd_mcertl = $mps_jd_optionsDB['mps_jd_mcertl'];
	$mps_jd_jperma = $mps_jd_optionsDB['mps_jd_jperma'];
	$mps_jd_autoyk = $mps_jd_optionsDB['mps_jd_autoyk'];
	$mps_jd_editjalali = $mps_jd_optionsDB['mps_jd_editjalali'];
	$mps_jd_country = $mps_jd_optionsDB['mps_jd_country'];
	$mps_jd_dashboard = $mps_jd_optionsDB['mps_jd_dashboard'];
	
	if((isset($mps_ERR)) && (!empty($mps_ERR))) {
	?>
		<br clear="all" />
		<div style="background-color: rgb(255, 251, 204);" id="message" class="updated fade" style="direction: rtl"><p><strong><?php _e($mps_ERR); ?></strong></p></div>
	<?php
	}
	?>
	
	<?php
	$logo_uri = MPS_JD_URI.'/images/wp-fa-logo.png';
	?>
	<div id="wpbody-content" style="direction:rtl; text-align: right">
	<div class="wrap" style="direction:rtl; text-align: right">
	<p style="text-align:center">
		<a href="http://wp-persian.com" style="border:none" title="وردپرس فارسی"><img src="<?php echo $logo_uri?>" alt="Persian Wordpress Logo" border="0"/></a>
	</p>
	<form method="post">
	<input type="hidden" name="action" value="update" />
	<?php if (version_compare($_wp_version, '2.4', '<')) : ?>
	<h2>اخبار وردپرس فارسی</h2>
	<h3>وبلاگ توسعه وردپرس فارسی</h3>
	
	<?php
		$rss = @fetch_rss('http://wp-persian.com/feed/');
		if ( isset($rss->items) && 0 != count($rss->items) ) {
			?>
				<?php
					$rss->items = array_slice($rss->items, 0, 3);
					foreach ($rss->items as $item ) {
					?>
						<h4 dir="rtl" style="color:gray; margin-right:25px; "><a href='<?php echo wp_filter_kses($item['link']); ?>'><?php echo wp_specialchars($item['title']); ?></a> &#8212; <?php printf(__('%s ago'), human_time_diff(strtotime($item['pubdate'], time() ) ) ); ?></h4>
					<?php
					}
				}
			?>
	<div id="planetnews" style="direction:rtl; text-align: right">		
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
					<li><?php echo wp_specialchars($item['dc']['creator']); ?>: <a href='<?php echo wp_filter_kses($item['link']); ?>'><?php echo wp_specialchars($item['title']); ?></a><?php// printf(__('%s ago'), human_time_diff(strtotime($item['pubdate'], time() ) ) ); ?></li>
					<?php
					}
				?>
			</ul><br style="clear:both;"/>
		<?php
			}
		?>
	</div>
	<?php else: //Wordpress 2.5 Dashboard widget API ?> 
	<h2>اخبار وردپرس فارسی</h2>
	<table class="form-table">
		<tr valign="top"> 
    		<th scope="row">چگونگی نمایش اخبار در پیش‌خوان</th> 
    		<td>
    			<select name="mps_jd_dashboard" id="mps_jd_dashboard">
    				<option value="0" <?php echo $mps_jd_dashboard==0? 'selected="selected"':'' ?>>بر اساس تنظیمات  زبان</option>
    				<option value="1" <?php echo $mps_jd_dashboard==1? 'selected="selected"':'' ?>>نمایش اخبار اصلی وردپرس به زبان انگلیسی</option>
    				<option value="2" <?php echo $mps_jd_dashboard==2?'selected="selected"':'' ?>>نمایش اخبار وردپرس فارسی</option>
    			</select>
    		</td> 
  		</tr>
  	</table>
	<?php endif; ?>
	
	<h2>تنظیمات وردپرس فارسی</h2>
	
	<table class="form-table">
	<tr valign="top"> 
        <th scope="row">تبدیل خودکار تاریخ نوشته‌ها و نظرات به تاریخ خورشیدی(شمسی)</th> 
        <td>
        	<select name="mps_jd_autodate" id="mps_jd_autodate">
        		<option value="1" <?php echo $mps_jd_autodate==true? 'selected="selected"':'' ?>>فعال (پیشنهاد می‌شود)</option>
        		<option value="0" <?php echo $mps_jd_autodate==false?'selected="selected"':'' ?>>غیر فعال</option>
        	</select>
        </td> 
      </tr>
	<tr valign="top"> 
        <th scope="row">تبدیل اعداد به فارسی</th> 
        <td>
			<table border="0" cellpadding="2" cellspacing="2">
				<tr>
					<td style="border-bottom-width: 0">متن نوشته‌ها</td>
					<td style="border-bottom-width: 0"><input type="checkbox" name="mps_jd_farsinum_content" <?php echo $mps_jd_farsinum_content==true? 'checked="checked"':'' ?> /></td>
					<td style="border-bottom-width: 0">متن نظر‌ها</td>
					<td style="border-bottom-width: 0"><input type="checkbox" name="mps_jd_farsinum_comment" <?php echo $mps_jd_farsinum_comment==true? 'checked="checked"':'' ?> /></td>
					<td style="border-bottom-width: 0">تعداد نظر‌ها</td>
					<td style="border-bottom-width: 0"><input type="checkbox" name="mps_jd_farsinum_commentnum" <?php echo $mps_jd_farsinum_commentnum==true? 'checked="checked"':'' ?> /></td>
				</tr>
				<tr>
					<td style="border-bottom-width: 0">عنوان نوشته‌ها</td>
					<td style="border-bottom-width: 0"><input type="checkbox" name="mps_jd_farsinum_title" <?php echo $mps_jd_farsinum_title==true? 'checked="checked"':'' ?> /></td>
					<td style="border-bottom-width: 0">تاریخ‌ها</td>
					<td style="border-bottom-width: 0"><input type="checkbox" name="mps_jd_farsinum_date" <?php echo $mps_jd_farsinum_date==true? 'checked="checked"':'' ?> /></td>
					<td style="border-bottom-width: 0">فهرست دسته‌ها</td>
					<td style="border-bottom-width: 0"><input type="checkbox" name="mps_jd_farsinum_category" <?php echo $mps_jd_farsinum_category==true? 'checked="checked"':'' ?> /></td>
				</tr>
			</table>
        	
        </td>
	</tr>
    <tr>
    	<th scope="row">استفاده از <code>٫</code> به‌جای نقطه به‌عنوان نشانه‌ی اعداد اعشاری</th> 
    	<td>
        	<select name="mps_jd_decimal" id="mps_jd_decimal">
        		<option value="1" <?php echo $mps_jd_decimal==true? 'selected="selected"':'' ?>>بله</option>
        		<option value="0" <?php echo $mps_jd_decimal==false?'selected="selected"':'' ?>>خیر</option>
        	</select>
            <br />
        	<strong>مثال:</strong> استفاده از ۲٫۶ به‌جای ۲<span>.</span>۶
            <br />
            <strong>توضیح:</strong> همان‌طور که می‌دانیم نشانه‌ی اعشار در فارسی (٫) است٬ اما به‌دلیل ناسازگاری برخی مرورگرها با اعداد ممیزدار٬ این گزینه را به‌انتخاب کاربران گذاشتیم.
        </td>
    </tr>
      <tr valign="top"> 
        <th scope="row">جهت ویرایشگر متنی صفحه نوشتن</th> 
        <td>
        	<select name="mps_jd_mcertl" id="mps_jd_mcertl">
        		<option value="1" <?php echo $mps_jd_mcertl==true? 'selected="selected"':'' ?>>راست به چپ</option>
        		<option value="0" <?php echo $mps_jd_mcertl==false?'selected="selected"':'' ?>>چپ به راست</option>
        	</select>
        	<br />
        	در نگارش‌های بالاتر از وردپرس ۲٫۳ در صورتی که زبان وردپرس خود را فارسی انتخاب کنید، جهت ویرایشگر به صورت خودکار راست به چپ خواهد بود. در این نگارش‌ها تنها در صورتی از این گزینه استفاده کنید که زبان وردپرس خود را انگلیسی انتخاب کرده باشید.
        </td> 
      </tr>
	  <tr valign="top"> 
        <th scope="row">تبدیل خودکار تاریخ در نشانی (URI) نوشته‌ها</th> 
        <td>
        	<select name="mps_jd_jperma" id="mps_jd_jperma">
        		<option value="1" <?php echo $mps_jd_jperma==true? 'selected="selected"':'' ?>>بله</option>
        		<option value="0" <?php echo $mps_jd_jperma==false?'selected="selected"':'' ?>>خیر</option>
        	</select>
			<br />
	        تبدیل خودکار تاریخ در نشانی نوشته‌ها، مثلا از yourblog.ir/2008/04/02/post به yourblog.ir/1387/01/13/post
        </td> 
      </tr>
      <tr valign="top"> 
        <th scope="row">تبدیل هوشمند حروف عربی به فارسی</th> 
        <td>
        	<select name="mps_jd_autoyk" id="mps_jd_autoyk">
        		<option value="1" <?php echo $mps_jd_autoyk==true? 'selected="selected"':'' ?>>بله</option>
        		<option value="0" <?php echo $mps_jd_autoyk==false?'selected="selected"':'' ?>>خیر</option>
        	</select>
        	<br />
        	تبدیل خودکار حروف (ي) و (ك) عربی به (ی) و (ک) فارسی در هنگام نمایش و جستجوی هوشمند برای تمامی ترکیب‌های ممکن در هنگام جستجو.
        </td> 
      </tr>
      <tr valign="top"> 
        <th scope="row">ویرایش تاریخ نوشته‌ها و برگه‌ها</th> 
        <td>
        	<select name="mps_jd_editjalali" id="mps_jd_editjalali">
        		<option value="1" <?php echo $mps_jd_editjalali==true? 'selected="selected"':'' ?>>خورشیدی (شمسی)</option>
        		<option value="0" <?php echo $mps_jd_editjalali==false?'selected="selected"':'' ?>>میلادی</option>
        	</select>
        	<br />
        	در نگارش‌های بالاتر از وردپرس ۲/۵ می توانید نحوه ویرایش تاریخ نوشته‌ها و برگه‌ها را تنظیم کنید.
        </td> 
      </tr>
	  <tr>
		<th>نام ماه‌ها مطابق با کشور</th>
		<td>
			<select name="mps_jd_country" id="mps_jd_country">
        		<option value="IR" <?php echo $mps_jd_country == 'IR' ? 'selected="selected"':'' ?>>ایران</option>
        		<option value="AF" <?php echo $mps_jd_country == 'AF' ?'selected="selected"':'' ?>>افغانستان</option>
        	</select>
			<br />
			نام ماه‌های ایران: فروردین٬ اردیبهشت و... / نام ماه‌های افغانستان: حمل٬ ثور و...
		</td>
	  </tr>
      </table>
      <br />
      <h2>تنظیمات ساعت و تاریخ</h2>
      <table class="form-table"> 
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
      	<td>ساختار‌های زیر مانند <a href="http://php.net/date">تابع <code>date()</code> PHP</a> است. برای نمایش تغییرات این صفحه را به روز کنید.</td>
      	</tr>
      <tr>
      	<th scope="row">ساختار تاریخ پیش‌فرض</th>
      	<td><input style="direction:rtl; text-align:left" name="date_format" type="text" id="date_format" size="30" value="<?php form_option('date_format'); ?>" /><br />
خروجی : <strong><?php echo jdate(get_option('date_format'), $gmt + (get_option('gmt_offset') * 3600)); ?></strong></td>
      	</tr>
      <tr>
        <th scope="row">ساختار زمان پیش‌فرض</th>
      	<td><input style="direction:rtl; text-align:left" name="time_format" type="text" id="time_format" size="30" value="<?php form_option('time_format'); ?>" /><br />
خروجی : <strong><?php echo jdate(get_option('time_format'), $gmt + (get_option('gmt_offset') * 3600)) ; ?></strong></td>
      	</tr> 
      <tr>
        <th scope="row">روز شروع هفته در تقویم</th>
        <td><select name="start_of_week" id="start_of_week">
	<?php
for ($day_index = 0; $day_index <= 6; $day_index++) :
	if ($day_index == get_option('start_of_week')) $selected = " selected='selected'";
	else $selected = '';
echo "\n\t<option value='$day_index' $selected>$j_day_name[$day_index]</option>";
endfor;
?>
</select></td>
       		</tr>

</table>
	
	
      
		<p class="submit">
      	<input class="button-primary" type="submit" name="Submit" value="به روز رسانی &raquo;" />
     </p>
	</form>
	
	<br />
</div>
	<div id="wp-bookmarklet" class="wrap" style="direction:rtl; text-align:right">
<h3>پروژه وردپرس فارسی</h3>
	<p>این افزونه، بخشی از <a href="http://wp-persian.com/">پروژه وردپرس فارسی</a> است. برای اطلاعات بیشتر در مورد این  افزونه می‌توانید <a href="http://wp-persian.com/wp-jalali/">صفحه مخصوص این افزونه</a> را مشاهده کنید.</p>
	</div>
	</div>
	
	<?php
	
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
		echo jdate(get_option('date_format'), $timestamp);
	} else {
		echo jdate($d, $timestamp);
	}

}

function comment_jtime($d='') {
	global $comment;
	$m = $comment->comment_date;
	$timestamp = mps_maketimestamp($m);
	if ($d == '') {
		echo jdate(get_option('time_format'), $timestamp);
	} else {
		echo jdate($d, $timestamp);
	}
}

function mps_the_jdate($input,$d='',$before='', $after='') {
	global $id, $post, $day, $previousday, $newday;
	$result = '';
	if ($d == "") $d = get_option('time_format');
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
		if ($d == "") $d = get_option('time_format');
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
		$result = jdate(get_option('date_format'), $timestamp);
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
		$result = jdate(get_option('time_format'), $timestamp);
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
	if (is_admin()) {
		// Skip admin pages
		return $where;
	}
	$_wp_version = get_bloginfo("version");
			
	/* Wordpress 1.6+ */
	global $wp_query, $wpdb;
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
		$tablename_prefix = $wpdb->prefix.'posts.';
		$sna = (strpos($where, $tablename_prefix) === false) ? '' : $tablename_prefix;
		// TODO: Remove above line and improve the regex
		if (version_compare($_wp_version, '3.7', '<')) {
			$patterns =  array(
				"YEAR\(".$sna."post_date\)='*[0-9]{4}'*",
				"DAYOFMONTH\(".$sna."post_date\)='*[0-9]{1,}'*",
				"MONTH\(".$sna."post_date\)='*[0-9]{1,}'*",
				"HOUR\(".$sna."post_date\)='*[0-9]{1,}'*",
				"MINUTE\(".$sna."post_date\)='*[0-9]{1,}'*",
				"SECOND\(".$sna."post_date\)='*[0-9]{1,}'*"
			);
		} else {
			$patterns =  array(
				"YEAR\(\s*post_date\s*\)\s*=\s*'*[0-9]{4}'*",
				"DAYOFMONTH\(\s*post_date\s*\)\s*=\s*'*[0-9]{1,}'*",
				"MONTH\(\s*post_date\s*\)\s*=\s*'*[0-9]{1,}'*",
				"HOUR\(\s*post_date\s*\)\s*=\s*'*[0-9]{1,}'*",
				"MINUTE\(\s*post_date\s*\)\s*=\s*'*[0-9]{1,}'*",
				"SECOND\(\s*post_date\s*\)\s*=\s*'*[0-9]{1,}'*"
			);
		}
		
		foreach ($patterns as $pattern){
			$where = preg_replace('/'.$pattern.'/',"1=1",$where); // :D good idea ! isn't it ?
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
		
		$where .= " AND ".$sna."post_date >= '$g_startdate' AND ".$sna."post_date < '$g_enddate' ";
	}
	return  $where;
}

function mps_get_jarchives($type='', $limit='', $format='html', $before = '', $after = '', $show_post_count = false) {
	//Added in 3.5 for backward compability
	$_wp_version = get_bloginfo("version");
	if (version_compare($_wp_version, 2.1, '>=') ) {
		$_query_add = " post_type='post' ";
	} else {
		$_query_add = " 1 = 1 "; // =)) 11-5-2007 0:38
	}
	global $month, $wpdb;
	global $j_month_name;

	if ('' == $type) {
		$type = 'monthly';
	}
	
	if ('' != $limit && 0 != $limit) {
		$limit = (int) $limit;
		if("daily" == $type || "postbypost" == $type) $limit = ' LIMIT '.$limit;
	}
	
	
	// this is what will separate dates on weekly archive links
	$archive_week_separator = '&#8211;';

	// archive link url
	$archive_link_m = get_option('siteurl') . '/?m=';    # monthly archive;
	$archive_link_p = get_option('siteurl') . '/?p=';    # post-by-post archive;

	// over-ride general date format ? 0 = no: use the date format set in Options, 1 = yes: over-ride
	$archive_date_format_over_ride = 0;

	// options for daily archive (only if you over-ride the general date format)
	$archive_day_date_format = 'Y/m/d';

	// options for weekly archive (only if you over-ride the general date format)
	$archive_week_start_date_format = 'Y/m/d';
	$archive_week_end_date_format   = 'Y/m/d';

	if (!$archive_date_format_over_ride) {
		$archive_day_date_format = get_option('date_format');
		$archive_week_start_date_format = get_option('date_format');
		$archive_week_end_date_format = get_option('date_format');
	}

	$add_hours = intval(get_option('gmt_offset'));
	$add_minutes = intval(60 * (get_option('gmt_offset') - $add_hours));

	if ("yearly" == $type) {
		$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth` FROM $wpdb->posts WHERE post_date < NOW() AND post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC");
		if ($arcresults) {
			//$afterafter = $after;
			$jal_years_array = array();
			foreach ($arcresults as $arcresult) {
				list($jal_year,$jal_month,$jal_dayofmonth) = gregorian_to_jalali($arcresult->year,$arcresult->month,$arcresult->dayofmonth);
				$jal_years_array[] = $jal_year;
			}
			
			$jal_years = array_unique($jal_years_array);
			$i = 1;
			foreach($jal_years as $jal_year) {
				$gre_start = date("Y-m-d H:i:s",jmaketime(0,0,0,1,1,$jal_year));
				$gre_end = date("Y-m-d H:i:s",jmaketime(0,0,0,1,1,$jal_year+1));
				$count_query = $wpdb->get_results("SELECT count(ID) as 'post_count' FROM $wpdb->posts WHERE post_date < NOW() AND post_type = 'post' AND post_status = 'publish' AND post_date >= '$gre_start' AND post_date < '$gre_end' ORDER BY post_date DESC");
				$count_posts = farsi_num($count_query[0]->post_count);
				$url  = get_year_link($jal_year);
				$text = farsi_num($jal_year);
				if ($show_post_count)
						$after = '&nbsp;('.$count_posts.')' . $afterafter;
				echo get_archives_link($url, $text, $format, $before, $after);
				if($i == $limit) break;
				$i++;
			}
		}
	} elseif ("monthly" == $type) {
		$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) as 'day',count(ID) as 'posts' FROM $wpdb->posts WHERE ".$_query_add." AND post_date < NOW() AND post_status = 'publish' GROUP BY YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) ORDER BY post_date DESC ");
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
			$i = 1;
			while (jmaketime(0,0,0,$jal_month,1,$jal_year) >= jmaketime(0,0,0,$jal_startmonth,1,$jal_startyear)){
				$jal_nextmonth = $jal_month-1;
				$jal_nextyear = $jal_year;
				if ($jal_nextmonth < 1) {
					$jal_nextmonth = 12;
					$jal_nextyear--;
				}
				$gre_end = date("Y:m:d H:i:s",jmaketime(0,0,0,$jal_month,1,$jal_year));
				$gre_start = date("Y:m:d H:i:s",jmaketime(0,0,0,$jal_nextmonth,1,$jal_nextyear));
				
				$jal_post_count = $wpdb->get_results("SELECT COUNT(id) as 'post_count' FROM $wpdb->posts WHERE ".$_query_add." AND post_date < NOW() AND post_status = 'publish' AND post_date >= '$gre_start' AND post_date < '$gre_end'");
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
					
					$text = sprintf('%s %s', $j_month_name[$jal_nextmonth], $jal_year_text);	
					if ($show_post_count)
						$after = '&nbsp;('.$jal_posts.')' . $afterafter;
					echo get_archives_link($url, $text, $format, $before, $after);
				}
				$jal_month = $jal_nextmonth;
				$jal_year = $jal_nextyear;
				if($i == $limit) break;
				$i++;
			}
		}
	} elseif ("daily" == $type) {
		$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth` FROM $wpdb->posts WHERE ".$_query_add." AND post_date < NOW() AND post_status = 'publish' ORDER BY post_date DESC" . $limit);
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
		$arcresults = $wpdb->get_results("SELECT ID, post_date, post_title FROM $wpdb->posts WHERE ".$_query_add." AND post_date < NOW() AND post_status = 'publish' ORDER BY post_date DESC" . $limit);
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
	
	$_wp_version = get_bloginfo("version");
	if (version_compare($_wp_version, '2.1', '>=')) {
		$_query_add = " post_type='post' ";
	} else {
		$_query_add = " 1 = 1 "; // =)) 11-5-2007 0:38
	}
	
	if (!$posts) {
		$gotsome = $wpdb->get_var("SELECT ID from $wpdb->posts WHERE ".$_query_add." AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
		if (!$gotsome) return;
	}

	$week_begins = intval(get_option('start_of_week'));
	$add_hours = intval(get_option('gmt_offset'));
	$add_minutes = intval(60 * (get_option('gmt_offset') - $add_hours));

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
		$thisyear = gmdate('Y', current_time('timestamp') + get_option('gmt_offset') * 3600);
		$thismonth = gmdate('m', current_time('timestamp') + get_option('gmt_offset') * 3600);
		$thisday = gmdate('d', current_time('timestamp') + get_option('gmt_offset') * 3600);
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
    								WHERE $_query_add
    								AND post_date < '$g_startdate'
    								AND post_status = 'publish'
    								AND post_date < '" . current_time('mysql') . '\'', ARRAY_N);

	$next = $wpdb->get_results("SELECT count(id) AS next FROM $wpdb->posts
    								WHERE $_query_add
    								AND post_date > '$g_enddate'
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
            FROM $wpdb->posts 
        	WHERE $_query_add 
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
	$ak_post_titles = $wpdb->get_results(
	"SELECT post_title, DAYOFMONTH(post_date) as dom, MONTH(post_date) as month, YEAR(post_date) as year "
	."FROM $wpdb->posts "
	."WHERE ".$_query_add." AND post_date > '$g_startdate' AND post_date < '$g_enddate' "
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
		if ($thisday == gmdate('j', (time() + (get_option('gmt_offset') * 3600))) && $thismonth == gmdate('m', time()+(get_option('gmt_offset') * 3600)) && $thisyear == gmdate('Y', time()+(get_option('gmt_offset') * 3600)))
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

function get_jpermalink($old_perma, $post) {
	/* Detecting $leavename 2.5+ */
	$leavename = ((strpos($old_perma, '%postname%') !== false) || (strpos($old_perma, '%pagename%') !== false));
	
	$_wp_version = get_bloginfo("version");
	$rewritecode = array(
		'%year%',
		'%monthnum%',
		'%day%',
		'%hour%',
		'%minute%',
		'%second%',
		$leavename? '' : '%postname%',
		'%post_id%',
		'%category%',
		'%author%',
		$leavename? '' : '%pagename%',
	);
	
	
	if ( empty($post->ID) ) return FALSE;
	
	if (( $post->post_status == 'static' ) || ( $post->post_type == 'page' ))
		return get_page_link($post->ID);
	elseif (($post->post_status == 'object') || ($post->post_type == 'attachment'))
		return get_subpost_link($post->ID);

	$permalink = get_option('permalink_structure');

	if ( '' != $permalink && !in_array($post->post_status, array('draft', 'pending', 'auto-draft')) ) {
		$unixtime = strtotime($post->post_date);

		$category = '';
		if ( strpos($permalink, '%category%') !== false ) {
			$cats = get_the_category($post->ID);
			if ( $cats )
				usort($cats, '_usort_terms_by_ID'); // order by ID
			$category = (version_compare($_wp_version, '2.4', '<')) ? $cats[0]->category_nicename : $category = $cats[0]->slug;
			if ( $parent=$cats[0]->category_parent )
				$category = get_category_parents($parent, FALSE, '/', TRUE) . $category;
		}
		
		if ( empty($category) ) {
			$default_category = get_category( get_option( 'default_category' ) );
			$category = is_wp_error( $default_category)? '' : $default_category->slug; 
		}

		$author = '';
		if ( strpos($permalink, '%author%') !== false ) {
			$authordata = get_userdata($post->post_author);
			$author = $authordata->user_nicename;
		}
		
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
		$permalink = get_option('home') . str_replace($rewritecode, $rewritereplace, $permalink);
		$permalink = user_trailingslashit($permalink, 'single');
		return $permalink;
	} else { // if they're not using the fancy permalink option
		$permalink = get_option('home') . '/?p=' . $post->ID;
		return $permalink;
	}
}

function mps_comments_number($input){
	$input = farsi_num($input);
	return $input;
}

function mps_fixmonthnames() {
	global $month;
	if($mps_jd_optionsDB['mps_jd_country'] == 'AF') {
		$month['01'] = "حمل";
		$month['02'] = "ثور";
		$month['03'] = "جوزا";
		$month['04'] = "سرطان";	
		$month['05'] = "اسد";
		$month['06'] = "سنبله";	
		$month['07'] = "میزان";
		$month['08'] = "عقرب";
		$month['09'] = "قوس";
		$month['10'] = "جدی";
		$month['11'] = "دلو";
		$month['12'] = "حوت";
	} else {
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


function mps_mce_set_direction( $input ) {
	global $wp_locale;

	// The paradox is logical, because we just do this in the case that Wordpress is in LTR Mode
	if (!( 'rtl' == $wp_locale->text_direction )) {
		$input['directionality'] = 'rtl';
		$input['plugins'] .= ',directionality';
		$input['theme_advanced_buttons1'] .= ',ltr';
	}
	return $input;
}


function mps_farsikeyboard() {
	/* Simple API for adding farsitype.js to themes */
	if (!file_exists(MPS_JD_DIR . '/inc/js/farsitype.js') ) return;
	$script_uri = MPS_JD_URI.'/inc/js/farsitype.js';
	echo "<script language=\"javascript\" src=\"$script_uri\" type=\"text/javascript\"></script>";

}


$_wp_version = get_bloginfo("version");

add_action('admin_menu', 'mps_jd_menu');

if (version_compare($_wp_version, '2', '<')) {
	add_action('init', 'mps_fixmonthnames');
	add_action('wp_head', 'mps_fixmonthnames_restore');
} else {
	//add_filter('wp_title', 'mps_fixtitle',2);
	$mps_jd_optionsDB = get_option(MPS_JD_OPTIONS_NAME);
	$mps_jd_mcertl = $mps_jd_optionsDB['mps_jd_mcertl'];
	
	if (version_compare($_wp_version, '2.4', '<')) { // Older Versions of Wordpress < 2.5
		add_filter("mce_plugins","mps_mce_plugins");
		add_filter("mce_buttons","mps_mce_buttons");
		if ((isset($mps_jd_mcertl) && ($mps_jd_mcertl == true))) {
			add_action('mce_options','mps_fixMCEdir');
		}
	} else { // 2.5 and above
		if ((isset($mps_jd_mcertl) && ($mps_jd_mcertl == true))) { // Using Wordpress API for tinymce RTL
			add_filter('tiny_mce_before_init', 'mps_mce_set_direction');
		}
	}
}

/* Login Form Functions */
function login_url() {
	return 'http://wp-persian.com';
}
function login_text() {
	return 'با نیروی وردپرس فارسی';
}

function login_img() {
	echo '<style>#login h1 a {background: transparent url(wp-content/plugins/wp-jalali/images/wp-fa-logo.png) no-repeat scroll center top}</style>';
}

/* Tags */

/*
function mps_loadjs() {
	//wp_enqueue_script( 'jalalitags', MPS_JD_URI . '/inc/js/tags.js', array('jquery'), '1.1');
}
*/

/* Core changes */
update_option('rss_language', 'fa'); // change rss language to fa for some rss reader like IE7 to understand that the direction is RTL.
@define('WP_MEMORY_LIMIT', '64M'); // Increse memory limit because of translation pressure.

add_filter("posts_where","mps_jalali_query");

$mps_jd_optionsDB = get_option(MPS_JD_OPTIONS_NAME);

$mps_jd_autodate = $mps_jd_optionsDB['mps_jd_autodate'];
$mps_jd_farsinum_content = $mps_jd_optionsDB['mps_jd_farsinum_content'];
$mps_jd_farsinum_comment = $mps_jd_optionsDB['mps_jd_farsinum_comment'];
$mps_jd_farsinum_commentnum = $mps_jd_optionsDB['mps_jd_farsinum_commentnum'];
$mps_jd_farsinum_title = $mps_jd_optionsDB['mps_jd_farsinum_title'];
$mps_jd_farsinum_category = $mps_jd_optionsDB['mps_jd_farsinum_category'];
$mps_jd_jperma = $mps_jd_optionsDB['mps_jd_jperma'];
$mps_jd_autoyk = $mps_jd_optionsDB['mps_jd_autoyk'];
$mps_jd_editjalali = $mps_jd_optionsDB['mps_jd_editjalali'];


if ($mps_jd_autodate) {
	add_filter("the_date","mps_the_jdate",10,4);
	add_filter("the_time","mps_the_jtime",10,4);
	//add_filter("get_the_date","mps_the_jdate",10,4);
	//add_filter("get_the_time","mps_the_jtime",10,4);
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

if ($mps_jd_jperma) add_filter("post_link","get_jpermalink",10,3);

/* Y-K Solve */

if ($mps_jd_autoyk) {
	add_filter( 'posts_request', 'mps_yk_solve_search' );

	add_filter('the_content','mps_yk_solve_persian',10,1);
	add_filter('get_the_content','mps_yk_solve_persian',10,1);
	add_filter('the_excerpt','mps_yk_solve_persian',10,1);
	add_filter('get_the_excerpt','mps_yk_solve_persian',10,1);
	add_filter('link_title','mps_yk_solve_persian',10,1);
	add_filter('wp_title','mps_yk_solve_persian',10,1);
	add_filter('the_title','mps_yk_solve_persian',10,1);
	add_filter('the_title_rss','mps_yk_solve_persian',10,1);
	add_filter('get_the_title','mps_yk_solve_persian',10,1);
	add_filter('get_the_title_rss','mps_yk_solve_persian',10,1);
	add_filter('get_comment_excerpt','mps_yk_solve_persian',10,1);
	add_filter('get_comment_text','mps_yk_solve_persian',10,1);
	add_filter('get_comment_author','mps_yk_solve_persian',10,1);
	add_filter('the_author','mps_yk_solve_persian',10,1);
	//To Do : Bookmarks & Tags - get_bookmarks & get_tags
}



if (!version_compare($_wp_version, '2.4', '<')) { //Wordpress 2.5+ Only
	/* Dashboard Widgets */
	
	add_filter('dashboard_primary_link', 'mps_dashboard_primary_link',10,1);
	add_filter('dashboard_primary_feed', 'mps_dashboard_primary_feed',10,1);
	add_filter('dashboard_primary_title', 'mps_dashboard_primary_title',10,1);
	
	add_filter('dashboard_secondary_link', 'mps_dashboard_secondary_link',10,1);
	add_filter('dashboard_secondary_feed', 'mps_dashboard_secondary_feed',10,1);
	add_filter('dashboard_secondary_title', 'mps_dashboard_secondary_title',10,1);
	
	/* Edit Jalali timestamp in Write Page */
	
	if ($mps_jd_editjalali) {
		add_action('edit_form_advanced', 'jalali_timestamp_admin'); // for posts
		add_action('edit_page_form', 'jalali_timestamp_admin'); // for pages
	}

}

/* Login Form */

add_filter('login_headerurl', 'login_url',999);
add_filter('login_headertitle', 'login_text',999);
add_action('login_head', 'login_img',999);

/* Theme Widgets */

add_action('widgets_init', 'widget_jarchive_init');
add_action('widgets_init', 'widget_mps_calendar_init');

// add_action('wp_print_scripts', 'mps_loadjs');
?>