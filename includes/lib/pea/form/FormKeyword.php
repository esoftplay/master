<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// khusus untuuk phpSearchAdmin
/*
// EXAMPLE
$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField($field = 'title,description,keyword,tags,intro,content', $isFullText = true);
*/
class FormKeyword extends Form
{
	var $searchField = array();

	function __construct()
	{
		$this->type = 'keyword';
		$this->setIsIncludedInUpdateQuery( false );
		$this->setIsIncludedInSelectQuery( false );
		$this->setIsIncludedInReport( false );
		$this->searchField = array(0 => array(), 1 => array());
	}

	function addSearchField( $field, $isFullText = true )
	{
		if (empty($field))
		{
			return;
		}
		$id = $isFullText ? true : false;
		$r = (!is_array($field)) ? explode(',', $field) : array($field);
		foreach ($r as $col)
		{
			$col = trim($col);
			if (!empty($col))
			{
				if (substr($col, 0, 1)!='`' && substr($col, -1)!='`')
				{
					$col = "`{$col}`";
				}
				$this->searchField[$id][] = $col;
			}
		}
	}

	function getSearchQuery()
	{
		$searchCondition	= '';
		if (!empty($this->defaultValue) &&
			(!empty($this->searchField[0]) || !empty($this->searchField[1])))
		{
			$key = $this->defaultValue;
			$arr = array();
			if (!empty($this->searchField[0]))
			{
				$r = array();
				foreach ($this->searchField[0] as $field)
				{
					$r[] = $field.' LIKE "%'.$key.'%"';
				}
				$arr[] = '('.implode(' OR ', $r).')';
			}
			if (!empty($this->searchField[1]))
			{
				$field = implode(',', $this->searchField[1]);
				$arr[] = 'MATCH ('.$field.') AGAINST ("'.$key.'" IN BOOLEAN MODE)';
			}
			if (!empty($arr))
			{
				if (count($arr) > 1)
				{
					$searchCondition = '('.implode(' OR ', $arr).')';
				}else{
					$searchCondition = implode('', $arr);
				}
			}
		}
		return $searchCondition;
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ($this->isPlaintext)
		{
			return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		}
		$name	 = ( $str_name == '' ) ? $this->name : $str_name;
		$extra = $this->extra .' '. $str_extra;
		$out   = '<input name="'. $name .'" type="text" size="'.$this->size.'" value="'. $str_value .'" '.$extra.' placeholder="'.$this->title.'" />';
		return $out;
	}
}