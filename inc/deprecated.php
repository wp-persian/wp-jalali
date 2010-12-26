<?php
if (!function_exists('fetch_rss'))	require_once (ABSPATH . WPINC . '/class-simplepie.php');
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

function mps_mce_pretext($input=null){
	$_nbsp = "&nbsp;";
	$_p = "<p dir=\"rtl\">".$_nbsp."</p>";
	return (strlen($input)==0?$_p:$input);
}

?>