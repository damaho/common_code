<?php
/* *********************************************************************
 * Copyright 2009 David Horn
 * 
 * $Id: Utils.php 78 2013-01-26 15:58:07Z Dave $
 * 
  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
  BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
  ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  SOFTWARE.
  * 
  ******************************************************************* */
require_once ('logger.php');

function genericEscape($str) {
	return mysql_real_escape_string($str);
}

function strToHex($string) {
	$hex='';
	for ($i=0; $i < strlen($string); $i++) {
		$hex .= dechex(ord($string[$i]));
	}
	return $hex;
}


function getAjaxLoadingXHTML($prefix = '', $suffix = '') {
	if ($prefix != '')
		$prefix = $prefix . "__";
	if ($suffix != '')
		$suffix = "__" . $suffix;
	$xhtml = "";
	$xhtml .= "<div id='{$prefix}ajax_loading_div{$suffix}'>";
	$xhtml .= "<img src='images/loading.gif' id='{$prefix}ajax_loading{$suffix}' style='display:none;' alt='Loading...' />";
	$xhtml .= "<span id='{$prefix}ajax_loading{$suffix}__message'></span>";
	$xhtml .= "</div>\n";
	return $xhtml;
}

function filenameFromURL($url) {
	$filename = false;
	$pieces = explode("/",$url);
	$len = count($pieces);
	if ($len > 0) {
		$tmp = $pieces[$len - 1];
		if ($tmp != '')
			$filename = $tmp;
	}
	return $filename;
}



/**
* Launch Background Process
*
* Launches a background process (note, provides no security itself, $call must be sanitized prior to use)
* @param string $call the system call to make
* @author raccettura
*/
function launchBackgroundProcess($call) {
	log_info("I WANT TO RUN $call");
	
	// Windows
	if(is_windows()) {
		log_info("IN WINDOWS");
		//pclose(popen('start /b '.$call, 'r'));
	} else { // Some sort of UNIX
		log_info("IN UNIX");
		//exec("$call > /dev/null &");
		//pclose(popen($call.' /dev/null &', 'r'));
		exec("bash -c \"exec nohup setsid $call > /dev/null 2>&1  &\"");
	}
	return true;
}

// TODO - this was made as a workaround - make a nice class for it
function curlBGPost($url,$json) {
	$p_args = json_decode($json);
	$get = '';
	if (count($p_args) > 0) {
		$get = '?';
		foreach ($p_args as $name => $value) {
			if ($get != '?')
				$get .= '&';
			$get .= "{$name}={$value}";
		}
	}
	$url .= $get;
	$c = curl_init();
	curl_setopt($c,CURLOPT_URL,$url);
	curl_setopt($c,CURLOPT_POST,true);
	curl_setopt($c,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($c,CURLOPT_TIMEOUT,1);
	$x = curl_exec($c);
	return $x;
}


function getUTCDateFromDate($datetime) {
	$ts = strtotime($datetime);
	return date('Y-m-d H:i:s',$ts + (strtotime(gmdate('Y-m-d H:i:s')) - $ts));
}

function getLocalDateFromUTC($datetime) {
	$ts = strtotime($datetime);
	return date('Y-m-d H:i:s',$ts - (strtotime(gmdate('Y-m-d H:i:s')) - time()));
}
 
/**
* Is Windows
*
* Tells if we are running on Windows Platform
* @author raccettura
*/
function is_windows(){
	log_info("CHECKING FOR WINDOWS");
	if(PHP_OS == ‘WINNT’ || PHP_OS == ‘WIN32′){
		return true;
	}
	return false;
}
?>
