<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
Input
untuk menggabungkan beberapa input menjadi satu (hanya tampilannya saja)
SAMPLE PENGGUNAAN
$form->edit->addInput('NAMABEBAS','multiform');
$form->edit->input->NAMABEBAS->setTitle('JUDUL INPUT');
$form->edit->input->NAMABEBAS->setReferenceTable('film');										# untuk ngeset table yang mau di select sebagai reference
$form->edit->input->NAMABEBAS->setReferenceField( 'ref_id', 'film_id' );		# untuk ngeset nama field reference yang digunakan untuk foreign key serta ID primary yang biasanya bernama `id`
#form->edit->input->NAMABEBAS->setReferenceCondition( 'active=1' );					# Menentukan tambahan untuk perintah MySQL di WHERE
$form->edit->input->NAMABEBAS->setToogle($bool_show = false);								# INI HANYA DIGUNAKAN JIKA INGIN MENAMPILKAN DALAM BENTUK TOOGLE
$form->edit->input->NAMABEBAS->addInput('NAMAFIELD_1', 'INPUT_TYPE_1', 'PLACEHOLDER_1');
$form->edit->input->NAMABEBAS->addInput('NAMAFIELD_2', 'INPUT_TYPE_2', 'PLACEHOLDER_2');

CUSTOMIZE :
$form->edit->input->NAMABEBAS->elements->NAMAFIELD_1->setCaption('LABEL');
OR
$form->edit->input->NAMAFIELD_1->setCaption('LABEL');

modified method
setDelimiter()
		-> untuk ngeset delimiter antar element saat di outputkan
addInput()
		-> untuk menambah input yang menjadi anggota multiform ini
		-> penggunaan nya sama persis seperti manggunaan input lain (lihat CUSTOMIZE di atas)
getElements()
		-> untuk mendapatkan array berisi object elements anggota
*/
include_once __DIR__.'/FormMultiinput.php';
class FormMultiform extends FormMultiinput
{
	var $elements; // menyimpan element2 yang termasuk dalam multi ini
	var $delimiter;
	var $parent;
	var $referenceTable;
	var $referenceField;
	var $referenceFields;
	var $referenceCondition;
	var $isToogle;
	var $showToogle;
	var $extraFields = array();
	var $extraValues = array();

	function __construct()
	{
		$this->type     = 'multiform';
		$this->elements = new stdClass;
		$this->setIsIncludedInSearch( false );
		$this->setIsIncludedInSelectQuery( false );
		$this->setIsIncludedInUpdateQuery( true );
		$this->setIsIncludedInDeleteQuery( true );
		$this->setIsNeedDbObject( true );
		$this->setDelimiter(' ');
		$this->referenceTable     = '';
		$this->referenceFields    = array();
		$this->referenceField     = array();
		$this->referenceCondition = array();
	}

	function setParent($obj)
	{
		if ($this->actionType == 'roll' || $this->actionType == 'search')
		{
			die('FormMultiform:: maaf form field ini hanya bisa digunakan untuk tipe edit dan add saja');
		}
		$this->parent = $obj;
	}

	function setReferenceTable( $str_reference_table )
	{
		$this->referenceTable	= $str_reference_table;
	}

	function setReferenceField( $reference_id, $table_id='id' )
	{
		$this->referenceField['ref_id']	= $reference_id;
		$this->referenceField['tbl_id']	= $table_id;
	}

	function setReferenceCondition($value)
	{
		if (is_array($value))
		{
			foreach ($value as $val)
			{
				$this->setReferenceCondition($val);
			}
		}else{
			if (!empty($value))
			{
				$this->referenceCondition[] = $value;
			}
		}
	}

	function getTableId()
	{
		if (!isset($this->tableID))
		{
			if ($this->actionType=='add')
			{
				$this->tableID = 0;
			}else{
				$this->tableID = intval($this->db->getOne("SELECT {$this->tableId} FROM {$this->parent->table} {$this->sqlCondition}"));
			}
		}
		return $this->tableID;
	}

	function getReferenceCondition()
	{
		$out = 'WHERE `'.$this->referenceField['ref_id'].'`='.$this->getTableId();
		if (!empty($this->referenceCondition))
		{
			$out .= " AND ".implode(' AND' , $this->referenceCondition);
		}else{
			$out .= '';
		}
		return $out;
	}


