<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
untuk membuat select form yg simple
Example:
$form->edit->addInput( 'film_id', 'selecttable' );
$form->edit->input->film_id->setTitle('Film yang ditayangkan');
$form->edit->input->film_id->setReferenceTable('filmes ORDER BY title');			# untuk ngeset table yang mau di select sebagai reference
$form->edit->input->film_id->setReferenceField( 'title', 'id' );							# untuk ngeset nama field yang mau jadi label dan jadi value pada select
#form->edit->input->film_id->setReferenceCondition( 'active=1' );							# Menentukan tambahan untuk perintah MySQL di WHERE
#form->edit->input->film_id->setReferenceNested( 'par_id' );									# untuk membuat option selection berbentuk akar
#form->edit->input->film_id->setAllowNew($bool_or_str, $add_query = array());	# jika boolean (true/false) akan mengaktifkan option tambahan untuk menambah option, jika string maka dianggap true dan string tsb dijadikan title pada opsi tambahan tsb
#form->edit->input->film_id->setDB('db1');																		# diisi string untuk database lain berdasarkan urutan di config.php
Eg. $add_query = array('active=1', 'publish=1') || 'active=1'
                 ===========array==============    ==string==

->addOption( $option, $value);
		untuk menambah option dari select form
		c: $this->addOption( "umur 18", 18 );

->addOptionArray( $arrOption, $arrValue = array() );
		untuk menambah option, tapi langsung, jadi dalam bentuk array

->setLinks($url);
		untuk meng-link kan ke URL lain, sehingga selection otomatis kondisi plaintext

->setAutoComplete(true);																// Menjadi kan selection field berbentuk text suggestion, berguna untuk table ber data besar (->setReferenceNested() bakal gak jalan)
->setAutoComplete('parent_name'[, 'parent_field_sql']); // menjadikan field parent_name menjadi parentnya dan jika diperlukan nilai parent_name akan menjadi nilai parent_field dlm table berhubungan
->setAutoComplete($option);															// $option adalah array yg digunakan untuk me replace function autocomplete yang sudah ada
## Contoh bikin AutoComplete tanpa PEA _ROOT.'modules/user/tags.php';

## $option di atas bisa di input variable array dengan pilihan key sebagai berikut: (key dari array incasesensitive)
			'onfind'         => "function(a){if(a==null)return alert('No match!');if(!!a.extra){$('#test_onfind_ac').val(a.extra[0]);$('#test_onfind').val(a.extra[1]);alert('I execute special function!! AND Unbind content tags suggestion');$('#tag_id').autocomplete('clear');}else{var b=a.selectValue;}}"
			'onselect'       => "null"
			'formatItem'     => "function(a){return a[2]}"
			'url'            => _URL."user/selecttable" 			// BISA DIGANTI KE URL YG DIINGINKAN
			'data'           => "function(){return {}};"
			'inputClass'     => "ac_input"
			'resultsClass'   => "ac_results"
			'loadingClass'   => "ac_loading"
			'lineSeparator'  => "\n"
			'cellSeparator'  => "|"
			'minChars'       => "2"
			'delay'          => "10"
			'matchCase'      => "0"
			'matchSubset'    => "1"
			'matchContains'  => "1"
			'cacheLength'    => "10"
			'mustMatch'      => "0"
			'extraParams'    => "function(){return {}};"
			'selectFirst'    => "true"
			'selectOnly'     => "false"
			'maxItemsToShow' => "-1"
			'autoFill'       => "false"
		Contoh:
		->setAutoComplete(array('minChars'=>3));
		->setAutoComplete(array('url' => $Bbc->mod['circuit'].'.'.$Bbc->mod['task']));
*/
include_once( _PEA_ROOT . 'form/FormMulticheckbox.php' );
class FormSelecttable extends FormMulticheckbox
{
	var $links;
	var $getName = 'id';
	var $referenceTable;
	var $referenceField;
	var $isAllowedNew          = false;
	var $referenceNested       = false;
	var $isAutoComplete        = false;
	var $parentField           = '';
	var $addSQL                = '';
	var $referenceNestedField  = '';
	var $sqlReferenceCondition = array();
	var $allowNewQuery         = array();
	var $option                = array();
	var $optionValue           = array();
	var $isModal;

