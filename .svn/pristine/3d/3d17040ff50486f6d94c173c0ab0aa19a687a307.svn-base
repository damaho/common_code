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
** ******************************************************************** */
define ('SLIDESHOW_LOCATION','content/slideshow');
define ('GD_X_INDEX',0);
define ('GD_Y_INDEX',1);
define ('MAXX',800);
define ('MAXY',600);

// TODO - maybe extend pagelayout
class SlideShow {
	private $name;
	private $id;
	private $images = array();
	private $display_order = array();
	private $css_files = array();
	private $js_files = array();
	private $size_to_smallest = false;
	private $interval = 5000;
	private $direction = 'up';
	private $smallest_image_size = null;
	
	private $max_x = MAXX;  // max setting 
	private $max_y = MAXY;
	
	function __construct($name, $interval = 5, $up = true, $size_to_smallest = false) {
		$this->name = $name;
		$this->id = "slideshow__{$name}";
		$this->setInterval($interval);
		$this->direction = $up ? 'up' : 'down';
		$this->size_to_smallest = $size_to_smallest;
		$this->get_images();
		//$this->css_files[] = 'slideshow.css';
		$this->js_files[] = 'slideshow.js';
	}
	
	function getCSSFiles() {
		return $this->css_files;
	}
	
	function getJSFiles() {
		return $this->js_files;
	}
	
	private function get_image_css($data) {
		$css = "";
		$x = $data[GD_X_INDEX];
		$y = $data[GD_Y_INDEX];
		if ($x > $y) {
			if ($x > $this->max_x)
				$css = "width:{$this->max_x}px;";
		} else {
			if ($y > $this->max_y)
				$css = "height:{$this->max_y}px;";
		}
		return $css;
	}
	
	function getHeaderCSS() {
		$css = "";
		$css .= "#{$this->id} {\n";
		$css .= "    position:relative;\n";
		$css .= "    overflow:hidden;\n";
		$css .= "}\n";
		$css .= "#{$this->id} IMG {\n";
		$css .= "    position:absolute;\n";
		$css .= "    top:0;\n";
		$css .= "    left:0;\n";
		$css .= "    z-index:8;\n";
		$css .= "}\n";
		$css .= "#{$this->id} IMG.active {\n";
		$css .= "    z-index:10;\n";
		$css .= "}\n";
		$css .= "#{$this->id} IMG.last-active {\n";
		$css .= "    z-index:9;\n";
		$css .= "}\n";
		$css .= "#{$this->id} IMG {\n";
		if ($this->direction == 'up')
			$css .= "	top: {$this->max_y}px;\n";
		else
			$css .= "	top: -{$this->max_y}px;\n";
		$css .= "}\n";
		
		return $css;
	}
	
	function getXHTML() {
		$xhtml = "";
		$xhtml .= "<div id='{$this->id}' style='border:1px #fff solid;width:{$this->max_x}px;height:{$this->max_y}px'>\n";
		//$xhtml .= "<div id='{$this->id}' style='width:{$this->max_x}px;height:{$this->max_y}px'>\n";
		$i = 0;
		foreach ($this->images as $name => $data) {
			$css = $this->get_image_css($data['data']);
			$xhtml .= "<img src='" . $data['path'] . "/" . $data['file'] . "' alt=\"" . $name . "\"";
			if ($i==0)
				$xhtml .= " class='active'";
			$xhtml .= " style=\"$css\"";
			$xhtml .= "/>\n";
			$i++;
		}
		$xhtml .= "</div>\n";
		return $xhtml;
	}
	
	function getJavascript() {
		return "SlideShowManager.addSlideShow('{$this->id}',{$this->interval},'{$this->direction}',{$this->max_y});\n";
	}
	
	private function checkImageSize($data) {
		if (!isset($data[GD_X_INDEX]) || !isset($data[GD_Y_INDEX]))
			return;
		$x = $data[GD_X_INDEX];
		$y = $data[GD_Y_INDEX];
		$square = $x * $y;
		if (is_null($this->smallest_image_size) || $this->smallest_image_size > $square) {
			$this->smallest_image_size = $square;
			if ($this->size_to_smallest) {
				//$this->setMaxX($x);
				$this->setMaxY($y);
			}
		}
	}
	
	public function addImage($file,$path,$name=NULL) {
		$fullpath = $path . "/" . $file;
		$tmp = array();
		$tmp['file'] = $file;
		$tmp['path'] = $path;
		$tmp['name'] = $name;
		$tmp['data'] = getimagesize($fullpath);

		$this->checkImageSize($tmp['data']);
			
		if (is_null($name))
			$this->images[$file] = $tmp;
		else
			$this->images[$name] = $tmp;
	}
	
	public function setOrder($name,$order) {
		$this->display_order[$name] = $order;
	}
	
	private function get_images() {
		if ($handle = opendir(SLIDESHOW_LOCATION)) {
			while (($file = readdir($handle)) !== false) {
				if ($file == "." || $file == "..")
					continue;
				if (is_readable(SLIDESHOW_LOCATION . "/" . $file) && @getimagesize(SLIDESHOW_LOCATION . "/" . $file)) {
					$this->addImage($file,SLIDESHOW_LOCATION,$file);
				}
			}
		}
	}

	public function setMaxX($x) {
		$this->max_x = $x;
	}
	
	public function setMaxY($y) {
		$this->max_y = $y;
	}

	public function setInterval($seconds) {
		$this->interval = round($seconds * 1000);
	}
}
?>
