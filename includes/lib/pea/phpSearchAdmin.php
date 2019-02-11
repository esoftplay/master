<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
EXAMPLE: membuat form Search
$form = _lib('pea',  'table_name');
$form->initSearch();

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('field_names_with_comma', $isFullText);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();

echo $form->search->getForm();
*/
class phpSearchAdmin extends phpEasyAdminLib
{
	var $tableId;

	function __construct( $str_table, $str_sql_condition = '1', $current_file = '' )
	{
		$this->initialize('search', $str_table);
		$this->sqlCondition = $str_sql_condition;
		$this->sqlCondition = ( $this->sqlCondition == '' ) ? '1' : $this->sqlCondition;

		$this->setSaveTool(true);
		$this->setResetTool(true);
		$this->setDeleteTool(false);

		$this->setSaveButton('submit_search', 'SEARCH', 'search', '' );
		$this->setResetButton('submit_search', 'RESET', 'remove-circle', '' );
	}

	function getSearchCondition()
	{
		return $this->searchCondition;
	}

	// untuk mengambil semua keyword dalam pencarian
	function keyword()
	{
		$output = array();
		if ( empty($this->arrInput) ) $this->arrInput	= get_object_vars( $this->input );
		if (!empty($_SESSION[$this->formName][$this->table]))
		{
			foreach ($_SESSION[$this->formName][$this->table] as $key => $value)
			{
				$output[str_replace($this->formName.'_', '', $key)] = $value;
			}
		}
		foreach ( $this->arrInput as $input )
		{
			if ( $input->isIncludedInSearch )
			{
				$name = str_replace($this->formName.'_', '', $input->name);
				if (isset($_GET[$name]))
				{
					$output[$name] = $_GET[$name];
				}
			}
		}
		//menambahkan yang additional field dan valuenya
		foreach ( $this->extraField->field as $i => $field )
		{
			$output[$field] = $this->extraField->value[$i];
		}
		$this->arrResult = $output;
		return $output;
	}

	function fetchSearchCondition()
	{
		$cond	= '';
		if ( !empty( $this->arrSearchCondition ) )
		{
			$cond = implode(' AND ', $this->arrSearchCondition);
		}
		if ($this->sqlCondition != '1')
		{
			if (!empty($cond))
			{
				$cond = $this->sqlCondition.' AND '.$cond;
			}else{
				$cond = $this->sqlCondition;
			}
		}else{
			if (!empty($cond))
			{
				$cond = 'WHERE '.$cond;
			}else{
				$cond = 'WHERE 1';
			}
		}
		$this->searchCondition	= $cond;
	}

	// getMainForm() mengembalikan form complete, tapi tanpa submit button, tanpa navigasi, tanpa header title
	function getMainForm()
	{
		$this->arrInput	= get_object_vars( $this->input );
		$out = '';
		foreach( $this->arrInput as $input )
		{
			if (!$input->isInsideMultiInput)
			{
				$defaultValue = $this->getDefaultValue($input);
				$inputField   = $input->getOutput( $defaultValue, $input->name, $this->setDefaultExtra($input));
				if ( $input->isInsideRow &&  $input->isInsideCell )
				{
					if ($input->isHeader)
					{
						if (!empty($input->title))
						{
							$out .= '<p class="form-group text-info"><strong>'.$input->title.'</strong></p>';
						}
					}else{
						if ($input->type=='checkbox' || $input->type=='radio')
						{
							$out .= '<div class="'.$input->type.'">';
							$out .= '<label>'.$inputField.'</label>';
						}else{
							$out .= '<div class="form-group">';
							$out .= '<label class="sr-only">'.ucwords($input->title).'</label>';
							$out .= $inputField;
						}
						$out	.= '</div> ';
					}
				}else $out .= $inputField;
			}
		}
		return $out;
	} // end getMainForm

