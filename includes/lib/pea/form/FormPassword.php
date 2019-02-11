<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

class FormPassword extends Form
{
	var $typeEncription; //menentukan type encripsi nya
	var $setDefault;
	function __construct()
	{
		$this->type = 'password';
		_func('password');
		$this->setEncryption();
		if($this->actionType !== 'add')
		{
			$this->setDefault();
		}
	}
	function setEncryption($type='encode')
	{
		$this->typeEncription=$type;
	}
	function setDefault($bool_default=true)
	{
		$this->setDefault=$bool_default;
	}
	function getAddSQL()
	{
		$name = $this->name;
		if(empty($this->typeEncription) || $this->typeEncription=='')
		{
			$out['into']	= $this->fieldName .", ";
			$out['value']	= "'".$_POST[$name]."', ";
		}else{
			$out['into']	= $this->fieldName .", ";
			$out['value']	= "'".call_user_func($this->typeEncription,$_POST[$name]) ."', ";
		}
		return $out;
	}
	function getRollUpdateSQL( $i = '' )
	{
		if ( $i == '' && !is_int($i) )
		{
			$val	= $_POST[$this->name];
		}else{
			$val	= $_POST[$this->name][$i];
		}
		if($val !== '******')
		{
			if(empty($this->typeEncription))
			{
				return $query = "`". $this->fieldName ."`='".$val."', ";
			}else{
				return $query = "`". $this->fieldName ."`='".call_user_func($this->typeEncription, $val) ."', ";
			}
		}
		return '';
	}
	function getReportOutput($str_value = '')
	{
		return '******';
	}
	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( $this->isPlaintext ) return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		if ( $this->setDefault ) { $str_value='******'; }

		$name	= ( $str_name == '' ) ? $this->name : $str_name;
		$extra	= $this->extra .' '. $str_extra;
		$out	= '<input name="'. $name .'" type="password" value="'.$str_value.'" '.$extra.'>';
		return $out;
	}
}