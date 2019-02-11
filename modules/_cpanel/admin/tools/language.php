<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Language Installer');
$form = _lib('pea', 'bbc_lang');
$form->initAdd();

$form->add->addInput( 'header', 'header' );
$form->add->input->header->setTitle( 'Add New Language' );

$form->add->addInput( 'title', 'text' );
$form->add->input->title->setTitle( 'Title' );
$form->add->input->title->setSize( 40 );

$form->add->addInput( 'code', 'text' );
$form->add->input->code->setTitle( 'Code' );

#$form->add->onInsert('tool_language_add','',true);
$form->add->action();

$form->initRoll( "ORDER BY title ASC", 'id' );

$form->roll->addInput( 'title', 'text' );
$form->roll->input->title->setTitle( 'Title' );
$form->roll->input->title->setSize( 40 );

$form->roll->addInput( 'code', 'text' );
$form->roll->input->code->setTitle( 'Code' );

$form->roll->addInput('link1', 'editlinks');
$form->roll->input->link1->setTitle('Download');
$form->roll->input->link1->setIcon('download');
$form->roll->input->link1->setFieldName('id');
$form->roll->input->link1->setAlign('center');
$form->roll->input->link1->setLinks($Bbc->mod['circuit'].'.tools&act=language_download');

$form->roll->onSave('tool_language_repair', '', true);
$form->roll->onDelete('tool_language_repair', '', true);

$r_lang = $db->getAssoc("SELECT id, title FROM bbc_lang");
if(isset($_POST['Submit_params']))
{
	if(is_uploaded_file($_FILES['params']['tmp_name']) AND strtolower(substr($_FILES['params']['name'], -4)) == '.cfg')
	{
		$_file = _ROOT.'images/param.cfg';
		move_uploaded_file($_FILES['params']['tmp_name'], $_file);
		@chmod ($_file, 0777);
		$txt = file_read($_file);
		@unlink($_file);
		$param = config_decode($txt);
		$lang_id = intval($_POST['lang_id']);
		// DELETE AVAILABLE LANGUAGE
		$q = "DELETE FROM bbc_lang_text WHERE lang_id=$lang_id";
		$db->Execute($q);
		$left = $db->getOne("SELECT COUNT(*) FROM bbc_lang_text WHERE 1");
		if (empty($left))
		{
			$db->Execute("TRUNCATE TABLE bbc_lang_text");
		}
		$q = "SELECT CONCAT(LOWER(code), '_',module_id) AS code, id FROM bbc_lang_code";
		$r_words = $db->getAssoc($q);
		$q = "SELECT name, id FROM bbc_module";
		$r_module = $db->getAssoc($q);
		$r_module[''] = 0;
		$module_array = array_flip($r_module);
		foreach((array)$param AS $d)
		{
			$word = $d['name'].'_'.@$r_module[$d['module']];
			if(in_array($d['module'], $module_array) && isset($r_words[$word]))
			{
				$q = "INSERT INTO bbc_lang_text
							SET lang_id= $lang_id
							, code_id  = ".$r_words[$word]."
							, content  = '".$d['content']."'
							";
				$db->Execute($q);
			}
		}
		echo msg('Success updating data.');
	}else echo msg('Failed updating data.');
}
ob_start();
?>
<form name="lang" enctype="multipart/form-data" action="" method="post" role="form">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Upload Language Parameter</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label>Language</label>
				<select name="lang_id" class="form-control"><?php echo createOption($r_lang, @$_POST['lang_id']);?></select>
			</div>
			<div class="form-group">
				<label>Parameter</label>
				<input type="file" name="params" class="form-control" />
			</div>
		</div>
		<div class="panel-footer">
			<button name="Submit_params" type="submit" value="Submit" class="btn btn-primary btn-sm">
				<span class="glyphicon glyphicon-floppy-disk"></span>
				SAVE
			</button>
			<button type="reset" class="btn btn-warning btn-sm">
				<span class="glyphicon glyphicon-repeat"></span>
				RESET
			</button>
		</div>
	</div>
</form>
<?php
$upload_form = ob_get_contents();
ob_end_clean();
$tabs = array('Languages' => $form->roll->getForm(), 'Add' => $form->add->getForm(), 'Upload' => $upload_form);
echo tabs($tabs);

function tool_language_repair()
{
	global $db;
	$lang_ids = $db->getCol("SELECT id FROM bbc_lang");
	$r = $db->getCol("SHOW TABLES");
	$tables = array();
	foreach((array)$r AS $tbl)
	{
		if(preg_match('~_text$~is', $tbl))
			$tables[] = $tbl;
	}
	foreach((array)$tables AS $table)
	{
		$db->Execute("DELETE FROM $table WHERE lang_id NOT IN(".implode(',', $lang_ids).")");
	}
	lang_refresh();
}