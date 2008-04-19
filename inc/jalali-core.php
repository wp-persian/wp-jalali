<?php
	
/* Jalali Core Functions */

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
$j_month_name = array("", "فروردین", "اردیبهشت", "خرداد", "تیر","مرداد", "شهریور", "مهر", "آبان", "آذر","دی", "بهمن", "اسفند");
$j_day_name = array("یکشنبه","دوشنبه","سه شنبه","چهارشنبه","پنجشنبه","جمعه","شنبه");
$jday_abbrev = array("ی","د","س","چ","پ","ج","ش");

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

?>