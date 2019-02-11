<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
DIGUNAKAN UNTUK COLUMN DI ROLL APABILA RUANG KOLOM TERLALU BANYAK MAKA BISA DIJADIKAN TOOLTIP
EXAMPLE :
$form->roll->addInput('field1','texttip');
$form->roll->input->field1->setTitle('Judul Input');
// $form->roll->input->field1->setCaption('Judul Tooltip {field2}');
$form->roll->input->field1->setNumberFormat();
$form->roll->input->field1->setTemplate('<table>
	<tr> <td>TEXT_BEBAS1</td> <td>: {field3}</td> </tr>
	<tr> <td>TEXT_BEBAS2</td> <td>: {field4}</td> </tr>
</table>');

$form->roll->input->field2->setNumberFormat();
$form->roll->input->field3->setNumberFormat();
$form->roll->input->field4->setNumberFormat();
*/

include_once _PEA_ROOT.'form/FormSqlplaintext.php';
class FormTexttip extends FormSqlplaintext
{
	var $elements; // menyimpan element2 yang termasuk dalam multi ini
	var $parent;
	var $position;
	var $template='';
	var $arrInput=array();

	function __construct()
	{
		$this->type = 'texttip';
		$this->elements	= new stdClass;
		$this->setIsIncludedInSearch( false );
		$this->setIsIncludedInUpdateQuery( false );
		$this->setIsNeedDbObject( true );
		$this->setPlaintext(true);
		$this->setPosition();
	}

	function setParent($obj)
	{
		$this->parent = $obj;
	}

	function setPosition($position='auto')
	{
		$this->position = $position;
	}
	function setCaption( $str_caption='' )
	{
		$this->caption	= $str_caption;
		$this->setInputs();
	}
	function setTemplate($template)
	{
		if (!empty($template))
		{
			$this->template = $template;
			$this->setInputs();
		}
	}
	function setInputs()
	{
		$arrInput = array();
		preg_match_all('~\{([a-z_]+)\}~is', $this->template, $match);
		if (!empty($match[1]))
		{
			foreach ($match[1] as $input)
			{
				$arrInput[] = $input;
			}
		}
		preg_match_all('~\{([a-z_]+)\}~is', $this->caption, $match);
		if (!empty($match[1]))
		{
			foreach ($match[1] as $input)
			{
				$arrInput[] = $input;
			}
		}
		$arrInput = array_unique($arrInput);
		foreach ($arrInput as $input)
		{
			if (!in_array($input, $this->arrInput)
				&& $input!=$this->fieldName)
			{
				$this->addInput($input,'sqlplaintext');
				$this->arrInput[] = $input;
			}
		}
	}
	function addInput( $inputName, $inputType = 'sqlplaintext', $inputTitle='' )
	{
		$this->parent->addInput($inputName, $inputType);
		$this->parent->input->$inputName->setIsMultiInput( true );
		if (!empty($inputTitle))
		{
			$this->parent->input->$inputName->setTitle($inputTitle);
		}
		$this->elements->$inputName = $this->parent->input->$inputName;
	}

	function setPlaintext( $bool_is_plaintext = false )
	{
		$this->isPlaintext		= $bool_is_plaintext;
		foreach ($this->elements as $element)
		{
			$element->setIsIncludedInUpdateQuery(!$bool_is_plaintext);
		}
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		$out      = parent::getReportOutput($str_value);
		$template = $this->template;
		$caption  = $this->caption;
		$template = str_replace('{'.$this->fieldName.'}', $out , $template);
		$caption  = str_replace('{'.$this->fieldName.'}', $out , $caption);
		if (!empty($template))
		{
			foreach ($this->elements as &$element)
			{
				$fieldName = trim($element->fieldName);
				if (!empty($element->myfield))
				{
					$fieldName = $element->myfield;
				}else{
					preg_match('~([a-z0-9_]+)$~is', $fieldName, $m);
					if (!empty($m[1]))
					{
						$fieldName = $m[1];
						$element->myfield = $m[1];
					}
				}
				if (!empty($fieldName))
				{
					$fieldValue = $element->getOutput(@$this->parent->arrResult[$fieldName]);
					$template   = str_replace('{'.$fieldName.'}', $fieldValue , $template);
					$caption    = str_replace('{'.$fieldName.'}', $fieldValue , $caption);
				}
			}
		}
		if (empty($caption))
		{
			$caption=$out;
		}
		$output = '<span class="tips" title="'.htmlentities($caption)
				.'" data-toggle="popover" data-placement="'.$this->position
				.'" data-content="'.htmlentities(trim($template)).'">'.$this->extra[0].$out.$this->extra[1].'</span>';
		if (empty($GLOBALS['sys']->tips))
		{
			$GLOBALS['sys']->tips = 1;
			$output .= '<script type="text/javascript">var BS3load_popover = 1;</script>';
		}
		return $this->getReturn($output);
	}
}