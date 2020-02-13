<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// untuk buat checkbox
// modified
// setCaption()
// ngeset caption, hasilnya:  [X] caption
// c: $obj->setCaption( 'publish' );
// setValue
// argumen berupa array value dari checkbox,
// c: 	$arr_value = array('y','n') => default 0 dan 1
//		$obj->setValue( $arr_value );
// array[0] merupakan checkbox yang dicentang
// array[1] merupakan checkbox yang tidak dicentang
// setCheckAll( true/false )
// untuk ngeset apakah mau diberi fasilitas checkAll, default true
// c: $obj->setCheckAll( false );
class FormCheckbox extends Form
{
	var $caption; 	// caption dari checkbox
	var $value; 	// value dari checkbox tersebut. dan value apa agar checkbox tercentang
	var $isCheckAll; // apakah fasilitas checkAll dihidupkan ato enggak
	function __construct()
	{
		$this->type = 'checkbox';
		$this->caption	= '';
		$this->setAlign( "Center" );
		$this->setValue();
		$this->setCheckAll( true );
		$this->setDefaultValue('1');
	}

	function setOption( $str_title = "Title", $str_field_name = "title", $set_caption = '', $arr_checked_value = array( '1'=>'1' ), $str_extra = "" )
	{
		$this->setTitle( $str_title );
		$this->setFieldName( $str_field_name );
		$this->setExtra( $str_extra );
		$this->setCheckedCaption( $arr_checked_value );
		$this->setValue( $arr_value );
		$this->setDefaultValue('1');
	}
	// array value dari checkbox, c: $arr_value = array('y','n') => default 0 dan 1
	// array[0] merupakan checkbox yang dicentang
	// array[1] merupakan checkbox yang tidak dicentang
	function setValue( $arr_value = array( '1', '0' ) )
	{
		$this->value	= $arr_value;
	}
	// untuk ngeset apakah mau diberi fasilitas checkAll
	function setCheckAll( $bool_is_check_all = false )
	{
		$this->isCheckAll	= $bool_is_check_all;
	}

	function getRollUpdateSQL( $i='' )
	{
		if ( $i == '' && !is_int($i) )
			$val	= isset( $_POST[$this->name] ) ? $this->value[0] : $this->value[1];
		else
			$val	= isset( $_POST[$this->name][$i] ) ? $this->value[0] : $this->value[1];
		return $query = "`". $this->fieldName ."` = '". $this->cleanSQL($val) ."', ";
	}

	function getAddSQL()
	{
		$name         = $this->name;
		$val          = isset( $_POST[$this->name] ) ? $this->value[0] : $this->value[1];
		$out['into']  = $this->fieldName .", ";
		$out['value'] = "'$val', ";
		return $out;
	}

	function getSearchQuery()
	{
		$searchCondition = '';
		if (isset($this->defaultValue))
		{
			$val  = $this->defaultValue ? $this->value[0] : $this->value[1];
			if (!is_numeric($val))
			{
				$val = "'{$val}'";
			}
			$searchCondition	= '`'.$this->fieldName.'`='.$val;
		}
		return $searchCondition;
	}

	function getReportOutput( $str_value = '' )
	{
		$label = ( $this->caption == '' ) ? $str_value : $this->caption;
		$out   = ( $str_value == $this->value[0] ) ? $label : "-";
		return $out;
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( $this->isPlaintext ) return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		$name    = ( $str_name == '' ) ? $this->name : $str_name;
		$extra   = $this->extra .' '. $str_extra;
		$checked = ( $str_value == $this->value[0] ) ? 'checked' : '';
		$out     = '<div class="checkbox"><label><input name="'.$name.'" class="'.$this->name.'" type="checkbox" value="'. $str_value .'" '.$checked .' '.$extra.'> '.$this->caption.'</label></div>';
		return $out;
	}
}
