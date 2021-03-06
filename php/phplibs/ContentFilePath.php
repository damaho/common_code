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
 * $Id: ContentFilePath.php 76 2012-11-03 14:18:04Z Dave $
 * 
 * Based on UserFilePath (brew_net_users)
 * 
* ****************************************************************** */
require_once("Utils.php");

class ContentFilePath {
	var $id;
	var $name;
	var $file_path_array = array();
	var $calculated_path = '';
	var $fullpath = '';
	var $moredirs = array();
	var $created_paths = array();
	
	var $base_path; // ie content/venues
	
	function __construct($base_path, $filesystem, $id, $name) {
		$this->base_path = $base_path;
		$this->fullpath = $filesystem;
		$this->id = $id;
		$this->name = $name;
		
	}

	function execute() {
		if (!is_dir($this->base_path . '/' . $this->fullpath))
			mkdir($this->base_path .  '/' . $this->fullpath);
		
		$this->createFilePath();
		
		$this->mkfromarray($this->fullpath,$this->file_path_array);
		
		foreach ($this->created_paths as $fpath) {
			foreach ($this->moredirs as $mdir) {
				if (!is_dir($this->base_path . '/' . $fpath . "/" . $mdir))
					mkdir($this->base_path . '/' . $fpath . "/" . $mdir);
			}
		}
	}

	function createFilePath() {
		$s1 = strToHex($this->name);
		if (strlen($s1) < 4) {  // shouldn't happen, but...
			$s1 .= "0000";
		}
		$level1 = substr($s1,0,2);
		$level2 = substr($s1,2,2);
		$level3 = md5($this->id);
		$this->file_path_array[$level1][$level2][$level3] = array();
		$this->calculated_path = sprintf("%s/%s/%s/%s",$this->fullpath,$level1,$level2,$level3);
	}

	function mkfromarray($path,$array) {
		if (!is_array($array) || count($array) == 0) {
			$this->created_paths[] = $path;
			return $path;
		}
		foreach ($array as $key => $subs) {
			if ($path == '')
				$dir = $key;
			else
				$dir = $path . "/" . $key;
			if (!is_dir($this->base_path . '/' . $dir))
				mkdir($this->base_path . '/' . $dir);
			$this->mkfromarray($dir,$subs);
		}
	}
	
	function addMoreDir($dir) {
		$this->moredirs[] = $dir;
	}
	
	function getPath() {
		return $this->calculated_path;
	}

}

?>
