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
 * $Id: common_objects.php 78 2013-01-26 15:58:07Z Dave $
 * 
* ****************************************************************** */
require_once ('PageContent.php');
require_once ('FieldElements.php');
require_once ('Utils.php');

class noObject extends PageContent {
	function getXHTML() {
		return "";
	}
}

class common__adspace extends PageContent {
	var $id;
	var $class = '';
	
	function __construct($id,$class) {
		$this->id = $id;
		$this->class = $class;
		parent::__construct('',0);
	}
	
	function getXHTML() {
		$xhtml = "";
		$xhtml .= "<div id='common__adspace__{$this->id}' class='common_adspace ad_space {$this->class} nodisplay '>\n";
		$xhtml .= "your ad here";
		$xhtml .= "</div>\n";
		return $xhtml;
	}
}

class common_block_list_container extends PageContent {
	var $id = '';
	
	function __construct($id) {
		parent::__construct($id);
		$this->id = $id;
	}
	
	function getXHTML() {
		$xhtml = "";
		$xhtml .= "<div id='list_container__{$this->id}__main'></div>\n";
		return $xhtml;
	}
}

class common_data_control_element extends PageContent {
	var $text;
	var $action;
	var $onclick;
	var $class;
	var $id = '';
	var $inner_id = '';
	
	function __construct($id,$text,$action,$onclick,$class='',$inner_id='') {
		$this->id = $id;
		$this->text = $text;
		$this->action=$action;
		$this->onclick = $onclick;
		$this->class=$class;
		$this->setInnerId($inner_id);
		parent::__construct($text);
	}
	
	function getXHTML() {
		$xhtml = "";
		$xhtml .= "<a id='control__{$this->inner_id}__{$this->id}' href='{$this->action}' class='{$this->class}' onclick='{$this->onclick}'>{$this->text}</a>";
		return $xhtml;
	}
	
	function setInnerId($id) {
		$this->inner_id = $id;
	}
}

class common_data_block extends PageContent {
	protected $id='';
	protected $headline = '';
	protected $wrapper_class = '';
	protected $elements = array('info' => array(), 'notes' => array(), 'control' => array());
	public $small;
	
	function __construct($id, $headline,$small=false) {
		$this->id = $id;
		$this->headline = $headline;
		$this->small=$small;
	}
	
	function addElement($type,$element) {
		if (isset($this->elements[$type]))
			$this->elements[$type][] = $element;
	}
	
	function addInfoElement($element) {
		$this->addElement('info',$element);
	}
	
	function addNoteElement($element) {
		$this->addElement('notes',$element);
	}
	
	function addControlElement($element) {
		$this->addElement('control',$element);
	}
	
	function getImageXHTML() {
		$small='';
		if ($this->small)
			$small .= '_small';
		$xhtml = "";
		$xhtml .= "<div id='wrapper__{$this->id}__image' class='block_element block_wrapper block_element_image_wrapper{$small}'>\n";
		$xhtml .= "<img id='image__{$this->id}__image' src='' alt='{$this->headline}' class='block_element_image' />\n";
		$xhtml .= "</div>\n";
		return $xhtml;
	}
	
	function getInfoXHTML() {
		$xhtml = "";
		$xhtml .= "<div id='wrapper__{$this->id}__info' class='block_element block_wrapper block_element_info_wrapper'>\n";
		$xhtml .= "<div id='header__{$this->id}__info' class='block_header'>{$this->headline}</div>\n";
		foreach ($this->elements['info'] as $element) {
			$xhtml .= $element->getXHTML();
		}
		$xhtml .= "</div>\n";
		return $xhtml;
	}
	
	function getNotesXHTML() {
		$xhtml = "";
		$xhtml .= "<div id='wrapper__{$this->id}__notes' class='block_element block_wrapper block_element_notes_wrapper'>\n";
		foreach ($this->elements['notes'] as $element) {
			$xhtml .= $element->getXHTML();
		}
		$xhtml .= "</div>\n";
		return $xhtml;
	}
	
	function getControlXHTML() {
		$xhtml = "";
		$xhtml .= "<div id='wrapper__{$this->id}__control' class='block_element block_wrapper block_element_control_wrapper'>\n";
		foreach ($this->elements['control'] as $element) {
			$xhtml .= $element->getXHTML();
		}
		$xhtml .= "</div>\n";
		return $xhtml;
	}
	
	function getXHTML() {
		$xhtml = "";
		$xhtml .= "<div id='wrapper__{$this->id}__main' class='common_data_block_wrapper block_wrapper {$this->wrapper_class}'>\n";
		$xhtml .= $this->getImageXHTML();
		$xhtml .= $this->getInfoXHTML();
		$xhtml .= $this->getNotesXHTML();
		$xhtml .= $this->getControlXHTML();
		$xhtml .= "<div style='clear:both'></div>\n";
		$xhtml .= "</div>\n";
		return $xhtml;
	}
}

class common_clone_block extends common_data_block {
	function __construct($id,$small=false) {
		parent::__construct($id . '_hidden','',$small);
		$this->wrapper_class = 'nodisplay';
	}
}

?>
