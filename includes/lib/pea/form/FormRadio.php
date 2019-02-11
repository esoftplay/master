<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// untuk membuat radio form yg simple

// addRadio( $title, $value )
// 		untuk menambah radio button
//		c: $this->addRadio( "umur 18", 18 );

// addRadioArray( $arrTitle, $arrValue )
//		untuk menambah radio, tapi langsung, jadi dalam bentuk array

class FormRadio extends Form
{
	var $option = array();
	var $optionValue=array();
	var $delimiter = '&nbsp;';

	function __construct()
	{
		$this->type = 'radio';
		$this->setIsNeedDbObject( true );
		$this->isLoaded	= new stdClass();
		$this->isLoaded->addRadioFromTable = false;
	}

	function addOption( $option, $value = null )
	{
		if (is_array($option))
		{
			$this->addOptionArray($option,$value);
		}else{
			$this->option[]      = $option;
			$this->optionValue[] = isset($value) ? $value : $option;
		}
	}

	function addOptionArray( $arrOption, $arrValue = array() )
	{
		$i = 0;
		foreach( $arrOption as $key => $title)
		{
			if (empty($arrValue) && is_array($title))
			{
				list($value, $label) = array_values($title);
			}else{
				if (!isset($key_is_value))
				{
					$key_is_value = ($key!=$i && empty($arrValue)) ? true : false;
				}
				$value = $key_is_value ? $key : ( isset($arrValue[$key]) ? $arrValue[$key] : (isset($arrValue[$i]) ? $arrValue[$i] : $title) );
				$label = is_array($title) ? reset($title) : $title;
			}
			$this->addOption( $label, $value );
			$i++;
		}
	}

	function addRadio( $title, $value = '' )
	{
		$this->addOption( $title, $value );
	}

	function addRadioArray( $arrTitle, $arrValue = array() )
	{
		$this->addOptionArray( $arrTitle, $arrValue );
	}

	function setReferenceTable( $str_reference_table )
	{
		$this->referenceTable	= $str_reference_table;
	}

	function setReferenceField( $str_reference_option_field, $str_reference_value_field )
	{
		$this->referenceField['value']	= $str_reference_value_field;
		$this->referenceField['option']	= $str_reference_option_field;
	}

	function addRadioFromTable()
	{
		$this->isLoaded->addRadioFromTable = true;

		if ( $this->referenceTable == ''
				|| $this->referenceField['value'] == ''
				|| $this->referenceField['option'] == ''
			)
		{
			//die( "FormSelecttable::  setReferenceTable() dan setReferenceField() harus diset untuk menentukan table dan field yang digunakan." );
		}

		// meng query data dari table reference
		$sql = "SELECT ". $this->referenceField['value'] ." as `value`,
					 ". $this->referenceField['option'] ." as `option`
					FROM ". $this->referenceTable;
		$result	= $this->db->Execute( $sql );

		while ( $a = $result->FetchRow() )
		{
			extract( $a );
			$this->addRadio( $option, $value);
		}

	}



	// untuk ngeset delimiter antar element saat di output kan
	function setDelimiter( $str_delimiter	= '&nbsp;' )
	{
		$this->delimiter	= $str_delimiter;
	}

	function getReportOutput( $str_value = '' )
	{
		if ( !$this->isLoaded->addRadioFromTable )
		{
			$this->addRadioFromTable();
		}
		$out= '';
		foreach ( $this->optionValue as $i => $value )
		{
			if ($value==$str_value)
			{
				$out = $this->option[$i];
			}
		}
		return $out;
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( $this->isPlaintext ) return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );

		if(isset($this->referenceTable))
		{
			if ( !$this->isLoaded->addRadioFromTable ) $this->addRadioFromTable();
		}
		$name	 = ( $str_name == '' ) ? $this->name : $str_name;
		$extra = $this->extra .' '. $str_extra;
		$out   = array();
		foreach ( (array)@$this->option as $i => $title )
		{
			$value = $this->optionValue[$i];
			$sel   = $str_value == $value ? ' checked' : '';
			$out[] = '<label><input name="'. $name .'" type="radio" value="'.$value.'"'. $sel . $extra . '> '. $title .'</label>';
		}
		return '<div class="radio">'.implode($this->delimiter, $out).'</div>';
	}
}