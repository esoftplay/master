<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');
/*
Example:
$form->search->addInput( 'speaker_id', 'hidden' );
$form->search->input->speaker_id->setDefaultValue($speaker_id);
*/
class FormHidden extends Form
{
	function __construct()
	{
		$this->type = 'hidden';
		$this->setIsInsideRow( false );
		$this->setIsIncludedInReport( false );
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		$name  = ( $str_name == '' ) ? $this->name : $str_name;
		$extra = trim($this->extra .' '. $str_extra);
		if (!preg_match('~\s?id="~', $extra))
		{
			$extra .= ' id="'.rtrim(str_replace('[','_', $name),']').'"';
		}
		$out   = '<input name="'. $name .'" type="hidden" value="'. $str_value .'" '.$extra.' />';
		return $out;
	}
}