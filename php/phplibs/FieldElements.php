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
 * $Id: FieldElements.php 59 2012-09-19 01:17:33Z Dave $
 * 
 * ****************************************************************** */
require_once('Utils.php');
require_once('PageContent.php');
 
 class FormFieldResult {
	var $error = false;
	var $message = "";
}

class Field extends PageContent {
	public $fieldname;
	public $fieldlabel;
	public $tablename = null;
	public $required = false;
	public $show_required_parens = true;
	public $label_always=false;
	public $fieldclass='';
	public $default_value;
	public $field_level_message = "";
	public $more_info = "";
	public $alternate_label = "";
	
	function __construct($fieldname,$fieldlabel,$required=false,$label_always = false) {
		$this->fieldname = $fieldname;
		$this->fieldlabel = $fieldlabel;
		$this->required = $required;
		$this->label_always = $label_always;
	}
	
	function getLabelXHTML() {
		$xhtml = "";
		$star = $this->more_info != "" ? "*" : "";
		$label = $this->alternate_label != "" ? $this->alternate_label : $this->fieldlabel;
		$required = $this->required && $this->show_required_parens ? "(required)" : "";
		$xhtml .= "<span class='label_span'>{$label} {$required} {$star}</span>\n";
		return $xhtml;
	}
	
	function getMoreInfoXHTML() {
		$xhtml = "";
		return "<br /><span class='more_info_span' id='{$this->fieldname}__more_info'>* {$this->more_info}</span>\n";
		return $xhtml;
	}
	
	function getErrorSpanXHTML() {
		return "<br /><span class='error_span' id='{$this->fieldname}__error'></span>\n";
	}
	
	function getXHTML() {}
	
	function setFieldLevelMessage($message) {
		$this->field_level_message = $message;
	}
	
	function getFieldLevelMessage() {
		return $this->field_level_message;
	}
	
	function setAlternateLabel($label) {
		$this->alternate_label = $label;
	}
	
	function test($value) {
		
		return true;
	}
}

class TextField extends Field {
	public $width;
	function __construct($fieldname,$fieldlabel, $width,$label_always = false,$required=false) {
		parent::__construct($fieldname,$fieldlabel,$required,$label_always);
		$this->width = $width;
	}
	
	function getXHTML() {
		$field_size = $this->width > 50 ? 50 : $this->width;
		$xhtml = "";
		$xhtml .= "<div class='field_container text_field_container' id='field_div__{$this->fieldname}'>\n";
		if ($this->label_always)
			$xhtml .= $this->getLabelXHTML();
		$xhtml .= "<input type='text' size='$field_size' maxlength='{$this->width}' name='{$this->fieldname}' id='{$this->fieldname}' value='' />\n";
		if ($this->more_info != "")
			$xhtml .= $this->getMoreInfoXHTML();
		$xhtml .= $this->getErrorSpanXHTML();
		$xhtml .= "</div>\n";
		return $xhtml;
	}
	
	function test($value) {
		if ($this->required && ($value == '' || is_null($value))) {
			$this->setFieldLevelMessage($this->fieldlabel . " is required");
			return false;
		}
		return true;
	}
}

class PasswordField extends TextField {
	function getXHTML() {
		$field_size = $this->width > 50 ? 50 : $this->width;
		$xhtml = "";
		$xhtml .= "<div class='field_container text_field_container' id='field_div__{$this->fieldname}'>\n";
		if ($this->label_always)
			$xhtml .= $this->getLabelXHTML();
		$xhtml .= "<input type='password' size='$field_size' maxlength='{$this->width}' name='{$this->fieldname}' id='{$this->fieldname}' value='' />\n";
		if ($this->more_info != "")
			$xhtml .= $this->getMoreInfoXHTML();
		$xhtml .= $this->getErrorSpanXHTML();
		$xhtml .= "</div>\n";
		return $xhtml;
	}
}

class HiddenField extends Field {
	
	function __construct($fieldname,$default_value) {
		parent::__construct($fieldname, "",false);
		$this->default_value = $default_value;
	}
	
	function getXHTML() {
		$xhtml = "";
		$xhtml .= "<div class='field_container hidden_field_container' id='field_div__{$this->fieldname}'>\n";
		if ($this->label_always)
			$xhtml .= $this->getLabelXHTML();
		$xhtml .= "<input type='hidden' name='{$this->fieldname}' id='{$this->fieldname}' value='{$this->default_value}' />\n";
		if ($this->more_info != "")
			$xhtml .= $this->getMoreInfoXHTML();
		$xhtml .= $this->getErrorSpanXHTML();
		$xhtml .= "</div>\n";
		return $xhtml;
	}
}

class TextAreaField extends Field {
	public $rows;
	public $cols;
	function __construct($fieldname,$fieldlabel, $rows,$cols,$label_always = false,$required=false) {
		parent::__construct($fieldname, $fieldlabel,$required,$label_always);
		$this->rows = $rows;
		$this->cols = $cols;
	}
	
	function getXHTML() {
		$xhtml = "";
		$xhtml .= "<div class='field_container text_field_container' id='field_div__{$this->fieldname}'>\n";
		if ($this->label_always)
			$xhtml .= $this->getLabelXHTML();
		$xhtml .= "<textarea class='contact_text' name='{$this->fieldname}' id='{$this->fieldname}' rows='{$this->rows}' cols='{$this->cols}'></textarea>\n";
		if ($this->more_info != "")
			$xhtml .= $this->getMoreInfoXHTML();
		$xhtml .= $this->getErrorSpanXHTML();
		$xhtml .= "</div>\n";
		return $xhtml;
	}

