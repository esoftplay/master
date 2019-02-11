<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

class phpEasyAdminLib
{
	var $arrInput;		// untuk nyimpan object input menjadi array, biar mudah di loop
	var $arrResult;		// untuk nyimpan hasil query select
	var $db;
	var $error;
	var $errorMsg;
	var $extraField;
	var $formName;
	var $formType;
	var $disableInput    = array();
	var $HideToolInit    = 'hide';
	var $isHideToolOn    = false;
	var $columnNumber    = 1;
	var $isMultiLanguage = false;
	var $isFormRequire   = false;
	var $isReportOn;
	var $LanguageTable;
	var $LanguageTableId;
	var $LanguageTableUpdate = array();
	var $LanguageTableWhere = '';
	var $report;
	var $reportData;
	var $saveTool   = true;		// apakah tool untuk update dipake ato tdk
	var $resetTool  = false; 	// apakah tool untuk reset dipake ato tdk
	var $deleteTool = false; 	// apakah tool untuk delete dipake ato tdk
	var $actionUrl  = '';
	var $methodForm = 'POST';
	var $sqlCondition;

	//untuk menset message berhasil yang akan keluar
	var $setSuccessSaveMessage='Success update data.';
	var $setFailSaveMessage='Failed update data.';

	//untuk menset message berhasil yang akan keluar
	var $setSuccessDeleteMessage='Success deleting data.';
	var $setFailDeleteMessage='Failed to delete data.';

	function initialize( $type, $str_table, $str_table_id='id', $str_sql_condition='')
	{
		$this->formType          = $type;
		$this->table             = $str_table;
		$this->tableId           = $str_table_id;
		$this->error             = false;
		$this->errorMsg          = '';
		$this->isReportOn        = false;
		$this->help              = new stdClass();
		$this->tip               = new stdClass();
		$this->input             = new stdClass();
		$this->report            = new stdClass();
		$this->saveButton        = new stdClass();
		$this->resetButton       = new stdClass();
		$this->deleteButton      = new stdClass();
		$this->isLoaded          = new stdClass();
		$this->extraField        = new stdClass();
		$this->arrInput          = array();
		$this->extraField->field = array();
		$this->extraField->value = array();
		$this->isLoaded->action  = false;
		$this->setDB();
		$this->setHideTool( $bool_is_hide_tool	= false, $init = 'SHOW' );
		$this->setFormName();
		if (!empty($str_sql_condition))
		{
			$this->setSqlCondition( $str_sql_condition );
		}
	}

	// agar tiap form itu punya identitas tersendiri
	function setFormName($string_form_name='')
	{
		if (empty($string_form_name))
		{
			$string_form_name=$this->formType;
		}
		$this->formName	= $string_form_name;
		$this->setSaveButton();
		$this->setDeleteButton();
	}
	// untuk ngeset nama instance dari objectDB yg mau digunakan
	function setDB( $db = '' )
	{
		if ( $db == '' )
		{
			global $db;
		}
		$this->db = $db;
	}
	function setQuoteSQL($sql)
	{
		$sql = trim($sql);
		if (preg_match('~^[a-z_0-9]+$~is', $sql))
		{
			$sql = "`{$sql}`";
		}
		return $sql;
	}
	function setSqlCondition( $sql_condition = '' )
	{
		if ( !empty( $sql_condition ) )
		{
			if ( !preg_match( '~where\s+~is', $sql_condition ) )
				$sql_condition	= 'where ' . $sql_condition;
		}
		$this->sqlCondition	= $sql_condition;
	}
	function setActionUrl($url='')
	{
		$this->actionUrl = $url;
	}
	function setMethodForm($method)
	{
		$this->methodForm=$method;
	}

