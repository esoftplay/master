<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

class FormColor extends Form
{
	function __construct()
	{
		$this->type = 'color';
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( $this->isPlaintext ) return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		$GLOBALS['sys']->link_js(_PEA_URL.'includes/FormColor.js');
		$name	= ( $str_name == '' ) ? $this->name : $str_name;
		$extra	= $this->extra .' '.$str_extra;
		$def_value = !empty($str_value) ? $str_value : '';
		$out = '<input type="color" name="'.$name.'" value="'.$def_value.'" '.$extra.' />';
		return $out;

		$out	= '';
		$fileLocation	= _PEA_ROOT . "form/collor/";
		$name	= ( $str_name == '' ) ? $this->name : $str_name;
		$field_name=$name;
		//str_replace(''
		$name=str_replace('[','',$name);
		$name=str_replace(']','',$name);
		$extra	= $this->extra .' '. $str_extra;

		// javascricpt hanya perlu di includekan sekali saja, jika dalam suatu halaman terdapat lebih dari satu
		// pengguna date
		link_js( $fileLocation . 'colourpicker.js' );
		$out	.= '  <input name="'. $field_name .'" id="'. $this->formName.'_'.$name .'" type="text" size="'.$this->size.'" value="'. $str_value .'" onClick="javascript:showPicker(this);" '.$extra.'>';
		return $out;
	}
}