<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
Input
untuk menggabungkan beberapa input menjadi satu (hanya tampilannya saja)
SAMPLE PENGGUNAAN
$form->edit->addInput('NAMABEBAS','multiinput');
$form->edit->input->NAMABEBAS->setTitle('JUDUL INPUT');
$form->edit->input->NAMABEBAS->setToogle($bool_show = false); // INI HANYA DIGUNAKAN JIKA INGIN MENAMPILKAN DALAM BENTUK TOOGLE
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
		-> untuk menambah input yang menjadi anggota multiinput ini
		-> penggunaan nya sama persis seperti manggunaan input lain (lihat CUSTOMIZE di atas)
getElements()
		-> untuk mendapatkan array berisi object elements anggota
*/

class FormMultiinput extends Form
{
	var $elements; // menyimpan element2 yang termasuk dalam multi ini
	var $delimiter;
	var $parent;
	var $isToogle;
	var $showToogle;

	function __construct()
	{
		$this->type     = 'multiinput';
		$this->elements = new stdClass;
		$this->setIsIncludedInSearch( false );
		$this->setIsIncludedInSelectQuery( false );
		$this->setIsIncludedInUpdateQuery( false );
		$this->setIsNeedDbObject( true );
		$this->setDelimiter(' ');
	}

	function setParent($obj)
	{
		$this->parent = $obj;
	}

	function addInput( $inputName, $inputType = 'text', $inputTitle='' )
	{
		$this->parent->addInput($inputName, $inputType);
		$this->parent->input->$inputName->setIsMultiInput( true );
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
		$this->elements->$inputName = $this->parent->input->$inputName;
	}

	function setToogle($show = false)
	{
		if ($this->actionType == 'roll' || $this->actionType == 'search')
		{
			die('FormMultiinput:: maaf form field ini hanya bisa digunakan untuk tipe edit dan add saja');
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

	function getElements()
	{
		return $this->elements;
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
		$out	= array();
		foreach ( $this->elements as $id => $input )
		{
			$object   = !empty($objects[$id]) ? $objects[$id] : new stdClass();
			$value    = $this->parent->getDefaultValue($input, @$object->data, @$object->i);
			$output   = $input->getOutput( $value, @$object->name, $this->parent->setDefaultExtra($input) );
			if ($this->isToogle)
			{
				if ( $input->isInsideRow &&  $input->isInsideCell )
				{
					$inputField = '';
					$title      = ucwords($input->title);
					if (!empty($input->textHelp))
					{
						$this->parent->help->value[$input->name] = $input->textHelp;
					}
					if (!empty($input->textTip))
					{
						$this->parent->tip->value[$input->name] = $input->textTip;
					}
					if(!empty ( $this->parent->help->value[$input->name] ))
					{
						$title .= ' '.help('<span style="font-weight: normal;">'.$this->parent->help->value[$input->name].'</span>');
					}
					$inputField .= '<div class="form-group"><label>'.$title.'</label>';
					if ($input->type=='checkbox' || $input->type=='multicheckbox' || $input->type=='radio')
					{
						$cls = ($input->type=='multicheckbox') ? 'checkbox' : $input->type;
						$inputField .= '<div class="input-group '.$cls.'">'.$output.'</div>';
					}else{
						$inputField .= $output;
					}
					if(!empty($this->parent->tip->value[$input->name]))
					{
						$inputField .= '<p class="help-block">'.$this->parent->tip->value[$input->name].'</p>';
					}
					$inputField	.= '</div>';
					$output = $inputField;
				}
			}else{
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
			}
			$out[] = $output;
		}
		if ($this->isToogle)
		{
			$display = $this->showToogle ? 'in' : 'on';
			$title   = ucwords($this->title);
			if(!empty($this->parent->help->value[$this->name]))
			{
				$title .= ' '.help('<span style="font-weight: normal;">'.$this->parent->help->value[$this->name].'</span>');
			}
			$inputField = implode('', $out);
			$out = <<<EOT
<div class="panel-group" id="accordion{$this->name}">
	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h4 class="panel-title" data-toggle="collapse" data-parent="#accordion{$this->name}" href="#pea_isHideToolOn{$this->name}" style="cursor: pointer;">
	    	{$title}
	    </h4>
	  </div>
	  <div id="pea_isHideToolOn{$this->name}" class="panel-collapse collapse {$display}">
	    <div class="panel-body">
	    	{$inputField}
			</div>
	  </div>
	</div>
</div>
EOT;
		}else{
			$out = implode($this->delimiter, $out);
			$out = preg_replace('~(<div[^>]+class=")(form-control)~is', '$1input-group', $out);
			$out = '<div class="form-inline">'.$out.'</div>';
		}
		return $out;
	}
}