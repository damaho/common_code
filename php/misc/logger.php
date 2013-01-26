<?php
$log_path="site.log";
$log_write = array(
	'QUERY' => true,
	'DEBUG' => true,
	'INFO' => true,
	'ERROR' => true
);
if (file_exists('log_settings.php'))
	require_once('log_settings.php');

function log_write($type,$str) {
	global $log_path;
	global $log_write;
	if (!file_exists($log_path)) {
		reset_log();
	}
	if (array_key_exists($type,$log_write) && !$log_write[$type])
		return;
	$h = fopen($log_path,"a");

	$full_self = $_SERVER['PHP_SELF'];
	$split_self = explode("/",$full_self);
	$only_self="";
	if (count($split_self) > 0)
		$only_self = end($split_self);
	
	$message = date("YmdHis") . " " . sprintf("%-15s",$_SERVER['REMOTE_ADDR']) . " " . sprintf("%-20s",$only_self) . " " . sprintf("%-10s",$type) . " " . $str . "\r\n";
	fwrite($h,$message);
	fclose($h);
}


function log_error($str) {
	log_write("ERROR",$str);
}

function log_info($str) {
	log_write("INFO",$str);
}

function log_debug($str) {
	log_write("DEBUG",$str);
}

function log_other($type,$str) {
	log_write($type,$str);
}

function reset_log() {
	global $log_path;
	if (file_exists($log_path))
		unlink($log_path);
	$h = fopen($log_path,"a");
	fwrite($h,"Site Log created " . date("Y-m-d H:i:s") . "\r\n");
	fclose($h);
}

function getLog() {
	global $log_path;
	if (!file_exists($log_path)) {
		reset_log();
	}
	$contents = file_get_contents($log_path);
	return $contents;	
}
?>
