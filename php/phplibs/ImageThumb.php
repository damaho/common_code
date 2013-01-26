<?php
/* *********************************************************************
 * Copyright (C) 2007-2012 Dave horn
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
 * $Id: ImageThumb.php 78 2013-01-26 15:58:07Z Dave $
 * 
 * ********************************************************************* */
class ImageThumb {
	var $original_filename;
	var $original_filepath;
	var $new_filename;
	var $new_filepath;
	var $maxX = 150;
	var $maxY = 150; // not used at the moment
	
	function __construct($location,$filename,$thumblocation=null,$thumbname=null,$x=150,$y=150) {
		$this->original_filename = $filename;
		$this->original_filepath = getcwd() . "/" . $location;
		$this->new_filename = !is_null($thumbname) ? $thumbname : 'thumb_' . $this->original_filename;
		$this->new_filepath = !is_null($thumblocation) ?  (getcwd() . "/" . $thumblocation) : ($this->original_filepath . '/thumbs');
		$this->maxX = $x;
		$this->maxY = $y;
	}
	
	function execute() {
		$this->checkPath();
		$img = @imagecreatefromjpeg("{$this->original_filepath}/{$this->original_filename}");
		if ($img === false) {
			log_error("FAILED TO CREATE IMAGE FROM {$this->original_filepath}/{$this->original_filename}");
			return false;
		}
		$width = imagesx( $img );
		$height = imagesy( $img );
		$new_width = $this->maxX;
		$new_height = floor($height * ($this->maxX / $width));
		//log_info("X:$new_width Y:$new_height");
		$tmp_img = imagecreatetruecolor( $new_width, $new_height );
		imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
		imagejpeg( $tmp_img, "{$this->new_filepath}/{$this->new_filename}");
	}
	
	function checkPath() {
		if (!is_dir($this->new_filepath))
			mkdir($this->new_filepath,0777);
	}
}

?>

