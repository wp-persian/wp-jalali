<?php
/* These 2 Functions are new Farsi Num convert implemented in ver 4,
Originally written by Farhadi , www.farhadi.ir 
*/

function convertToFarsi($matches) {
	$out = ''; 
	if (isset($matches[1])) {
		return str_replace(
			array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "."),
			array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "/"), 
			$matches[1]);
	}
	return $matches[0];
}

function farsi_num($num,$fake = null,$fake2=null) {
	return preg_replace_callback('/(?:&#\d{2,4};)|(\d+[\.\d]*)|(?:[a-z](?:[\x00-\x3B\x3D-\x7F]|<\s*[^>]+>)*)|<\s*[^>]+>/i', 'convertToFarsi', $num);
}
?>