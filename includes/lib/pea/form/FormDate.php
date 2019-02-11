<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Input
// untuk menampilkan Tanggal dengan javascript
//
// modified method:
// setDefaultValue bisa diisi NOW untuk ngeset ke sekarang

class FormDate extends Form
{
	function __construct()
	{
		$this->type = 'date';
		$this->setDefaultValue();
	}

	// untuk ngeset default value pada Add Form
	// bisa diberi argumen NOW agar terisi saat ini
	function setDefaultValue( $str_default_value = 'NOW' )
	{
		if ( trim(strtolower($str_default_value)) == 'now' )
			$str_default_value	= date('Y-m-d');

		$this->defaultValue		= $str_default_value;
	}

	// untuk memformat date bentuk mysql 0000-00-00 ke bentuk 00/00/0000
	function formatDateMysql2Indo( $mysqlDate )
	{
		$mysqlDate	= trim( $mysqlDate );
		// if ( preg_match( "/^(\d{4})\-(\d{2})\-(\d{2})/", $mysqlDate, $match ) >= 1 )
		// {
		// 	$mysqlDate	= substr( $mysqlDate, 0, 10 );
		// 	$mysqlDate	= $match[3] ."/". $match[2] ."/". $match[1];
		// }
		return $mysqlDate;
	}

	// untuk memformat date  bentuk 00/00/0000 ke bentuk mysql 0000-00-00
	function formatDateIndo2Mysql( $indoDate )
	{
		$indoDate	= trim( $indoDate );
		if ( preg_match( "/^(\d{2})\/(\d{2})\/(\d{4})/", $indoDate, $match ) >= 1 )
		{
			$indoDate	= substr( $indoDate, 0, 10 );
			$indoDate	= $match[3] ."-". $match[2] ."-". $match[1];
		}
		return $indoDate;
	}

	function getRollUpdateSQL( $i='' )
	{
		if ( $i == '' && !is_int($i) )
			$val	= $this->formatDateIndo2Mysql( $_POST[$this->name] );
		else
			$val	= $this->formatDateIndo2Mysql( $_POST[$this->name][$i] );

		return $query = "`". $this->fieldName ."` = '". $val ."', ";
	}

	function getAddSQL()
	{
		$name			= $this->name;
		$out['into']	= $this->fieldName .", ";
		$out['value']	= "'". $this->formatDateIndo2Mysql( $_POST[$name] ) ."', ";

		return $out;
	}

	function getReportOutput( $str_value = '' )
	{
		$out	= $this->formatDateMysql2Indo( $str_value );
		return $out;
	}
	function getSearchQuery()
	{
		$searchCondition = '';
		if (!empty($this->defaultValue))
		{
			$date = $this->formatDateIndo2Mysql( $this->defaultValue );
			if (!preg_match('~^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$~s', $date) || !$this->like)
			{
				$searchCondition = '`'.$this->fieldName.'` LIKE \''.$date.'%\'';
			}else{
				$searchCondition = '`'.$this->fieldName.'`=\''.$date.'\'';
			}
		}
		return $searchCondition;
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( $this->isPlaintext ) return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		$GLOBALS['sys']->link_js(_PEA_URL.'includes/FormDate.js');
		$name	= ( $str_name == '' ) ? $this->name : $str_name;
		$extra	= $this->extra .' '.$str_extra;
		$str_value = str_replace(strrchr($str_value, ' '), '', $str_value);
		$def_value = !empty($str_value) ? $str_value : '';
		$def_format= preg_match('~\-~s', $str_value) ? 'yyyy-mm-dd' : 'dd/mm/yyyy';
		$out = '<input type="date" name="'.$name.'" value="'.$def_value.'" data-date-format="'.$def_format.'"'.$extra.' />';
		return $out;
	}
}