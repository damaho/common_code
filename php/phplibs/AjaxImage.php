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
 * $Id: AjaxImage.php 75 2012-11-03 14:17:16Z Dave $
 * 
* ****************************************************************** */
require_once ('DataObjects.php');
require_once ('PageContent.php');
require_once ('ServiceClasses.php');

class AjaxImage {
	var $form_prefix = "";
	var $username;
	var $destination;
	var $userdata = array();
	var $name;
	var $ext;
	var $gd_data = array();
	var $size;
	var $tmp;
	var $maxX;
	var $maxY;
	var $valid_imgtypes;
	var $response;
	var $image_type;
	var $uid = 0;
	var $lastpath = '';
	var $fullpath;
	
	var $default_to_users = true;
	var $content_path;
	var $tablename;
	var $primary_key;
	var $image_field;
	
	function __construct($content_path = 'content/users') {
		$this->setMimeTypes();
		$this->response = new JSONRPC_Response();
		$this->response->result = array('thumb' => null, 'message' => '', 'file' => 'thumb.jpg');
		if (isset($_REQUEST['form_prefix']))
			$this->form_prefix = $_REQUEST['form_prefix'];

		if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {
			$this->setFileData();
		} else {
			$this->setError("Invalid Request");
		}
		$this->setContentPath($content_path);
		$this->getUser();
		$this->getAlbum();
		$this->setOverrideName();
		$this->getMaxXY();
		$this->fullpath = getcwd() . "/" . $this->destination;
		$this->checkDir();
		//log_info(print_r($this,true));
	}
	
	function execute() {
		if ($this->isError())
			return;
			
		if ($this->fileNeedsResize())
			$this->resize_file();
		else
			$this->copy_file();
	}
	
	function setMimeTypes() {
		$this->valid_imgtypes = array(
			image_type_to_mime_type(IMAGETYPE_GIF), 
			image_type_to_mime_type(IMAGETYPE_JPEG), 
			image_type_to_mime_type(IMAGETYPE_PNG)
		);
		 
	}
	
	function fileNeedsResize() {
		if (!is_null($this->maxX) && $this->gd_data[0] > $this->maxX)
			return true;
		if (!is_null($this->maxY) && $this->gd_data[1] > $this->maxY)
			return true;
		return false;
	}
	
	function resize_file() {
		$extension = strtolower($this->ext);
		
		$mime = $this->gd_data['mime'];
		
		if($mime == image_type_to_mime_type(IMAGETYPE_JPEG)) {
			$src = imagecreatefromjpeg($this->tmp);
		} else if ($mime == image_type_to_mime_type(IMAGETYPE_PNG)) {
			$src = imagecreatefrompng($this->tmp);
		} else {
			$src = imagecreatefromgif($this->tmp);
		}
		if ($this->gd_data[1] > $this->gd_data[0]) {
			//log_info('Taller');
			$newY = $this->maxY;
			$newX = ($this->gd_data[0] / $this->gd_data[1]) * $newY;
		} else {
			//log_info('Wider');
			$newX = $this->maxX;
			$newY = ($this->gd_data[1] / $this->gd_data[0]) * $newX;
		}
		
		$tmp = imagecreatetruecolor($newX,$newY);
		imagecopyresampled($tmp,$src,0,0,0,0,$newX,$newY, $this->gd_data[0],$this->gd_data[1]);
		
		$image_name = $this->name . "." . $this->ext;
		$file_name = getcwd() . "/" . $this->destination . "/" . $image_name;
		
		imagejpeg($tmp,$file_name,100);
		
		imagedestroy($src);
		imagedestroy($tmp);
		
		$this->setThumb($this->destination,$image_name);
	}
	
	function copy_file() {
		$image_name = $this->name . "." . $this->ext;
		$copy_to_location = getcwd() . "/" . $this->destination . "/" . $image_name;
		if (move_uploaded_file($this->tmp, $copy_to_location)) {
			//log_info("XXXX uploaded file moved to $copy_to_location");
			$this->setThumb($this->destination,$image_name);
		} else {
			$this->setError("File Copy Failed");
		}
	}
	
	function checkDir() {
		if (!is_dir($this->fullpath))
			mkdir($this->fullpath,0777);
	}

	function setUser($username) {
		$this->username = $username;
	}
	
	function setOverrideName() {
		if (isset($_REQUEST[$this->form_prefix . "__force_name"])) {
			$this->name = $_REQUEST[$this->form_prefix . "__force_name"];
		}
	}
	
