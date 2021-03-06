<?php
/* *********************************************************************
 * Copyright 2009 David Horn
 * 
 * $Id: DataObjects.php 65 2012-09-23 17:00:02Z Dave $
 * 
  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
  BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
  ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  SOFTWARE.
  * 
  ******************************************************************* */
require_once "phpconnect.php";
require_once "Utils.php";
require_once "logger.php";

class DataObject {
	var $resultSet = null;
	var $numRows = 0;
	var $resultArr = array();
	var $table;
	var $key_field;
	var $__db_errno;
	var $__db_error;
	var $__last_id=0;

	function DataObject($table = '',$key = '') {
		$where = '';
		if ($key != '') {
		
		}
		if ($table != '') {
			
		}
	}
	
	function setQuery($query) {
		$r = mysql_query($query);
		
		if ($r != false) {
			$this->resultSet = $r;
			log_other("QUERY","Query returned results for query: " . $query);
			
		} else {
			log_other("QUERY","Query returned no results for query: " . $query);
			
		}
		$this->__db_errno = mysql_errno();
		$this->__db_error = mysql_error();
		$this->__last_id = mysql_insert_id();
		if ($this->__db_errno > 0) {
			log_error($this->__db_errno . ": " . $this->__db_error);
			return false;
		}
		return true;
	}

	function insertUpdate($query) {
		return $this->setQuery($query);
	}
	
	function fetch($result_type=MYSQL_BOTH) {
		if ($this->resultSet != null) {
			//return ($this->resultArr = mysql_fetch_assoc($this->resultSet));
			return ($this->resultArr = mysql_fetch_array($this->resultSet,$result_type));
		}
	}
	
	function getRowAsArray() {
		return $this->resultArr;
	}
	
	function getValue($fieldname) {
		if (!isset($this->resultArr[$fieldname]))
			return false;
		return $this->resultArr[$fieldname];
	}
	
	function getField($field) {
		if (isset($this->resultArr[$field])) 
			return $this->resultArr[$field];
		else
			return false;
	}

	function getLastId() {
		return $this->__last_id;
	}

	function get_db_errno() {
		return $this->__db_errno;
	}

	function get_db_error() {
		return $this->__db_error;
	}

	function escape_str($str) {
		return genericEscape($str);
	}
	
	function escapeStr($str) {
		return $this->escape_str($str); //alias
	}

	function getFieldNames($table) {
		$fieldnames = array();
		$table_name = $this->escape_str($table);
		$this->setQuery("SHOW COLUMNS FROM {$table_name}");
		while($this->fetch()) {
			$fieldnames[] = $this->getField('Field');
		}
		return $fieldnames;
	}
	
	function escapeString($str) {
		return mysql_real_escape_string($str);
	}
}
?>
