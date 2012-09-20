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
require_once ('PageLayout.php');

define ('CONTENT_TAG','pagecontent');

class IndexedLayout extends PageLayout {
	protected $content_obj;
	
	function __construct($title = "") {
		parent::__construct($title);
		$this->get_request();
	}
	
	protected function get_request() {
		if (isset($_REQUEST[CONTENT_TAG]))
			$this->add_content($_REQUEST[CONTENT_TAG]);
	}
	
	protected function add_content($name, $arg='') {
		if (!file_exists("{$name}.php"))
			return;

		ob_start();

		include_once ("{$name}.php");
		
		if (class_exists($name)) {
			$obj = new $name($arg);
			if ($obj instanceof PageContent) {
				$body_content = $obj->getBodyContent();
				$js_files_top = $obj->getJSFilesTop();
				$js_files_bottom = $obj->getJSFilesBottom();
				$js_top = $obj->getJSTop();
				$js_bottom = $obj->getJSBottom();
				$css_files = $obj->getCSSFiles();
				$content = $obj->getBodyContent();
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
				$this->setBodyContent($content);

				if (!$obj->hasMenu())
					$this->setHasMenu(false);
				
				$parent_title = $this->getTitle();
				$title = $obj->getTitle();
				if ($title != "") {
					if ($parent_title != "")
						$title = $parent_title . " :: " . $title;
					$this->setTitle($title);
				}
			} else {
				$content = ob_get_contents();
				$this->setBodyContent($content);
			}
		} else {
			$content = ob_get_contents();
			$this->setBodyContent($content);
		}

		ob_end_clean();
	}
}

?>
