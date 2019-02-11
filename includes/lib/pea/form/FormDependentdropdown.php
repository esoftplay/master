<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
untuk membuat select form dengan turunan

EXAMPLES :

$form->edit->addInput( 'NAMABEBAS', 'dependentdropdown' );
$form->edit->input->NAMABEBAS->setTitle('Location');

// MEMBUAT SELECTION DENGAN NAMA FIELD country_id YANG BUKAN TURUNAN DARI SELECTION LAIN
$form->edit->input->NAMABEBAS->addInput('country_id', 'bbc_country', ''); 				// $input1 = nama field untuk table tsb, $input2 = nama table sebagai referensi, $input3 = nama field parentnya (kosongkan jika tidak ada parent)
$form->edit->input->NAMABEBAS->setTable('country_id', 'name', 'id');							// $input1 = nama field mana yang akan di set (yang telah ditentukan di method addInput pada input pertama), $input2 = nama field pada table referensi yang akan dijadikan label, $input3 = nama field pada table referensi yang akan dijadikan value
#form->edit->input->NAMABEBAS->addOption('country_id', '--Select Country--', '');	// $input1 = nama field mana yang akan di set (yang telah ditentukan di method addInput pada line atas), $input2 = label yang ditampilakan pada option tambahan, $input3 = value yang akan di gunakan pada option tambahan

// MEMBUAT SELECTION DENGAN NAMA FIELD state_id TURUNAN DARI country_id
$form->edit->input->NAMABEBAS->addInput('state_id', 'bbc_country_state', 'country_id');
$form->edit->input->NAMABEBAS->setTable('state_id', 'name', 'id');
#form->edit->input->NAMABEBAS->addOption('state_id', '--Select State--', '');

// MEMBUAT SELECTION DENGAN NAMA FIELD city_id TURUNAN DARI state_id
$form->edit->input->NAMABEBAS->addInput('city_id', 'bbc_country_city', 'state_id');
$form->edit->input->NAMABEBAS->setTable('city_id', 'name', 'id');
#form->edit->input->NAMABEBAS->addOption('city_id', '--Select City--', '');

$form->edit->input->NAMABEBAS->setPlaintext(true);

NB: MEMANG AGAK RUMIT PENGGUNAANYA, TP TUJUANNYA BIAR SELECT TURUNAN TIDAK PUNYA BATASAN (BISA BANYAK)

*/
include_once _PEA_ROOT.'form/FormMultiinput.php';
class FormDependentdropdown extends FormMultiinput
{
	var $allOption         = array();
	var $options           = array();
	var $allSelectionInput = array();
	var $isOptionLoad      = false;

	function __construct()
	{
		parent::__construct();
		$this->type = 'dependentdropdown';
	}

	function addInput( $fieldName, $inputTable='', $inputParent='')
	{
		$this->parent->addInput($fieldName, 'select');
		$this->elements->$fieldName     = $this->parent->input->$fieldName;
		$this->elements->$fieldName->setIsMultiInput( true );
		$this->allSelectionInput[$fieldName] = array($inputTable,'id','name',$inputParent,'');
	}

	function setTable($fieldName,$sqlLabel='name',$sqlValue='id')
	{
		if (!isset($this->allSelectionInput[$fieldName]))
		{
			die('Anda belum menentukan addInput( $fieldName="'.$fieldName.'", $inputTable="", $inputParent="")');
		}
		$this->allSelectionInput[$fieldName][1] = $sqlValue;
		$this->allSelectionInput[$fieldName][2] = $sqlLabel;
		$this->options[$fieldName] = array();
		// JIKA PARENT DITEMUKAN MAKA TENTUKAN CHILD_FIELD_NAME NYA
		if (!empty($this->allSelectionInput[$fieldName][3]))
		{
			if (!empty($this->allSelectionInput[$this->allSelectionInput[$fieldName][3]]))
			{
				$this->allSelectionInput[$this->allSelectionInput[$fieldName][3]][4] = $fieldName;
			}
		}
	}
	function addOption($fieldName, $label, $value='')
	{
		if (is_array($label))
		{
			foreach ($label as $var => $val)
			{
				$this->addOption($fieldName,$var,$val);
			}
		}else{
			$value = isset($value) ? $value :  $label;
			$this->options[$fieldName][$value] = $label;
		}
	}