	function getForm()
	{
		$this->action();
		$this->keyword();
		$mainForm = $this->getMainForm();
		if	($this->saveTool)
		{
			$mainForm .='<button type="submit" name="'.$this->saveButton->name.'" value="'.$this->saveButton->value
								.	'" class="btn btn-default"><span class="glyphicon glyphicon-'.$this->saveButton->icon.'"></span>'
								.	$this->saveButton->label .'</button> ';
		}
		if ($this->resetTool)
		{
			$mainForm .= '<button type="submit" name="'.$this->resetButton->name.'" value="'.$this->resetButton->value
								.	'" class="btn btn-default"><span class="glyphicon glyphicon-'.$this->resetButton->icon
								.	'"></span>'.$this->resetButton->label.'</button> ';
		}
		$out         = <<<EOT
<form method="{$this->methodForm}" action="{$this->actionUrl}" name="{$this->formName}" class="form-inline pull-right" role="form">
	{$mainForm}
</form>
<div class="clearfix"></div>
EOT;
		return $out;
	}

	function action()
	{
		if ($this->isLoaded->action)
		{
			return $this->getSearchCondition();
		}
		if ( empty($this->arrInput) ) $this->arrInput	= get_object_vars( $this->input );
		$this->isLoaded->action	= true;
		if (isset($_POST[$this->saveButton->name]))
		{
			$search_key = array();
			if ($_POST[$this->saveButton->name]==$this->saveButton->value)
			{
				foreach ( $this->arrInput as $input )
				{
					$search_key[] = $input->name;
					if ( $input->isIncludedInSearch )
					{
						$false = '0';
						// ini untuk jaga2 jika menggunakan pencarian checkbox
						if ($input->type == 'checkbox')
						{
							if (!isset($_POST[$input->name]))
							{
								$_POST[$input->name] = $false = @$input->value[1];
							}
						}
						if (!empty($_POST[$input->name]) || @$_POST[$input->name] == $false)
						{
							$_SESSION[$this->formName][$this->table][$input->name] = $_POST[$input->name];
						}else{
							unset($_SESSION[$this->formName][$this->table][$input->name]);
						}
					}
				}
			}else{
				foreach ( $this->arrInput as $input )
				{
					if ( $input->isIncludedInSearch )
					{
						$search_key[] = $input->name;
						unset($_SESSION[$this->formName][$this->table][$input->name]);
					}
				}
			}
			$this->redirect($search_key);
		}
		$searchCondition = array();
		foreach ((array)$this->arrInput as $input)
		{
			if ($input->isIncludedInSearch)
			{
				$cond = $input->getSearchQuery();
				if (!empty($cond))
				{
					$searchCondition[$input->name] = $cond;
				}
			}
		}
		//menambahkan yang additional field dan valuenya
		foreach ( $this->extraField->field as $i => $field )
		{
			$value = $this->extraField->value[$i];
			if (!is_numeric($value))
			{
				$value = "'{$value}'";
			}
			$searchCondition[$field] = '`'.$field.'`='.$value;
		}
		$this->arrSearchCondition	= $searchCondition;
		$this->fetchSearchCondition();
		return $this->getSearchCondition();
	} // eo action() method

	function redirect($search_key=array())
	{
		$search_key = array_merge($search_key, array($this->formName.'_menu_id'));
		if(preg_match('~^(.*?)(?:\?(.*?))?$~s',$_SERVER['REQUEST_URI'], $m))
		{
			$url = $m[1];
		  if(!empty($m[2]))
		  {
		  	parse_str($m[2], $r);
		  	foreach ($r as $key => $value)
		  	{
		  		if (in_array($this->formName.'_'.$key, $search_key))
		  		{
		  			unset($r[$key]);
		  		}
		  	}
		    if (!empty($r))
		    {
		    	$url .= '?'.http_build_query($r);
		    }
		  }
		  if (_ADMIN=='')
		  {
		  	global $Bbc;
		  	if (!empty($Bbc->menu) && !empty($Bbc->menu->all_array))
		  	{
		  		$uri = preg_replace('~^'._URI.'~', '', $url);
			  	foreach ($Bbc->menu->all_array as $d)
			  	{
			  		if ($d['link']==$uri)
			  		{
			  			$url = _URL.$d['seo'].'.html';
			  			break;
			  		}
			  	}
		  	}
		  }
		}else{
		  $url = _URL;
		}
		redirect($url);
	}

} // eo class