<?php
/*
Plugin Name: wp-jalali
Plugin URI: http://wp-persian.com/wp-jalali
Description: Full Jalali calendar support for Wordpress and localization improvements for Persian/Afghan/Tajik users.
Version: 5.0.0
Author: Zakrot Web Solutions (in collaboration with WP-Persian team)
Author URI: http://zakrot.com/
Text Domain: ztjalali
Domain Path: /languages
*/

# Copyright 2005-2014  Wordpress Persian Project  (email : info@wp-persian.com)
# 
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
# 
# 
# Contributors:
#
# Since 5.0:
#       Zakrot Web Solutions
#       Mani Monajjemi
#
# 3.x, 4.x:
#       Mani Monajjemi
#       Gonahkar
#       Reza Moallemi
#
# Special Thanks to :
#       Ali Farhadi (farhadi.ir) for improving Farsi Number Convertor and js jalali date lib.
#       Vahid Sohrablu (iranphp.org) for pdate lib (https://gitorious.org/pdate).
#

/*
 * define plugin dir
 */
defined('JALALI_DIR') or define('JALALI_DIR',  dirname(__FILE__).DIRECTORY_SEPARATOR);
/* =================================================================== */

/**
 * include structor
 */
include JALALI_DIR.'wp-jalali-functions.php';
include JALALI_DIR.'wp-jalali-config.php';
include JALALI_DIR.'wp-jalali-init.php';
/* =================================================================== */

/**
 * include libs
 */
include JALALI_DIR.'lib'.DIRECTORY_SEPARATOR.'date.php';
include JALALI_DIR.'lib'.DIRECTORY_SEPARATOR.'deprecated_fns.php';
/* =================================================================== */

/**
 * initialize...
 */
ztjalali_init();
register_activation_hook(__FILE__, 'ztjalali_installer');
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'ztjalali_add_settings_link' );
/* =================================================================== */

/**
 * include admin stuff and filter handlers
 */
include JALALI_DIR.'inc'.DIRECTORY_SEPARATOR.'wp-jalali-admin.php';
include JALALI_DIR.'inc'.DIRECTORY_SEPARATOR.'wp-jalali-filters.php';
/* =================================================================== */

/**
 * include widgets (include low important libs here)
 */
include JALALI_DIR.'widget'.DIRECTORY_SEPARATOR.'widget_archive.php';
include JALALI_DIR.'widget'.DIRECTORY_SEPARATOR.'widget_calendar.php';
/* =================================================================== */
