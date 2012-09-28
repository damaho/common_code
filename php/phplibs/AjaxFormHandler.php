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
 * $Id: FieldElements.php 65 2012-09-23 17:00:02Z Dave $
 * 
 * ****************************************************************** */
require_once('Utils.php');

class AjaxFormHandler {
	protected $collection;
	protected $formname;
	protected $primary_key;
	protected $primary_key_value;
	protected $primary_tablename;
	protected $datafields = array();
	protected $manager;
	
	function __construct($collection,$primary_table,$formname,$manager = "") {
		parent::__construct($name);
		
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
			if ($pe_obj instanceof FormHandler) {
				$sub_array = $pe_obj->getDataFields();
				foreach($sub_array as $name => $df) {
					$all_fields[$name] = $df;
				}
			}
		}
		return $all_fields;
	}
	
	function addDataFieldElement($name,$element,$always_send_value = false,$tablename = null) {
		parent::addPageElement($name,$element);
		$fieldname = $element->fieldname;
		$this->datafields[$name] = $tablename;
		$js_send = $always_send_value ? 'true' : 'false';
		$this->addJSBottom($this->manager . ".addDataField('{$this->collection}','{$this->formname}','{$fieldname}',{$js_send});\n");
	}
	
	function rowLevelValidate($formdata,&$result) {
		$datafields = $this->getDataFields();
		foreach ($datafields as $name => $element) {
			$fieldresult = new FormFieldResult();
			if (!array_key_exists($element->fieldname,$formdata)) {
				if ($element->required) {
					$fieldresult->error = true;
					$element->setFieldLevelMessage($element->fieldlabel . " is required");
				}
			} else {
				if (!$element->test($formdata[$element->fieldname]))
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
		$this->rowLevelValidate($p_arguments['formvalues'],$result);
		$sorted_values = $this->sort_submitted_fields($p_arguments['formvalues']);

		if (!$result->error) {
			$sorted_arguments = $this->sort_submitted_fields($p_arguments['formvalues']);
			$this->save($sorted_arguments);
		}
		return $result;
	}
}
?>
