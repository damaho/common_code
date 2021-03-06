<?php
/* *********************************************************************
 * Copyright 2009 David Horn
 * 
 * $Id: ajax_utils.php 182 2010-05-29 17:06:12Z dhorn $
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
* ******************************************************************* */
require_once "DataObject.php";
require_once "JSON.php";

function checkData($p_arguments) {
	// Need a collection
	if (!array_key_exists('Collection',$p_arguments)) {
		return false;
	}
	// Need a function in that collection
	if (!array_key_exists('RPC',$p_arguments))
		return false;
		
	if (!array_key_exists('Args',$p_arguments))
		return false;
	return true;
}

$json = new Services_JSON();
if (checkData($_REQUEST)) {
	

	$collection = $_REQUEST['Collection'];
	$RPC = $_REQUEST['RPC'];

	$m_params = stripslashes($_REQUEST['Args']);

	$Args = $json->decode($m_params);
	require_once "{$collection}.php";
	$obj = new $collection();
	$response = $obj->$RPC($Args);
	log_other("JSON",$json->encode($response));
	print($json->encode($response));

} else if (array_key_exists('Service',$_REQUEST) && is_file($_REQUEST['Service'])) {
	require_once ($_REQUEST['Service']);
	$func = $_REQUEST['RPC'];
	$Args=(array)$json->decode(stripslashes($_REQUEST['Args']));
	$response = $func($Args);
	log_other("JSON",$json->encode($response));
	print($json->encode($response));
}
?>
