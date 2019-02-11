<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
EXAMPLE : untuk menampilkan Range Tanggal dengan javascript
$form->edit->addInput('date_start','dateinterval');
$form->edit->input->date_start->setTitle('Available');					# optional jika tidak ada maka akan diambil dari inputFieldName yg d ucwords() kan
$form->edit->input->date_start->setEndDateField('date_end');
$form->edit->input->date_start->setPlaintext(true);							# optional
$form->edit->input->date_start->setDefaultValue('now');					# optional
$form->edit->input->date_start->setCaption('Start Date');				# optional jika tidak ada maka caption akan diambil dari title
$form->edit->input->date_end->setTitle('End Date');							# optional INGAT InputFieldName yang digunakan adalah input dari ->setEndDateField()

JIKA SEARCH :
$form->search->addInput('created','dateinterval');
$form->search->input->created->setIsSearchRange();

*/
include_once _PEA_ROOT.'form/FormMultiinput.php';
class FormDateInterval extends FormMultiinput
{
	var $start;
	var $endDate;
	var $isSearchRange = false;
	function __construct()
	{
		parent::__construct();
		$this->type = 'dateinterval';
		$this->setIsIncludedInSearch( true );
		$this->setIsIncludedInSelectQuery( true );
		$this->setIsIncludedInUpdateQuery( true );
		$this->setSearchQueryLike(true);
		$this->setDelimiter('&nbsp;-&nbsp;');
	}

	function setIsSearchRange()
	{
		if ($this->actionType=='search')
		{
			$this->setTitle('Start Date');
			$this->setEndDateField($this->fieldName.'_until', 'End Date');
			$this->isSearchRange = true;
		}
	}

	function setEndDateField($end_date_field, $end_date_title='')
	{
		if ($end_date_field != $this->fieldName.'_until')
		{
			$this->isSearchRange = false;
			$fieldName = $this->fieldName.'_until';
			unset($this->endDate, $this->elements->$fieldName);
		}
		$this->setExtra('data-date-format="yyyy-mm-dd"');
		$this->addInput($end_date_field,'text',$end_date_title);
		$this->endDate = $this->elements->$end_date_field;
		$this->endDate->setExtra($this->extra);
		if (empty($_POST[$this->endDate->name]))
		{
			$this->endDate->setIsIncludedInSearch( false );
			if (!empty($this->endDate->defaultValue))
			{
				$this->defaultValueEnd = $this->endDate->defaultValue;
			}
		}
		if ($this->isPlaintext)
		{
			$this->endDate->setPlaintext(true);
		}
		if (!empty($this->defaultValueEnd))
		{
			$this->endDate->setDefaultValue($this->defaultValueEnd);
		}
	}

	function setPlaintext( $bool_is_plaintext = false )
	{
		Form::setPlaintext($bool_is_plaintext);
		if (!empty($this->endDate))
		{
			$this->endDate->setPlaintext($bool_is_plaintext);
		}
	}

	// untuk ngeset default value pada Add Form
	// bisa diberi argumen NOW agar terisi saat ini
	function setDefaultValue( $value_start = 'now', $value_end='')
	{
		if ($this->actionType=='search' && $value_start == 'now')
		{
			$value_start = '';
		}
		if (!empty($value_start))
		{
			if (!preg_match('~^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$~s', $value_start))
			{
				$value_start = date('Y-m-d',strtotime($value_start));
			}
		}else{
			$value_start = '';
		}
		if (!empty($value_end))
		{
			if (!preg_match('~^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$~s', $value_end))
			{
				$value_end = date('Y-m-d',strtotime($value_end));
			}
		}else{
			$value_end = $this->actionType=='search' ? '' : $value_start;
		}
		$this->defaultValue    = $value_start;
		$this->defaultValueEnd = $value_end;
		if (!empty($this->endDate))
		{
			$this->endDate->setDefaultValue($value_end);
		}
	}