	function getAllOption($fieldName='')
	{
		$output = array();
		if (empty($this->allSelectionInput))
		{
			die('FormDependentdropdown::  harus ada addInput($fieldName, $inputTable="", $inputParent="") untuk menampilkan selection');
		}
		if (empty($fieldName))
		{
			$firstFieldName = '';
			foreach ($this->allSelectionInput as $key => $data)
			{
				if (empty($data[3]))
				{
					$firstFieldName = $key;
					break;
				}
			}
			if (!empty($firstFieldName))
			{
				$output = $this->getAllOption($firstFieldName);
			}
		}else{
			if (empty($this->allSelectionInput[$fieldName]))
			{
				die('FormDependentdropdown::  fieldName "'.$fieldName.'" belum ditentukan!');
			}else{
				$output = $this->getSQLQuery($fieldName);
			}
		}
		return $output;
	}
	function getSQLQuery($fieldName, $reference_id = '')
	{
		global $Bbc;
		$id     = 'pea_'._func('menu','save',$fieldName);
		$output = array();
		if (empty($this->allSelectionInput[$fieldName]))
		{
			return $output;
		}
		if (!isset($Bbc->$id))
		{
			list($table,$value,$label,$parentFieldName,$childFieldName) = $this->allSelectionInput[$fieldName];
			$sql = !empty($parentFieldName) ? ', '.$parentFieldName.' AS ref_id' : '';
			if (!preg_match('~\s+order\s+by\s+~is', $table))
			{
				$table .= ' ORDER BY name ASC';
			}
			$q = "SELECT {$value} AS id, {$label} AS name{$sql} FROM {$table}";
			$Bbc->$id = $this->db->getAll($q);
		}
		if (!empty($this->options[$fieldName]))
		{
			foreach ($this->options[$fieldName] as $key => $value)
			{
				$output[] = array($key, $value, array());
			}
		}
		foreach ($Bbc->$id as $data)
		{
			if (!empty($data['ref_id']))
			{
				if ($reference_id==$data['ref_id'])
				{
					$output[] = array($data['id'], $data['name'], $this->getSQLQuery($this->allSelectionInput[$fieldName][4],$data['id']));
				}
			}else{
				$output[] = array($data['id'], $data['name'], $this->getSQLQuery($this->allSelectionInput[$fieldName][4],$data['id']));
			}
		}
		return $output;
	}
	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		$out	= array();
		// RENDERING PLAINTEXT OUTPUT
		if ( $this->isPlaintext )
		{
			$option = $this->getAllOption();
			foreach ($this->elements as $id => $element)
			{
				$value  = @$this->parent->arrResult[$element->fieldName];
				$name   = $element->name;
				if (!empty($value))
				{
					foreach ($option as $d)
					{
						if ($d[0]==$value)
						{
							$value = $d[1];
							$option = $d[2];
							break;
						}
					}
				}
				$element->setPlaintext(true);
				$out[] = $value;
			}
			return $this->getReturn(implode($this->delimiter, $out));
		}
		// RENDERING NORMAL INPUT OUTPUT
		foreach ( $this->elements as $id=>$element )
		{
			$value  = @$this->parent->arrResult[$element->fieldName];
			$name   = $element->name;
			if (!empty($value))
			{
				$element->setExtra('rel="'.$value.'"');
			}
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
		$out = '<div class="form-inline dependentdropdown" rel="'.$this->fieldName.'">'.implode($this->delimiter, $out);
		if (!$this->isOptionLoad)
		{
			$id = 'dependentdropdown_'.$this->fieldName;
			if (!isset($GLOBALS['Bbc']->$id))
			{
				link_js(_PEA_URL.'includes/FormDependentdropdown.js');
				$out .= '<script type="text/javascript"> var '.$this->fieldName.'_dependentdropdown = '.json_encode($this->getAllOption()).';</script>';
				$GLOBALS['Bbc']->$id = 1;
			}
			$this->isOptionLoad = true;
		}
		$out .= '</div>';
		return $out;
	}
}