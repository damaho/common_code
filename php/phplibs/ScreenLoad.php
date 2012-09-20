<?php
/* *********************************************************************
 * Copyright (C) 2007-2012 Kelly Stratton Music
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
* ****************************************************************** */
class ScreenLoad {
	private $screen_location;
	private $images = array();
	private $loaded_image=null;
	
	function __construct($screen_location) {
		$this->screen_location = $screen_location;
		$this->get_images();
		$this->load_image();
		if (!is_null($this->loaded_image)) {
			header ('content-type: image/gif'); 
			readfile($this->loaded_image);
		}
	}
	
	private function load_image() {
		$num_images = count($this->images);
		if ($num_images == 0)
			return;
		$min = 0;
		$max = $num_images - 1;
		$image =  $this->images[rand($min,$max)];
		$this->loaded_image = $image['path'] . "/" . $image['file'];
	}
	
	public function addImage($file,$path,$name=NULL) {
		$fullpath = $path . "/" . $file;
		$tmp = array();
		$tmp['file'] = $file;
		$tmp['path'] = $path;
		$tmp['name'] = $name;
		$tmp['data'] = getimagesize($fullpath);

		$this->images[] = $tmp;
	}
	
	private function get_images() {
		if ($handle = opendir($this->screen_location)) {
			while (($file = readdir($handle)) !== false) {
				if ($file == "." || $file == "..")
					continue;
				if (is_readable($this->screen_location . "/" . $file) && @getimagesize($this->screen_location . "/" . $file)) {
					$this->addImage($file,$this->screen_location,$file);
				}
			}
		}
	}
	
	private function getXHTML() {
		$xhtml = "";
//		$xhtml .= "<html>\n";
//		$xhtml .= "<head>\n";
//		$xhtml .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"background.css\" />\n";
//		$xhtml .= "</head>\n";
//		$xhtml .= "<body>\n";
		if (!is_null($this->loaded_image))
			$xhtml .= "<img style=\"-webkit-user-select: none; \" src=\"{$this->loaded_image}\" alt='' />\n";
//		$xhtml .= "</body>\n";
//		$xhtml .= "</html>\n";
		return $xhtml;
	}

}

?>
