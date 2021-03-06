<?php
/* *********************************************************************
 * Copyright (C) 2007-2012 Dave Horn
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
 * $Id: RPCSetSession.php 76 2012-11-03 14:18:04Z Dave $
 * 
 * ********************************************************************* */
require_once ('Session.php');
require_once ('ServiceClasses.php');

class RPCSetSession {
	
	function SETSESSION($p_arguments) {
		log_info(print_r($p_arguments,true));
		$response = new GenericResult();
		if (!isset($p_arguments['session_variable']) || !isset($p_arguments['session_value'])) {
			$response->error = true;
			return $response;
		}
		$_SESSION[$p_arguments['session_variable']] = $p_arguments['session_value'];
		
		return $response;
	}
}
?>