	function __construct()
	{
		$this->type = 'selecttable';
		$this->setIsNeedDbObject( true );
		$this->setReferenceTable( '' );
		$this->setReferenceField( '', '' );
		$this->isModal = false;
		if(empty($this->isLoaded))
		{
			$this->isLoaded = new stdClass();
		}
		$this->isLoaded->addOptionFromTable = false;
	}

	function addOption( $option, $value = null )
	{
		if (is_array($option))
		{
			$this->addOptionArray($option,$value);
		}else{
			$this->option[]      = $option;
			$this->optionValue[] = isset($value) ? $value : $option;
		}
	}

	function setAutoComplete($bool_or_array_or_string = true, $parent_field_sql='')
	{
		if ($bool_or_array_or_string && !empty($this->relationTable))
		{
			die( "FormSelecttable::  setAutoComplete() hanya bisa digunakan jika tidak menggunakan Relation Table." );
		}
		if ($bool_or_array_or_string && !$this->isIncludedInSelectQuery && !$this->isIncludedInUpdateQuery)
		{
			die( "FormSelecttable::  setAutoComplete() tidak bisa digunakan jika `isIncludedInSelectQuery` dan `isIncludedInUpdateQuery` bernilai FALSE, contoh jika digunakan dalam multiform" );
		}
		if (is_bool($bool_or_array_or_string))
		{
			$this->isAutoComplete = $bool_or_array_or_string;
		}else{
			$this->isAutoComplete = true;
			$r = array();
			if (is_string($bool_or_array_or_string))
			{
				$r[] = 'data-parent="'.$this->formName.'_'.$bool_or_array_or_string.'"';
				if (empty($parent_field_sql))
				{
					$parent_field_sql = $bool_or_array_or_string;
				}
				$this->parentField = $parent_field_sql;
			}else
			if (is_array($bool_or_array_or_string))
			{
				if (!empty($bool_or_array_or_string['url']))
				{
					$bool_or_array_or_string['url'] = site_url($bool_or_array_or_string['url']);
				}
				foreach ($bool_or_array_or_string as $key => $value)
				{
					if ($key=='sql')
					{
						$this->addSQL = $value;
					}else{
						$r[] = 'data-'.$key.'="'.$value.'"';
					}
				}
			}
			if (!empty($r))
			{
				$this->setExtra(implode(' ', $r));
			}
		}
	}

	function addOptionFromTable()
	{
		if ( $this->isLoaded->addOptionFromTable ) return;
		$this->isLoaded->addOptionFromTable = true;

		if( $this->referenceTable == ''
			||$this->referenceField['value'] == ''
			||$this->referenceField['label'] == ''
			)
		{
			die( "FormSelecttable::  setReferenceTable() dan setReferenceField() harus diset untuk menentukan table dan field yang digunakan." );
		}

		// meng query data dari table reference
		$result = array();
		if ($this->referenceNested)
		{
			$sql = "SELECT "
			. $this->referenceField['value'] ." AS `id`,"
			. $this->referenceNestedField ." AS `par_id`, "
			. $this->referenceField['label'] ." AS `title`"
			. " FROM ". $this->getTableCondition($this->referenceTable, $this->sqlReferenceCondition);
			if (!preg_match('~\s+order\s+by\s+~is', $sql))
			{
				$sql .= ' ORDER BY `par_id`, `title` ASC';
			}
			$arr	= _func('array', 'path', $this->db->getAll($sql), 0, '&gt;', '', '--');
			foreach ($arr as $key => $value)
			{
				$result[] = array(
					'label' => $value,
					'value' => $key
					);
			}
		}else{
			$sql = "SELECT ". $this->referenceField['value'] ." as `value`,". $this->referenceField['label'] ." as `label` FROM ". $this->getTableCondition($this->referenceTable, $this->sqlReferenceCondition);
			$result	= $this->db->getAll( $sql );
		}
		foreach ( $result AS $a )
		{
			$this->addOption( $a['label'], $a['value']);
		}
	}

	function setLinks( $str_links = '' )
	{
		$this->setPlaintext(true);
		$this->links = $str_links;
	}

