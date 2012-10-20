<?php
/* *********************************************************************
 * Copyright (C) 2007-2012 Brwe-Net
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
 * $Id$
 * 
 * This method uses a borrowed script from php snippets that usess netip
 * to get city state country
* 
* ****************************************************************** */
class LocationFromIp {
	public $ip_address;
	public $country;
	public $region;
	public $city;
	public $domain;
	public $error = false;
	public $message = "";
	public $url_source;
	public $data = array(
			'domain' => '',
			'country' => '',
			'state' => '',
			'town' => ''
		);
	
	function __construct($ip) {
		$this->setIPAddress($ip);
		$this->url_source = 'http://www.netip.de/search?query=' . $ip;
		
		$this->error = $this->getLocation();
	}
	
	function setIPAddress($ip) {
		$this->ip_address = $ip;
	}
	
	function get() {
		return sprintf("%s, %s, %s",$this->data["town"],$this->data["state"],substr($this->data["country"], 4));
	}
	
	private function getLocation() {
		//Thanks to Bennett at phpdevtips.com for this function 
		if(!filter_var($ip, FILTER_VALIDATE_IP)) {
			$this->message = "Invalid IP Address";
			return false;
		}
		 
		$response=@file_get_contents($this->url_source);
		 
		if (empty($response)) {
			$this->message = "Error connecting to source: " . $this->url_source;
			return false;
		}
		 
		$patterns=array();
		$patterns["domain"] = '#Domain: (.*?)&nbsp;#i';
		$patterns["country"] = '#Country: (.*?)&nbsp;#i';
		$patterns["state"] = '#State/Region: (.*?)<br#i';
		$patterns["town"] = '#City: (.*?)<br#i';
		 
		foreach ($patterns as $key => $pattern) {
			$this->data[$key] = preg_match($pattern,$response,$value) && !empty($value[1]) ? $value[1] : '';
		}
		
		return true;
	}
}
?>
