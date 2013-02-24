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
require_once('PageContent.php');

class PhotoAlbum extends PageContent {
	private $date;
	public $photos = array();
	private $thumb_x = 50;
	private $thumb_y = 50;
	
	function __construct($album_name) {
		$this->title = $album_name;
	}
	
	function getHeaderXHTML() {
		$xhtml = "";
		$xhtml .= "<div class='album_header'>{$this->title}</div>\n";
		return $xhtml;
	}
	
	function getPhotoXHTML($photo) {
		$new_x = 50;
		$new_y = 50;
		$pos_x = 0;
		$pos_y = 0;
		$ratio = 1;
		
		$data = $photo['data'];
		$x = $data[0];
		$y = $data[1];
		if ($x > $y) {
			$ratio = $y == 0 ? 1 : $this->thumb_y / $y;
		} else {
			$ratio = $x == 0 ? 1 : $this->thumb_x / $x;
		}
		$new_x = $x * $ratio;
		$new_y = $y * $ratio;
		
		$pos_x = ($new_x - $this->thumb_x) / 2;
		$pos_x = ($new_y - $this->thumb_y) / 2;
		
		$xhtml = "";
		$xhtml .= "<div class='lightbox_holder' style='width:{$this->thumb_x}px;height:{$this->thumb_y}px;'>\n";

		$xhtml .= "<a class='lightbox' href=\"{$photo['thumb']}\">\n";
		$style = "style='width:{$new_x}px;height:{$new_y}px;left:{$pos_x}px;top:{$pos_y}px;'";
		$xhtml .= "<img class='lb_thumb' src=\"{$photo['photo']}\" $style alt=\"\" />\n";
		$xhtml .= "</a>\n";

	
		$xhtml .= "</div>\n";
		return $xhtml;
	}
	
	function getXHTML() {
		if (count($this->photos) == 0)
			return "";
		$xhtml = "";
		$xhtml .= "<div class='photo_album_container'>\n";
		$xhtml .= $this->getHeaderXHTML();
		foreach ($this->photos as $photo) {
			$xhtml .= $this->getPhotoXHTML($photo);
		}
		$xhtml .= "<div id='finish_album'></div>\n";
		$xhtml .= "</div>\n";
		return $xhtml;
	}
	
	function setThumbDimensions($x,$y) {
		$this->thumb_x = $x;
		$this->thumb_y = $y;
	}
	
	function addPhoto($photo_file, $title = '', $caption = '' , $thumb_file='') {
		if (!file_exists($photo_file))
			return;
		if (!file_exists($thumb_file))
			$thumb_file = $photo_file;
		$data = getimagesize($thumb_file);
		if ($data[0] == 0 || $data[1] == 0) // TODO - figure this out
			return;
		$this->photos[] = array('photo'=>$photo_file, 'thumb'=>$thumb_file, 'title'=> $title, 'caption'=>$caption, 'data'=>$data);
	}
}

class PhotoAlbumManager extends PageContent {
	public $albums;
	private $thumb_x = 50;
	private $thumb_y = 50;

	function __construct() {
		$this->addJSFileTop('jquery.lightbox-0.5.min.js');
		$this->addCSSFile('jquery.lightbox-0.5.css');
		$this->addCSSFile('photo_album.css');
		$this->addJSFileBottom('PhotoAlbum.js');
	}
	
	function setThumbDimensions($x,$y) {
		$this->thumb_x = $x;
		$this->thumb_y = $y;
	}

	function addAlbum($aid,$title) {
		if (isset($this->albums[$aid]))
			return;
		$this->albums[$aid] = new PhotoAlbum($title);
		$this->albums[$aid]->setThumbDimensions($this->thumb_x,$this->thumb_y);
	}
	
	function addPhoto($aid,$photo_file, $title = '', $caption = '' , $thumb_file='') {
		$this->albums[$aid]->addPhoto($photo_file,$title,$caption,$thumb_file);
	}
	
	function addElements() {
		foreach ($this->albums as $aid => $album) {
			$this->addPageElement("album__{$aid}",$album);
		}
	}
	
	function execute() {
		$this->addElements();
		parent::execute();
	}
}

?>
