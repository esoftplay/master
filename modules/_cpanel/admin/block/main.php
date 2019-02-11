<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

if (!empty($_GET['backup']))
{
	$data = array(
		'template' => $_CONFIG['template'],
		'block'    => $db->getAll("SELECT * FROM `bbc_block` WHERE `template_id`={$template_id} ORDER BY `id` ASC"),
		'theme'    => $db->getAll("SELECT * FROM `bbc_block_theme` WHERE `template_id`={$template_id} ORDER BY `id` ASC")
		);
	foreach ($data['block'] as &$dt)
	{
		$dt['title'] = $db->getAssoc("SELECT `lang_id`, `title` FROM `bbc_block_text` WHERE `block_id`={$dt['id']} ORDER BY `lang_id` ASC");
	}
	_func('download', 'file', $_CONFIG['template'].'_'.date('Y-m-d-H-i-s').'.json', json_encode($data, JSON_PRETTY_PRINT));
}else
if (!empty($_POST['restore']))
{
	$msg   = '';
	$error = true;
	if (@is_uploaded_file($_FILES['blocks']['tmp_name']))
	{
		if (preg_match('~\.json$~is', $_FILES['blocks']['name']))
		{
			$json = file_read($_FILES['blocks']['tmp_name']);
			if (!empty($json))
			{
				$data = json_decode($json, 1);
				if (!empty($data))
				{
					if (@$data['template']==$_CONFIG['template'])
					{
						$keys = array('template', 'block', 'theme');
						if ($keys==array_keys($data))
						{
							$keys = array('id', 'template_id', 'block_ref_id', 'position_id', 'show_title', 'link', 'cache', 'theme_id', 'group_ids', 'menu_ids', 'menu_ids_blocked', 'module_ids_allowed', 'module_ids_blocked', 'config', 'orderby', 'active', 'title');
							if ($keys==array_keys($data['block'][0]))
							{
								$is_ok = true;
								$ids   = $db->getCol("SELECT `id` FROM `bbc_block` WHERE `template_id`={$template_id}");
								ids($ids);
								$db->Execute("SET autocommit=0");
								$db->Execute("START TRANSACTION");
								$db->Execute("DELETE FROM `bbc_block` WHERE `template_id`={$template_id}");
								$db->Execute("DELETE FROM `bbc_block_theme` WHERE `template_id`={$template_id}");
								if (!empty($ids))
								{
									$db->Execute("DELETE FROM `bbc_block_text` WHERE `block_id` IN ({$ids})");
								}
								foreach ($data['block'] as $dt)
								{
									$is_ok = $db->Insert('bbc_block', $dt, ['title']);
									if (!$is_ok)
									{
										$is_ok = false;
										break;
									}else{
										foreach ($dt['title'] as $lang_id => $title)
										{
											$txt = array(
												'block_id' => $dt['id'],
												'title'    => $title,
												'lang_id'  => $lang_id
												);
											$db->Insert('bbc_block_text', $txt);
										}
									}
								}
								if ($is_ok)
								{
									foreach ($data['theme'] as $dt)
									{
										$is_ok = $db->Insert('bbc_block_theme', $dt, []);
										if (empty($is_ok))
										{
											$is_ok = false;
											break;
										}
									}
								}
								if ($is_ok)
								{
									$db->Execute("COMMIT");
									$db->cache_clean();
									$msg   = 'Your blocks and themes has been restore';
									$error = false;
								}else{
									$db->Execute("ROLLBACK");
									$msg = 'Please check your file configuration, before you upload to restore the blocks';
								}
							}else{
								$msg = 'Your file has been modified incorrectly';
							}
						}else{
							$msg = 'your file does not match for block configuration';
						}
					}else{
						$msg = 'it seems you\'re uploading configuration file for another template';
					}
				}else{
					$msg = 'Please upload the file with block configuration in it';
				}
			}else{
				$msg = 'Your file is empty';
			}
		}else{
			$msg = 'You must upload the correct format file';
		}
	}else{
		$msg = 'Please upload the configuration file to restore the blocks';
	}
	if (!empty($msg))
	{
		echo msg($msg, ($error ? 'danger' : 'success'));
	}
}

$linkto      = $Bbc->mod['circuit'].'.block&act=edit'.$add_link;
$edit_fields = !empty($_SESSION['block_edit_field']) ? $_SESSION['block_edit_field'] : array();