	// addInput( $name, $type, $noColumn)
	// $noColumn digunakan untuk menentukan posisi kolom
	// apabila form tersebut menggunakan multi kolom
	function addInput( $name, $type, $noColumn = 1)
	{
		$type 		= ucfirst( strtolower( $type ) );
		$formClass 	= 'Form'. $type;
		$file		= _PEA_ROOT . 'form/Form'. $type .'.php';
		if ( !is_file( $file ) )
			die('Input Type <strong>'.$type.'</strong> not exist. '.$file.' not exist');

		include_once( $file );
		$this->input->$name = new $formClass;
		$lowername = strtolower($name);
		$this->input->$name->setActionType($this->formType);
		$this->input->$name->setFieldName($lowername);
		$this->input->$name->setFormName($this->formName);
		$this->input->$name->setName($lowername);
		$this->input->$name->setTitle(ucwords(str_replace('_', ' ', $lowername)));
		if ($this->formType=='search')
		{
			$key = str_replace($this->formName.'_','',$name);
			if (!empty($_GET[$key]))
			{
				$this->input->$name->setDefaultValue($_GET[$key]);
			}else{
				if (isset($_SESSION[$this->formName][$this->table][$this->input->$name->name]))
				{
					$this->input->$name->setDefaultValue($_SESSION[$this->formName][$this->table][$this->input->$name->name]);
				}else{
					$this->input->$name->setDefaultValue('');
				}
			}
		}else{
			if ($noColumn > $this->columnNumber)
			{
				$noColumn = $this->columnNumber%$noColumn;
			}
			$this->input->$name->setNoColumn($noColumn);
		}
		if ( $this->input->$name->isNeedDbObject )
		{
			$this->input->$name->setDbObject( $this->db );
			$this->input->$name->setTableName( $this->table );
			$this->input->$name->setTableId( $this->tableId );
			if (isset($this->sqlCondition)) {
				$this->input->$name->setSqlCondition( $this->sqlCondition );
			}
			$this->input->$name->setParent($this);
		}
		return $this->input->$name;
	}

	function setColumn($columnNumber)
	{
		if ($this->formType!='edit' && $this->formType!='add')
		{
			die('->setColumn($x); hanya berlaku untuk Form Add dan Edit');
		}
		if (is_numeric($columnNumber) && $columnNumber > 0)
		{
			if ((12%$columnNumber) > 0)
			{
				die('pada ->setColumn($x); $x hanya support salah satu dari angka 1,2,3,4,6 atau 12');
			}else{
				$this->columnNumber = $columnNumber;
			}
		}else{
			die('pada ->setColumn($x); $x harus di isi angka minimal satu');
		}
	}

	// EG. $add_query = array('class_id=1','room_id=2');
	function setLanguage($LanguageTableId = '', $LanguageTable = '', $add_query=array())
	{
		$this->isMultiLanguage = true;
		if ($LanguageTableId)$this->LanguageTableId= $LanguageTableId;
		else								$this->LanguageTableId= $this->table.'_id';
		if ($LanguageTable)	$this->LanguageTable	= $LanguageTable;
		else								$this->LanguageTable	= $this->table.'_text';
		if (!empty($add_query))
		{
			if (!is_array($add_query))
			{
				$add_query = array($add_query);
			}
			$this->LanguageTableWhere = ' AND '.implode(' AND ', $add_query);
			foreach ($add_query as $query)
			{
				list($var,$val) = explode('=', $query); // gak perlu di suppress (@) biar ketahuan kalo ada script error
				$this->LanguageTableUpdate[$var] = $val;
			}
		}
	}

	function addHeader( $name, $value = '' )
	{
		$this->addInput( $name, "header" );
		$this->input->$name->setTitle( $value );
	}

	// untuk melihat apakah form require (perlu validasi)
	// fungsi ini di panggil di action() sebelum mainForm
	function setIsFormRequire()
	{
		if (!$this->isFormRequire)
		{
			foreach ($this->arrInput as $input)
			{
				if ($input->isRequire)
				{
					$this->isFormRequire = true;
					break;
				}
			}
		}
		return $this->isFormRequire;
	}

