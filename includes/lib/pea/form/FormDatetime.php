<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Input
// untuk menampilkan Tanggal dengan javascript
//
// modified method:
// setDefaultValue bisa diisi NOW untuk ngeset ke sekarang

class FormDatetime extends Form
{
	var $dateFormat;
	var $jsDateFormat;
	var $defaultValue = 'now';
	function __construct()
	{
		$this->type = 'datetime';
		$this->setDateFormat();
	}

	function setDateFormat($format = 'Y-m-d H:i:s')
	{
		$this->dateFormat = $format;
		$format = str_replace('Y', 'yy', $format);
		$format = preg_replace('~([a-z])~s', '$1$1', strtolower($format));
		$this->jsDateFormat = $format;
		$this->setDefaultValue($this->defaultValue);
	}

	// untuk ngeset default value pada Add Form
	// bisa diberi argumen NOW agar terisi saat ini -------->sama dengan yang ada di mysql 0000-00-00 00:00:00
	function setDefaultValue( $value = 'now' )
	{
		$this->defaultValue = date($this->dateFormat, strtotime($value));
	}

	function getDateFormat($value)
	{
		return date('Y-m-d H:i:s', strtotime($value));
	}

	function getRollUpdateSQL( $i='' )
	{
		if ( $i == '' && !is_int($i) )
		{
			$val	= $this->getDateFormat($_POST[$this->name]);
		}else{
			$val	= $this->getDateFormat($_POST[$this->name][$i]);
		}
		return $query = "`".$this->fieldName."` = '".$val."', ";
	}

	function getAddSQL()
	{
		$out['into']	= $this->fieldName.', ';
		if(empty($_POST[$this->name]))
		{
			$out['value']	= "'".$this->defaultValue."', ";
		}else{
			$out['value']	= "'".$this->getDateFormat($_POST[$this->name])."', ";
		}
		return $out;
	}

	function getSearchQuery()
	{
		$searchCondition = '';
		if (isset($this->defaultValue) && $this->defaultValue !== '')
		{
			$searchCondition	= '`'. $this->fieldName .'` LIKE \'%'. $this->defaultValue .'\'';
		}
		return $searchCondition;
	}

	function getReportOutput( $str_value = '' )
	{
		return date($this->dateFormat, strtotime($str_value));
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( $this->isPlaintext ) return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		link_js(_PEA_URL.'includes/FormDatetime.js', false);
		$name	= ( $str_name == '' ) ? $this->name : $str_name;
		$extra	= $this->extra .' '.$str_extra;
		$str_value = str_replace(strrchr($str_value, ' '), '', $str_value);
		$def_value = !empty($str_value) ? date($this->dateFormat, strtotime($str_value)) : '';

		$out = '<input type="datetime" name="'.$name.'" value="'.$def_value.'" data-date-format="'.$this->jsDateFormat.'"'.$extra.' />';
		return $out;
	}
}