<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Input
// untuk membuat header dari form Add dan form Edit
//
//
// ada satu modified method disini
// yaitu setExtra() -> setExtra menerima argumen berupa array
// yaitu array ('sebelum','sesudah')
// sehingga outputnya akan berupa: sebelum [title] sesudah
class FormHeader extends Form
{
	function __construct()
	{
		$this -> type = 'header';
		$this -> setIsIncludedInUpdateQuery( false );
		$this -> setIsIncludedInSelectQuery( false );
		$this -> setIsIncludedInSearch( false );
		$this -> setIsInsideCell( false );
		$this -> setIsHeader( true );
	}

	function setExtra( $arr_extra = array("sebelum","sesudah") )
	{
		if ( !is_array( $arr_extra ) )
		{
			die( "Extra <strong>header</strong> harus berupa array('sebelum','sesudah'); pada \$object->setExtra();" );
		}
		$this -> extra		= $arr_extra;
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		$out	= '';
		if ( is_array($this -> extra) )
			$out	.= $this -> extra[0];

		$out	.= $this->title;

		if ( is_array($this -> extra) )
			$out	.= $this -> extra[1];
		return $out;
	}
}