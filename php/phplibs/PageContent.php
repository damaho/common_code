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
 * $Id: PageContent.php 72 2012-10-11 13:16:19Z Dave $
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
	private $js_top_outer = "";
	private $js_bottom = "";
	private $has_menu = true;
	protected $form_handler;
	protected $page_elements = array();
	protected $extra_content = array();  // retrievable, but not output with getXHTML - ex - used to pull out and put in feature section on ksm
	protected $custom_content = array(); // This is content used by the specific build and pulled any way they want it - it gets assimilated but nothing is done with output here
	public $is_form = false;
	
	// TODO - following attr are for form handling - need to be moved to separate object - I suggest AjaxFormHandler
	protected $collection;
	protected $formname;
	protected $primary_key;
	protected $primary_key_value;
	protected $primary_tablename;
	protected $datafields = array();
	protected $datafield_translation = array();
	protected $manager;
	
	
	function __construct($title = '') {
		$this->setTitle($title);
	}
	
	public function setTitle($title) {
		//log_info("SET TITLE TO $title");
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
	
	public function addJSTopOuter($js) {
		$this->js_top_outer .= $js;
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
	
	public function getJSTopOuter() {
		return $this->js_top_outer;
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
	
	function formatLoadRequest() {
		return false;
	}
	
	function assimilatePageElement($obj) {
		$obj->execute();
		$js_files_top = $obj->getJSFilesTop();
		$js_files_bottom = $obj->getJSFilesBottom();
		$js_top_outer = $obj->getJSTopOuter();
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
		$this->addJSTopOuter($js_top_outer);
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


	// TODO - The following are form handling functions.  They need to be handled in a seperate object, but I don't have time to do that now
	function setAsForm($collection,$primary_table,$formname,$manager = "") {
		$this->is_form = true;
		$this->manager = $manager;
		$this->primary_tablename = $primary_table;
		$this->collection = $collection;
		$this->formname = $formname;
		if (!is_null($this->primary_key) && !isset($this->datafields[$this->primary_key]))
			$this->addDataFieldElement($this->primary_key,new HiddenField($this->primary_key,$this->primary_key_value),true,$this->primary_tablename);
		$this->initDataFields();
	}
	
	function initDataFields() {}
	
	function getDataFields() {
		$all_fields = array();
		foreach ($this->datafields as $name => $table) {
			$el = $this->getPageElement($name);
			$el->tablename = $table;
			$all_fields[$name] = $el;
		}
		foreach ($this->page_elements as $pe_name => $pe_obj) {
			if ($pe_obj instanceof PageContent && $pe_obj->is_form) {
				$sub_array = $pe_obj->getDataFields();
				foreach($sub_array as $name => $df) {
					$all_fields[$name] = $df;
				}
			}
		}
		return $all_fields;
	}
	
	function addDataFieldElement($name,$element,$always_send_value = false,$tablename = null) {
		require_once("FieldElements.php");
		$this->addPageElement($name,$element);
		$fieldname = $element->fieldname;
		$this->datafields[$name] = $tablename;
		if (!$always_send_value)
			$always_send_value = $element->always_send;
		$js_send = $always_send_value ? 'true' : 'false';
		$this->addJSBottom($this->manager . ".addDataField('{$this->collection}','{$this->formname}','{$fieldname}',{$js_send});\n");
	}
	
	function rowLevelValidate($formdata,&$result,$mode) {
		$datafields = $this->getDataFields();
		
		foreach ($datafields as $name => $element) {
			$fieldresult = new FormFieldResult();
			if (!array_key_exists($element->fieldname,$formdata) && $mode == 'NEW') {
				if ($element->required) {
					$fieldresult->error = true;
					$element->setFieldLevelMessage($element->fieldlabel . " is required");
				}
			} else {
				if (isset($formdata[$element->fieldname]) && !$element->test($formdata[$element->fieldname]))
					$fieldresult->error = true;
			}
			$fieldresult->message = $element->getFieldLevelMessage();
			if ($fieldresult->error) {
				$result->message = "Form contained errors";
				$result->error = true;
			}
			$result->data['fieldresults'][$element->fieldname] = $fieldresult;
		}
	}
	
	function callUpdateQuery($qry) {
		$dob = new DataObject();
		$dob->insertUpdate($qry);
		return $dob->getLastId();
	}
	
	function UPDATE($p_arguments) {
		$this->updateHook($p_arguments);
		$qry = "UPDATE {$this->primary_tablename} SET ";
		$set = "";
		$where = "WHERE {$this->primary_key} = '" . genericEscape($p_arguments[$this->primary_tablename][$this->primary_key]) . "' ";
		unset($p_arguments[$this->primary_tablename][$this->primary_key]);
		if (count($p_arguments[$this->primary_tablename]) == 0)
			return;
		foreach ($p_arguments[$this->primary_tablename] as $fieldname => $fieldvalue) {
			if ($set != "")
				$set .= ",";
			$set .= $fieldname . " = '" . genericEscape($fieldvalue) . "' ";
			
		}
		$qry .= $set;
		$qry .= $where;
		$insert_uid = $this->callUpdateQuery($qry);
		return $insert_uid;
	}
	
	function INSERT($p_arguments) {
		$this->insertHook($p_arguments);
		unset($p_arguments[$this->primary_tablename][$this->primary_key]);
		$qry = "INSERT INTO {$this->primary_tablename} ";
		$qry .= "(" . implode(',',array_keys($p_arguments[$this->primary_tablename])) . ") VALUES (";
		$ins = "";
		foreach ($p_arguments[$this->primary_tablename] as $fieldname => $fieldvalue) {
			if ($ins != "")
				$ins .= ",";
			$ins .= "'" . genericEscape($fieldvalue) . "'";
		}
		$qry .= $ins;
		$qry .= ")";
		$insert_uid = $this->callUpdateQuery($qry);
		return $insert_uid;
	}
	
	function updateHook(&$p_arguments) {}
	
	function insertHook(&$p_arguments) {}
	
	function save($sorted_arguments) {
		// convert to real fields
		
		if (isset($sorted_arguments[$this->primary_tablename])) {
			if (!isset($sorted_arguments[$this->primary_tablename][$this->primary_key]) || $sorted_arguments[$this->primary_tablename][$this->primary_key] == -1) {
				$this->INSERT($sorted_arguments);
			} else {
				$this->UPDATE($sorted_arguments);
			}
		}
		// otehrwise do nothing unless using a custom save method
	}
	
	function sort_submitted_fields($p_arguments) {
		$datafields = $this->getDataFields();
		$sorted_fields = array();
		foreach ($datafields as $name => $element) {
			if (is_null($element->tablename))
				continue;
			if (!isset($p_arguments[$element->fieldname]))
				continue;
			if (!isset($sorted_fields[$element->tablename]))
				$sorted_fields[$element->tablename] = array();
			$sorted_fields[$element->tablename][$name] = $p_arguments[$element->fieldname];
		}
		return $sorted_fields;
	}
	
	function FORMSUBMIT($p_arguments) {
		$result = new GenericResult();
		$result->data['collection'] = $this->collection;
		$result->data['form_name'] = $this->formname;
		$result->data['fieldresults'] = array();
		
		$this->rowLevelValidate($p_arguments['formvalues'],$result,$p_arguments['mode']);
		$sorted_values = $this->sort_submitted_fields($p_arguments['formvalues']);

		if (!$result->error) {
			$sorted_arguments = $this->sort_submitted_fields($p_arguments['formvalues']);
			
			$this->save($sorted_arguments);
		}
		return $result;
	}
}
?>
