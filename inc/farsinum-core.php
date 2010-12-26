<?php

/* Originally written by Farhadi , www.farhadi.ir */

function convertToFarsi($matches) {
	$mps_jd_optionsDB = get_option(MPS_JD_OPTIONS_NAME);
	$mps_jd_decimal = $mps_jd_optionsDB['mps_jd_decimal'];
	if($mps_jd_decimal == true)
		//$farsi_array = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "<sub><small>/</small></sub>");
		$farsi_array = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "٫");
	else
		$farsi_array = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", ".");
	
	$english_array = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", ".");
	
	$out = '';
	if (isset($matches[1])) {
		return str_replace($english_array, $farsi_array, $matches[1]);
	}
	return $matches[0];
}

function farsi_num($num,$fake = null,$fake2=null) {
	return preg_replace_callback('/(?:&#\d{2,4};)|(\d+[\.\d]*)|(?:[a-z](?:[\x00-\x3B\x3D-\x7F]|<\s*[^>]+>)*)|<\s*[^>]+>/i', 'convertToFarsi', $num);
}

function english_num($num) {
	$mps_jd_optionsDB = get_option(MPS_JD_OPTIONS_NAME);
	$mps_jd_decimal = $mps_jd_optionsDB['mps_jd_decimal'];
	if($mps_jd_decimal == true)
		//$farsi_array = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "<sub><small>/</small></sub>");
		$farsi_array = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "٫");
	else
		$farsi_array = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", ".");
	
	$english_array = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", ".");
	
	return str_replace($farsi_array, $english_array, $num);
}
?>