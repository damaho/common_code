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
 * $Id: PageContent.php 59 2012-09-19 01:17:33Z Dave $
 * 
 * ****************************************************************** */
require_once("Session.php");
require_once("ServiceClasses.php");
require_once("DataObjects.php");
require_once("PageLibs.php");

class PageContent {
	protected $title;
	private $js_files_top = array();
	private $js_files_bottom = array();
	private $css_files = array();
	private $header_css = "";
	private $body_content = "";
	private $js_top = "";
	private $js_bottom = "";
	private $has_menu = true;
	protected $form_handler;
	protected $page_elements = array();
	protected $extra_content = array();  // retrievable, but not output with getXHTML - ex - used to pull out and put in feature section on ksm
	protected $custom_content = array(); // This is content used by the specific build and pulled any way they want it - it gets assimilated but nothing is done with output here
	
	function __construct($title = '') {
		$this->setTitle($title);
	}
	
	public function setTitle($title) {
		log_info("SET TITLE TO $title");
		$this->title = $title;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getXHTML() {
		$xhtml = "";
		$elements = $this->getPageElements();
		foreach ($elements as $name => $element) {
			$xhtml .= $element->getXHTML();
		}
		return $xhtml;
	}
	
	public function getHeaderCSS() {
		return $this->header_css;
	}

	public function addCSSFile($filename) {
		$this->css_files[$filename] = true;  // todo - might we add more here?
	}
	
	public function addHeaderCSS($css) {
		$this->header_css .= $css;
	}
	
	public function addJSTop($js) {
		$this->js_top .= $js;
	}
	
	public function addJSBottom($js) {
		$this->js_bottom .= $js;
	}

	public function addJSFileTop($filename) {
		$this->js_files_top[$filename] = true;
	}
	
	public function addJSFileBottom($filename) {
		$this->js_files_bottom[$filename] = true;
	}
	
	public function addPageElement($name,$element) {
		if (!($element instanceof PageContent))
			return;
		$this->page_elements[$name] = $element;
	}
	
	public function addExtraContent($name,$element) {
		if (!($element instanceof PageContent))
			return;
		$this->extra_content[$name] = $element;
	}
	
	public function addCustomContent($name,$element) {
		if (!($element instanceof PageContent))
			return;
		$this->custom_content[$name] = $element;
	}
	
	public function getCustomContent() {
		return $this->custom_content;
	}
	
	public function removePageElement($name) {
		if (isset($this->page_elements[$name]))
			unset($this->page_elements[$name]);
	}

	public function removeExtraContent($name) {
		if (isset($this->extra_content[$name])) {
			unset($this->extra_content[$name]);
		}
	}

	public function setBodyContent($content,$replace = false) {
		if ($replace)
			$this->body_content = "";
		$this->body_content .= $content;
	}
	
	public function getBodyContent() {
		return $this->body_content;
	}
	
	public function getCSSFiles() {
		return $this->css_files;
	}
	
	public function getJSFilesTop() {
		return $this->js_files_top;
	}
	
	public function getJSFilesBottom() {
		return $this->js_files_bottom;
	}

	public function getJSTop() {
		return $this->js_top;
	}
	
	public function getJSBottom() {
		return $this->js_bottom;
	}
	
	public function getPageElements() {
		return $this->page_elements;
	}
	
	public function getExtraContent() {
		return $this->extra_content;
	}
	
	public function getPageElement($name) {
		if (isset($this->page_elements[$name]))
			return $this->page_elements[$name];
	}
	
	public function setHasMenu($setting) {
		$this->has_menu = $setting;
	}
	
	public function hasMenu() {
		return $this->has_menu;
	}
	
	function assimilatePageElement($obj) {
		$obj->execute();
		$js_files_top = $obj->getJSFilesTop();
		$js_files_bottom = $obj->getJSFilesBottom();
		$js_top = $obj->getJSTop();
		$js_bottom = $obj->getJSBottom();
		$css_files = $obj->getCSSFiles();
		
		foreach ($js_files_top as $filename => $data) {
			$this->addJSFileTop($filename);
		}
		foreach ($js_files_bottom as $filename => $data) {
			$this->addJSFileBottom($filename);
		}
		foreach ($css_files as $filename => $data) {
			$this->addCSSFile($filename);
		}
		$this->addHeaderCSS($obj->getHeaderCSS());
		$this->addJSTop($js_top);
		$this->addJSBottom($js_bottom);
	}

	function execute() {
		foreach ($this->page_elements as $name => $obj) {
			$this->assimilatePageElement($obj);
		}
		foreach ($this->extra_content as $name => $obj) {
			$this->assimilatePageElement($obj);
		}
		// TODO - protected assimilateCustomContent method - makes this more flexible (allows nesting)
		foreach ($this->custom_content as $name => $obj) {
			$this->assimilatePageElement($obj);
		}
	}

}
?>
