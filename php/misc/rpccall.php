<?php
/* *********************************************************************
 * Copyright (C) 2007-2012 Brew Net
 *
 *   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 *  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 *  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 *  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 *  BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 *  ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 *  SOFTWARE.
 *
 * $Id: rpccall.php 73 2012-10-20 22:29:18Z Dave $
 * 
 * ********************************************************************* */
require_once('ServiceClasses.php');
require_once('logger.php');

class SendResponse {
	var $response;
	var $collection;
	var $RPC;
	var $arguments;
	var $good_to_go = false;
	
	function __construct() {
		$this->response = new JSONRPC_Response();
		$this->good_to_go = $this->getArguments();
	}
	
	function getArguments() {
		if (isset($_REQUEST['COLLECTION']))
			$this->collection = $_REQUEST['COLLECTION'];
		else
			return false;

		if (isset($_REQUEST['RPC']))
			$this->RPC = $_REQUEST['RPC'];
		else
			return false;

		if (isset($_REQUEST['ARGS']))
			$this->arguments = $_REQUEST['ARGS'];
		else
			$this->arguments = array();
		return true;
	}
	
	function execute() {
		if (!$this->good_to_go)
			return;
		$coll = $this->collection;
		$rpcname = $this->RPC;

		if (!file_exists("{$coll}.php"))
			return;
		require_once("{$coll}.php");

		if (!class_exists($coll))
			return;
		$obj = new $coll();

		$result = $obj->$rpcname($this->arguments);
		if (!$result)
			$this->response->error = 1;
		if (property_exists($result,'collection'))
			$result->collection = $coll;
		$this->response->result = $result;
		if (is_object($result) && property_exists($result,'error'))
			$this->response->error = $result->error;
	}
	
	function output() {
		log_info(json_encode($this->response));
		echo json_encode($this->response);
	}
}

$foo = new SendResponse();
$foo->execute();
$foo->output();
?>
