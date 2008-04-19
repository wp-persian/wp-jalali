<?php
function mps_yk_solve_callback($matches) { 
	$arabic = array("ي", "ك");
	$persian = array("ی", "ک");
	
	$clause = $matches[1];
	$phrase = $matches[2];
	
	$phrase_persian = str_replace($arabic,$persian,$phrase); //Pure Persian
	$phrase_arabic = str_replace($persian,$arabic,$phrase); //Pure Arabic
	
	$clause_persian = str_replace($phrase, $phrase_persian, $clause);
	$clause_arabic = str_replace($phrase, $phrase_arabic, $clause);
	
	return "( ".$clause_persian." OR ".$clause_arabic." )";
}

function mps_yk_solve_search($query) {
	$pattern = "/(\([^\)\(]* LIKE '([^']*)'\))/";
	
	if (strstr($query,"LIKE"))  { //Is Search?
		if (strstr($query,"ی") || strstr($query,"ک") || strstr($query,"ي") || strstr($query,"ك")) {
			$query = preg_replace_callback($pattern, 'mps_yk_solve_callback', $query);
		}
		
	} 
	//echo "<!--".$query."-->";
	return $query;
}
function mps_yk_solve_persian($content) {
	$arabic = array("ي", "ك");
	$persian = array("ی", "ک");
	$content = str_replace($arabic,$persian,$content);
	return $content;
}

function mps_yk_solve_persian_debug($content) {
	print_r($content);
}


?>