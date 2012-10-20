<?php
/* *********************************************************************
 * Copyright (C) 2012 Dave Horn
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
 * $Id: PageFormManager.php 73 2012-10-20 22:29:18Z Dave $
 * 
 * ****************************************************************** */
require_once ('PageContent.php');
require_once ('FieldElements.php');

class PageFormManager extends PageContent {
	var $data_fields = array();
	
	function __construct($formname,$manager,$title='') {
		$this->form_name = $formname;
		$this->manager = $manager;
		parent::__construct($title);
	}
	
	function addDataField($collection_name,$fieldname,$element,$tablename=null,$send_always=false) {
		$this->data_fields[$fieldname] = array('table' => $tablename, 'element' => $element);
		$form_field = $element->fieldname;
		$js_send = $send_always ? "true" : "false";
		$this->addJSBottom($this->manager . ".addDataField('{$collection_name}','{$this->form_name}','{$form_field}',{$js_send});\n");
	}
	
	function getXHTML() {
		$xhtml = "";
		$xhtml .= "<form name='{$this->form_name}' id='{$this->form_name}' enctype='multipart/form-data' action='self' method='post'>\n";
		foreach ($this->data_fields as $name => $fielddata) {
			$xhtml .= $fielddata['element']->getXHTML();
		}
		$xhtml .= "</form>\n";
		return $xhtml;
	}
	
	function validateDataFields($formdata,$mode=null) {
		$datafields = $this->data_fields;
		$result = new GenericResult();
		foreach ($datafields as $name => $arr) {
			$element = $arr['element'];
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
		return $result;
	}
	
	function getDataFieldValues($p_arguments) {
		$response = array('table_values' => array(), 'free_values' => array());
		foreach ($this->data_fields as $fieldname => $data) {
			$table = $data['table'];
			$element = $data['element'];
			$form_field = $element->fieldname;
			if (is_null($table)) {
				if (isset($p_arguments[$form_field]))
					$response['free_values'][$fieldname] = $p_arguments[$form_field];
			} else {
				if (!isset($response['table_values'][$table]))
					$response['table_values'][$table] = array();
				$response['table_values'][$table][$fieldname] = null;
				if (isset($p_arguments[$form_field]))
					$response['table_values'][$table][$fieldname] = $p_arguments[$form_field];
			}
		}
		return $response;
	}
	
	function FORMSUBMIT($p_arguments) {
		// No saving is done - just initial row validation and sorting
		$result = $this->validateDataFields($p_arguments['formvalues'],$p_arguments['mode']);
		$result->data['sorted_results'] = $this->getDataFieldValues($p_arguments['formvalues']);
		return $result;
	}
}
?>