$r_info = array();
$path = _ROOT.'blocks/';
$r = _func('path', 'list', $path);
foreach ($r as $p)
{
	$r_info[$p] = '';
	if (file_exists($path.$p.'/_switch.php'))
	{
		$txt = file_read($path.$p.'/_switch.php');
		if (preg_match('~(?:\n|\r)//([^\r\n]+)~', $txt, $match))
		{
			$r_info[$p] = $match[1];
		}
	}
}

/* BLOCK REFERENCE */
$form2 = _lib('pea',  $str_table = "bbc_block_ref" );
$form2->initRoll( 'WHERE 1 ORDER BY name', "id" );

$form2->roll->setFormName('ref');
$form2->roll->setSaveButton('block', 'Scan New', 'search');
$form2->roll->setDeleteTool(false);

$form2->roll->addInput('name','sqllinks');
$form2->roll->input->name->setGetName( 'ref_id' );
$form2->roll->input->name->setLinks( $linkto );

$r = $db->getAssoc("SELECT block_ref_id, COUNT(*) AS total FROM bbc_block WHERE template_id={$template_id} GROUP BY block_ref_id");
$form2->roll->addInput( 'used', 'select' );
$form2->roll->input->used->setFieldName('id AS used');
$form2->roll->input->used->addOption($r);
$form2->roll->input->used->setPlaintext( true );

$form2->roll->addInput('info','select');
$form2->roll->input->info->setFieldName('name AS info');
$form2->roll->input->info->addOption(array_values($r_info), array_keys($r_info));
$form2->roll->input->info->setPlaintext( true );

$form2->roll->onSave('block_scan');
$formBlockEdit = $form2->roll->getForm();

/* BLOCK SEARCH */
$form = _lib('pea','bbc_block');
$form->initSearch();

$form->search->addInput('position_id','selecttable');
$form->search->input->position_id->addOption('Select Position','');
$form->search->input->position_id->setReferenceTable('bbc_block_position');
$form->search->input->position_id->setReferenceField('name','id');

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('title,config', false);

$form->search->addExtraField('template_id',$template_id);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();


/* BLOCK LIST */
$form = _lib('pea',  "bbc_block LEFT JOIN bbc_block_text ON (bbc_block_text.block_id=bbc_block.id AND bbc_block_text.lang_id=".lang_id().")" );
$form->initRoll( $add_sql." ORDER BY position_id, orderby ASC", "id" );

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Block List on "'.$_CONFIG['template'].'"');

if (in_array('title', $edit_fields))
{
	$form->roll->addInput( 'title', 'text' );
}else{
	$form->roll->addInput( 'title', 'sqllinks' );
	$form->roll->input->title->setTitle( 'Title' );
	$form->roll->input->title->setGetName( 'id' );
	$form->roll->input->title->setLinks( $linkto );
}

$form->roll->addInput( 'block_ref_id', 'selecttable' );
$form->roll->input->block_ref_id->setTitle( 'Name' );
$form->roll->input->block_ref_id->setReferenceTable( 'bbc_block_ref' );
$form->roll->input->block_ref_id->setReferenceField( 'name', 'id' );
$form->roll->input->block_ref_id->setPlaintext( true );

$form->roll->addInput( 'theme_id', 'selecttable' );
$form->roll->input->theme_id->setTitle( 'Theme' );
$form->roll->input->theme_id->setReferenceTable( 'bbc_block_theme' );
$form->roll->input->theme_id->setReferenceField( 'name', 'id' );
$form->roll->input->theme_id->setPlaintext(!in_array('theme', $edit_fields));

$type = in_array('cache', $edit_fields) ? 'text' : 'sqlplaintext';
$form->roll->addInput( 'cache', $type );
$form->roll->input->cache->setNumberFormat();

$q = "SELECT DISTINCT position_id  FROM bbc_block AS b LEFT JOIN bbc_block_text AS t ON (t.block_id=b.id AND t.lang_id=".lang_id().") $add_sql";
$db->Execute($q);
if($db->Affected_rows() > 1)
{
	$form->roll->addInput( 'position_id', 'selecttable' );
	$form->roll->input->position_id->setTitle( 'Position' );
	$form->roll->input->position_id->setReferenceTable( 'bbc_block_position' );
	$form->roll->input->position_id->setReferenceField( 'name', 'id' );
	$form->roll->input->position_id->setPlaintext( true );

	$form->roll->addInput( 'orderby', 'sqlplaintext' );
	$form->roll->input->orderby->setTitle( 'Order' );
}else{
	$form->roll->addInput( 'orderby', 'orderby' );
	$form->roll->input->orderby->setTitle( 'Order' );
}

