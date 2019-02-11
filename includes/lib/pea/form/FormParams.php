<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
EXAMPLE:

$form->edit->addInput('params','params');

## JIKA MENGGUNAKAN REFERENCE TABLE UNTUK MENENTUKAN FIELD NYA
$form->edit->input->params->setReferenceTable('bbc_user_field');
$form->edit->input->params->setReferenceCondition('group_id=0');
$form->edit->input->params->setReferenceCondition('active=1');
#form->edit->input->params->setEncode(true);

## JIKA MENGGUNAKAN VARIABLE ARRAY UNTUK MENENTUKAN FIELD NYA
$form->edit->input->params->setParams($config);
-- sample $config bisa dilihat di includes/class/params.php di line paling bawah --
*/
class FormParams extends Form
{
	var $params;
	var $isEncode       = false;
	var $isStripTags    = false;
	var $isSubStr       = false;
	var $isStrReplace   = false;
	var $isNumberFormat = false;
	var $isDateFormat   = false;
	var $isHtmlEntities = false;

	var $referenceTable;
	var $sqlReferenceCondition = array();

	function __construct()
	{
		$this->type   = 'params';
		$this->lang_r = lang_assoc();
		$this->setIsNeedDbObject( true );
		$this->setIsIncludedInReport(false);
		$this->setIsInsideCell(false);
		if ($this->actionType == 'roll' || $this->actionType == 'search')
		{
			die( 'FormParams:: Hanya bisa dipakai untuk formType edit dan add saja!' );
		}
	}
	function setParent($obj)
	{
		$this->parent = $obj;
	}
	function setParams($params)
	{
		$output = array();
		if (!empty($params))
		{
			if (is_string($params))
			{
				$output = json_decode($params, 1);
			}else{
				$output = $params;
			}
		}
		$this->params = $output;
	}
	function setEncode($is_encode=true)
	{
		$this->isEncode = $is_encode ? true : false;
	}
	function setReferenceTable( $str_reference_table )
	{
		$this->referenceTable	= $str_reference_table;
	}
	function setReferenceCondition($value)
	{
		if (is_array($value))
		{
			foreach ($value as $val)
			{
				$this->setReferenceCondition($val);
			}
		}else{
			if (!empty($value))
			{
				$this->sqlReferenceCondition[] = $value;
			}
		}
	}
	function getDataFromReferenceTable()
	{
		if ( @$this->isLoadedgetDataFromReferenceTable == true ) return;

		$this->isLoadedgetDataFromReferenceTable = true;
		if (empty($this->referenceTable))
		{
			die( 'FormParams:: setReferenceTable($str_reference_table) harus diset untuk menentukan table dan field yang digunakan.' );
		}
		// meng query data dari table reference
		$sql = "SELECT * FROM ". $this->getTableCondition($this->referenceTable, $this->sqlReferenceCondition);
		if (!preg_match('~\s+order\s+by\s+~is', $sql))
		{
			$sql .= ' ORDER BY `orderby` ASC';
		}
		$this->params	= $this->db->getAll( $sql );
	}
	function getTableCondition($table, $condition, $method='WHERE')
	{
		$output = trim($table);
		if (!empty($condition))
		{
			$delimiter = $method == 'WHERE' ? ' AND ' : ', ';
			$method    = $method == 'WHERE' ? ' WHERE ' : '';
			$condition = implode($delimiter, $condition);
			if (preg_match('~\s+where\s+~is', $output))
			{
				$output = preg_replace('~\s+(where)\s+~is', $method.'%s'.$delimiter, $output);
			}else
			if (preg_match('~\s+order\s+by\s+~is', $output))
			{
				$output = preg_replace('~\s+(order\s+by)\s~is', $method.'%s ORDER BY ', $output);
			}else{
				$output .=  $method.'%s';
			}
			$output = sprintf($output, $condition);
		}
		return $output;
	}
	function getFormFields($value)
	{
		if (empty($this->params) && empty($this->referenceTable))
		{
			die( 'FormParams:: setParams($array); atau setReferenceTable($str_table_name); Harus di set terlebih dahulu untuk menentukan field tambahan.' );
		}
		if (empty($this->params))
		{
			$this->getDataFromReferenceTable();
		}
		$form     = _class('params');
		$form->db = $this->db;
		$params   = $form->set_param($this->params);
		foreach ($params as $field)
		{
			if (!empty($field['mandatory']))
			{
				$this->parent->isFormRequire = true;
				$this->isRequire = 'any';
				break;
			}
		}
		ob_start();
		$values = json_decode($value, 1);
		if ($this->isEncode)
		{
			$values = urldecode_r($values);
		}
		$form->show_param($params, $values, $this->name);
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	function getRollUpdateSQL( $i = '' )
	{
		if ( $i == '' && !is_int($i) )
			$val	= $_POST[$this->name];
		else
			$val	= $_POST[$this->name][$i];
		if ($this->isEncode)
		{
			$val = urlencode_r($val);
		}
		return $query = "`". $this->fieldName ."` = '". $this->cleanSQL(json_encode($val)) ."', ";
	}
	function getAddSQL()
	{
		$out['into']	= '`'.$this->fieldName .'`, ';
		$out['value']	= "'".$this->cleanSQL(json_encode($_POST[$this->name]))."', ";
		return $out;
	}
	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		$name      = ( $str_name == '' ) ? $this->name : $str_name;
		$str_value = $str_value;
		$extra     = $this->extra .' '. $str_extra;
		if ( $this->isPlaintext )
		{
			return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		}
		return $this->getFormFields($str_value);
	}
}
