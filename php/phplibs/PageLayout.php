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
 * $Id: PageLayout.php 78 2013-01-26 15:58:07Z Dave $
 * 
 * Purpose is to place content into a generic html framework
 * 
 * ****************************************************************** */
require_once 'PageContent.php';
 
class PageLayout extends PageContent {
	private $body_onload = "";
	private $content_tag = 'pagecontent';
	private $default_content = 'Home';
	protected $pagecontent_title = '';
	private $site_jsmanager = null;
	
	function __construct($title='') {
		parent::__construct($title);
		$this->addJSFileTop("jquery-min.js");
		//$this->addJSFileTop("jquery-ui.min.js");
		$this->addCSSFile("common.css");
		$this->addJSFileTop("PageLayout.js");
	}
	
	
	public function preXHTML() {
		return "";
	}
	
	public function postXHTML() {
		return "";
	}
	
	public function getXHTML() {
		$p = new PageLibs();
		$xhtml = $p->get_start_html();
		$xhtml .= $p->get_head_start();
		$xhtml .= "<title>{$this->title}</title>\n";
		$css_files = $this->getCSSFiles();
		foreach ($css_files as $filename => $data) {
			$xhtml .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$filename\" />\n";
		}
		
		$js_files = $this->getJSFilesTop();
		foreach ($js_files as $filename => $data) {
			$xhtml .= "<script type=\"text/javascript\" src=\"$filename\"></script>\n";
		}
		if ($this->getJSTop() != "" || $this->getJSTopOuter() != "") {
			$xhtml .= "<script type='text/javascript'>\n";
			$xhtml .= $this->getJSTopOuter();
			$xhtml .= "$(function() {\n";
			$xhtml .= $this->getJSTop();
			$xhtml .= "});\n";
			$xhtml .= "</script>\n";
		}
		
		if ($this->getHeaderCSS() != "") {
			$xhtml .= "<style type=\"text/css\">\n";
			$xhtml .= $this->getHeaderCSS();
			$xhtml .= "</style>\n";
		}
		
		$xhtml .= $p->get_head_end();
		$xhtml .= $p->get_body_start($this->body_onload);
		$xhtml .= $this->preXHTML();
		$elements = $this->getPageElements();
		foreach ($elements as $name => $element) {
			$xhtml .= $element->getXHTML();
		}
		$xhtml .= $this->getBodyContent();
		
		$xhtml .= $this->postXHTML();
		$js_files = $this->getJSFilesBottom();
		foreach ($js_files as $filename => $data) {
			$xhtml .= "<script type=\"text/javascript\" src=\"$filename\"></script>\n";
		}
		if ($this->getJSBottom() != "") {
			$xhtml .= "<script type='text/javascript'>\n";
			$xhtml .= "$(function() {\n";
			$xhtml .= $this->getJSBottom();
			$xhtml .= "});\n";
			$xhtml .= "</script>\n";
		}
		
		$xhtml .= $p->get_body_end();
		$xhtml .= $p->get_end_html();
		
		return $xhtml;
	}
	
	protected function setCollection($collection_name) {} /* IMPLEMENT ME */
	
	protected function updateMenus($content_name) {} /* IMPLEMENT ME */
	
	public function setContentTitle($title) {
		$this->pagecontent_title = $title;
	}
	
	protected function registerLocation($name) {
		if (!isset($_SESSION['current_collection']) || $name != $_SESSION['current_collection']) {
			if (!isset($_SESSION['current_collection']))
				$_SESSION['current_collection'] = $name;
			$_SESSION['last_collection'] = $_SESSION['current_collection'];
			$_SESSION['current_collection'] = $name;
		}
	}

	protected function get_request() {
		if (isset($_REQUEST[$this->content_tag]))
			$this->add_content($_REQUEST[$this->content_tag]);
		else
			$this->add_content($this->default_content);
	}
	
	protected function add_from_buffer() {
		$new_element = new PageContent();
		$content = ob_get_contents();
		$new_element->setBodyContent($content);
		$this->addPageElement('',$new_element);
	}
	
	protected function add_content($name, $arg='') {
		if (!file_exists("{$name}.php"))
			return;

		ob_start();

		include_once ("{$name}.php");
		
		$this->updateMenus($name);  // menu has a state where the active collection is styled differently
		$this->registerLocation($name);
		if (class_exists($name)) {
			$obj = new $name($arg);
			$this->setCollection($name);
			if ($obj instanceof PageContent) {
				if (!$obj->hasMenu())
					$this->setHasMenu(false);
				if (property_exists($name,'show_header_title'))  {
					$this->show_main_content_title = $obj->show_header_title;
				}
				$parent_title = $this->getTitle();
				$title = $obj->getTitle();
				$args = $obj->formatLoadRequest();
				
				if (!is_null($this->site_jsmanager) && $args !== false) {
					$this->addJSBottom("{$this->site_jsmanager}.loadCollection('{$name}',{$args});\n");
				}

				if ($title != "") {
					if ($parent_title != "") {
						$this->setContentTitle($title);
						$title = $parent_title . " :: " . $title;
						$this->setTitle($title);
					}
				}
				$this->addPageElement($title,$obj);
				$this->current_id = $title;
			} else {
				$this->add_from_buffer();
			}
		} else {
			$this->add_from_buffer();
		}

		ob_end_clean();
	}
}

?>
