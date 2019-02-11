<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$template_id = intval($_GET['id']);
$data        = $db->getRow("SELECT * FROM bbc_template WHERE id={$template_id}");
if(!$db->Affected_rows())
{
	redirect($Bbc->mod['circuit'].'.template');
}else{
  $sys->nav_add('Edit Template');
}
if(!empty($_POST['Submit']))
{
	include 'template-edit-action.php';
	$data = $db->getRow("SELECT * FROM bbc_template WHERE id={$template_id}");
}
$r_templates = $r_syncron_from = array();
$q = "SELECT * FROM bbc_template WHERE 1 ORDER BY name ASC";
$r = $db->getAll($q);
foreach($r AS $dt)
{
	$value = array($dt['id'], $dt['name']);
	if($dt['id'] != $data['id'])
	{
		$r_templates[] = $value;
		if($dt['syncron_to'] == 0)
		{
			$r_syncron_from[] = $value;
		}
	}
}
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $data['name'].' ('.date('D - M jS, Y', strtotime($data['installed'])).')'; ?></h3>
	</div>
	<div class="panel-body">
		<div class="form-group">
			<label>Download blocks <?php echo help('Download this blocks parameter');?></label>
			<div class="form-control-static">
				<form action="" class="form-inline" method="post" name="Download" role="form">
					<input name="name" type="text" value="<?php echo $data['name'];?>" class="form-control">
					<input name="Submit" type="submit" value="Download" class="btn btn-default">
				</form>
			</div>
		</div>
		<div class="form-group">
			<label>Upload blocks <?php echo help('Upload Your own block parameter to this template.');?></label>
			<div class="form-control-static">
				<form action="" method="post" name="Upload" class="form-inline" enctype="multipart/form-data">
					<input name="params" type="file" class="form-control" />
					<input name="Submit" type="submit" value="Upload" class="btn btn-default">
				</form>
			</div>
		</div>
<?php
if (count($r_templates) > 0)
{
	?>
		<div class="form-group">
			<label>Import blocks <?php echo help('Import template\'s blocks from another template.<br /><b>CAUTION :</b> current blocks will be truncated before processing');?></label>
			<div class="form-control-static">
				<form action="" method="post" name="import" class="form-inline">
					<select name="id" class="form-control">
						<option value=0>--Import From--</option>
						<?php echo createOption($r_templates, $data['last_copy_from']);?>
					</select>
					<input name="Submit" type="submit" value="Import" class="btn btn-default">
				</form>
			</div>
		</div>
		<div class="form-group">
			<label>Synchronize from <?php echo help('Synchronize this template from another template. All changes to parent template will affect to this template either.<br /><b>CAUTION :</b> destination template\'s blocks will be disabled not deleted');?></label>
			<div class="form-control-static">
				<form action="" method="post" name="Syncronize_to" class="form-inline">
					<select name="id" class="form-control"><?php echo createOption($r, $data['syncron_to']);?></select>
					<input name="Submit" type="submit" value="Syncron" class="btn btn-default">
					<input name="Submit" type="submit" value="Reset" class="btn btn-default">
				</form>
			</div>
		</div>
	<?php
}
?>
	</div>
	<div class="panel-footer">
		<span type="button" class="btn btn-default btn-sm" onclick="document.location.href='index.php?mod=_cpanel.template';"><span class="glyphicon glyphicon-chevron-left"></span></span>
	</div>
</div>
<?php
$tpl_path = _ROOT.'templates/'.$data['name'].'/config/';
if(file_exists($tpl_path.'_switch.php'))
{
	include $tpl_path.'_switch.php';
}