	function getSearchQuery()
	{
		$searchCondition = '';
		if ($this->isSearchRange)
		{
			if (!empty($this->defaultValue))
			{
				if (!empty($this->defaultValueEnd))
				{
					$searchCondition = 'DATE(`'. $this->fieldName .'`) >= \''. $this->defaultValue .'\' AND DATE(`'. $this->fieldName .'`) <= \''.$this->defaultValueEnd.'\'';
				}else{
					$searchCondition = 'DATE(`'. $this->fieldName .'`) = \''. $this->defaultValue .'\'';
				}
			}
		}else{
			if (isset($this->defaultValue) && $this->defaultValue !== '') // pakai !== supaya 0 (nol) masih bisa di lewatkan
			{
				$val	= $this->defaultValue;
				if($this->like)
				{
					$searchCondition	= '`'. $this->fieldName .'` <= \''. $val .'\'';
				}else{
					$searchCondition	= '`'. $this->fieldName .'` = \''. $val .'\'';
				}
			}// eof if ( isset( $_POST[$this->searchButton->name] ) )
			if (isset($this->defaultValueEnd) && $this->defaultValueEnd !== '') // pakai !== supaya 0 (nol) masih bisa di lewatkan
			{
				if (!empty($searchCondition))
				{
					$searchCondition .= ' AND ';
				}
				$val	= $this->defaultValueEnd;
				if($this->like)
				{
					$searchCondition	.= '`'. $this->endDate->fieldName .'` >= \''. $val .'\'';
				}else{
					$searchCondition	.= '`'. $this->endDate->fieldName .'` = \''. $val .'\'';
				}
			}
		}
		return $searchCondition;
	}

	function getReportOutput( $str_value = '' )
	{
		$output = '';
		if (!is_array($str_value))
		{
			return date('Y M d', strtotime($str_value));
		}
		_func('date');
		list($start,$end) = array_values($str_value);
		$output = date_interval($start, $end);
		$tips = array();
		foreach ( $this->elements as $id=>$element )
		{
			if ($id!=$this->endDate->fieldName)
			{
				$value = !empty($this->parent->arrResult[$element->fieldName]) ? $this->parent->arrResult[$element->fieldName] : @$element->defaultValue;
				$tips[] = '<tr><td>'.$element->title.'</td><td>: '.$element->getReportOutput($value).'</td></tr>';
			}
		}
		if (!empty($tips))
		{
			if (empty($this->caption))
			{
				$this->caption = $this->title;
			}
			$output = '<span class="tips" title="'.htmlentities($this->caption).'" data-toggle="popover" data-placement="auto" data-content="'.htmlentities('<table>'.implode('', $tips).'</table>').'">'.$output.'</span>';
			if (empty($GLOBALS['sys']->tips))
			{
				$GLOBALS['sys']->tips = 1;
				$output .= '<script type="text/javascript">var BS3load_popover = 1;</script>';
			}
		}
		return $output;
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if (empty($this->endDate)) {
			die( 'FormDateInterval:: setEndDateField($end_date_field, $end_date_title) harus ditentukan terlebih dahulu untuk mengetahui range dari tanggal satu ke lainya' );
		}
		$extra    = $this->extra .' '. $str_extra;
		$name     = ( $str_name == '' ) ? $this->name : $str_name;
		$names    = array($name);
		$values   = array($str_value);
		$extras   = array($extra);
		$enddate  = $this->endDate->objectName;
		$element  = $this->elements->$enddate;
		$names[]  = $element->name;
		$values[] = !empty($this->parent->arrResult[$element->fieldName]) ? $this->parent->arrResult[$element->fieldName] : @$element->defaultValue;
		$extras[] = $element->extra.' '. $this->parent->setDefaultExtra($element);
		// RENDERING PLAINTEXT OUTPUT
		if ($this->isPlaintext) {
			return $this->getPlaintexOutput($values, $names, $extras);
		}
		// RENDERING NORMAL INPUT OUTPUT
		$out   = array();
		$out[] = '<input name="'.$names[0].'" type="text" value="'. htmlentities($values[0]) .'" '.$extras[0].'>';
		$out[] = '<input name="'.$names[1].'" type="text" value="'. htmlentities($values[1]) .'" '.$extras[1].'>';
		foreach ( $this->elements as $id=>$element )
		{
			if ($id!=$enddate)
			{
				$value  = !empty($this->parent->arrResult[$element->fieldName]) ? $this->parent->arrResult[$element->fieldName] : @$element->defaultValue;
				$name   = $element->name;
				$output = $element->getOutput( $value, $name, $this->parent->setDefaultExtra($element) );
				if (preg_match('~form\-control(\-static)?~is', $output, $m))
				{
					if (!empty($m[1]))
					{
						$output = str_replace('-static', '', $output);
					}
				}else{
					if ($this->actionType!='roll')
					{
						$output = '<div class="form-control">'.$output.'</div>';
					}
				}
				$out[] = $output;
			}
		}


		$GLOBALS['sys']->link_js(_PEA_URL.'includes/FormDateInterval.js');
		$output = implode('<span class="input-group-addon">'.$this->delimiter.'</span>', $out);
		$out = '<div class="input-daterange input-group">'.$output.'</div>';
		return $out;
	}
}