	function getDefaultValue($input, $arrResult = array(), $i='')
	{
		$output = '';
		switch ($this->formType)
		{
			case 'roll':
				$output = isset($arrResult[$input->objectName]) ? $arrResult[$input->objectName] : '';
				switch ($input->type)
				{
					// jika type nya multiinput, maka argument untuk get Output beda
					case 'multiinput':
						$output = $this->getMultiElementObject($input, $arrResult, $i);
						break;
					// jika type nya multiselect atau multicheckbox dan relationtable di set, maka argument untuk get Output adalah value id
					case 'multiid':
					case 'multifile':
					case 'multiselect':
					case 'multicheckbox':
					case 'tags':
						if (!$input->isIncludedInSelectQuery) {
							$output = $arrResult[$input->tableId];
						}
						break;
					case 'sqllinks':
					case 'condition':
						$output = array($arrResult[$input->objectName], $arrResult[$this->tableId]);
						break;
				}
				break;
			case 'edit':
				$output	= ($input->type != 'multiinput') ? @$arrResult[$input->objectName] : $this->getMultiElementObject( $input, $arrResult, $i );
				switch ($input->type)
				{
					// jika type nya multiselect atau multicheckbox dan relationtable di set, maka argument untuk getOutput adalah value dari tableId
					case 'multiid':
					case 'multifile':
					case 'multiselect':
					case 'multicheckbox':
					case 'tags':
						if (!$input->isIncludedInSelectQuery)
						{
							$output = @$arrResult[$input->tableId];
						}
						break;
					case 'sqllinks':
					case 'condition':
						$output = array($arrResult[$input->objectName], $arrResult[$this->tableId]);
						break;
				}
				break;
			case 'add':
				$output = ( isset($input->defaultValue) ) ? $input->defaultValue : '';
				$output	= ( $input->type != 'multiinput' ) ? $output : $this->getMultiElementObject( $input, $arrResult, $i );
				break;
			case 'search':
				$output	= ($input->type != 'multiinput') ? $input->defaultValue : $this->getMultiElementObject($input, $arrResult, $i);
				break;
		}
		return $output;
	}

	// untuk membuat object yang dibutuhkan oleh FromMultiinput
	function getMultiElementObject ( $input, $arrResult, $i )
	{
		$out = array();
		$elements = $input->getElements();
		foreach ( $elements as $id=>$element )
		{
			$object       = new stdClass();
			$object->data = $arrResult;
			$object->name = is_numeric($i) ? $element->name."[$i]" : $element->name;
			$object->i    = $i;
			$out[$id]     = $object;
		}
		return $out;
	}

	function replaceTrailingComma( $sql )
	{
		return rtrim(trim($sql), ',');
	}

	//men set Message
	function setSuccessSaveMessage( $suc_message )
	{
		$this->setSuccessSaveMessage	= $suc_message;
	}
	//fail_message
	function setFailSaveMessage( $fail_message )
	{
		$this->setFailSaveMessage = $fail_message;
	}
	//men set Message
	function setSuccessDeleteMessage( $suc_message )
	{
		$this->setSuccessDeleteMessage	= $suc_message;
	}
	//fail_message
	function setFailDeleteMessage( $fail_message )
	{
		$this->setFailDeleteMessage = $fail_message;
	}

	//tambahin helpnya
	function addHelp( $field = '', $value = '' )
	{
		$field=$this->formName.'_'.$field."";
		$this->help->value[$field]	= $value;
	}
	function addTip( $field = '', $value = '' )
	{
		$field=$this->formName.'_'.$field."";
		$this->tip->value[$field]	= $value;
	}

	// untuk menambahkan suatu field itu diisi apa pada saat pemanggilan metho $this->action();
	// misalnya saat menambahkan news disuatu kategori, maka kita ingin cat_id di table news otomatis diisi 2 misalnya
	// c: $form->add->addExtraField( 'id', '2', 'edit' );
	function addExtraField( $field = '', $value = '', $formType='' )
	{
		if (empty($formType) || $formType==$this->formType)
		{
			$this->extraField->field[]	= $field;
			$this->extraField->value[]	= $value;
		}
	}

	function addReport( $type = 'all' )
	{
		if ( strtolower( $type ) == 'all' )
			$this->addReportAll();
		else
		{
			$type  = strtolower( $type );
			$class = 'phpRoll'. ucfirst( $type );
			$file  = _PEA_ROOT . 'report/'. $class .'.php';
			if ( !is_file( $file ) )
				die( 'Report Type <strong>'.$type.'</strong> not exist. '.$file.' not exist'  );
			include_once( $file );
			if (!$this->isReportOn)
			{
				link_css(_ROOT.'templates/admin/bootstrap/css/font-awesome.min.css');
			}
			$this->isReportOn	= true;
			$title = !empty($this->input->header->title) ? strip_tags($this->input->header->title) : 'Report';
			$this->report->$type = new $class($title);
		}
	}

