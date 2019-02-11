<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
$form->edit->addInput( 'fieldname', 'select' );
$form->edit->input->fieldname->setTitle('Judul');

## MENAMBAH OPTION LABEL DAN VALUE DALAM FORMAT STRING
$form->edit->input->fieldname->addOption('label', 'value');

## LABEL DAN VALUE NILAINYA AKAN SAMA
$form->edit->input->fieldname->addOption(array('nol', 'satu', 'dua', 'tiga'));

## LABEL DIAMBIL DARI INPUT KE 1, DAN VALUE DIAMBIL DARI INPUT KE 2 DENGAN KEY ARRAY YANG SAMA
$form->edit->input->fieldname->addOption(array('nol', 'satu', 'dua', 'tiga'), array(0,1,2,3));

## VALUE DIAMBIL DARI ARRAY KEY 0, LABEL DIAMBIL DARI ARRAY KEY 1 DI DALAM INPUT ARRAY
$form->edit->input->fieldname->addOption(array(array('0', 'nol'), array('1', 'satu'), array('2', 'dua'), array('3', 'tiga')));

## HASILNYA AKAN SAMA PERSIS SEPERTI DIATAS JADI MENGACUHKAN NILAI DARI TIAP ARRAYKEY (LANGSUNG DIAMBIL VALUENYA)
$form->edit->input->fieldname->addOption(array(array('key1' => '0','key2' => 'nol'), array('key1' => '1','key2' => 'satu'), array('key1' => '2','key2' => 'dua'), array('key1' => '3','key2' => 'tiga')));
*/
include_once( _PEA_ROOT . 'form/FormSelecttable.php' );
class FormSelect extends FormSelecttable
{
	function __construct()
	{
		$this->setIsNeedDbObject( true );
		$this->type = 'select';
		if(empty($this->isLoaded))
		{
			$this->isLoaded = new stdClass();
		}
		$this->isLoaded->addOptionFromTable = true; // untuk menghindari method parent addOptionFromTable() terpanggil
	}
	function setAllowNew($boolean_or_string = true, $add_query = array())
	{
		die('formSelect:: tidak menerima method setAllowNew(); dikarenakan tidak terhubung dengan table apapun');
	}
	function setAutoComplete($bool_or_array_or_string = true, $parent_field='')
	{
		die('formSelect:: tidak menerima method setAutoComplete(); dikarenakan tidak terhubung dengan table apapun');
	}
	function getReportOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if (in_array($str_value, $this->optionValue))
		{
			$i = array_search($str_value, $this->optionValue);
			return @$this->option[$i];
		}
		return '';
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( $this->isPlaintext )
		{
			$out = $this->getPlaintexOutput($str_value, $str_name, $str_extra);
			/* JIKA EDIT PENGKONDISIAN DI BAWAH MAKA EDIT JUGA FormSelecttable::getOutput() */
			if (!empty($this->links))
			{
				$txt   = $out;
				$out   = '';
				$link  = $this->links;
				$link .= !preg_match( '~\?~s', $link) ? '?' : '&';
				$link .= $this->getName.'='.$str_value;
				$link .= '&return='.urlencode(seo_uri());
				$extra = trim(preg_replace('~\s+class="form\-control"~is', '', $this->extra .' '. $str_extra));
				if (!empty($extra))
				{
					$extra = ' '.$extra;
				}
				if (!empty($this->linksPop))
				{
					global $Bbc;
					if (empty($Bbc->FormSelecttable_is_load))
					{
						$Bbc->FormSelecttable_is_load = 1;
						$out .= '<script type="text/javascript">function selectPop(a){var b=this.open(a.href, "'.$this->linksPop.'", "width='.$this->popWidth.', height='.$this->popHeight.', align=top, scrollbars=yes, status=no, resizable=yes");b.window.focus(); return false;}</script>';
					}
					$link .= '" onclick="return selectPop(this);';
				}
				$out .= '<a href="'.$link.'"'.$extra.'>'. $txt .'</a>';
			}
			return $out;
		}
		$name	= ( $str_name=='' ) ? $this->name : $str_name;
		$extra= $this->extra .' '. $str_extra;
		$out 	= '<select name="'. $name .'" '.$extra.'>';
		foreach ( (array)@$this->option as $i => $option )
		{
			$value = $this->optionValue[$i];
			$sel   = '';
			if ($str_value == $value && empty($hasSelected))
			{
				$sel = ' selected';
				$hasSelected = 1;
			}
			$out .= '<option value="'.$value.'"'.$sel.'>'. $option .'</option>';
		}
		$out	.= '</select>';
		return $out;
	}
}