	function setUrlPop( $str_links,$title="View")
	{
		$this->setPlaintext(true);
		$this->links    = $str_links;
		$this->linksPop = $title;
		$this->setModal(false);
		if (empty($this->popWidth))
		{
			$this->setSizePop();
		}
	}

	function setSizePop( $widht=480,$height=640)
	{
		$this->popWidth		= $widht;
		$this->popHeight	= $height;
	}

	function setGetName( $str_get_name = 'id' )
	{
		$this->getName = $str_get_name;
	}

	function setModal($boolean = true)
	{
		$this->isModal = $boolean ? true : false;
	}

	function setAllowNew($boolean_or_string = true, $add_query = array())
	{
		if (is_string($boolean_or_string))
		{
			$this->isAllowedNew      = true;
			$this->isAllowedNewTitle = $boolean_or_string;
		}else{
			$this->isAllowedNew = $boolean_or_string ? true : false;
			if (empty($this->isAllowedNewTitle))
			{
				$this->isAllowedNewTitle = '+++ New '.$this->title.' +++';
			}
		}
		if (!empty($add_query))
		{
			$this->setAllowNewQuery($add_query);
		}
	}

	function setAllowNewQuery($values)
	{
		if (is_array($values))
		{
			foreach ($values as $value)
			{
				$this->setAllowNewQuery($value);
			}
		}else{
			$this->allowNewQuery[] = $values;
		}
	}