$form->roll->addInput( 'show_title', 'checkbox' );
$form->roll->input->show_title->setTitle( 'Title' );
$form->roll->input->show_title->setCaption( 'show' );

$form->roll->addInput( 'active', 'checkbox' );
$form->roll->input->active->setTitle( 'Active' );
$form->roll->input->active->setCaption( 'publish' );

$form->roll->onDelete( 'block_delete' );
$form->roll->onSave( 'block_repair', array(), true );

/* BLOCK DISPLAY */
$tabs = array(
	'List Block'=> $form->roll->getForm(),
	'Add Block'	=> $formBlockEdit
);
ob_start();
$arr = !empty($_SESSION['block_edit_field']) ? $_SESSION['block_edit_field'] : array();
?>
<form method="POST" action="index.php?mod=_cpanel.block&act=edit_field" name="search" class="form-inline pull-right" role="form">
	<div class="form-group">
		<div class="input-group checkbox">
			<label><input type="checkbox" name="edit[]" value="title" <?php echo is_checked(in_array('title', $arr)); ?> /> Edit Title</label> &nbsp;
			<label><input type="checkbox" name="edit[]" value="theme" <?php echo is_checked(in_array('theme', $arr)); ?> /> Edit Theme</label> &nbsp;
			<label><input type="checkbox" name="edit[]" value="cache" <?php echo is_checked(in_array('cache', $arr)); ?> /> Edit Cache</label> &nbsp;
		</div>
	</div>
	<input type="hidden" name="return" value="<?php echo htmlentities(seo_uri()); ?>" />
	<button type="submit" name="block_edit_field" value="EDIT" class="btn btn-default">
		<span class="glyphicon glyphicon-edit"></span>
	</button>
	<button type="submit" name="block_edit_field" value="RESET" class="btn btn-default">
		<span class="glyphicon glyphicon-remove-circle"></span>
	</button>
</form>
<?php

$tabs['List Block'] .= ob_get_contents();
ob_end_clean();
echo tabs($tabs);


function block_scan()
{
	global $db;
	include 'block-update.php';
}
function block_delete($ids = array())
{
	global $db, $template_id;
	$ids[] = 0;
	$q = "DELETE FROM bbc_block WHERE id IN (".implode(',', $ids).")";
	if($db->Execute($q)){
		$q = "DELETE FROM bbc_block_text WHERE block_id IN (".implode(',', $ids).")";
		$db->Execute($q);
		block_repair();
	}
}
function block_repair()
{
	global $db, $template_id;
	include_once 'block-repair.php';
}
link_js(_PEA_URL.'includes/formIsRequire.js', false);
?>
<button type="button" class="btn btn-default btn-sm" data-href="<?php echo seo_uri(); ?>&backup=1" id="backup">
	<span class="glyphicon glyphicon-cloud-download" title="Backup All Blocks"></span>
	Backup All Blocks
</button>
&nbsp;
<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModal">
	<span class="glyphicon glyphicon-cloud-upload" title="Restore All Blocks"></span>
	Restore All Blocks
</button>
<div class="clearfix"></div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    	<form action="" method="POST" enctype="multipart/form-data" class="formIsRequire">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Restore All Blocks</h4>
	      </div>
	      <div class="modal-body">
        	<div class="form-group">
        		<label>Upload recovery file</label>
        		<input type="file" name="blocks" class="form-control" placeholder="configuration file" req="any true" />
        		<div class="display-block">
        			Upload your recovery file from the last time you downloaded from "<?php echo $_CONFIG['template']; ?>" template.
        			Once you submit this form, all changes you've made after you download the configuration will be replaced based
        			on your configuration file.
        		</div>
        	</div>
	      </div>
	      <div class="modal-footer">
	        <button type="submit" name="restore" value="block" class="btn btn-primary"><?php echo icon('floppy-save'); ?> Submit</button>
	      </div>
    	</form>
    </div>
  </div>
</div>
<script type="text/javascript">
	_Bbc(function($){
		$("#backup").on("click", function(e){
			e.preventDefault();
			if (confirm("You're about to download current Block configuraton, which is only used to recover the blocks for this particular tempate (<?php echo $_CONFIG['template']; ?>). Do you want to continue?")) {
				document.location.href=$(this).data("href");
			}
		})
	});
</script>