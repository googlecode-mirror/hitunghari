<?php
/**
 * HitungHari Countdown Banner Application
 * Version 1.3
 * 
 * Author: M. Fauzilkamil Zainuddin (ApOgEE)
 * Author URL: http://coderstalk.blogspot.com
 * 
 * Copyright (C) M. Fauzilkamil Zainuddin
 * Since: 19 April 2011
 * 
 * Description: 
 * This application started when I'm just playing with PHP + GD. Coincidentally with 
 * the upcoming MOSC2011 event, I heard some people asking in the mailing list if it
 * could be possible to have countdown banner for the event. Therefore, I modified 
 * my useless code and make this HitungHari countdown banner application.
 * 
 */

$dbg=0;
$dbgmsg = "";
$testflag=0; /* testing purpose. set to 0 for actual production */ 

define('BGDIR',"countbg-images/");
include('./hh-config.php');
/*
 * for debug purpose, we can set the url to "/yourbanner.php?dbg" to see
 * debug message. 
 */
if (isset($_GET['dbg'])) {
	//$dbg=1; /* uncomment this to enable debug. */
	$dbgmsg="<pre>";
	$dbgmsg.="HitungHari Countdown Banner Version 1.3 by ApOgEE\n";
	$dbgmsg.="=================================================\n";
}

/*
 * since version 1.1, I have added multiple sizes support to this banner.
 */
if (isset($_GET['size'])) {
	/* size variable only take integer. 
	 * So, let's sanitize it for security */
	$size = filter_var($_GET['size'],FILTER_SANITIZE_NUMBER_INT);
	$dbgmsg.="Size: $size\n";
}

/* HitungHari Class by ApOgEE */
Class HitungHari {
	/* configurations */
	var $config;
	var $is_today;
	var $countbg;
	var $counttext;
	var $fontsize;
	var $pos;
	var $size;
	
	function setconfig($day,$month,$year,$hour,$size) {
		$this->config['day'] = $day;
		$this->config['month'] = $month;
		$this->config['year'] = $year;
		$this->config['hour'] = $hour;		
		$this->size = $size;		
	}
	
	/* this function is only to view current config
	 * in the debug mode */
	function showconfig() {
		global $dbg;
		global $dbgmsg;
		
		if ($dbg) {
			$dbgmsg.="Date (dd-mm-yyyy : hour): {$this->config['day']}-{$this->config['month']}-{$this->config['year']} : {$this->config['hour']}<br/>";
		}
	}
	
	function countdown() {
		global $dbg;
		global $dbgmsg;
		
		$calculation = ((mktime ($this->config['hour'],0,0,$this->config['month'],$this->config['day'],$this->config['year']) - time(void))/3600);
		$hours = (int)$calculation;
		$days  = (int)($hours/24);
		$this->is_today = 0;		

		if ($dbg) {
			$dbgmsg.="Calculation: $calculation<br/>";
			$dbgmsg.="Hours: $hours<br/>";
			$dbgmsg.="Days: $days<br/>";
		}		
		
		if ($days<=0) {
			if (($hours) <= 0 ) {
				switch($this->size){
					case 1:
						$this->countbg = BGDIR . "cbg-300x250-today.png";
						break;
					case 2:
						$this->countbg = BGDIR . "cbg-728x90-today.png";
						break;
					case 3:
						$this->countbg = BGDIR . "cbg-160x600-today.png";
						break;
					default:
						$this->countbg = BGDIR . "cbg-default-today.png";
				}
				$this->is_today = 1;
			} else {
				switch($this->size){
					case 1:
						$this->countbg = BGDIR . "cbg-300x250-hour.png";
						break;
					case 2:
						$this->countbg = BGDIR . "cbg-728x90-hour.png";
						break;
					case 3:
						$this->countbg = BGDIR . "cbg-160x600-hour.png";
						break;
					default:
						$this->countbg = BGDIR . "cbg-default-hour.png";
				}				
	
				// The text to draw
				$this->counttext = "$hours";
			}
		} else {
			switch($this->size){
				case 1:
					$this->countbg = BGDIR . "cbg-300x250-day.png";
					break;
				case 2:
					$this->countbg = BGDIR . "cbg-728x90-day.png";
					break;
				case 3:
					$this->countbg = BGDIR . "cbg-160x600-day.png";
					break;
				default:
					$this->countbg = BGDIR . "cbg-default-day.png";
			}
			// The text to draw
			$this->counttext = "$days";
		}	
		
		if ($this->is_today == 0) {
			/* if it is not today, we should set the font size and 
			 * x,y position of the countdown number for each banner size */
			switch($this->size) {
				case 1: // 300x250
					$this->fontsize = 75;

					if (strlen($this->counttext) == 1) {
						$this->pos['x']=68;
					} else {
						$this->pos['x']=28;
					}
					$this->pos['y']=203;

					break;

				case 2: // 728x90
					$this->fontsize = 60;

					if (strlen($this->counttext) == 1) {
						$this->pos['x']=395;
					} else {
						$this->pos['x']=350;
					}
					$this->pos['y']=73;

					break;

				case 3: // 160x600
					$this->fontsize = 80;

					if (strlen($this->counttext) == 1) {
						$this->pos['x']=56;
					} else {
						$this->pos['x']=21;
					}
					$this->pos['y']=210;

					break;

				default: // 290x200
					$this->fontsize = 75;

					if (strlen($this->counttext) == 1) {
						$this->pos['x']=68;
					} else {
						$this->pos['x']=28;
					}
					$this->pos['y']=175;
			} // end switch size
		} // end if not today

		if ($dbg) {
			$dbgmsg.="countbg: {$this->countbg}<br/>";
			$dbgmsg.="counttext: {$this->counttext}<br/>";
		}			
	}
	
	/* this function draw the png image and reply to the http request */
	function createbanner() {
		
		$im = imagecreatefrompng($this->countbg);
		imagesavealpha( $im, true );

		if ($this->is_today == 0) {
			$yellow = imagecolorallocate($im, 255, 255, 0);
			$grey = imagecolorallocate($im, 88, 88, 88);

			$font = 'CenturyGothic.ttf';

			// Add the shadow
			imagettftext($im, $this->fontsize, 0, $this->pos['x']+2, $this->pos['y']+2, $grey, $font, $this->counttext);
			// Add the text
			imagettftext($im, $this->fontsize, 0, $this->pos['x'], $this->pos['y'], $yellow, $font, $this->counttext);
		}

		imageantialias($im, true);

		header('Content-Type: image/png');

		imagepng($im);
		imagedestroy($im);		
	}
	
}

ini_set("date.timezone",$timezone);

/* testing purpose */
if ($testflag) {
	//testing
	$day   = 21;     // Day of the countdown
	$month = 4;      // Month of the countdown
	$year  = 2011;   // Year of the countdown
	$hour  = 9;     // Hour of the day (east coast time)
}

$banner = new HitungHari();
$banner->setconfig($day,$month,$year,$hour,$size);

/* just to check if the config is working in debug mode */
if ($dbg) $banner->showconfig();

$banner->countdown();

/* only display the banner if not in debug mode */
if (!$dbg) $banner->createbanner();

unset($banner);

/* show the debug message */
if ($dbg) echo "$dbgmsg</pre>";

?>