	function test($value) {
		if ($this->required && ($value == '' || is_null($value))) {
			$this->setFieldLevelMessage($this->fieldlabel . " is required");
			return false;
		}
		return true;
	}
}

class CheckboxField extends Field {
	public $checked;
	public $value;
	
	function __construct($fieldname,$fieldlabel,$value=1,$checked=false,$label_always = false,$required=false) {
		parent::__construct($fieldname, $fieldlabel,$required,$label_always);
		$this->checked = $checked;
		$this->value = $value;
	}
	
	function getXHTML() {
		$xhtml = "";
		$xhtml .= "<div class='field_container checkbox_field_container' id='field_div__{$this->fieldname}'>\n";
		$xhtml .= "<input type='checkbox' name='{$this->fieldname}' id='{$this->fieldname}' value='{$this->value}' " . ($this->checked ? "checked='checked'" : "") . " />\n";
		if ($this->label_always)
			$xhtml .= $this->getLabelXHTML();
		$xhtml .= $this->getErrorSpanXHTML();
		if ($this->more_info != "")
			$xhtml .= $this->getMoreInfoXHTML();
		$xhtml .= "</div>\n";
		return $xhtml;
	}

	function test($value) {
		if ($this->required && $value != $this->value) {
			$this->setFieldLevelMessage($this->fieldlabel . " is required");
			return false;
		}
		return true;
	}
}

class ButtonField extends Field {
	var $extra_class = '';
	var $onclick = '';
	
	function __construct($id, $label, $onclick = '',$class='') {
		$this->fieldname = $id;
		$this->fieldlabel = $label;
		$this->onclick = $onclick;
		$this->extra_class = $class;
	}
	
	function getXHTML() {
		$xhtml = "";
		$onclick = "";
		if ($this->onclick != "")
			$onclick = "onclick='" . genericEscape($this->onclick) . "'";
		$xhtml .= "<input type='button' id='{$this->fieldname}' value='{$this->fieldlabel}' class='fieldbutton {$this->extra_class}' {$onclick} />\n";
		return $xhtml;
	}
}

class SelectField extends Field {
	var $has_null_value = false;
	var $null_value = -1;
	var $null_text = 'Select One';
	var $range = array();
	
	function __construct($fieldname, $fieldlabel, $has_null_value=false, $multiple=false, $label_always = false,$required=false) {
		parent::__construct($fieldname,$fieldlabel,$required,$label_always);
		$this->has_null_value = $has_null_value;
	}
	
	function setRange($array) {
		$this->range = $array;
	}
	
	function addToRange($val,$text) {
		$this->range[$val] = $text;
	}
	
	function getXHTML() {
		$xhtml = "";
		$xhtml .= "<div class='field_container text_field_container' id='field_div__{$this->fieldname}'>\n";
		if ($this->label_always)
			$xhtml .= $this->getLabelXHTML();
		$xhtml .= "<select name='{$this->fieldname}' id='{$this->fieldname}'>\n";
		if ($this->has_null_value)
			$xhtml .= "<option value='{$this->null_value}'>{$this->null_text}</option>\n";
		foreach ($this->range as $value => $text) {
			$xhtml .= "<option value='{$value}'>{$text}</option>\n";
		}
		$xhtml .= "</select>\n";
		if ($this->more_info != "")
			$xhtml .= $this->getMoreInfoXHTML();
		$xhtml .= $this->getErrorSpanXHTML();
		$xhtml .= "</div>\n";
		return $xhtml;
	}
	
	function test($value) {
		if ($this->required && $value == $this->null_value) {
			$this->setFieldLevelMessage($this->fieldlabel . " is required");
			return false;
		}
		return true;
	}
}

class FormHandler extends PageContent {
	protected $collection;
	protected $formname;
	protected $primary_key;
	protected $primary_key_value;
	protected $primary_tablename;
	protected $datafields = array();
	protected $manager;
	
	function __construct($name,$collection,$primary_table,$formname,$manager = "") {
		parent::__construct($name);
		$this->manager = $manager;
		$this->primary_tablename = $primary_table;
		$this->collection = $collection;
		$this->formname = $formname;
		$this->initDataFields();
		if (!is_null($this->primary_key) && !isset($this->datafields[$this->primary_key]))
			$this->addDataFieldElement($this->primary_key,new HiddenField($this->primary_key,$this->primary_key_value),true,$this->primary_tablename);
	}
	
	function FormValuesTest($value_pairs) {
		
		foreach ($this->datafields as $name => $data) {
		}
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
		$fieldname = $element->fieldname;
		parent::addPageElement($name,$element);
		$this->datafields[$name] = $tablename;
		$js_send = $always_send_value ? 'true' : 'false';
		$this->addJSBottom($this->manager . ".addDataField('{$this->collection}','{$this->formname}','{$fieldname}',{$js_send});\n");
	}
	
	function rowLevelValidate($formdata,&$result) {
		$datafields = $this->getDataFields();
		log_info(print_r($formdata,true));
		log_info(print_r($datafields,true));
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
		log_info("Validation Complete: " . $result->error);
		if (!$result->error) {
			$sorted_arguments = $this->sort_submitted_fields($p_arguments['formvalues']);
			log_info(print_r($sorted_arguments,true));
			$this->save($sorted_arguments);
		}
		return $result;
	}
}


?>