	function addInput( $inputName, $inputType = 'text', $inputTitle='' )
	{
		$realName = $inputName;
		if (!empty($this->parent->input->$inputName))
		{
			$inputName = $this->objectName.'_'.$inputName;
		}
		$this->parent->addInput($inputName, $inputType);
		$this->parent->input->$inputName->setIsMultiInput( true );
		$this->parent->input->$inputName->tmpisIncludedInSelectQuery = $this->parent->input->$inputName->isIncludedInSelectQuery;
		$this->parent->input->$inputName->tmpisIncludedInUpdateQuery = $this->parent->input->$inputName->isIncludedInUpdateQuery;
		$this->parent->input->$inputName->setIsIncludedInSearch( false );
		$this->parent->input->$inputName->setIsIncludedInSelectQuery( false );
		$this->parent->input->$inputName->setIsIncludedInUpdateQuery( false );
		if (!empty($inputTitle))
		{
			$this->parent->input->$inputName->setTitle($inputTitle);
			switch($inputType)
			{
				case 'plaintext':
					$this->parent->input->$inputName->setValue($inputTitle);
					break;
				case 'checkbox':
					$this->parent->input->$inputName->setCaption($inputTitle);
					break;
			}
		}
		$this->parent->input->$inputName->setFieldName(strtolower($realName));
		$this->parent->input->$inputName->setName($this->objectName.'_'.$this->parent->input->$inputName->objectName);
		$this->parent->input->$inputName->name .= '[]';
		$this->elements->$realName = $this->parent->input->$inputName;
	}

	function addExtraField( $field = '', $value = '', $formType='' )
	{
		if (empty($formType) || $formType==$this->parent->formType)
		{
			$this->extraFields[]	= $field;
			$this->extraValues[]	= $value;
		}
	}

	function setToogle($show = false)
	{
		if ($this->actionType == 'roll' || $this->actionType == 'search')
		{
			die('FormMultiform:: maaf form field ini hanya bisa digunakan untuk tipe edit dan add saja');
		}else{
			$this->isToogle   = true;
			$this->showToogle = $show;
		}
	}

	// untuk ngeset delimiter antar element saat di output kan
	function setDelimiter( $str_delimiter	= '<br />' )
	{
		$this->delimiter	= $str_delimiter;
	}

