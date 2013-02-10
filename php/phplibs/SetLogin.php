<?php
/* *********************************************************************
 * Copyright (C) 2007-2013 Dave Horn
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
 * Purpose is to add standard elements to content them pass to parent
 * 
 * ********************************************************************* */
 class SetLogin {
	private $r_salt;
	private $salt;
	private $saltlen = 22;
	private $algo;
	 
	function __construct() {
		$this->setAlgo('$2a$12$');
		$this->init_salt();
	}
	
	private function init_salt() {
		$this->rsalt = microtime(true)*100000 + memory_get_usage(true);
		mt_srand($this->rsalt);
		$this->setSalt(substr(str_replace('+','.',base64_encode(sha1(mt_rand(),true))),0,$this->saltlen));
	}
	
	public function hash_pw($passwd) {
		$this->init_salt();
		$this->setAlgo('$1$');
		return crypt($passwd,$this->algo . $this->salt);
	}
	
	public function check_hash($str,$hash) {
		return (crypt($str,$hash) == $hash);
	}
	
	public function setAlgo($algo) {
		$this->algo = $algo;
	}
	
	public function setSalt($salt) {
		$this->salt = $salt;
	}
	
	public function testmd5($passwd) {
		$this->init_salt();
		$this->setAlgo('$1$');
		echo $this->hash_pw($passwd);
	}
	
	
	public function teststatic() {
		echo $this->check_hash("F1d0tHeD0g",'$1$Dn/f/AKA$P6VY.v/vhEIHvfRlGF0xy/') ? "SUCCESS" : "FAILURE";
	}
	
	public function test_crypt() {
		echo "<pre>\n";
		if (CRYPT_STD_DES == 1) {
			echo 'Standard DES: ' . crypt('rasmuslerdorf', 'rl') . "\n";
		}

		if (CRYPT_EXT_DES == 1) {
			echo 'Extended DES: ' . crypt('rasmuslerdorf', '_J9..rasm') . "\n";
		}

		if (CRYPT_MD5 == 1) {
			echo 'MD5:          ' . crypt('rasmuslerdorf', '$1$rasmusle$') . "\n";
		}

		if (CRYPT_BLOWFISH == 1) {
			echo 'Blowfish:     ' . crypt('rasmuslerdorf', '$2a$07$usesomesillystringforsalt$') . "\n";
		}

		if (CRYPT_SHA256 == 1) {
			echo 'SHA-256:      ' . crypt('rasmuslerdorf', '$5$rounds=5000$usesomesillystringforsalt$') . "\n";
		}

		if (CRYPT_SHA512 == 1) {
			echo 'SHA-512:      ' . crypt('rasmuslerdorf', '$6$rounds=5000$usesomesillystringforsalt$') . "\n";
		}
		echo "</pre>\n";
	}
}
?>
