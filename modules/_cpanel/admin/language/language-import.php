<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (empty($_GET['return']))
{
	$_GET['return'] = 'index.php?mod=_cpanel.language';
}
if (!empty($_POST['add_lang_id']))
{
	$msg   = '';
	$error = true;
	if (@is_uploaded_file($_FILES['add_code']['tmp_name']))
	{
		if (preg_match('~\.xlsx?$~is', $_FILES['add_code']['name']))
		{
			$tmp_file = _CACHE.$_FILES['add_code']['name'];
			if (move_uploaded_file($_FILES['add_code']['tmp_name'], $tmp_file))
			{
				$data = _lib('excel')->read($tmp_file)->sheet(1)->fetch();
				@unlink($tmp_file);
				if (!empty($data[2]))
				{
					$keys = array('key', 'Content', 'Module');
					$mods = $db->getAssoc("SELECT `name`, `id` FROM `bbc_module` WHERE 1");
					// tambahan untuk yang global
					$mods['GLOBAL SITE'] = 0;
					$cols = array_slice(array_values($data[1]), 0, 3);
					if ($keys == $cols)
					{
						$lang_id = intval($_POST['add_lang_id']);
						$keys    = array_slice(array_keys($data[1]), 0, 3);

						foreach ($data as $i => $row)
						{
							if ($i > 1)
							{
								$code    = $row[$keys[0]];
								$content = $row[$keys[1]];
								$mod_id  = @intval($mods[$row[$keys[2]]]);
								$code_id = intval($db->getOne("SELECT `id` FROM `bbc_lang_code` WHERE `module_id`={$mod_id} AND `code`='{$code}'"));
								if ($code_id)
								{
									$text_id = intval($db->getOne("SELECT `text_id` FROM `bbc_lang_text` WHERE `code_id`={$code_id} AND `lang_id`={$lang_id}"));
								}else{
									$text_id = 0;
								}
								if (empty($code_id))
								{
									$code_id = $db->Insert(
										'bbc_lang_code',
										array(
											'code'      => $code,
											'module_id' => $mod_id
											)
										);
								}
								$arr = array(
									'code_id' => $code_id,
									'lang_id' => $lang_id,
									'content' => $content
									);
								if ($text_id)
								{
									$db->Update('bbc_lang_text', $arr, 'text_id='.$text_id);
								}else{
									$db->Insert('bbc_lang_text', $arr);
								}
							}
						}
						$db->cache_clean();
						$msg   = 'Your languages have been successfully saved';
						$error = false;
					}else{
						$msg = 'Please check your excel file before uploading to import';
					}
				}else{
					$msg = 'Your file is empty';
				}
			}else{
				$msg = 'Your file is failed to upload';
			}
		}else{
			$msg = 'You must upload the correct file format';
		}
	}else{
		$msg = 'Please upload excel file to import';
	}
	if (!empty($msg))
	{
		echo msg($msg, ($error ? 'danger' : 'success'));
	}
}
$_POST = array();
$form  = _lib('pea',  'bbc_lang_code');
$form->initEdit();

$form->edit->addInput('header','header');
$form->edit->input->header->setTitle('Import Language');

$form->edit->addInput( 'lang_id', 'selecttable' );
$form->edit->input->lang_id->setTitle('Select Language');
$form->edit->input->lang_id->setReferenceTable('bbc_lang');
$form->edit->input->lang_id->setReferenceField( 'title', 'id' );
$form->edit->input->lang_id->setRequire();

$form->edit->addInput('code','file');
$form->edit->input->code->setTitle('Upload your excel');
$form->edit->input->code->setRequire();
$form->edit->input->code->setFolder(_CACHE);
$form->edit->input->code->setAllowedExtension(array('xls', 'xlsx'));
$form->edit->input->code->addTip('You can get example format for your excel file by exporting language in <a href="index.php?mod=_cpanel.language" rel="admin_link">Language Page</a>');

$form->edit->setSaveButton('import_language', 'Import Now', 'upload');
$form->edit->action();
echo $form->edit->getForm();