	function setPlaintext( $bool_is_plaintext = false )
	{
		$this->isPlaintext		= $bool_is_plaintext;
		foreach ($this->elements as $element)
		{
			$element->setIsIncludedInUpdateQuery(!$bool_is_plaintext);
		}
	}
	/*
	$elements = ->getElements();
	$fields   = ->getElements('field', 'selectupdate|delete');
	$names    = ->getElements('name');
	*/
	function getElements($option = '', $isInclude = '')
	{
		$obj = 'isIncludedIn'.ucwords($isInclude).'Query';
		if (!empty($this->referenceFields[$obj][$option]))
		{
			return $this->referenceFields[$obj][$option];
		}
		if (empty($this->referenceTable))
		{
			die('FormMultiform:: anda harus menentukan ->setReferenceTable($str_reference_table); terlebih dahulu');
		}
		if (empty($this->referenceField))
		{
			die('FormMultiform:: anda harus menentukan ->setReferenceField($reference_id, $table_id=\'id\'); terlebih dahulu');
		}
		$opt    = 'tmp'.$obj; // mengambil settingan sebelumnya karena telah di buat false di addInput();
		$output = array();
		switch ($option)
		{
			case 'field':
				$fields = array($this->referenceField['tbl_id']);
				foreach ($this->elements as $input)
				{
					if (!empty($input->$opt))
					{
						$fields[] = $input->getFieldName();
					}
				}
				$output = $fields;
				break;
			case 'name':
				$names = array($this->formName.'_'.$this->referenceField['ref_id']);
				foreach ($this->elements as $input)
				{
					if (!empty($input->$opt))
					{
						$names[] = preg_replace('~\[\]$~is', '', $input->name);
					}
				}
				$output = $names;
				break;
			case 'data':
				if ($this->actionType == 'edit')
				{
					$fields = $this->getElements('field', 'select');
					if (count($fields) > 2)
					{
						$output = $this->db->getAssoc("SELECT ".implode(', ', $fields)." FROM {$this->referenceTable} ".$this->getReferenceCondition()." ORDER BY ".$this->referenceField['tbl_id']." ASC");
					}else{
						$array  = $this->db->getAll("SELECT ".implode(', ', $fields)." FROM {$this->referenceTable} ".$this->getReferenceCondition()." ORDER BY ".$this->referenceField['tbl_id']." ASC");
						$output = array();
						foreach ($array as $d)
						{
							$output[$d[$this->referenceField['tbl_id']]] = $d;
							unset($output[$d[$this->referenceField['tbl_id']]][$this->referenceField['tbl_id']]);
						}
					}
				}
				break;
			default:
				$output = $this->elements;
				break;
		}
		$this->referenceFields[$obj][$option] = $output;
		return $output;
	}
	// di eksekusi ketika tumbol save di klik jika $this->isIncludedInUpdateQuery==true
	function getRollUpdateSQL($i='')
	{
		if ($i == '' && !is_numeric($i))
		{
			// UPDATE DARI EDIT FORM
			foreach ($this->elements as $input)
			{
				$input->isIncludedInUpdateQuery = $input->tmpisIncludedInUpdateQuery;
				$input->name = preg_replace('~\[\]$~is', '', $input->name);
			}
			$datas = $this->getElements('data', 'update');
			$ids   = @$_POST[$this->formName.'_'.$this->fieldName.'_'.$this->referenceField['tbl_id']];
			foreach ((array)$ids as $j => $id)
			{
				$is_filled = $id ? true : false;
				$sql_query = array();
				if (!$id)
				{
					$sql_query[] = '`'.$this->referenceField['ref_id'].'`='.$this->getTableId().', ';
					foreach ($this->referenceCondition as $sql)
					{
						$sql_query[] = $sql.', ';
					}
				}
				foreach ($this->elements as $input)
				{
					$sql = $input->getRollUpdateQuery($j);
					if (!empty($sql))
					{
						$sql_query[] = $sql;
						if (!$is_filled && preg_match("~'[^']+'~is", $sql) && !empty($_POST[$input->name]))
						{
							$is_filled = true;
						}
					}
				}
				if ($is_filled)
				{
					if (!empty($this->extraFields))
					{
						foreach ($this->extraFields as $i => $field)
						{
							$value = $this->extraValues[$i];
							$sql_query[] = "`{$field}`='{$value}', ";
						}
					}
					$sql = substr(implode('', $sql_query), 0, -2);
					if ($id > 0)
					{
						$this->db->Execute("UPDATE `{$this->referenceTable}` SET {$sql} WHERE `{$this->referenceField['tbl_id']}`={$id}");
					}else{
						$this->db->Execute("INSERT INTO `{$this->referenceTable}` SET {$sql}");
					}
				}
			}
			foreach ($this->elements as $input)
			{
				$input->name .= '[]';
				$input->setIsIncludedInUpdateQuery(false);
			}
			foreach ($datas as $id => $data)
			{
				if (!in_array($id, $ids))
				{
					$this->db->Execute("DELETE FROM `{$this->referenceTable}` WHERE `{$this->referenceField['tbl_id']}`={$id}");
				}
			}
		}else{
			// UPDATE DARI ROLL FORM TIDAK DIPROSES
		}
	}
	// di eksekusi ketika tombol add di klik jika $this->isIncludedInUpdateQuery==true
	function getAddSQL()
	{
		$pendingQuery = array();
		foreach ($this->elements as $input)
		{
			$input->isIncludedInUpdateQuery = $input->tmpisIncludedInUpdateQuery;
			$input->name = preg_replace('~\[\]$~is', '', $input->name);
		}
		$ids = @$_POST[$this->formName.'_'.$this->fieldName.'_'.$this->referenceField['tbl_id']];
		foreach ((array)$ids as $i => $id)
		{
			$is_filled   = false;
			$sql_query   = array();
			$sql_query[] = '`'.$this->referenceField['ref_id'].'`=\'_INSERT_ID\', ';
			foreach ($this->referenceCondition as $sql)
			{
				$sql_query[] = $sql.', ';
			}
			foreach ($this->elements as $input)
			{
				$sql = $input->getRollUpdateQuery($i);
				if (!empty($sql))
				{
					$sql_query[] = $sql;
					if (!$is_filled && preg_match("~'[^']+'~is", $sql) && !empty($_POST[$input->name]))
					{
						$is_filled = true;
					}
				}
			}
			if ($is_filled)
			{
				$pendingQuery[] = "INSERT INTO `{$this->referenceTable}` SET ".substr(implode('', $sql_query), 0, -2);
			}
		}
		foreach ($this->elements as $input)
		{
			$input->name .= '[]';
			$input->setIsIncludedInUpdateQuery(false);
		}
		if (!empty($pendingQuery))
		{
			$pendingQuery = array_merge(array('_PENDING_QUERY'), $pendingQuery);
		}
		return $pendingQuery;
	}
	// di eksekusi ketika tombol delete di klik jika $this->isIncludedInDeleteQuery==true
	function getDeleteSQL($ids)
	{
		foreach ($this->elements as $input)
		{
			$input->getDeleteQuery($ids);
		}
		return "DELETE FROM `{$this->referenceTable}` ".$this->getReferenceCondition();
	}
	// di eksekusi setelah database di masukkan (Form Add saja) jika $this->isIncludedInUpdateQuery==true
	function getAddAction($db, $Insert_ID)
	{
		foreach ($this->elements as $input)
		{
			$input->isIncludedInUpdateQuery = $input->tmpisIncludedInUpdateQuery;
			$input->name = preg_replace('~\[\]$~is', '', $input->name);
			$input->getAddAction($db, $Insert_ID);
			$input->name .= '[]';
			$input->setIsIncludedInUpdateQuery(false);
		}
		return '';
	}

