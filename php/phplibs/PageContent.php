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
 * $Id: PageContent.php 78 2013-01-26 15:58:07Z Dave $
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
	public $form_action = "index.php";
	public $form_enctype = null;
	public $form_onsubmit = "function() {return false}";
	
	// TODO - following attr are for form handling - need to be moved to separate object - I suggest AjaxFormHandler
	protected $collection;
	public $form_name = '';  // TODO: replace all instances of formname with form_name
	protected $primary_key;
	protected $primary_key_value;
	protected $primary_tablename;
	protected $secondary_tables = array();
	protected $datafields = array();
	protected $datafield_translation = array();
	protected $manager;
	public $is_tabbed = false;
	public $tab_class = "tabber";
	public $save_button = null;
	public $tabs = array();
	public $use_table_prefixes = false;
	
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
	
	public function canShow($element) {
		return true;
	}
	
	public function getPageElementXHTML($name) {
		if (!array_key_exists($name,$this->page_elements) || !$this->canShow($this->page_elements[$name]))
			return '';
		return $this->page_elements[$name]->getXHTML();
	}
	
	public function getXHTML() {
		$xhtml = "";
		$elements = $this->getPageElements();
		if ($this->is_form)
			$xhtml .= $this->startFormXHTML();
		foreach ($elements as $name => $element) {
			
			if (!$this->canShow($element) || isset($this->tabs[$name]))
				continue;
			$xhtml .= $element->getXHTML();
		}
		if ($this->is_tabbed) {
			$xhtml .= "<div class='{$this->tab_class}'>\n";
			foreach ($this->tabs as $name => $ditto) {
				if (!$this->canShow($this->page_elements[$name]))
					continue;
				$xhtml .= "<div class='tabbertab'>\n";
				$xhtml .= " <h3>{$this->page_elements[$name]->title}</h3>\n";
				$xhtml .= "<div>\n";
				$xhtml .= $this->page_elements[$name]->getXHTML();
				$xhtml .= "</div>\n";
				$xhtml .= "</div>\n";
			}
			$xhtml .= "</div>\n";
		}
		
		if (!is_null($this->save_button)) {
			require_once("FieldElements.php");
			$save_button = new ButtonField('save_settings','Save');
			$xhtml .= "<div class='padder' style='text-align:center;'>\n";
			$xhtml .= $save_button->getXHTML();
			$xhtml .= "<div id='{$this->form_name}__status' class='form_status'></div>\n";
			$xhtml .= "</div>\n";
		}
		if ($this->is_form)
			$xhtml .= $this->finishFormXHTML();
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
	
	public function addTabbedPageElement($name,$element) {
		$this->setTabbed(true);
		$this->tabs[$name] = $name;
		$this->addPageElement($name,$element);
	}
	
	public function setSaveButton() {
		$this->save_button = true;
	}
	
	public function initPageContent() {}
	
	public function addPageElement($name,$element) {
		if (!($element instanceof PageContent))
			return;
		$this->page_elements[$name] = $element;
		$this->page_elements[$name]->initPageContent();
	}
	
	public function addExtraContent($name,$element) {
		if (!($element instanceof PageContent))
			return;
		$this->extra_content[$name] = $element;
		$this->extra_content[$name]->initPageContent();
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
	
	public function getFormName() {
		return $this->form_name;
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
		if (!is_object($obj) || !$this->canShow($obj))
			return false;
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
	
	function startFormXHTML() {
		$enctype = is_null($this->form_enctype) ? "" : "enctype=\"{$this->form_enctype}\"";
		//return "<form id='{$this->form_name}' name='{$this->form_name}' $enctype action='{$this->form_action}' method='post' onsubmit='{$this->form_onsubmit}'>\n";
		return "<form id='{$this->form_name}' $enctype action='{$this->form_action}' method='post' onsubmit='{$this->form_onsubmit}'>\n";
	}


	function finishFormXHTML() {
		return "</form>\n";
	}

	// TODO - The following are form handling functions.  They need to be handled in a seperate object, but I don't have time to do that now
	function setAsForm($collection,$primary_table,$formname,$manager = "",$parent_collection = null) {
		$this->is_form = true;
		$this->manager = $manager;
		$this->primary_tablename = $primary_table;
		$this->collection = $collection;
		$this->form_name = $formname;
		if (!is_null($this->primary_key) && !isset($this->datafields[$this->primary_key]))
			$this->addDataFieldElement($this->primary_key,new HiddenField($this->primary_key,$this->primary_key_value),true,$this->primary_tablename, $parent_collection);
		$this->initDataFields();
		
		if (!isset($this->datafields[$this->primary_key]))
			$this->addDataFieldElement($this->primary_key,new HiddenField($this->primary_key,0),true,$this->primary_tablename,$parent_collection);
	}
	
	
	
	function setTabbed($tabbed=true) {
		$this->is_tabbed = $tabbed;
	}
	
	function isTabbed() {
		return $this->is_tabbed;
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
	
	function addSecondaryTable($tablename, $primary_key=null) {
		$this->secondary_tables[$tablename] = $primary_key;
	}
	
	function addDataFieldElement($name,$element,$always_send_value = false,$tablename = null,$collection_name = null) {
		require_once("FieldElements.php");
		$collection_name = is_null($collection_name) ? $this->collection : $collection_name;
		$tablename = is_null($tablename) ? $this->primary_tablename : $tablename;  // TODO - WATCH THIS - It may be that a null tablename wants to mean just a field to nowhere
		$this->addPageElement($name,$element);
		$fieldname = $element->fieldname;
		$this->datafields[$name] = $tablename;
		if (!$always_send_value)
			$always_send_value = $element->always_send;
		$js_send = $always_send_value ? 'true' : 'false';
		$default_value = is_numeric($element->default_value) ? $element->default_value : "'" . $element->default_value . "'";
		if (is_null($default_value))
			$default_value = 'null';
		$this->addJSBottom($this->manager . ".addDataField('{$collection_name}','{$this->form_name}','{$fieldname}',{$js_send}, {$default_value});\n");
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
		$result = array(
			'insert_id' => null,
			'errno' => 0,
			'error' => ''
		);
		$dob = new DataObject();
		$dob->insertUpdate($qry);
		$result['insert_id'] = $dob->getLastId();
		$result['errno'] = $dob->get_db_errno();
		$result['error'] = $dob->get_db_error();
		return $result;
	}
	
	function getLoadQuery($p_arguments) {
		$key_value = isset($p_arguments[$this->primary_key]) ? $p_arguments[$this->primary_key] : 0;
		$qry = "SELECT ";
		$qry .= "* ";
		$qry .= "FROM {$this->primary_tablename} ";
		$qry .= "WHERE {$this->primary_key} = '" . genericEscape($key_value) . " '";
		return $qry;
	}
	
	function getUpdateQuery($p_arguments, $table = null) {
		if (is_null($table) || $table == $this->primary_tablename) {
			$table = $this->primary_tablename;
			$primary_key = $this->primary_key;
		} else {
			if (!isset($this->secondary_tables[$table]))
				return false;
			$primary_key = $this->secondary_tables[$table];
		}
		if (is_null($primary_key) && isset($p_arguments[$table][$this->primary_key])) {
			$primary_key = $this->primary_key;
		}
		$qry = "UPDATE {$table} SET ";
		$set = "";
		$where = "WHERE {$primary_key} = '" . genericEscape($p_arguments[$table][$primary_key]) . "' ";
		unset($p_arguments[$table][$primary_key]);
		if (count($p_arguments[$table]) == 0)
			return;
		foreach ($p_arguments[$table] as $fieldname => $fieldvalue) {
			if ($set != "")
				$set .= ",";
			$set .= $fieldname . " = '" . genericEscape($fieldvalue) . "' ";
			
		}
		$qry .= $set;
		$qry .= $where;
		return $qry;
	}
	
	function getInsertQuery($p_arguments, $table = null) {
		if (is_null($table) || $table == $this->primary_tablename) {
			$table = $this->primary_tablename;
			$primary_key = $this->primary_key;
		} else {
			if (!array_key_exists($table,$this->secondary_tables))
				return false;
			$primary_key = $this->secondary_tables[$table];
		}
		
		$fld = "";
		$ins = "";
		
		foreach ($p_arguments[$table] as $fieldname => $fieldvalue) {
			if ($fieldname == $primary_key)
				continue;
			if ($fld != "")
				$fld .= ",";
			if ($ins != "")
				$ins .= ",";
			$fld .= $fieldname;
			$ins .= "'" . genericEscape($fieldvalue) . "'";
		}
		$qry = "INSERT INTO {$table} ($fld) VALUES ({$ins})";
		return $qry;
	}
	
	function LOAD($p_arguments) {
		$response = new GenericResult();
		if (!isset($p_arguments[$this->primary_key])) {
			$response->error = true;
			return $response;
		}
		$dob = new DataObject($this->tablename);
		$qry = $this->getLoadQuery($p_arguments[$this->primary_key]);
		$dob->setQuery($qry);
		if ($dob->fetch(MYSQL_ASSOC)) {
			$row = $dob->getRowAsArray();
			$response->type = 'content_load';
			$response->data = $row;
			$response->error = false;
		}
		return $response;
	}
		
	function UPDATE($p_arguments) {
		$response = new GenericResult();
		$response->type='update_result';
		$this->updateHook($p_arguments);
		$qry = $this->getUpdateQuery($p_arguments,$this->primary_tablename);
		$result = $this->callUpdateQuery($qry);
		if ($result['errno'] > 0) {
			$response->error = true;
			$response->message = $result['error'];
		} else {
			$insert_uid = isset($p_arguments[$this->primary_tablename][$this->primary_key]) ? $p_arguments[$this->primary_tablename][$this->primary_key] : $result['insert_id'];
			$response->id = $insert_uid;
			foreach ($this->secondary_tables as $tablename => $primary_key) {
				if (isset($p_arguments[$tablename])) {
					if (!isset($p_arguments[$tablename][$this->primary_key]))
						$p_arguments[$tablename][$this->primary_key] = $insert_uid;
					$qry = $this->getUpdateQuery($p_arguments,$tablename);
					$result = $this->callUpdateQuery($qry);
				}
			}
			$response->message = 'Changes Accepted';
		}
		return $response;
	}
	
	function INSERT($p_arguments) {
		$response = new GenericResult();
		$response->type='insert_result';
		$this->insertHook($p_arguments);
		$qry = $this->getInsertQuery($p_arguments,$this->primary_tablename);
		$result = $this->callUpdateQuery($qry);
		if ($result['errno'] > 0) {
			$response->error = true;
			$response->message = $result['error'];
		} else {
			$insert_uid = $result['insert_id'];
			$response->id = $insert_uid;
			foreach ($this->secondary_tables as $tablename => $primary_key) {
				if (isset($p_arguments[$tablename])) {
					if (!isset($p_arguments[$tablename][$this->primary_key]))
						$p_arguments[$tablename][$this->primary_key] = $insert_uid;
					$qry = $this->getInsertQuery($p_arguments,$tablename);
					$result = $this->callUpdateQuery($qry);
				}
			}
			$response->message = 'Changes Accepted';
		}
		
		return $response;
	}
	
	function updateHook(&$p_arguments) {}
	
	function insertHook(&$p_arguments) {}
	
	function save($sorted_arguments, $mode = null) {
		if (!isset($sorted_arguments[$this->primary_tablename]))
			return false;
			
		if (is_null($mode) && (!isset($sorted_arguments[$this->primary_tablename][$this->primary_key]) || $sorted_arguments[$this->primary_tablename][$this->primary_key] == -1))
			$mode = "NEW";
		
		if ($mode == "NEW") {
			return $this->INSERT($sorted_arguments);
		} else {
			return $this->UPDATE($sorted_arguments);
		}
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
	
	function mergeSortedResults($table_values, &$sorted_results) {
		foreach ($table_values as $tablename => $fieldvalues) {
			if (!isset($sorted_results[$tablename]))
				$sorted_results[$tablename] = array();
			foreach ($fieldvalues as $key => $val) {
				$sorted_results[$tablename][$key] = $val;
			}
		}
	}
	
	function getNavigationData($p_arguments) {
		$response = array();
		if (isset($p_arguments[$this->primary_key]) && !is_null($this->primary_tablename) && !is_null($this->primary_key)) {
			$dob = new DataObject($this->primary_tablename);
			$uid = $dob->escapeStr($p_arguments[$this->primary_key]);
			$qry = "SELECT * ";
			$qry .= "FROM {$this->primary_tablename} ";
			$qry .= "WHERE {$this->primary_key} = '{$uid}' ";
			$qry .= "LIMIT 1 ";
			$dob->setQuery($qry);
			if ($dob->fetch(MYSQL_ASSOC))
				$response = $dob->getRowAsArray();
		}
		return $response;
	}
	
	function NAVIGATE($p_arguments) {
		$result = new GenericResult();
		$result->type='navigation_response';
		$result->data = $this->getNavigationData($p_arguments);
		return $result;
	}
	
	function FORMSUBMIT($p_arguments) {
		$result = new GenericResult();
		$result->data['collection'] = $this->collection;
		$result->data['form_name'] = $this->form_name;
		$result->data['fieldresults'] = array();
		$result->data['sorted_results'] = array();
		$this->rowLevelValidate($p_arguments['formvalues'],$result,$p_arguments['mode']);
		$sorted_values = $this->sort_submitted_fields($p_arguments['formvalues']);

		if (!$result->error) {
			$sorted_arguments = $this->sort_submitted_fields($p_arguments['formvalues']);
			$result = $this->save($sorted_arguments,$p_arguments['mode']);
			$result->data['sorted_results'] = array('table_values' => $sorted_arguments);
		} else {
			$result->type='submit_error';
		}
		return $result;
	}
	
	function MULTIFORMSUBMIT($p_arguments) {
		// expects { mode: mode, data: data }
		$result = new GenericResult();
		$result->type='multi_submit_response';
		$result->data['fieldresults'] = array();
		$mode = $p_arguments['mode'];
		$form_data = $p_arguments['data'];
		$sorted_results =  array();
		foreach ($this->page_elements as $name => &$element) {
			if (isset($form_data[$element->getFormName()])) {
				$form_name = $element->getFormName();
				$form_result = $element->FORMSUBMIT(array('mode' => $mode, 'formvalues' => $form_data[$form_name]));
				$result->data['fieldresults'][$form_name] = $form_result->data['fieldresults'];
				$this->mergeSortedResults($form_result->data['sorted_results']['table_values'],$sorted_results);
				if ($form_result->error) {
					$result->error = true;
					$result->message = $form_result->message;
				}
			}
		}
		$result->data['sorted_results'] = $sorted_results;
		if (!$result->error) {
			$update_result = $this->save($sorted_results,$mode);
			
			$result->id = $update_result->id;
			if ($update_result->error) {
				$result->error = true;
			}
			$result->message = $update_result->message;
		}
		return $result;
	}
}
?>