	function addReportAll()
	{
		$file		= _PEA_ROOT . 'report/reportList.php';
		include( $file );
		foreach( $report as $type )
		{
			$this->addReport( $type );
		}
	}

	function setSaveButton( $name = 'submit_update', $value = 'SAVE', $icon = 'floppy-disk', $label='value' )
	{
		$this->__button('save', $name, $value, $icon, $label);
	}
	function setResetButton( $name = 'submit_update', $value = 'RESET', $icon = 'repeat', $label='value' )
	{
		$this->__button('reset', $name, $value, $icon, $label);
	}
	function setDeleteButton( $name = 'submit_delete', $value = 'DELETE', $icon = 'trash', $label='value' )
	{
		$this->__button('delete', $name, $value, $icon, $label);
	}
	private function __button($type, $name, $value, $icon, $label)
	{
		if ($label=='value') {
			$label = $value;
		}
		if (!empty($label)) {
			$label = ' '.$label;
		}
		$type .= 'Button';
		$this->$type->name  = $name ? $this->formName .'_'. $name : '';
		$this->$type->value = $value;
		$this->$type->icon  = $icon;
		$this->$type->label = $label;
	}

	// untuk ngeset, apakah akan mengaktifkan tombol save/reset/delete nya
	// jika tidak maka phpRollAdmin tidakakan menampilkan tombol sae
	// secara default saveTool aktif
	function setSaveTool( $bool_save_tool = false )
	{
		$this->__tool('save', $bool_save_tool);
	}
	function setResetTool( $bool_reset_tool = false )
	{
		$this->__tool('reset', $bool_reset_tool);
	}
	function setDeleteTool( $bool_delete_tool = false )
	{
		$this->__tool('delete', $bool_delete_tool);
	}
	private function __tool($type, $bool)
	{
		if ( $bool == 'on' ) $bool = true;
		elseif ( $bool == 'off' ) $bool = false;
		// set toggle button
		$tool = $type.'Tool';
		$this->$tool = $bool;
		// set Button Value
		$btn = $type.'Button';
		if (empty($this->$btn->name))
		{
			$func = 'set'.ucwords($btn);
			$this->$func();
		}
	}

	function getSuccessPage( $success_message = 'Succeed', $fail_message = 'Failed' )
	{
		$out = '';
		if ( !$this->error )
		{
			$out = msg($success_message,'success');
		}else{
			$out = msg($fail_message,'danger');
		}
		return $out;
	}

	function isError()
	{
		return $this->error;
	}

	function getErrorMsg()
	{
		return $this->errorMsg;
	}

	function getHeaderType()
	{
		$out	= '';
		foreach( $this->arrInput as $input )
		{
			if ( $input->isHeader )
			{
				if (!empty($input->title))
				{
					$out = $input->title;
					if (empty($this->hideToolTitle))
					{
						$this->setHideToolTitle($out);
					}
				}
				break;
			}
		}
		if ($this->isHideToolOn)
		{
			return '';
		}
		return $out;
	}

	//////////////////////////////////////////////////////////////////////////////////////////////
	//	Library untuk HideTool
	//////////////////////////////////////////////////////////////////////////////////////////////

	function setHideTool( $bool_is_hide_tool	= false, $init = 'HIDE' )
	{
		$this->isHideToolOn	= $bool_is_hide_tool;
		$this->HideToolInit	= $init;
	}

	function setHideToolTitle( $str_hide_tool_title	= '&nbsp;' )
	{
		$this->hideToolTitle	= $str_hide_tool_title;
	}

	function setTitle( $str_hide_tool_title	= '&nbsp;' )
	{
		$this->setHideToolTitle( $str_hide_tool_title );
	}

	function setHideToolInit( $init	= 'HIDE' )
	{
		$this->HideToolInit	= $init;
	}

