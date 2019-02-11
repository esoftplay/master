<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

include_once( _PEA_ROOT . "form/FormCheckbox.php" );
class FormCheckboxdelete extends FormCheckbox
{
	function __construct()
	{
		$this->type = 'checkboxdelete';
		$this->setValue( array( 1, 0 ) );
		$this->setIcon( 'trash' );
		$this->setTitle( "Delete" );
		$this->setAlign( "Center" );
		$this->setIsIncludedInUpdateQuery( false );
		$this->setIsIncludedInSelectQuery( false );
		$this->setIsIncludedInReport( false );
		$this->setIsIncludedInSearch( false );
		$this->setCheckAll( true );
	}
	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		$name  = ( $str_name == '' ) ? $this->name : $str_name;
		$extra = $this->extra .' '. $str_extra;
		$out   = '<input name="'. $name .'" class="'. $this->name .'" type="checkbox" value="'. $str_value .'" '.$extra.'>';
		if (!empty($this->caption))
		{
			$out = '<label>'.$out.' '.$this->caption.'</label>';
		}
		return $out;
	}
}