	// getRollUpdateSQL, getAddSQL, getSearchQuery di bikin supaya tidak menggunakan yang ada di FormMulticheckbox
	function getRollUpdateSQL( $i = '' )
	{
		if ($this->isAllowedNew)
		{
			if ( $i == '' && !is_int($i) )
			{
				$post	= $_POST[$this->name];
			}else{
				$post	= $_POST[$this->name][$i];
			}
			if (preg_match('~^new\|(.*?)$~s', $post, $match))
			{
				$val = $this->cleanSQL($match[1]);
				$q = "INSERT INTO {$this->referenceTable} SET `".$this->referenceField['label']."`='".$val."'";
				if (!empty($this->allowNewQuery))
				{
					$q .= ', '.implode(', ', $this->allowNewQuery);
				}else
				if (!empty($this->sqlReferenceCondition))
				{
					$q .= ', '.implode(', ', $this->sqlReferenceCondition);
				}
				if($this->db->Execute($q))
				{
					if ( $i == '' && !is_int($i) )
					{
						$_POST[$this->name] = $this->db->Insert_ID();
					}else{
						$_POST[$this->name][$i] = $this->db->Insert_ID();
					}
				}
			}
		}
		return Form::getRollUpdateSQL($i);
	}
	function getAddSQL()
	{
		if ($this->isAllowedNew)
		{
			$post = $this->cleanSQL($_POST[$this->name]);
			if (preg_match('~^new\|(.*?)$~s', $post, $match))
			{
				$q = "INSERT INTO {$this->referenceTable} SET `".$this->referenceField['label']."`='".$match[1]."'";
				if (!empty($this->allowNewQuery))
				{
					$q .= ', '.implode(', ', $this->allowNewQuery);
				}else
				if (!empty($this->sqlReferenceCondition))
				{
					$q .= ', '.implode(', ', $this->sqlReferenceCondition);
				}
				if($this->db->Execute($q))
				{
					$_POST[$this->name] = $this->db->Insert_ID();
				}
			}
		}
		return Form::getAddSQL();
	}
	function getSearchQuery()
	{
		return Form::getSearchQuery();
	}
	function getReportOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		$name	= ( $str_name == '' ) ? $this->name : $str_name;
		$extra= !empty($str_extra) ? $str_extra : $this->extra;
		if ( $this->isPlaintext || $this->isAutoComplete )
		{
			global $Bbc;
			if (in_array($str_value, $this->optionValue))
			{
				$i = array_search($str_value, $this->optionValue);
				return @$this->option[$i];
			}else
			if (preg_match('~^([a-z0-9_\-]+)~is', $name, $m))
			{
				$name = $m[1];
				if (!isset($Bbc->getPlaintexOutput[$name][$str_value]))
				{
					if (!isset($Bbc->getPlaintexOutputTable[$this->referenceTable]))
					{
						$table = $Bbc->getPlaintexOutputTable[$this->referenceTable] = $this->referenceTable;
						if(!preg_match('~ left join ~is', $this->referenceTable) && preg_match('~([a-z0-9_\-]+)~is', $this->referenceTable, $m))
						{
							$table = $Bbc->getPlaintexOutputTable[$this->referenceTable] = $m[1];
						}
					}else{
						$table = $Bbc->getPlaintexOutputTable[$this->referenceTable];
					}
					$str_value = is_numeric($str_value) ? $str_value : "'{$str_value}'";
					if (!empty($str_value) || $str_value==0)
					{
						$sql = 'SELECT '.$this->referenceField['label'].' FROM '.$table.' WHERE '.$this->referenceField['value']."={$str_value}";
						$out = $this->db->getOne($sql);
						$Bbc->getPlaintexOutput[$name][$str_value] = $out ? $out : '';
					}else{
						$Bbc->getPlaintexOutput[$name][$str_value] = '';
					}
				}
				if (is_array($extra))
				{
					$r = array_values($extra);
					$output = $r[0].$Bbc->getPlaintexOutput[$name][$str_value].$r[1];
				}else{
					$output = $Bbc->getPlaintexOutput[$name][$str_value];
				}
				return $output;
			}
		}
		if ( !$this->isLoaded->addOptionFromTable )
		{
			$this->addOptionFromTable();
		}
		$out= '';
		foreach ( $this->optionValue as $i => $value )
		{
			if ($value==$str_value)
			{
				$out = $this->option[$i];
			}
		}
		return $out;
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( $this->isPlaintext )
		{
			$out = $this->getPlaintexOutput($str_value, $str_name, $str_extra);
			/* JIKA EDIT PENGKONDISIAN DI BAWAH MAKA EDIT JUGA FormSelect::getOutput() */
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
						$out .= '<script type="text/javascript">function selecttablePop(a){var b=this.open(a.href, "'.$this->linksPop.'", "width='.$this->popWidth.', height='.$this->popHeight.', align=top, scrollbars=yes, status=no, resizable=yes");b.window.focus(); return false;}</script>';
					}
					$link .= '" onclick="return selecttablePop(this);';
				}else
				if ($this->isModal)
				{
					if (!empty($extra))
					{
						$extra = trim($extra).' ';
					}
					$extra .= ' rel="editlinksmodal"';
					link_js(_LIB.'pea/includes/formLinkModal.js', false);
					icon('fa-ok');
				}
				$out .= '<a href="'.$link.'"'.$extra.'>'. $txt .'</a>';
			}
			return $out;
		}
		$name	 = ( $str_name == '' ) ? $this->name : $str_name;
		$extra = $this->extra .' '. $str_extra;
		if ($this->isAutoComplete)
		{
			link_js(_PEA_URL.'includes/FormTags.js', false);
			$value = $this->getReportOutput($str_value);
			$param = array(
								'table'  => $this->getTableCondition($this->referenceTable, $this->sqlReferenceCondition),
								'field'  => $this->referenceField['label'],
								'id'     => $this->referenceField['value'],
								'expire' => strtotime('+2 HOURS'),
								);
			if (!empty($this->parentField))
			{
				$param['parent'] = $this->parentField;
			}
			if (!empty($this->db_str))
			{
				$param['db'] = $this->db_str;
			}
			$token = encode(json_encode($param));
			$out   = '<input type="text" name="'.$name.'" value="'.$str_value.'" rel="ac" data-value="'.$value.'" data-token="'.$token.'"'.$extra.' />';
		}else{
			if ( !$this->isLoaded->addOptionFromTable )
			{
				$this->addOptionFromTable();
			}
			if ($this->isAllowedNew)
			{
				link_js(_PEA_URL.'includes/FormSelecttable.js', false);
				$extra = str_replace('class="', 'data-newlabel="'.$this->isAllowedNewTitle.'" class="allow_new ', $extra);
			}
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
				$out  .= '<option value="'.$value.'"'.$sel.'>'. $option .'</option>';
			}
			if ($this->isAllowedNew)
			{
				if (empty($this->option))
				{
					$out  .= '<option></option>';
				}
				$out .= '<option>'. $this->isAllowedNewTitle .'</option>';
			}
			$out	.= '</select>';
		}
		return $out;
	}
}