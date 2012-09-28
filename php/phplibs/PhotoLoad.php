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
class PhotoLoad {
	protected $images = array();
	protected $image_name=null;
	protected $image_path;
	protected $image_fullpath=null;
	
	function __construct($image_path,$image,$name=null) {
		//$this->image_path = $image_path;
		//$this->addImage($image,$image_path,$name);
		//$this->get_images();
		//$this->load_image();
		$this->loaded_image = $image_path . "/" . $image;
		if (!is_null($this->loaded_image)) {
			header ('Content-type: image/gif'); 
			readfile($this->loaded_image);
		}
	}
	
	private function load_image() {
		$image =  $this->images[0];
		$this->loaded_image = $image['path'] . "/" . $image['file'];
	}
	
	private function get_images() {
		if ($handle = opendir($this->image_path)) {
			while (($file = readdir($handle)) !== false) {
				if ($file == "." || $file == "..")
					continue;
				if (is_readable($this->image_path . "/" . $file) && @getimagesize($this->image_path . "/" . $file)) {
					$this->addImage($file,$this->image_path,$file);
				}
			}
		}
		closedir($handle);
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
