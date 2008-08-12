<?php
$primary_replacement = 
	array (
		1 => array (
			'link' => 'http://wordpress.org/development/',
			'feed' => 'http://wordpress.org/development/feed/',
			'title' => 'WordPress Development Blog'
		),
		2 => array (
			'link' => 'http://wp-persian.com/',
			'feed' => 'http://wp-persian.com/feed/',
			'title' => 'اخبار وردپرس فارسی'
		)
	);
$secondary_replacement = 
	array (
		1 => array (
			'link' => 'http://planet.wordpress.org/',
			'feed' => 'http://localhost/w/?feed=rss2',
			'title' => 'Other WordPress News'
		),
		2 => array (
			'link' => 'http://planet.wp-persian.com/',
			'feed' => 'http://localhost/w/?feed=rss2',
			'title' => 'سیاره وردپرس فارسی'
		)
	);

function mps_dashboard_primary_link ($value) {
	global $primary_replacement,  $mps_jd_optionsDB;
	$mps_jd_dashboard = $mps_jd_optionsDB['mps_jd_dashboard'];
	
	$ret = ($mps_jd_dashboard == 0) ? $value : $primary_replacement[$mps_jd_dashboard]['link'];
	return $ret;
}

function mps_dashboard_primary_feed ($value) {
	global $primary_replacement,  $mps_jd_optionsDB;
	$mps_jd_dashboard = $mps_jd_optionsDB['mps_jd_dashboard'];
	
	return ($mps_jd_dashboard == 0) ? $value :  $primary_replacement[$mps_jd_dashboard]['feed'];
}

function mps_dashboard_primary_title ($value) {
	global $primary_replacement,  $mps_jd_optionsDB;
	$mps_jd_dashboard = $mps_jd_optionsDB['mps_jd_dashboard'];
	
	return ($mps_jd_dashboard == 0) ? $value :  $primary_replacement[$mps_jd_dashboard]['title'];
}

function mps_dashboard_secondary_link ($value) {
	global $secondary_replacement,  $mps_jd_optionsDB;
	$mps_jd_dashboard = $mps_jd_optionsDB['mps_jd_dashboard'];
	
	return ($mps_jd_dashboard == 0) ? $value :  $secondary_replacement[$mps_jd_dashboard]['link'];
}

function mps_dashboard_secondary_feed ($value) {
	global $secondary_replacement,  $mps_jd_optionsDB;
	$mps_jd_dashboard = $mps_jd_optionsDB['mps_jd_dashboard'];
	
	return ($mps_jd_dashboard == 0) ? $value :  $secondary_replacement[$mps_jd_dashboard]['feed'];
}

function mps_dashboard_secondary_title ($value) {
	global $secondary_replacement,  $mps_jd_optionsDB;
	$mps_jd_dashboard = $mps_jd_optionsDB['mps_jd_dashboard'];
	
	return ($mps_jd_dashboard == 0) ? $value :  $secondary_replacement[$mps_jd_dashboard]['title'];
}

?>