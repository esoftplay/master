<?php  if(!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
EXAMPLE: membuat form List beserta form filter nya
$form = _lib('pea',  'table_name');
$form->initSearch();

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('field_names_with_comma', $isFullText);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();

echo $form->search->getForm();

#$form = _lib('pea',  'table_name');
$form->initRoll($add_sql);

#$form->roll->setLanguage();
$form->roll->setSaveTool(true);

$form->roll->addInput('title','sqlplaintext');
$form->roll->input->title->setTitle('Title');
#$form->roll->input->title->setLanguage();

$form->roll->action();
echo $form->roll->getForm();
*/
include_once(_PEA_ROOT . 'nav.inc.php');
class phpRollAdmin extends phpEasyAdminLib
{
	var $tableId;
	var $nav;			// untuk nyimpen obyek dari class oNav (navigasi prev next)
	var $isChangeBc   = true;	// apakah roll berubah bgcolornya saat on hover
	var $onDelete     = array('', '');
	var $onDeleteArgs = array('', '');
	var $onSave       = array('', '');
	var $onSaveArgs   = array('', '');

	//men set method pada form
	var $onEachDelete;
	var $onEachSave;
	var $isActionExecute;
	var $isOptionalColumn;
	var $optionalColumn;
	var $optionalCaption;
	var $setFailSaveMessage;
	var $arrDeletedId, $intNumRows, $load_lang, $orderUrl, $strField2Lang;

	function __construct($str_table, $str_sql_condition = '', $str_table_id='id', $arr_folder= array())
	{
		$this->initialize('roll', $str_table, $str_table_id, $str_sql_condition);

		$this->setSaveTool(true);
		$this->setResetTool(false);
		$this->setDeleteTool(true);

		$this->isActionExecute  = true;
		$this->isOptionalColumn = false;
		$this->orderUrl         = '';
		$this->setNumRows();
	}

	function setNumRows($int_num_rows = 0)
	{
		$this->intNumRows	= (!$int_num_rows) ? config('rules', 'num_rows') : $int_num_rows;
	}

	function setActionExecute($bool=true, $msg='')
	{
		$this->isActionExecute = $bool;
		if (!empty($msg))
		{
			if ($bool)
			{
				$this->setSuccessSaveMessage($msg);
			}else{
				$this->setFailSaveMessage($msg);
			}
		}
	}

	function onDelete ($func_name_on_delete = '', $arr_on_delete_args = array(), $call_after_saved = false)
	{
		$i                      = $call_after_saved ? 1 : 0;
		$this->onDelete[$i]     = $func_name_on_delete;
		$this->onDeleteArgs[$i] = $arr_on_delete_args;
	}
	function onEachDelete ($func_name_on_each_delete = '')
	{
		$this->onEachDelete 	= $func_name_on_each_delete;
	}

	function onSave ($func_name_on_save = '', $arr_on_save_args = array(), $call_after_saved = false)
	{
		$i                    = $call_after_saved ? 1 : 0;
		$this->onSave[$i]     = $func_name_on_save;
		$this->onSaveArgs[$i] = $arr_on_save_args;
	}
	function onEachSave ($func_name_on_each_save = '')
	{
		$this->onEachSave 	= $func_name_on_each_save;
	}

	function getDeletedId()
	{
		if (!empty($this->arrDeletedId))
		{
			return $this->arrDeletedId;
		}
		$arrDeletedId = array();
		$checkName    = $this->formName . '_delete';
		$idName       = $this->formName . '_'. $this->tableId;
		if (isset($_POST[$checkName]))
		{
			foreach($_POST[$checkName] as $id=>$true)
			{
				$is_disable = false;
				if (!empty($this->disableInput['system_delete_tool']))
				{
					foreach ((array)$this->disableInput['system_delete_tool'] as $exec)
					{
						eval('if('.$_POST[$idName][$id].' '.$exec[0].' '.$exec[1].'){$is_disable=true;}');
						if ($is_disable)
						{
							break;
						}
					}
				}
				if (!$is_disable)
				{
					array_push($arrDeletedId, $_POST[$idName][$id]);
				}
			}
		}
		$this->arrDeletedId = $arrDeletedId;
		return $arrDeletedId;
	}

	function setIsChangeBc($bool_change_bc = false) {
		$this->isChangeBc = $bool_change_bc;
	}

	function setResetTool($bool_reset_tool = false)
	{
		if ($bool_reset_tool == 'on') $bool_reset_tool = true;
		elseif ($bool_reset_tool == 'off') $bool_reset_tool = false;
		$this->resetTool	= $bool_reset_tool;
	}

	// untuk membuat checkbox untuk checkAll
	// khusus untuk input bertipe checkAll dan checkboxdelete
	function getCheckAll($input)
	{
		$out = '';
		if (@$input->isCheckAll)
		{
			if ($input->type == 'checkbox' || $input->type == 'checkboxdelete')
			{
				link_js(_PEA_ROOT . 'includes/checkAll.js', false);
				$out .= '<input class="'. $input->name .'" type="checkbox" onClick="checkAll(this,\''. $this->formName .'\');"> ';
			}
		} // eof if ($input->isCheckAll)
		return $out;
	}

	function getOrderUrl($input, $title)
	{
		// menentukan field yang di jadikan variable _GET untuk orderby
		$objectName = '';
		switch ($input->type)
		{
			case 'multiinput':
			case 'dependentdropdown':
				if (!empty($input->elements))
				{
					foreach ($input->elements as $i => $element)
					{
						if ($element->isIncludedInSelectQuery)
						{
							$objectName = $element->objectName;
							break;
						}
					}
				}
				break;
			default:
				if ($input->isIncludedInSelectQuery)
				{
					$objectName = $input->objectName;
				}
				break;
		}
		if (empty($objectName))
		{
			return array('start' => '', 'end' => '');
		}
		// mencari basic Url nya
		if ($this->orderUrl	== '')
		{
			$this->orderUrl = preg_replace('#\?.*#', '', $_SERVER['REQUEST_URI']).'?';
			if (isset($_GET))
			{
				foreach($_GET as $name => $val)
				{
					if ($name != $this->formName.'_asc' && $name != $this->formName.'_order' && !empty($val))
					{
						$this->orderUrl	.= $name.'='.$val .'&';
					}
				}
			}
		}
		if (@$_GET[$this->formName.'_order'] == $objectName && @$_GET[$this->formName.'_asc'] == '0')
		{
			$img = 'desc';
			$href['start'] = '<a href="'.substr($this->orderUrl,0,-1).'" title="Reset order">';
		}else{
			$asc   = $this->formName.'_asc=1';
			$order = $this->formName.'_order='.$objectName;
			$img   = '';
			// mencari field mana yang mau di order by
			if (isset($_GET[$this->formName.'_order']))
			{
				if ($_GET[$this->formName.'_order'] == $objectName)
				{
					if (isset($_GET[$this->formName.'_asc']))
					{
						$asc = ($_GET[$this->formName.'_asc'] == '1') ? $this->formName.'_asc=0' : $this->formName.'_asc=1';
						$img = ($_GET[$this->formName.'_asc'] == '1') ? 'asc' : 'desc';
					}
				}
			}
			$href['start'] = '<a href="'.$this->orderUrl.$order.'&'.$asc.'" title="Sort by column '.$title.'" >';
		}
		$href['start'] .= !empty($img) ? icon('fa-sort-alpha-'.$img, $img).' ' : '';
		$href['end']    = '</a>';
		return $href;
	}

	function getOrderQuery($query)
	{
		if (isset($_GET[$this->formName . '_order']))
		{
			// hanya untuk validasi aja
			$_GET[$this->formName . '_asc']	= (isset($_GET[$this->formName . '_asc'])) ? $_GET[$this->formName . '_asc'] : '1';
			$_GET[$this->formName . '_asc']	= ($_GET[$this->formName . '_asc'] == '0' || $_GET[$this->formName . '_asc'] == '1') ? $_GET[$this->formName . '_asc'] : '1';

			$asc   = ($_GET[$this->formName . '_asc'] == '0') ? 'DESC' : 'ASC';
			$field = $_GET[$this->formName . '_order'];
			foreach ($this->input as $key => $dt)
			{
				if (@$dt->name == $this->formName.'_'.$field)
				{
					$field = $key;
					break;
				}
			}
			$orderQuery = 'ORDER BY '. $field . ' ' . $asc;

			if (preg_match('~order by ~is', $query))
				$query	= preg_replace("/order by.*?\$/is", $orderQuery, $query);
			else
				$query	.= ' '.$orderQuery;
		}
		return $query;
	}

	function addSystemInput()
	{
		// secara otomatis ditambah hidden input berupa tableID,
		// sebagai primari key dari tiap row dalam form tersebut
		$this->addInput('system_id', 'hidden', $setDefault=0);
		$this->input->system_id->setFormName($this->formName);
		$this->input->system_id->setFieldname($this->tableId);

		// tombol tool untuk delete otomatis
		if ($this->deleteTool)
		{
			$this->addInput('system_delete_tool', 'checkboxdelete');
			$this->input->system_delete_tool->setName('delete');
			$this->input->system_delete_tool->setTitle(' Delete');
		}
		/*	SHOW AND HIDE COLUMN IF OPTIONAL VIEW IS USED  */
		$this->optionalColumn  = array();
		$this->optionalCaption = array();
		// check is optional colom is used
		foreach ($this->input as $i => $input)
		{
			if (is_bool($input->isDisplayColumn))
			{
				$this->isOptionalColumn = true;
				$this->optionalColumn[$i] = $input->isDisplayColumn;
				$this->optionalCaption[$i] = !empty($input->caption) ? $input->caption : $input->title;
				// break;
			}
		}
		// proccess optional colom
		if ($this->isOptionalColumn)
		{
			$sesKey = menu_save(@$_GET['mod'].$this->formName.session_id());
			// save post in session
			if (!empty($_POST[$this->formName.'_ColView']))
			{
				ob_end_clean();
				if ($_POST[$this->formName.'_ColView'] == "EDIT")
				{
					if (!empty($_POST['ColView']))
					{
						$_SESSION['ColView'][$sesKey] = $_POST['ColView'];
					}else{
						$_SESSION['ColView'][$sesKey] = array();
					}
				}else{
					unset($_SESSION['ColView'][$sesKey]);
				}
				die("saved_ColView");
			}
			// replace variable $this->optionalColumn
			if (isset($_SESSION['ColView'][$sesKey]))
			{
				$r = (array)$_SESSION['ColView'][$sesKey];
				foreach ($this->optionalColumn as $key => $val)
				{
					$this->optionalColumn[$key] = in_array($key, $r) ? true : false;
				}
			}
			// show / hide column based on variable $this->optionalColumn
			foreach ($this->input as $i => $input)
			{
				if (is_bool($input->isDisplayColumn))
				{
					if (!$this->optionalColumn[$i])
					{
						if (isset($input->elements))
						{
							foreach ($input->elements as $j => $subInput)
							{
								if (isset($this->input->$j))
								{
									unset($this->input->$j);
								}
							}
						}
						unset($this->input->$i);
					}
				}
			}
		}
	}

	function getSaveSuccessPage()
	{
		if (isset($_POST[$this->saveButton->name]))
			return $this->getSuccessPage($this->setSuccessSaveMessage, $this->setFailSaveMessage);
		else return '';
	}

	function getDeleteSuccessPage()
	{
		if (isset($_POST[$this->deleteButton->name]))
			return $this->getSuccessPage($this->setSuccessDeleteMessage, $this->setFailDeleteMessage);
		else return '';
	}

	function getReport($page=0)
	{
		$out	= '';
		if ($this->isReportOn)
		{
			$export_all = !empty($_GET[$this->formName.'_export_all']);


			$out  .= '<span class="input-group-addon checkbox roll-export">';
			$out  .= 'Export: ';
			$name  = menu_save(@$_GET['mod'].$this->formName.session_id(), false, '_');
			$name .= $export_all ? '' : $page;
			$title = !empty($this->input->header->title) ? strip_tags($this->input->header->title) : 'Report';
			if (!empty($this->table) && preg_match('~([a-z0-9_]+)~is', $this->table, $m) && empty($this->input->header->title))
			{
				$title .= ' '.$m[1];
			}
			// Jika diakses oleh includes/exportAll.js
			if (!empty($_GET[$this->formName.'_export_type']))
			{
				$data = $this->buildReport($page, $export_all, $_GET[$this->formName.'_export_type']);
				$o = ob_get_contents();
				ob_end_clean(); // membersihkan output dari script sebelumnya (jika ada)
				// Jika export semua halaman
				if ($export_all)
				{
					if ($this->nav->int_cur_page > $this->nav->int_tot_page)
					{
						$out  = array(
							'ok'      => 1,
							'data'    => $data,
							'done'    => 100
							);
						output_json($out);
					}else{
						$out  = array(
							'ok'      => 1,
							'data'    => $data,
							'done'    => ($page ? intval($page/$this->nav->int_tot_page*100) : 0)
							);
						output_json($out);
					}
				}else{
					// Jika hanya export halaman itu saja
					$out  = array(
						'ok'      => 1,
						'data'    => $data,
						'done'    => 100
						);
					output_json($out);
				}
			}else{
				if ($this->nav->int_tot_page > 1 && $page > 0)
				{
					$title .= ' - Page '.money($page);
				}
				foreach($this->report as $type => $val)
				{
					$icon = $type == 'html' ? 'text' : $type;
					$out .= ' <a class="fa fa-file-'.$icon.'-o fa-lg" rel="'.$name.'='.urlencode($title).'" data-type="'.$type.'" style="cursor: pointer" title="Export to '.ucfirst($type).'"></a>';
				}
			}
			link_js(_PEA_ROOT . 'includes/exportAll.js', false);
			if ($this->nav->int_tot_page > 1)
			{
				$out .= '<label style="min-height: 0;padding-left: 25px;"><input type="checkbox" class="export_all" data-form="'.$this->formName.'" data-page="'.$this->nav->string_name.'" title="'.lang('Export All Data').'" />'.lang('All Pages').'</label>';
			}else{
				$out .= '<label style="display: none;"><input type="checkbox" class="export_all" data-form="'.$this->formName.'" data-page="'.$this->nav->string_name.'" title="'.lang('Export All Data').'" /></label>';
			}
			$out .= '</span>';
		}
		if ($this->isOptionalColumn && empty($_GET['is_ajax']))
		{
			link_js(_PEA_ROOT . 'includes/optionalColumn.js', false);
			$out .= sprintf('<div class="btn-group input-group-addon show_hide_column%s">', (($this->nav->int_num_rows_this_page >= 8) ? ' dropup' : ''));
			$out .= sprintf('<button type="button" class="btn btn-default btn-secondary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> %s <span class="caret"></span> </button> <ul class="dropdown-menu">', lang('Show/Hide Columns'));
			foreach ($this->optionalColumn as $key => $toogle)
			{
				$checked = $toogle ? ' checked' : '';
				$out .= '<li> <a href="#"> <label><input type="checkbox" value="'.$key.'"'.$checked.' /> '.$this->optionalCaption[$key].'</label> </a> </li>';
			}
			$out .= '<li role="separator" class="divider dropdown-divider"></li>
								<li>
										<div class="btn-group btn-group-justified" role="group" aria-label="...">
											<div class="btn-group" role="group">
												<button type="button" rel="btn_ColView" data-name="'.$this->formName.'_ColView" value="EDIT" class="btn btn-default btn-secondary">
													Submit
												</button>
											</div>
											<div class="btn-group" role="group">
												<button type="button" rel="btn_ColView" data-name="'.$this->formName.'_ColView" value="RESET" class="btn btn-default btn-secondary">
													Reset
												</button>
											</div>
										</div>
								</li>
							</ul>
					</div>';
		}
		return $out;
	}
	function buildReport($page, $is_export_all, $type)
	{
		switch ($type)
		{
			case 'excel':
				$out = [];
				if (($page==1 && $is_export_all) || !$is_export_all)
				{
					$row = [];
					foreach ($this->reportData['header'] as $dt)
					{
						$row[] = '"'.str_replace('"', '""', $dt).'"';
					}
					$out[] = implode(',', $row);
				}
				foreach ($this->reportData['data'] as $rows)
				{
					$row = [];
					foreach ($rows as $dt)
					{
						$row[] = '"'.str_replace('"', '""', $dt).'"';
					}
					$out[] = implode(',', $row);
				}
				$output = implode("\n", $out)."\n";
				break;

			default: // html
				$output = '';
				if (($page==1 && $is_export_all) || !$is_export_all)
				{
					$output .= '<thead><tr>';
					foreach ($this->reportData['header'] as $row)
					{
						$output .= '<th>'.$row.'</th>';
					}
					$output .= '</tr></thead>';
					$output .= '<tbody>';
				}
				foreach ($this->reportData['data'] as $rows)
				{
					$output .= '<tr>';
					foreach ($rows as $dt)
					{
						$output .= '<td>'.$dt.'</td>';
					}
					$output .= '</tr>';
				}
				break;
		}
		return $output;
	}

	function getMainQuery()
	{
		$this->arrInput  = get_object_vars($this->input);
		$strField2Select = array($this->tableId);

		//Buat query untuk select, buat ngambil data yang mau ditampilkan di input
		$this->strField2Lang = array();

		foreach($this->arrInput as $input)
		{
			if ($input->isMultiLanguage)
			{
				$this->strField2Lang[] = $input->fieldName; // gak perlu di cleanSQL krn untuk nama key array dan sudah di clean pas diquery
			}else
			if ($input->isIncludedInSelectQuery)
			{
				$strField2Select[] = $this->setQuoteSQL($input->fieldName);
			}
		}
		$strField2Select 	= implode(', ', $strField2Select);

		$query	= '';
		// query dimasukkan object oNav, biar sekalaian untuk navigasinya
		$this->nav	= new oNav($query, $this->tableId , $this->intNumRows, 10, 'page', $this->db);

		// ini untuk mendapatkan suatu query, berdasarkan order link
		$this->nav->sqlCondition	= $this->getOrderQuery($this->sqlCondition);
		// ini untuk jaga2 jika ada view_all yg di panggil di $this->nav->getData()
		if (isset($_GET[$this->nav->string_name . '_viewAll']))
		{
			if ($_GET[$this->nav->string_name . '_viewAll'] == '1')
			{
				$this->nav->setNumMaxRows('500');
			}
		}
		$page  = !empty($_GET[$this->nav->string_name]) ? intval($_GET[$this->nav->string_name])-1 : 0;
		$start = $page * intval($this->nav->int_max_rows);
		$table = $this->setQuoteSQL($this->table) .' '. $this->sqlCondition;
		$query = "SELECT $strField2Select FROM $table";
		$query = $this->getOrderQuery($query);
		$query .= " LIMIT ". $start .", ". intval($this->nav->int_max_rows);

		$this->nav->completeQuery	= $query;
	}

	// getMainForm() mengembalikan form complete, tapi tanpa submit button, tanpa navigasi, tanpa header title
	function getMainForm()
	{
		// mendapatkan form-form nya row per row.
		// dan kemudian memasukkan value dari query database kedalam input form masing2 yang sesuai
		$i                = 0;
		$arrData          = array();
		$out              = '<tbody>';
		$this->arrInput   = get_object_vars($this->input);
		while ($arrResult = $this->nav->fetch())
		{
			$this->arrResult = $arrResult;
			$tableId         = $this->arrResult[$this->tableId];
			$out            .= '<tr data-id="'.$tableId.'">';
			foreach($this->arrInput AS $input)
			{
				if (!$input->isInsideMultiInput && !$input->isHeader)
				{
					// digunakan pada sqllinks
					if (preg_match ('~ as ~is',$this->tableId))
					{
						if (preg_match('~(.*) (as) (.*)~is', $this->tableId, $match))
						{
							$this->tableId=$match[3];
						}
					}
					if ($this->isMultiLanguage && !isset($this->load_lang[$i]) && !empty($this->strField2Lang))
					{
						$q = "SELECT `lang_id`, `".implode('`, `', $this->strField2Lang)."` FROM `$this->LanguageTable` WHERE `$this->LanguageTableId`={$tableId}".$this->LanguageTableWhere;
						$this->load_lang[$i] = 1;
						$r = $this->db->getAll($q);
						foreach($r AS $d)
						{
							foreach($this->strField2Lang AS $f)
							{
								$arrResult[$f][$d['lang_id']] = $d[$f];
							}
						}
					}
					$arrResult[$input->objectName] = $this->getDefaultValue($input, $arrResult, $i);
					// dapatkan array data report
					if ($this->isReportOn && $input->isIncludedInReport && !empty($_GET[$this->formName.'_export_type']))
					{
						$irow = $input->getReportOutput($arrResult[$input->objectName]);
						if ($input->reportFunction && is_callable($input->displayFunction))
						{
							$irow = call_user_func_array($input->displayFunction, array($irow));
						}
						if (is_callable($input->exportFunction))
						{
							$irow = call_user_func_array($input->exportFunction, array($irow));
						}
						$arrData[$i][]	= $irow;
					}

					if ($input->isInsideRow)
					{
						$out	.= '<td>';
					}
					$str_value = in_array($input->objectName, ['system_delete_tool']) ? $arrResult[$this->tableId] : $arrResult[$input->objectName];
					$tmp       = $input->getOutput($str_value, $input->name.'['.$i.']', $this->setDefaultExtra($input));
					if (!empty($this->disableInput[$input->objectName]))
					{
						$is_disable = false;
						foreach ((array)$this->disableInput[$input->objectName] as $exec)
						{
							$comparator = is_array($arrResult[$exec[2]]) ? current($arrResult[$exec[2]]) : $arrResult[$exec[2]];
							eval('if($exec[1] '.$exec[0].' $comparator){$is_disable=true;}');
							if ($is_disable)
							{
								break;
							}
						}
						if ($is_disable)
						{
							$tmp = preg_replace(array('~(<input\s?)~is', '~(<select\s?)~is', '~(<textarea\s?)~is'), '$1 disabled ', $tmp);
							if ($input->objectName != 'system_delete_tool')
							{
								$tmp.= $this->setDisableInputRecovery($arrResult[$input->objectName], $input->name.'['.$i.']', $tmp);
							}
						}
					}
					if ($input->isInsideRow)
					{
						$out	.= $tmp.'</td>';
					}else{
						if (!empty($tmp) && preg_match('~hidden~is', $tmp))
						{
							$out .= $tmp;
						}else{
							$out .= '<div class="hidden">'.$tmp.'</div>';
						}
					}
				}
			} // end foreach
			$out .= '</tr>';
			$i++;
		}
		$out .= '</tbody>';
		if ($this->isReportOn)
		{
			$this->reportData['data'] = $arrData;
		}
		return $out;
	} // eof function getMainForm()

	// getForm() adalah method utama
	// disini manggil action() dan getMainForm()
	// ini untuk ngambil form ROll Secara complete, beserta action2nya
	function getForm()
	{
		$this->action();
		$mainForm	= $this->getMainForm();
		if($this->isFormRequire)
		{
			$cls = ' class="formIsRequire"';
			link_js(_PEA_URL.'includes/formIsRequire.js', false);
		}else{
			$cls = '';
		}

		$i = 0;
		$out = '';

		$out .= '<form method="'.$this->methodForm.'" action="'.$this->actionUrl.'" name="'. $this->formName .'"'.$cls.' enctype="multipart/form-data" role="form">';
		$out .= $this->getSaveSuccessPage();
		$out .= $this->getDeleteSuccessPage();

		$hover= $this->isChangeBc ? ' table-hover' : '';
		$out .= '<table class="table table-striped table-bordered'.$hover.'">';
		$out .= '<thead><tr>';

		// ngambil tr title
		$numColumns = 0;

		foreach($this->arrInput as $input)
		{
			if ($input->isInsideRow && !$input->isInsideMultiInput && !$input->isHeader)
			{
				// buat array data untuk report
				if ($this->isReportOn && $input->isIncludedInReport)
				{
					$arrHeader[]	= $input->title;
				}
				// dapatkan text bantuan
				if (!empty($input->textHelp))
				{
					$this->help->value[$input->name] = $input->textHelp;
				}
				if (!empty($input->textTip))
				{
					$this->tip->value[$input->name] = $input->textTip;
				}
				$label = '';
				if (@$input->isCheckAll)
				{
					$label = $this->getCheckAll($input);
				}
				$href   = $this->getOrderUrl($input, $input->title);
				if (!empty($this->tip->value[$input->name]))
				{
					$input->title = tip($input->title, $this->tip->value[$input->name]);
				}
				$label .= $href['start'].$input->title.$href['end'];
				$out .= '  <th>'.$label;
				if (!empty($this->help->value[$input->name]))
				{
					$out .= ' <span style="font-weight: normal;">'.help($this->help->value[$input->name],'bottom').'</span>';
				}
				$out		.= "</th>\n";
				$numColumns++;
			}
		}
		$out .= '</tr></thead>';
		$this->reportData['header']	= isset($arrHeader) ? $arrHeader : array();
		// ambil mainFormnya
		$out .= $mainForm;

		/* Return, Save, Reset, Navigation, Delete */
		$button = '';
		if (!empty($_GET['return']) && empty($_GET['is_ajax']))
		{
			$returl = preg_replace('~&is_ajax=1~s', '', $_GET['return']);
			$button.= $GLOBALS['sys']->button($returl);
		}
		if ($this->saveTool)
		{
			$button .= '<button type="submit" name="'. $this->saveButton->name .'" value="'. $this->saveButton->value
						.	'" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-'.$this->saveButton->icon.'"></span>'
						. $this->saveButton->label .'</button>';
		}
		if ($this->resetTool)
		{
			$button .= '<button type="reset" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-'.$this->resetButton->icon.'"></span>'.$this->resetButton->label.'</button> ';
		}
		$nav = $this->nav->getNav();
		if (!empty($nav))
		{
			if (!empty($button))
			{
				$button = '<table style="width: 100%;"><tr><td style="width: 10px;white-space: nowrap;">'.$button.'</td><td style="text-align: center;">'.$nav.'</td></tr></table>';
			}else{
				$button .= $nav;
			}
		}
		$footerTD = array();
		$colspan  = $numColumns;
		if ($this->deleteTool)
		{
			$colspan -= 1;
		}
		$attr = $colspan > 1 ? ' colspan="'.$colspan.'"' : '';
		$footerTD[] = '<td'.$attr.'>'.$button.'</td>';
		if ($this->deleteTool)
		{
			$footerTD[] = '<td>'
				. '<button type="submit" name="'.$this->deleteButton->name.'" value="'. $this->deleteButton->value.'" class="btn btn-danger btn-sm" '
				. 'onclick="if (confirm(\'Are you sure want to delete selected row(s) ?\')) { return true; }else{ return false; }">'
				. '<span class="glyphicon glyphicon-'.$this->deleteButton->icon.'"></span>'.$this->deleteButton->label .'</button>'
				. '</td>';
		}
		if (!empty($footerTD))
		{
			$out .= '<tfoot><tr>'.implode('', $footerTD).'</tr></tfoot>';
		}
		$out .= '</table>';
		$out .= '</form>';

		/* Export Tool, Page Status, Form Navigate */
		$nav = $this->nav->getViewAllLink();
		if (!empty($nav))
		{
			$nav = '<span class="input-group-addon">'.$nav.'</span>';
		}
		$nav .= $this->nav->getGoToForm(false);
		$out .= '<form method="get" action="" role="form" style="margin-top:-20px;margin-bottom: 20px;">'
				.	'<div class="input-group">'
				. $this->getReport($this->nav->int_cur_page)
				. '<span class="input-group-addon">'
				. $this->nav->getStatus().'</span>'.$nav.'</div></form>';

		/* Form Panel */
		$formHeader = $this->getHeaderType();
		if (!empty($formHeader))
		{
			$out = '
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">'.$formHeader.'</h3>
					</div>
					<div class="panel-body">
						'.$out.'
					</div>
				</div>';
		}
		$out = $this->getHideFormToolStart().$out.$this->getHideFormToolEnd();
		return $out;
	}

	function actionOnDelete($call_after_saved)
	{
		$out = true;
		$i   = $call_after_saved ? 1 : 0;
		if (!empty($this->onDelete[$i]))
		{
			$this->onDeleteArgs[$i] = !empty($this->onDeleteArgs[$i]) ? $this->onDeleteArgs[$i] : $this->getDeletedId();
			$tmp = call_user_func($this->onDelete[$i], $this->onDeleteArgs[$i]);
			if (is_bool($tmp) || $tmp=='1' || $tmp=='0')
			{
				$out = $tmp ? true : false;
			}
		}
		$this->isActionExecute = $out;
		return $out;
	}
	function actionOnEachDelete($id)
	{
		$out = true;
		if (!empty($this->onEachDelete))
		{
			$tmp = call_user_func($this->onEachDelete, $id);
			if (is_bool($tmp) || $tmp=='1' || $tmp=='0')
			{
				$out = $tmp ? true : false;
			}
		}
		return $out;
	}
	function actionOnSave($call_after_saved)
	{
		$out = true;
		$i = $call_after_saved ? 1 : 0;
		if (!empty($this->onSave[$i]))
		{
			$tmp = call_user_func($this->onSave[$i], $this->onSaveArgs[$i]);
			if (is_bool($tmp) || $tmp=='1' || $tmp=='0')
			{
				$out = $tmp ? true : false;
			}
		}
		$this->isActionExecute = $out;
		return $out;
	}
	function actionOnEachSave($id)
	{
		$out = true;
		if (!empty($this->onEachSave))
		{
			$tmp = call_user_func($this->onEachSave,$id);
			if (is_bool($tmp) || $tmp=='1' || $tmp=='0')
			{
				$out = $tmp ? true : false;
			}
		}
		return $out;
	}
	// INI UNTUK MENGAMANKAN INPUT UNTUK JAGA2 JIKA DIUBAH OLEH USER
	function actionSecurity()
	{
		if (!empty($_POST[$this->input->system_id->name]))
		{
			$ids = array();
			$ids = $this->db->getCol($this->nav->completeQuery);
			if ($_POST[$this->input->system_id->name] != $ids)
			{
				$this->setActionExecute(false, 'please try again, it looks like another user has made changes to the data');
				$this->error = true;
			}
		}
	}
	function action()
	{
		if ($this->isLoaded->action) return false;
		else $this->isLoaded->action = true;

		//menambah input hidden id dan delete tool
		$this->addSystemInput();
		$this->getMainQuery();

		if (empty($this->arrInput)) $this->arrInput	= get_object_vars($this->input);
		// untuk menandai apakah form perlu validasi
		$this->setIsFormRequire();
		if ($this->isActionExecute)
		{
			$this->actionSecurity();
		}

		if ($this->isActionExecute)
		{
			if (!isset($_POST[$this->tableId])) $_POST[$this->tableId] = array();

			// aksi yang dilakukan saat delete button di klik
			if (isset($_POST[$this->deleteButton->name]))
			{
				$this->error = !$this->actionOnDelete(false);
				if ($this->isActionExecute)
				{
					$ok = false;
					if (!empty($_POST[$this->formName.'_delete']))
					{
						$del_ids = $this->getDeletedId();
						ids($del_ids);
						if (!empty($del_ids))
						{
							$orderby  = '';
							/* CARI APAKAH ADA FIELD YANG PERLU DITANGANI SEBELUM DIHAPUS */
							foreach ($this->arrInput as $key => $input)
							{
								if ($input->isIncludedInDeleteQuery)
								{
									$q = $input->getDeleteSQL($del_ids); // untuk multifile, file, tags akan dihapus serta
									if (!empty($q))
									{
										$this->db->Execute($q);
									}
								}
								if ($input->type=='orderby')
								{
									$orderby = $input->objectName;
								}
							}
							$table = preg_replace('~((?:\s+as\s+.*?)?\s+left\s+join\s+.*?)$~is','',$this->table);
							$q = "DELETE FROM `{$table}` WHERE {$this->tableId} IN ({$del_ids})";
							$ok= $this->db->Execute($q);
							if ($ok)
							{
								if ($this->isMultiLanguage)
								{
									$q = "DELETE FROM ".$this->LanguageTable." WHERE `".$this->LanguageTableId."` IN (". $del_ids .")".$this->LanguageTableWhere;
									$this->db->Execute($q);
								}
								/* HAPUS SEMUA TABLE RELASI JIKA ADA YANG TER RELASI */
								foreach ($this->arrInput as $input)
								{
									if (!$input->isIncludedInDeleteQuery)
									{
										$q = $input->getDeleteQuery($del_ids);
										if (!empty($q))
										{
											$this->db->Execute($q);
										}
									}
								}
								/* URUTKAN KEMBALI PENGURUTAN JIKA DITEMUKAN INPUT ORDERBY */
								if (!empty($orderby))
								{
									$ord = ' ORDER BY '.$orderby.' ASC';
									$sql = $this->sqlCondition;
									if (!preg_match('~ order by ~is', $this->sqlCondition))
									{
										$sql.= $ord;
									}else{
										$sql = preg_replace('~(order by .*?)$~is', $ord, $sql);
									}
									$q = "SELECT {$this->tableId}, {$orderby} FROM ".$this->setQuoteSQL($this->table)." {$sql}";
									$r = $this->db->getAll($q);
									$i = 0;
									foreach ($r as $dt)
									{
										$i++;
										if ($dt[$orderby]!=$i)
										{
											$q = "UPDATE ".$this->setQuoteSQL($this->table)." SET `{$orderby}`=$i WHERE `{$this->tableId}`=".$dt[$this->tableId];
											$this->db->Execute($q);
										}
									}
								}
								$this->error = !$this->actionOnDelete(true);
								if (!$this->error && !empty ($this->onEachDelete))
								{
									$check_name = $this->formName . "_delete";
									foreach($_POST[$check_name] as $i => $id)
									{
										if (!$this->error)
										{
											$record_id = $_POST[$this->input->system_id->name][$i];
											$this->error = !$this->actionOnEachDelete($record_id);
										}
									}
								}
							}
						}
					}
					$this->debug($ok, "", "BBC", "Class phpEasyAdmin query error on rollAction method(DELETE), please check your arguments when initiate phpEasyAdmin Class : ".mysqli_error($this->db->link));
				}
			}else
			if (isset($_POST[$this->saveButton->name]) || isset($_POST[$this->formName.'_orderby']) || isset($_POST[$this->formName.'_file_delete_image']))
			{
				$formExecute = true;
				if ($this->isFormRequire && isset($_POST[$this->saveButton->name]))
				{
					foreach((array)$_POST[$this->input->system_id->name] as $i => $id)
					{
						foreach ($this->arrInput as $input)
						{
							if ($input->isRequire)
							{
								if($input->type=='file')
								{
									$text = @is_uploaded_file($_FILES[$input->name]['tmp_name'][$i]) ? '1' : @$_POST[$input->name][$i];
								}else{
									$text = $input->isMultiLanguage ? @current($_POST[$input->name][$i]) : @$_POST[$input->name][$i];
								}
								$req   = explode(' ', $input->isRequire);
								$i_row = strtoupper($input->title).' in line: '.money($i + 1);

								if (empty($text) && $text != '0')
								{
									if (empty($req[1]) || $req[1]=='true')
									{
										$this->setFailSaveMessage('"'.$i_row.'" must not empty!');
										$formExecute = false;
									}
								}else{
									switch ($req[0]) {
										case 'email':
											if (!is_email($text)) {
												$this->setFailSaveMessage('Please enter a valid email address in "'.$i_row.'"!');
												$formExecute = false;
											}
											break;
										case 'url':
											if (!is_url($text)) {
												$this->setFailSaveMessage('Please enter a valid URL in "'.$i_row.'"!');
												$formExecute = false;
											}
											break;
										case 'phone':
											if (!is_phone($text)) {
												$this->setFailSaveMessage('Please enter a valid phone number in "'.$i_row.'"!');
												$formExecute = false;
											}
											break;
										case 'money':
											if (!empty($text) && !preg_match('~^[0-9]+(?:\.[0-9]+)?$~s', $text)) {
												$this->setFailSaveMessage('Please enter a valid money format in "'.$i_row.'"!');
												$formExecute = false;
											}
											break;
										case 'number':
											if (!empty($text) && !preg_match('~^[0-9]+$~s', $text)) {
												$this->setFailSaveMessage('Please enter a valid number in "'.$i_row.'"!');
												$formExecute = false;
											}
											break;
									}
								}
							}
							if (!$formExecute)
							{
								$this->isActionExecute = $formExecute;
								$this->error           = true;
								break;
							}
						} // eo foreach ($this->arrInput as $input)
						if (!$formExecute)
						{
							break;
						}
					} // eo foreach((array)$_POST[$this->input->system_id->name] as $i => $id)
				} // eo if ($this->isFormRequire)
				if ($this->isActionExecute)
				{
					if (!empty($_POST[$this->input->system_id->name]))
					{
						if (!empty($_POST[$this->formName.'_orderby']))
						{
							$query = '';
							foreach((array)$_POST[$this->input->system_id->name] as $i => $id)
							{
								foreach ($this->arrInput as $input)
								{
									if (!$input->isMultiLanguage)
									{
										$query .= $input->getRollUpdateQuery($i);
									}
								}
							}
						}else{
							$this->error = !$this->actionOnSave(false);
							if ($this->isActionExecute)
							{
								foreach((array)$_POST[$this->input->system_id->name] as $i => $id)
								{
									$lang_text = array();
									$query = "UPDATE ". $this->setQuoteSQL($this->table) ." SET ";
									foreach ($this->arrInput as $input)
									{
										if ($input->isMultiLanguage)
										{
											$last = '';
											if (!empty($_POST[$input->name][$i]))
											{
												foreach((array)$_POST[$input->name][$i] AS $l => $p)
												{
													$t = $p ? $p : $last;
													if (@$input->nl2br) $t = nl2br($t);
													$lang_text[$l][$input->objectName] = $this->cleanSQL($t);;
													$last = $t;
												}
											}
										}else{
											$query .= $input->getRollUpdateQuery($i);
										}
										$this->setSuccessSaveMessage .= $input->status;
									}
									//menambahkan yang additional field dan valuenya
									foreach ($this->extraField->field as $i => $f)
									{
										$query .= '`'.$f .'`=\''.$this->extraField->value[$i].'\', ';
									}
									$query = $this->replaceTrailingComma($query) ." WHERE ". $this->tableId ." = '". $id ."' ";
									$this->error	= !$this->db->Execute($query);
									if (!$this->error && $this->isMultiLanguage && count($lang_text) > 0)
									{
										$q = "SELECT `lang_id` FROM `{$this->LanguageTable}` WHERE `{$this->LanguageTableId}`={$id}{$this->LanguageTableWhere}";
										$r_lang_id = $this->db->getCol($q);
										foreach($lang_text AS $lang_id => $value)
										{
											$field = array();
											foreach($value AS $f => $v)
											{
												$field[] = "`{$f}`='{$v}'";
											}
											if (!empty($field))
											{
												$fields = implode(', ', $field);
												if (in_array($lang_id, $r_lang_id))
												{
													$q = "UPDATE `{$this->LanguageTable}` SET {$fields} WHERE `lang_id`={$lang_id} AND `{$this->LanguageTableId}`={$id}{$this->LanguageTableWhere}";
												}else{
													foreach ($this->LanguageTableUpdate as $var => $val)
													{
														$field[] = "`{$var}`='{$val}'";
													}
													$fields = implode(', ', $field);
													$q = "INSERT INTO `{$this->LanguageTable}` SET `lang_id`={$lang_id}, `{$this->LanguageTableId}`={$id}, {$fields}";
												}
											}else{
												$q = '';
											}
											$this->db->Execute($q);
										}
									}
									if ($this->error)
									{
										$this->errorMsg	= $this->db->ErrorMsg();
									}else{
										$this->actionOnEachSave($id);
									}
								} // eo foreach((array)$_POST[$this->input->system_id->name] as $i => $id)
								// Jika onSave di eksekusi SETELAH form action di proses
								if (!$this->error)
								{
									$this->error = !$this->actionOnSave(true);
								}
							} // eo if ($this->isActionExecute)
						} // else if (!empty($_POST[$this->formName.'_orderby']))
					} // eo if (!empty($_POST[$this->input->system_id->name]))
				} // eo if ($this->isActionExecute)
			} // eo if (isset($_POST[$this->saveButton->name]) || isset($_POST[$this->formName.'_orderby']) || isset($_POST[$this->formName.'_file_delete_image']))
		} // eo if ($this->isActionExecute)
	} // eo action() method
} // eo class