	function getUser() {
		if (isset($_REQUEST[$this->form_prefix . "__identifier"])) {
			$this->uid = $_REQUEST[$this->form_prefix . "__identifier"];
		}
		if (isset($_REQUEST[$this->form_prefix . "__destination"])) {
			$this->destination = $this->content_path . "/" . $_REQUEST[$this->form_prefix . "__destination"];
			$this->lastpath = $_REQUEST[$this->form_prefix . "__destination"];
		} else {
			$this->destination = $this->content_path;
		}
	}
	
	function setFileDestination($destination) {
		$this->destination = $destination;
	}
	
	function setContentPath($path) {
		$this->content_path = $path;
	}
	
	function getUID() {
		return $this->uid;
	}
	
	function getUserData() {
		return $this->userdata;
	}
	
	function getMaxXY() {
		//log_info(print_r($_REQUEST,true));
		if (isset($_REQUEST[$this->form_prefix . "__maxX"])) {
			$this->maxX = $_REQUEST[$this->form_prefix . "__maxX"];
		}
		if (isset($_REQUEST[$this->form_prefix . "__maxY"])) {
			$this->maxY = $_REQUEST[$this->form_prefix . "__maxY"];
		}
		
		//log_info(print_r($this,true));
	}
	
	function getAlbum() {
		if (isset($_REQUEST[$this->form_prefix . "__album"]) && strstr($_REQUEST[$this->form_prefix . "__album"],".") === false) {
			$this->destination .= "/" . $_REQUEST[$this->form_prefix . "__album"];
		}
	}
	
	function setError($message) {
		$this->response->error = true;
		$this->response->result['message'] = $message;
	}
	
	function setThumb($dest,$file) {
		$thumb = $dest . "/" . $file;
		$this->response->result['thumb'] = $thumb . '?v=' . time();
		$this->response->result['file'] = $file;
	}
	
	function isError() {
		return $this->response->error;
	}
	
	function uploadError() {
		switch ($_FILES[$this->form_prefix . '__uploaded_image']['error']) {
			case 0:
				return false;
			case UPLOAD_ERR_INI_SIZE:
				$this->setError("Image is too large");
				return true;
			case UPLOAD_ERR_FORM_SIZE:
				$this->setError("Image is too large");
				return true;
			case UPLOAD_ERR_PARTIAL:
				$this->setError("Image did not load completely.  Please try again");
				return true;
			case UPLOAD_ERR_NO_FILE:
				$this->setError("No image was uploaded");
				return true;
			case UPLOAD_ERR_NO_TMP_DIR:
				$this->setError("System file error: "  . UPLOAD_ERR_NO_TMP_DIR);
				return true;
			case UPLOAD_ERR_CANT_WRITE:
				$this->setError("System file error: "  . UPLOAD_ERR_CANT_WRITE);
				return true;
			case UPLOAD_ERR_EXTENSION:
				$this->setError("System file error: "  . UPLOAD_ERR_EXTENSION);
				return true;
		}
		return false;
	}
	
	function setFileData() {
		if ($this->uploadError())
			return false;
			
		$this->size = $_FILES[$this->form_prefix . '__uploaded_image']['size'];
		$this->tmp = $_FILES[$this->form_prefix . '__uploaded_image']['tmp_name'];
		$name = $_FILES[$this->form_prefix . '__uploaded_image']['name'];
		$name_parts = explode(".",$name);
		if (count($name_parts) < 2) {
			$this->setError("Invalid file format");
			return false;
		}
		
		$this->ext = $name_parts[count($name_parts) - 1];
		unset($name_parts[count($name_parts) - 1]);
		$this->name = implode(".",$name_parts);
		//$this->image_type = exif_imagetype($_FILES[$this->form_prefix . '__uploaded_image']['tmp_name']);
		$this->gd_data = getimagesize($_FILES[$this->form_prefix . '__uploaded_image']['tmp_name']);
		$this->test();
	}
	
	function test() {
		if ($this->name == '' || is_null($this->name)) {
			$this->setError("No image name set");
			return false;
		}

		if($this->gd_data== false || !in_array(strtolower($this->gd_data['mime']),$this->valid_imgtypes)) {
			$this->setError("Invalid file format");
			return false;
		}
		
	//	if($this->size > (1024*1024)) {
	//		$this->setError("File is too large");
	//		return false;
	//	}
		
		return true;
	}
	
	function getResult() {
		return $this->response;
	}
	
	function printResult() {
		//log_info(json_encode($this->response));
		echo json_encode($this->getResult());
	}
}

?>
