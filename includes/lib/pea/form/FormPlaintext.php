<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Input
// untuk menampilkan suatu text apa adanya kedalam form atau table
//
//
// modified
// yaitu setValue()->argumennya adalah string yang ditampilkan apa adanya di dalam form
// c: $obj->setValur( " ini test text" );

include_once _PEA_ROOT.'form/FormSqlplaintext.php';
class FormPlaintext extends FormSqlplaintext
{
	var $value;

	function __construct()
	{
		$this->type = 'plaintext';
		$this->setPlaintext(true);
		$this->setIsIncludedInSelectQuery( false );
		$this->setIsIncludedInSearch( false );
		$this->setValue();
	}

	function setValue( $str_value ='' )
	{
		$this->value	= $str_value;
	}

	function getReportOutput( $str_value = '' )
	{
		return parent::getReportOutput($this->value);
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		return parent::getOutput($this->value,$str_name,$str_extra);
	}
}