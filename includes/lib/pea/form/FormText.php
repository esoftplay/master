<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
EXAMPLE : menampilkan input field normal

$form->edit->addInput('title','text', $numberOfColumn=1);
#$form->edit->input->title->setTitle('Title');
#$form->edit->input->title->setRequire($require='any' / * any/email/url/phone/money/number * /, $is_mandatory=1);

*/
class FormText extends Form
{
	var $isStripTags    = false;
	var $isSubStr       = false;
	var $isStrReplace   = false;
	var $isNumberFormat = false;
	var $isDateFormat   = false;
	var $isHtmlEntities = false;

	function __construct()
	{
		$this->type = 'text';
		$this->setDelimeter();
		$this->lang_r = lang_assoc();
	}

	function setDelimeter($delimeter = '<br />')
	{
		$this->delimeter = $delimeter;
	}

	function setStripTags($allowable_tags = '')
	{
		$this->isStripTags = true;
		$this->isStripTags_except = $allowable_tags;
	}

	function setHtmlEntities($bool = true)
	{
		$this->isHtmlEntities = $bool;
	}

	function setSubStr($delim1, $delim2='0')
	{
		$this->isSubStr = true;
		$this->subStrValue = array($delim1, $delim2);
	}

	function setStrReplace($strFrom, $strTo)
	{
		if(!empty($strFrom)){
			$this->isStrReplace = true;
			$this->strReplaceValue = array($strFrom, $strTo);
		}
	}

	function setNumberFormat( $decimals = 2, $dec_point = '.', $thousands_sep = ',')
	{
		if (is_bool($decimals))
		{
			$this->isNumberFormat = $decimals;
			$decimals = 2;
		}
		$this->isNumberFormat = true;
		$this->NumberFormatValue= array($decimals, $dec_point, $thousands_sep);
	}

	function setDateFormat( $dateFormatValue='M jS, Y', $dateEmptyValue = '')
	{
		$this->isDateFormat    = true;
		$this->dateFormatValue = $dateFormatValue;
		$this->dateEmptyValue  = $dateEmptyValue;
	}

	function doStripTags($str_value)
	{
		if($this->isMultiLanguage)
		{
			$out = array();
			foreach((array)$str_value AS $value) $out[] = strip_tags($value);
		}else{
			$out	= $str_value;
		}
		return $out;
	}
	function doSubstr($str_value, $start, $limit)
	{
		if($this->isMultiLanguage)
		{
			$out = array();
			foreach((array)$str_value AS $value) $out[] = substr($value, $start, $limit);
		}else{
			$out	= substr($str_value, $start, $limit);
		}
		return $out;
	}
	function doStrReplace($search, $replace, $str_value)
	{
		if($this->isMultiLanguage)
		{
			$out = array();
			foreach((array)$str_value AS $value) $out[] = str_replace($search, $replace, (string)$value);
		}else{
			$out	= str_replace($search, $replace, (string)$str_value);
		}
		return $out;
	}
	function doNumberFormat($str_value, $decimals, $dec_point, $thousands_sep)
	{
		$point = preg_quote($dec_point, '~');
		if($this->isMultiLanguage)
		{
			$out = array();
			foreach((array)$str_value AS $value)
			{
				$output = number_format(floatval($value), $decimals, $dec_point, $thousands_sep);
				$output = preg_replace('~0+$~', '',  $output);
				$output = preg_replace('~'.$point.'$~', '',  $output);
				$out[]  = $output;
			}
		}else{
			$out = number_format(floatval($str_value), $decimals, $dec_point, $thousands_sep);
			$out = preg_replace('~0+$~', '',  $out);
			$out = preg_replace('~'.$point.'$~', '',  $out);
		}
		return $out;
	}
	function doDateFormat($str_value)
	{
		if($this->isMultiLanguage)
		{
			$out = array();
			foreach((array)$str_value AS $value)
			{
				$time  = strtotime($value);
				$out[] = $time>0 ? date($this->dateFormatValue, $time) : $this->dateEmptyValue;
			}
		}else{
			$value = (string)$str_value;
			$time = strtotime($str_value);
			$out  = $time>0 ? date($this->dateFormatValue, $time) : $this->dateEmptyValue;
		}
		return $out;
	}
	function getReportOutput($str_value = '')
	{
		if ( $this->isStripTags )
		{
			$str_value = $this->doStripTags($str_value);
		}
		if ( $this->isSubStr )
		{
			$str_value = $this->doSubstr($str_value, $this->subStrValue[0], $this->subStrValue[1]);
		}
		if ( $this->isStrReplace )
		{
			$str_value = $this->doStrReplace($this->strReplaceValue[0], $this->strReplaceValue[1], $str_value);
		}
		if ( $this->isNumberFormat )
		{
			$str_value = $this->doNumberFormat($str_value, $this->NumberFormatValue[0], $this->NumberFormatValue[1], $this->NumberFormatValue[2]);
		}
		if ( $this->isDateFormat )
		{
			$str_value = $this->doDateFormat($str_value);
		}
		$str_value = current((array)@$str_value);
		return $str_value;
	}
	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		$name			= ( $str_name == '' ) ? $this->name : $str_name;
		$str_value= ($this->isHtmlEntities) ? htmlentities_r($str_value) : $str_value;
		$extra		= $this->extra .' '. $str_extra;
		if ( $this->isPlaintext )
		{
			return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		}
		$type = $this->isNumberFormat ? 'number' : 'text';
		if($this->isMultiLanguage)
		{
			$r = array();
			$i = count($this->lang_r) > 1 ? true : false;
			foreach($this->lang_r AS $d)
			{
				$alt   = $i ? $d['title'] : $this->title;
				$value = isset($str_value[$d['id']]) ? $str_value[$d['id']] : '';
				$extra = preg_replace(array('~\s{0,}title=".*?"~is', '~\s{0,}placeholder=".*?"~is'), '', $extra);
				$r[]   = '<input name="'.$name.'['.$d['id'].']" type="'.$type.'" value="'.htmlentities($value).'" '.$extra.' title="'.$alt.'" placeholder="'.$alt.'">';
			}
			$out = implode($this->delimeter, $r);
		}else{
			$out = '<input name="'. $name .'" type="'.$type.'" value="'. htmlentities($str_value) .'" '.$extra.'>';
		}
		return $out;
	}
}
