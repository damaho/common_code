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
 * $Id: PageLayout.php 65 2012-09-23 17:00:02Z Dave $
 * 
 * Purpose is to place content into a generic html framework
 * 
 * ****************************************************************** */
require_once 'PageContent.php';
 
class PageLayout extends PageContent {
	private $body_onload = "";
	
	function __construct($title='') {
		parent::__construct($title);
		$this->addJSFileTop("jquery-min.js");
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
	
}

?>
