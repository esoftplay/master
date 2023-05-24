<?php  if(!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
EXAMPLE: membuat form Add/Edit
$form = _lib('pea',  'table_name');
$form->initEdit(!empty($_GET['id']) ? 'WHERE id='.$_GET['id'] : '');
#$form->edit->setLanguage();
#$form->edit->setColumn(2);

$form->edit->addInput('header','header');
$form->edit->input->header->setTitle(!empty($_GET['id']) ? 'Edit Data' : 'Add Data');

$form->edit->addInput('name', 'text', $noColumn=1);
$form->edit->input->name->setTitle('Nama');
#$form->edit->input->name->setRequire($require='any', $is_mandatory=1);
#$form->edit->input->name->setLanguage();

$form->edit->addInput('email', 'text', $noColumn=2);
$form->edit->input->email->setTitle('Email Address');
#$form->edit->input->email->setRequire($require='email', $is_mandatory=1);
#$form->edit->input->email->setLanguage();

$form->edit->action();
echo $form->edit->getForm();
*/
include_once( _PEA_ROOT.'phpAddAdmin.php' );
class phpEditAdmin extends phpAddAdmin
{
	//untuk menset message berhasil yang akan keluar (diperlukan karena phpAddAdmin menentukan beda message)
	var $setSuccessSaveMessage = 'Success update data.';
	var $setFailSaveMessage    = 'Failed update data.';
	var $onSave                = array('', '');
	var $onSaveArgs            = array('', '');

	function __construct( $str_table, $str_table_id='id')
	{
		$this->initialize('edit', $str_table, $str_table_id);

		$this->setSaveTool(true);
		$this->setResetTool(true);
		$this->setDeleteTool(false);
	}

	function onSave ( $func_name_on_save = '', $var_name_on_save = '', $call_after_saved = true)
	{
		$i                     = $call_after_saved ? 1 : 0;
		$this->onSave[$i]      = $func_name_on_save;
		$this->onSaveArgs[$i]  = $var_name_on_save;
	}

	function getReport()
	{
		$out	= '';
		if ( $this->isReportOn )
		{
			// Jika diakses oleh includes/exportAll.js
			if (!empty($_GET[$this->formName.'_export_type']))
			{
				$data = $this->buildReport($_GET[$this->formName.'_export_type']);
				$o = ob_get_contents();
				ob_end_clean(); // membersihkan output dari script sebelumnya (jika ada)
				$out  = array(
					'ok'      => 1,
					'data'    => $data,
					);
				output_json($out);

			}else{
				$out   = '<div class="input-group edit-export"><span class="input-group-addon">Export:';
				$title = !empty($this->input->header->title) ? strip_tags($this->input->header->title) : 'Report';
				link_js(_PEA_ROOT . 'includes/exportAll.js', false);
				foreach( $this->report as $type => $val)
				{
					$icon = $type == 'html' ? 'text' : $type;
					$out .= ' <a class="fa fa-file-'.$icon.'-o fa-lg" data-title="'.urlencode($title).'" data-type="'.$type.'" data-form="'.$this->formName.'" style="cursor: pointer" title="Export to '.ucfirst($type).'"></a>';
				}
				$out .= '</span></div>';
			}
		}
		return $out;
	}

	function buildReport($type)
	{
		if (!empty($this->reportData['data'][0]))
		{
			$data = array_chunk($this->reportData['data'][0], 2);
		}else{
			return;
		}
		$output = '';
		switch ($type)
		{
			case 'excel':
				$out = ['"Column","Content"'];
				foreach ($data as $rows)
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
				$output .= '<thead><tr>';
				$output .= '<th>Column</th>';
				$output .= '<th>Content</th>';
				$output .= '</tr></thead>';
				$output .= '<tbody>';
				foreach ($data as $rows)
				{
					$output  .= '<tr>';
					foreach ($rows as $dt)
					{
						$output  .= '<td>'.$dt.'</td>';
					}
					$output  .= '</tr>';
				}
				break;
		}
		return $output;
	}

	// getMainForm() mengembalikan form complete, tapi tanpa submit button, tanpa header title
	function getMainForm()
	{
		if ( empty($this->sqlCondition))
			die( "phpEditAdmin : sqlCondition harus diset untuk menandai field mana yang mau dipilih untuk diedit. Contoh: \$obj->setSqlCondition('WHERE id=2')" );

		$this->arrInput	= get_object_vars( $this->input );

		$output  = '';
		$column  = array();
		$tableId = '';
		$fields  = $this->setQuoteSQL($this->tableId).', ';
		$texts   = array();
		//Buat query untuk select, buat ngambil data yang mau ditampilkan di input
		foreach( $this->arrInput as $input )
		{
			if($input->isMultiLanguage)
			{
				$texts[] = $input->fieldName;
			}else
			if ( $input->isIncludedInSelectQuery)
			{
				$fields .= $this->setQuoteSQL($input->fieldName).', ';
			}
		}
		$fields = substr( $fields, 0, -2 );
		$table  = $this->setQuoteSQL($this->table) ." ". $this->sqlCondition;
		$query  = "SELECT $fields FROM $table";

		$this->arrResult = $this->db->GetRow( $query );
		if(count($texts) > 0)
		{
			$q = "SELECT `lang_id`, `".implode('`, `', $texts)."` FROM `$this->LanguageTable`
			WHERE `$this->LanguageTableId`=".@intval($this->arrResult[$this->tableId]).$this->LanguageTableWhere;
			$r = $this->db->getAll($q);
			foreach((array)$r AS $d)
			{
				foreach((array)$d AS $f => $v)
				{
					if($f != 'lang_id') $this->arrResult[$f][$d['lang_id']] = $v;
				}
			}
		}
		// mendapatkan form-form nya field per field.
		$reportRow = 0;
		foreach( $this->arrInput as $input )
		{
			if ( !$input->isInsideMultiInput && !$input->isHeader)
			{
				$out = '';
				if ( $this->isReportOn && $input->isIncludedInReport )
				{
					$arrData[$reportRow][]	= $input->title;
				}
				$defaultValue = $this->getDefaultValue($input, $this->arrResult);
				$inputField = $input->getOutput( $defaultValue, $input->name, $this->setDefaultExtra($input));
				if (!empty($this->disableInput[$input->objectName]))
				{
					$is_disable = false;
					foreach ((array)$this->disableInput[$input->objectName] as $exec)
					{
						eval('if($exec[1] '.$exec[0].' @$this->arrResult[\''.$exec[2].'\']){$is_disable=true;}');
						if ($is_disable)
						{
							break;
						}
					}
					if ($is_disable)
					{
						$inputField = preg_replace(array('~(<input\s?)~is', '~(<select\s?)~is', '~(<textarea\s?)~is'), '$1 disabled ', $inputField);
						$inputField.= $this->setDisableInputRecovery($defaultValue, $input->name, $inputField);
					}
				}
				if ( $input->isInsideRow &&  $input->isInsideCell )
				{
					// dapatkan array data report
					if ($this->isReportOn && $input->isIncludedInReport)
					{
						$arrData[$reportRow][]	= $input->getReportOutput($defaultValue);
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
					$out .= '<div class="form-group">';
					if (!isset($input->isToogle))
					{
						$title = ucwords($input->title);
						if(!empty ( $this->help->value[$input->name] ))
						{
							$title .= ' '.help('<span style="font-weight: normal;">'.$this->help->value[$input->name].'</span>');
						}
						$out .= '<label>'.$title.'</label>';
					}
					$out .= $inputField;
					if(!empty($this->tip->value[$input->name]))
					{
						$out .= '<div class="help-block">'.$this->tip->value[$input->name].'</div>';
					}
					$out	.= '</div>';
				} // eof if ( $input->isInsideRow &&  $input->isInsideCell )
				else $out .= $inputField;
				if ($this->columnNumber > 1)
				{
					if (!empty($out))
					{
						if (empty($column[$input->noColumn]))
						{
							$column[$input->noColumn] = array();
						}
						$column[$input->noColumn][] = $out;
					}
				}else{
					$output .= $out;
				}
			} // eof if ( !$input->isMulti )
		} // eof foreach( $this->arrInput as $input )

		if ( $this->isReportOn )
		{
			$this->reportData['data']	= $arrData;
		}
		if ($this->columnNumber > 1)
		{
			$output = '<div class="clearfix"></div>';
			$j = 12 / $this->columnNumber;
			for ($i=1; $i <= $this->columnNumber; $i++)
			{
				if (empty($column[$i]))
				{
					$column[$i] = array();
				}
				$output .= '<div class="col-md-'.$j.' col-sm-'.$j.'">'.implode('', $column[$i]).'</div>';
			}
			$output .= '<div class="clearfix"></div>';
		}
		return $output;
	} // end getMainForm()

	function getDeleteSuccessPage()
	{
		if (isset($_POST[$this->deleteButton->name]) )
			return $this->getSuccessPage($this->setSuccessDeleteMessage, $this->setFailDeleteMessage);
		else return '';
	}

	function getSaveSuccessPage()
	{
		if (isset($_POST[$this->saveButton->name]) )
			return $this->getSuccessPage( $this->setSuccessSaveMessage, $this->setFailSaveMessage);
		else return '';
	}

	// getForm() adalah method utama
	// disini manggil action() dan getMainForm()
	// ini untuk ngambil form Form Secara complete, beserta action2nya
	function getForm()
	{
		$this->action();
		$out	= $this->getSaveSuccessPage();

		if (isset($_POST[$this->deleteButton->name]))
		{
			$out .= $this->getDeleteSuccessPage();
		}else{
			$out .= $this->getMainForm();
		}
		$formHeader = $this->getHeaderType();
		//buat row paling bawah(untuk submit button)
		$footer = '';
		if (!empty($_GET['return']) && empty($_GET['is_ajax']))
		{
			if (!empty($this->hideToolTitle))
			{
				$GLOBALS['sys']->nav_add($this->hideToolTitle);
			}
			$footer .=	$GLOBALS['sys']->button($_GET['return']);
		}
		if (!isset($_POST[$this->deleteButton->name]))
		{
			if	($this->saveTool)
			{
				$footer .= '<button type="submit" name="'.$this->saveButton->name.'" value="'.$this->saveButton->value;
				$footer .= '" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-'.$this->saveButton->icon.'"></span>';
				$footer .= $this->saveButton->label .'</button> ';
			}
			if ($this->resetTool)
			{
				$footer .= '<button type="reset" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-'.$this->resetButton->icon.'"></span>'.$this->resetButton->label.'</button> ';
			}
			if ($this->deleteTool)
			{
				$footer .= '<button type="submit" name="'.$this->deleteButton->name.'" value="'. $this->deleteButton->value.'" class="btn btn-danger btn-sm"';
				$footer .= ' onclick="if (confirm(\'Are you sure want to delete this data ? ?\')) { return true; } else { return false; }">';
				$footer .= '<span class="glyphicon glyphicon-'.$this->deleteButton->icon.'"></span>'.$this->deleteButton->label.'</button> ';
			}
			$footer .= $this->getReport();
		}
		if (!empty($formHeader))
		{
			$formHeader = '<div class="panel-heading"><h3 class="panel-title">'.$formHeader.'</h3></div>';
		}
		if($this->isFormRequire)
		{
			$cls = ' class="formIsRequire"';
			link_js(_PEA_URL.'includes/formIsRequire.js', false);
		}else{
			$cls = '';
		}
		$out = '
			<form method="'.$this->methodForm.'" action="'.$this->actionUrl.'" name="'.$this->formName.'"'.$cls.' enctype="multipart/form-data" role="form">
				<div class="panel panel-default">
					'.$formHeader.'
					<div class="panel-body">'.$out.'</div>
					<div class="panel-footer">
						'.$footer.'
					</div>
				</div>
			</form>';
		return $this->getHideFormToolStart().$out.$this->getHideFormToolEnd();
	}

	function actionOnSave($call_after_saved)
	{
		$out = true;
		$i = $call_after_saved ? 1 : 0;
		if ( !empty( $this->onSave[$i] ) )
		{
			if (!is_numeric($this->onSaveArgs[$i]) && empty($this->onSaveArgs[$i]))
			{
				$this->onSaveArgs[$i] = $this->db->getOne("SELECT {$this->tableId} FROM {$this->table} $this->sqlCondition");
			}
			$tmp = call_user_func( $this->onSave[$i], $this->onSaveArgs[$i] );
			if (is_bool($tmp) || $tmp=='1' || $tmp=='0')
			{
				$out = $tmp ? true : false;
			}
		}
		return $out;
	}

	// aksi jika button submit di click
	function action()
	{
		// action dipanggil jika blom pernah dipanggil
		if ( !$this->isLoaded->action )
		{
			$this->isLoaded->action	= true;
			// $this->arrInput diambil jika memang blom ada
			if ( empty($this->arrInput) ) $this->arrInput	= get_object_vars( $this->input );
			// untuk menandai apakah form perlu validasi
			$this->setIsFormRequire();
			$into	= $values = $tableId = '';
			if ( isset( $_POST[$this->deleteButton->name] ) )
			{
				$q = "SELECT $this->tableId FROM ". $this->table ." ". $this->sqlCondition;
				$tableId= $this->db->getOne($q);
				/* CARI APAKAH ADA FIELD YANG PERLU DITANGANI SEBELUM DIHAPUS */
				foreach ($this->arrInput as $input)
				{
					if ($input->isIncludedInDeleteQuery)
					{
						$q = $input->getDeleteSQL($tableId); // untuk multifile, file, tags akan dihapus serta
						if (!empty($q))
						{
							$this->db->Execute($q);
						}
					}
				}
				$q = "DELETE FROM ". $this->table ." ". $this->sqlCondition;
				$this->error	= !$this->db->Execute($q);
				if ( $this->error )
				{
					$this->errorMsg	= $this->db->ErrorMsg();
				}else{
					if($this->isMultiLanguage)
					{
						$q = "DELETE FROM `{$this->LanguageTable}`  WHERE `{$this->LanguageTableId}`={$tableId}".$this->LanguageTableWhere;
						$this->db->Execute($q);
					}
					foreach ($this->arrInput as $input)
					{
						if (!$input->isIncludedInDeleteQuery)
						{
							$q = $input->getDeleteQuery($tableId);
							if (!empty($q))
							{
								$this->db->Execute($q);
							}
						}
					}
				}
			}else
			// jika submit diklik atau first inputnya isset, do something
			if ( isset($_POST[$this->saveButton->name]) || isset($_POST[$this->formName.'_file_delete_image']))
			{
				$formExecute = true;
				if ($this->isFormRequire && isset($_POST[$this->saveButton->name]))
				{
					foreach ($this->arrInput as $input)
					{
						if ($input->isRequire)
						{
							if($input->type=='file')
							{
								$text = @is_uploaded_file($_FILES[$input->name]['tmp_name']) ? '1' : @$_POST[$input->name];
							}else{
								$name = $input->name;
								if (preg_match('~\[\]$~s', $name)) // Jika dia berada dalam multiinput seperti multiform dll
								{
									$text = @current($_POST[preg_replace('~\[\]$~s', '', $name)]);
								}else{
									$text = $input->isMultiLanguage ? @current($_POST[$name]) : @$_POST[$name];
								}
							}
							$req  = explode(' ', $input->isRequire);
							if (empty($text) && $text != '0')
							{
								if (empty($req[1]) || $req[1]=='true')
								{
									$this->setFailSaveMessage('"'.$input->title.'" must not empty!');
									$formExecute = false;
								}
							}else{
								switch ($req[0]) {
									case 'email':
										if (!is_email($text)) {
											$this->setFailSaveMessage('Please enter a valid email address in "'.$input->title.'"!');
											$formExecute = false;
										}
										break;
									case 'url':
										if (!is_url($text)) {
											$this->setFailSaveMessage('Please enter a valid URL in "'.$input->title.'"!');
											$formExecute = false;
										}
										break;
									case 'phone':
										if (!is_phone($text)) {
											$this->setFailSaveMessage('Please enter a valid phone number in "'.$input->title.'"!');
											$formExecute = false;
										}
										break;
									case 'money':
										if (!empty($text) && !preg_match('~^[0-9]+(?:\.[0-9]+)?$~s', $text)) {
											$this->setFailSaveMessage('Please enter a valid money format in "'.$input->title.'"!');
											$formExecute = false;
										}
										break;
									case 'number':
										if (!empty($text) && !preg_match('~^[0-9]+$~s', $text)) {
											$this->setFailSaveMessage('Please enter a valid number in "'.$input->title.'"!');
											$formExecute = false;
										}
										break;
								}
							}
						}
						if (!$formExecute)
						{
							$this->error = true;
							break;
						}
					}
				}
				if ($formExecute)
				{
					$formExecute = $this->actionOnSave(false);
				}
				if ($formExecute)
				{
					$lang_text = array();
					$query     = array();
					foreach ($this->arrInput as $input)
					{
						if($input->isMultiLanguage)
						{
							$last = '';
							foreach((array)$_POST[$input->name] AS $i => $v)
							{
								$t = !empty($v) ? $v : $last;
								if(@$input->nl2br)
								{
									$t = nl2br($t);
								}
								$lang_text[$i][$input->objectName] = $this->cleanSQL($t);
								$last = $t;
							}
						}else{
							$t = $input->getRollUpdateQuery();
							if (!empty($t))
							{
								$query[] = $t;
							}
						}
						$this->setSuccessSaveMessage .= $input->status;
					}
					//menambahkan yang additional field dan valuenya
					foreach ( $this->extraField->field as $i => $f )
					{
						$query[] = '`'.$f .'`=\''.$this->extraField->value[$i].'\', ';
					}
					if (!empty($query))
					{
						$q = "UPDATE ". $this->table ." SET  ".$this->replaceTrailingComma(implode('', $query)) .' '. $this->sqlCondition;
						$this->error	= !$this->db->Execute($q);
					}else{
						$this->error = false;
					}
					if ( $this->error )
					{
						$this->errorMsg	= $this->db->ErrorMsg();
					}else{
						if ($this->isMultiLanguage)
						{
							if(count($lang_text) > 0)
							{
								$q = "SELECT $this->tableId FROM $this->table ".$this->sqlCondition;
								$tableId = $this->db->getOne($q);
								$q = "SELECT `lang_id` FROM `{$this->LanguageTable}` WHERE `{$this->LanguageTableId}`={$tableId} ".$this->LanguageTableWhere;
								$r_lang_id = $this->db->getCol($q);
								foreach($lang_text AS $lang_id => $data)
								{
									$field = array();
									foreach((array)$data AS $var => $val)
									{
										$field[] = "`$var`='$val'";
									}
									$fields = implode(', ', $field);
									if(in_array($lang_id, $r_lang_id))
									{
										$q = "UPDATE `{$this->LanguageTable}` SET {$fields} WHERE `lang_id`={$lang_id} AND `{$this->LanguageTableId}`={$tableId} ".$this->LanguageTableWhere;
									}else{
										foreach ($this->LanguageTableUpdate as $var => $val)
										{
											$field[] = "`{$var}`='{$val}'";
										}
										$fields = implode(', ', $field);
										$q = "INSERT INTO `{$this->LanguageTable}` SET `lang_id`={$lang_id}, `{$this->LanguageTableId}`={$tableId}, {$fields}";
									}
									$this->db->Execute($q);
								}
							}
						}
					}
					$this->error = !$this->actionOnSave(true);
				}// eof if ($formExecute)
			}// eof if ( isset($_POST[$this->saveButton->name]) )
		}// eof isloaded
	}//eof action() method
}