	/*
	$input_name   = input name / fieldName yang akan di disable
	$value        = nilai yang akan dijadikan pembanding
	$preprocessor = symbol pembanding yang akan di gunakan di if()
	$field_name   = semisal akan mendisable $input_name tertentu tetapi acuan nya dari field lain, misal untuk disable input_name `active` jika  field_name `image`==''
	*/
	function setDisableInput($input_name, $value, $preprocessor='==', $field_name = '')
	{
		if (empty($field_name))
		{
			$field_name = $input_name;
		}
		if ($preprocessor=='=')
		{
			$preprocessor = '==';
		}
		if (strtolower($input_name) == 'delete')
		{
			if (empty($field_name) || $field_name == $input_name)
			{
				$field_name = $this->tableId;
			}
			$input_name = 'system_delete_tool';
		}
		$this->disableInput[$input_name][] = array($preprocessor, $value, $field_name);
	}
	function setDisableInputRecovery($values, $input_name)
	{
		$output = '';
		if (is_array($values))
		{
			foreach ($values as $i => $value)
			{
				$output .= $this->setDisableInputRecovery($value, $input_name.'['.$i.']');
			}
		}else{
			$output = '<input type="hidden" name="'.$input_name.'" value="'.urlencode($values).'" />';
		}
		return $output;
	}

	function setDefaultExtra($input)
	{
		$output = '';
		switch ($input->type)
		{
			case 'radio':
			case 'checkbox':
				if ($input->isRequire)
				{
					$output .= ' req="'.$input->isRequire.'"';
				}
			break;
			case 'checkboxdelete':
			case 'sqllinks':
			case 'sqlplaintext':
			case 'editlinks':
			case 'hidden':
			case 'plaintext':
			break;
			default:
				$title  = !empty($input->caption) ? $input->caption : $input->title;
				$title  = htmlentities($title);
				$output = 'class="form-control" title="'.$title.'" placeholder="'.$title.'"';
				if ($input->isRequire)
				{
					$output .= ' req="'.$input->isRequire.'"';
				}
				break;
		}
		return $output;
	}

	function getHideFormToolStart()
	{
		$out	= '';
		if ( $this->isHideToolOn )
		{
			if (empty($this->hideToolTitle))
			{
				$this->hideToolTitle = 'Form '.ucwords(str_replace('_', ' ', strtolower($this->table)));
			}
			$display = strtolower($this->HideToolInit)=='hide' ? 'on' : 'in';
			$out = <<<EOT
<div class="panel-group" id="accordion{$this->formName}">
	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h4 class="panel-title">
	      <a data-toggle="collapse" data-parent="#accordion{$this->formName}" href="#pea_isHideToolOn{$this->formName}">
	        {$this->hideToolTitle}
	      </a>
	    </h4>
	  </div>
	  <div id="pea_isHideToolOn{$this->formName}" class="panel-collapse collapse {$display}">
	    <div class="panel-body">
EOT;
		}
		return $out;
	}

	function getHideFormToolEnd()
	{
		$out	= '';
		if ( $this->isHideToolOn )
		{
			$out	.= <<<EOT
			</div>
	  </div>
	</div>
</div>
EOT;
		}
		return $out;
	}
	// clean SQL string
	function cleanSQL($q)
	{
		$o = preg_replace('~\\{2,}(\'|")~s', '\\$1', $q);
		$o = preg_replace("~([^\\\])('|\")~s", '$1\\\$2', $o);
		return $o;
	}

	//////////////////////////////////////////////////////////////////////////////////////////////
	//	DEBUG, hanya untuk backward compatibility
	//////////////////////////////////////////////////////////////////////////////////////////////

	var $bool_debug = false;

	function debug($bool_check, $string_debug = "", $string_ok_message="", $string_error_message = "")
	{
		if ($this->bool_debug){
			echo "<ul>\n";
			if ($bool_check)
				echo "	<li>OK</li>\n<li>$string_debug</li><li>$string_ok_message</li>\n";
			else
				echo "	<li>ERROR</li>\n<li>$string_debug</li><li>$string_error_message</li>\n";
			echo "</ul>\n";
		}
	}

	function setDebug($bool_debug="on"){
		if ($bool_debug == "on")
		{
			$this->bool_debug = true;
		}
		elseif ($bool_debug == "off")
		{
			$this->bool_debug = false;
		}
		else
			$this->bool_debug = $bool_debug;
	}

} // eof class