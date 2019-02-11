<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

class FormDelete extends Form
{
	function __construct()
	{
		$this->type = 'delete';
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		$name	= ( $str_name == '' ) ? $this->fieldName : $str_name;
		$extra	= $this->extra .' '. $str_extra;
		$out	= "<input name=\"". $name ."\" type=\"text\" size=\"".$this->size."\" value=\"". $str_value ."\" ".$extra.">";
		return $out;
	}
}