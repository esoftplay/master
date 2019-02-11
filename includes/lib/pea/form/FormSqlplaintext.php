<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Input
// untuk menampilkan hasil query dari database, kedalam table form berupa plain text
//
//
// ada satu modified method disini
// yaitu setExtra()->setExtra menerima argumen berupa array
// yaitu array ('sebelum','sesudah')
// sehingga outputnya akan berupa: sebelum [hasil query] sesudah

include_once _PEA_ROOT.'form/FormText.php';
class FormSqlplaintext extends FormText
{
	function __construct()
	{
		$this->type = 'sqlplaintext';
		$this->setPlaintext(true);
		$this->setIsIncludedInSearch( false );
		$this->extra = array('','');
	}

	function setExtra( $arr_extra/* = array('sebelum','sesudah')*/ )
	{
		if ( !is_array( $arr_extra ) )
		{
			die( "Extra <strong>Sqlplaintext</strong> harus berupa array('sebelum','sesudah'); pada \$object->setExtra();" );
		}
		$this->extra		= $arr_extra;
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		return $this->getReturn($this->extra[0].$this->getReportOutput($str_value).$this->extra[1]);
	}
}