	function getReportOutput( $objects = '' )
	{
		$output = array();
		foreach ( $this->elements as $id => $input )
		{
			$object = !empty($objects[$id]) ? $objects[$id] : new stdClass();
			$value  = $this->parent->getDefaultValue($input, @$object->data, @$object->i);
			$out    = $input->getReportOutput( $value );
			if (!empty($out))
			{
				$output[] = $out;
			}
		}
		return implode($this->delimiter, $output);
	}

	// $objects berisi object dari memanggi method di class phpEasyAdminLib bernama: getMultiElementObject( $input, $arrResult, $i );
	function getOutput( $objects = '', $str_name = '', $str_extra = '' )
	{
		$arrResults    = $this->getElements('data');
		$output        = array();
		$arrResults[0] = array();

		foreach ($arrResults as $id => $arrResult)
		{
			$out	= array('<input type="hidden" sytle="display: none;" name="'.$this->formName.'_'.$this->fieldName.'_'.$this->referenceField['tbl_id'].'[]" value="'.$id.'" />');
			foreach ( $this->elements as $id => $input )
			{
				$object    = !empty($objects[$id]) ? $objects[$id] : new stdClass();
				$value     = $this->parent->getDefaultValue($input, @$arrResult, @$object->i);
				$thisField = $input->getOutput( $value, @$object->name, $this->parent->setDefaultExtra($input) );
				if ($this->isToogle)
				{
					if ( $input->isInsideRow &&  $input->isInsideCell )
					{
						$inputField = '';
						$title      = ucwords($input->title);
						if (!empty($input->textHelp))
						{
							if (!isset($this->parent->help->value[$this->name]))
							{
								$this->parent->help->value[$this->name] = '';
							}
							$this->parent->help->value[$this->name] .= $title.': '.$input->textHelp.'<br />';
						}
						if (!empty($input->textTip))
						{
							if (!isset($this->parent->tip->value[$this->name]))
							{
								$this->parent->tip->value[$this->name] = '';
							}
							$this->parent->tip->value[$this->name] .= $title.': '.$input->textTip.'<br />';
						}
						$inputField .= '<div class="form-group">';
						if ($input->type=='checkbox' || $input->type=='multicheckbox' || $input->type=='radio')
						{
							$cls         = ($input->type == 'multicheckbox') ? 'checkbox' : $input->type;
							$inputField .= '<div class="input-group '.$cls.'">'.$thisField.'</div>';
						}else{
							$inputField .= $thisField;
						}
						$inputField .= '</div>';
						$thisField   = $inputField;
					}
				}else{
					if (preg_match('~form\-control(\-static)?~is', $thisField, $m))
					{
						if (!empty($m[1]))
						{
							$thisField = str_replace('-static', '', $thisField);
						}
					}else{
						if ($this->actionType!='roll')
						{
							$thisField = '<div class="form-control">'.$thisField.'</div>';
						}
					}
				}
				$out[] = $thisField;
			}
			$icon     = !empty($arrResult) ? 'trash' : 'plus';
			$out[]    = '<button type="button" class="btn btn-default btn-secondary btn-multiform">'.icon($icon).'</button>';
			$output[] = implode($this->delimiter, $out);
		}

		$allFields = '<div class="multiform"><div class="form-inline">'.implode('</div><div class="form-inline">', $output).'</div></div>';
		link_js(_PEA_URL.'includes/FormMultiform.js');

		if ($this->isToogle)
		{
			$display = $this->showToogle ? 'in' : 'on';
			$title   = ucwords($this->title);
			if(!empty($this->parent->help->value[$this->name]))
			{
				$title .= ' '.help('<span style="font-weight: normal;">'.$this->parent->help->value[$this->name].'</span>');
			}
			$out = '
				<div class="panel-group" id="accordion'.$this->name.'">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title" data-toggle="collapse" data-parent="#accordion'.$this->name.'" href="#pea_isHideToolOn'.$this->name.'" style="cursor: pointer;">
								'.$title.'
							</h4>
						</div>
						<div id="pea_isHideToolOn'.$this->name.'" class="panel-collapse collapse '.$display.'">
							<div class="panel-body">
								'.$allFields.'
							</div>
						</div>
					</div>
				</div>'
		}else{
			$out = preg_replace('~(<div[^>]+class=")(form-control)~is', '$1input-group', $allFields);
		}
		return $out;
	}
}