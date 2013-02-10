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
 * $Id: FieldElements.php 73 2012-10-20 22:29:18Z Dave $
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
	public $default_value='';
	public $field_level_message = "";
	public $more_info = "";
	public $alternate_label = "";
	public $always_send = false;
	
	function __construct($fieldname,$fieldlabel,$required=false,$label_always = false) {
		$this->fieldname = $fieldname;
		$this->fieldlabel = $fieldlabel;
		$this->required = $required;
		$this->label_always = $label_always;
	}
	
	function getDomFieldName() {
		if (!$this->use_table_prefixes)
			return $this->fieldname;
		return $this->tablename . "__" . $this->fieldname;
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
		$domfield_name = $this->getDomFieldName();
		return "<br /><span class='more_info_span' id='{$domfield_name}__more_info'>* {$this->more_info}</span>\n";
		return $xhtml;
	}
	
	function getErrorSpanXHTML() {
		$domfield_name = $this->getDomFieldName();
		return "<br /><span class='error_span' id='{$domfield_name}__error'></span>\n";
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
		$domfield_name = $this->getDomFieldName();
		$xhtml = "";
		$xhtml .= "<div class='field_container text_field_container' id='field_div__{$domfield_name}'>\n";
		if ($this->label_always)
			$xhtml .= $this->getLabelXHTML();
		$xhtml .= "<input type='text' size='$field_size' maxlength='{$this->width}' name='{$domfield_name}' id='{$domfield_name}' value='' />\n";
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
		$domfield_name = $this->getDomFieldName();
		$field_size = $this->width > 50 ? 50 : $this->width;
		$xhtml = "";
		$xhtml .= "<div class='field_container text_field_container' id='field_div__{$domfield_name}'>\n";
		if ($this->label_always)
			$xhtml .= $this->getLabelXHTML();
		$xhtml .= "<input type='password' size='$field_size' maxlength='{$this->width}' name='{$domfield_name}' id='{$domfield_name}' value='' />\n";
		if ($this->more_info != "")
			$xhtml .= $this->getMoreInfoXHTML();
		$xhtml .= $this->getErrorSpanXHTML();
		$xhtml .= "</div>\n";
		return $xhtml;
	}
}

class HiddenField extends Field {
	
	function __construct($fieldname,$default_value,$required=false) {
		parent::__construct($fieldname, "",$required,false);
		$this->default_value = $default_value;
		$this->always_send = true;
	}
	
	function getXHTML() {
		$domfield_name = $this->getDomFieldName();
		$xhtml = "";
		$xhtml .= "<div class='field_container hidden_field_container' id='field_div__{$domfield_name}'>\n";
		if ($this->label_always)
			$xhtml .= $this->getLabelXHTML();
		$xhtml .= "<input type='hidden' name='{$domfield_name}' id='{$domfield_name}' value='{$this->default_value}' />\n";
		if ($this->more_info != "")
			$xhtml .= $this->getMoreInfoXHTML();
		$xhtml .= $this->getErrorSpanXHTML();
		$xhtml .= "</div>\n";
		return $xhtml;
	}
}

class UUIDField extends HiddenField {
	function __construct($fieldname) {
		parent::__construct($fieldname, "",true);
		$this->default_value = null;
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
		$domfield_name = $this->getDomFieldName();
		$xhtml = "";
		$xhtml .= "<div class='field_container text_field_container' id='field_div__{$domfield_name}'>\n";
		if ($this->label_always)
			$xhtml .= $this->getLabelXHTML();
		$xhtml .= "<textarea class='contact_text' name='{$domfield_name}' id='{$domfield_name}' rows='{$this->rows}' cols='{$this->cols}'></textarea>\n";
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
		$this->default_value = false;
		$this->checked = $checked;
		$this->value = $value;
	}
	
	function getXHTML() {
		$domfield_name = $this->getDomFieldName();
		$xhtml = "";
		$xhtml .= "<div class='field_container checkbox_field_container' id='field_div__{$domfield_name}'>\n";
		$xhtml .= "<input type='checkbox' name='{$domfield_name}' id='{$domfield_name}' value='{$this->value}' " . ($this->checked ? "checked='checked'" : "") . " />\n";
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
		$domfield_name = $this->getDomFieldName();
		$xhtml = "";
		$onclick = "";
		if ($this->onclick != "")
			$onclick = "onclick='" . genericEscape($this->onclick) . "'";
		$xhtml .= "<div><input type='button' id='{$domfield_name}' value='{$this->fieldlabel}' class='fieldbutton {$this->extra_class}' {$onclick} /></div>\n";
		return $xhtml;
	}
}

class SubmitField extends ButtonField {
	function getXHTML() {
		$domfield_name = $this->getDomFieldName();
		$xhtml = "";
		$onclick = "";
		if ($this->onclick != "")
			$onclick = "onclick='" . genericEscape($this->onclick) . "'";
		$xhtml .= "<div><input type='submit' id='{$domfield_name}' value='{$this->fieldlabel}' class='fieldbutton {$this->extra_class}' {$onclick} /></div>\n";
		return $xhtml;
	}
}

class ImageField extends SubmitField {
	var $extra_class = '';
	var $onclick = '';
	
	function getXHTML() {
		$domfield_name = $this->getDomFieldName();
		$xhtml = "";
		$onclick = "";
		if ($this->onclick != "")
			$onclick = "onclick='" . genericEscape($this->onclick) . "'";
		$xhtml .= "<div class='field_container'><input type='image' src='{$this->fieldlabel}' id='{$domfield_name}' class='{$this->extra_class}' {$onclick} /></div>\n";
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
		$this->default_value = 0;
		$this->has_null_value = $has_null_value;
	}
	
	function setRange($array) {
		$this->range = $array;
	}
	
	function addToRange($val,$text) {
		$this->range[$val] = $text;
	}
	
	function getXHTML() {
		$domfield_name = $this->getDomFieldName();
		$xhtml = "";
		$xhtml .= "<div class='field_container text_field_container' id='field_div__{$domfield_name}'>\n";
		if ($this->label_always)
			$xhtml .= $this->getLabelXHTML();
		$xhtml .= "<select name='{$domfield_name}' id='{$domfield_name}'>\n";
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

class FileField extends Field {
	function getXHTML() {
		$domfield_name = $this->getDomFieldName();
		$xhtml = "";
		$xhtml .= "<div class='field_container text_field_container' id='field_div__{$domfield_name}'>\n";
		if ($this->label_always)
			$xhtml .= $this->getLabelXHTML();
		$xhtml .= "<input type='file' name='{$domfield_name}' id='{$domfield_name}' />\n";
		if ($this->more_info != "")
			$xhtml .= $this->getMoreInfoXHTML();
		$xhtml .= $this->getErrorSpanXHTML();
		$xhtml .= "</div>\n";
		return $xhtml;
	}
}


?>
