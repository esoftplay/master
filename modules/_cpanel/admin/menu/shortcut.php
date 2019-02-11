<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sql1 = "SELECT id AS i, id, par_id, title, is_shortcut FROM bbc_menu AS m LEFT JOIN bbc_menu_text AS t ON (m.id=t.menu_id AND t.lang_id=".lang_id().") WHERE is_admin=1 ORDER BY par_id, orderby ASC";
$menu = $db->getAssoc($sql1);
if (empty($menu))
{
	$r_q = '
ALTER TABLE `bbc_menu` DROP INDEX `active`;
ALTER TABLE `bbc_menu` ADD `is_shortcut` TINYINT(1)  NULL  DEFAULT \'0\'  AFTER `is_admin`;
ALTER TABLE `bbc_menu` ADD INDEX (`is_shortcut`);
ALTER TABLE `bbc_menu` ADD INDEX (`active`);
ALTER TABLE `bbc_cpanel` DROP INDEX `active`;
ALTER TABLE `bbc_cpanel` ADD `is_shortcut` TINYINT(1)  NULL  DEFAULT \'0\'  AFTER `link`;
ALTER TABLE `bbc_cpanel` ADD INDEX (`is_shortcut`);
ALTER TABLE `bbc_cpanel` ADD INDEX (`active`)';
	$arr = explode(';', $r_q);
	foreach ($arr as $q)
	{
		$q = trim($q);
		if (!empty($q))
		{
			$db->Execute($q);
		}
	}
	$menu = $db->getAssoc($sql1);
	if (empty($menu))
	{
		pr($r_q);
		echo explain('Execute above query to your database server to continue!');
		die();
	}else{
		redirect(seo_uri());
	}
}
$sql2   = "SELECT id AS i, id, par_id, title, is_shortcut FROM bbc_cpanel WHERE 1 ORDER BY par_id, orderby ASC";
$cpanel = $db->getAssoc($sql2);
$return = !empty($_GET['return']) ? $_GET['return'] : _URL.'admin/index.php?mod=_cpanel.menu';

if (!empty($_POST['shortcut_submit_update']))
{
	$db->Execute("UPDATE bbc_menu SET is_shortcut=0 WHERE is_admin=1");
	$db->Execute("UPDATE bbc_cpanel SET is_shortcut=0 WHERE 1");
	if (!empty($_POST['menus']))
	{
		$ids = $_POST['menus'];
		ids($ids);
		if (!empty($ids))
		{
			$db->Execute("UPDATE bbc_menu SET is_shortcut=1 WHERE id IN ({$ids})");
		}
	}
	if (!empty($_POST['cpanels']))
	{
		$ids = $_POST['cpanels'];
		ids($ids);
		if (!empty($ids))
		{
			$db->Execute("UPDATE bbc_cpanel SET is_shortcut=1 WHERE id IN ({$ids})");
		}
	}
	$menu   = $db->getAssoc($sql1);
	$cpanel = $db->getAssoc($sql2);
	echo msg("Admin shortcut has been updated, please refresh all the page to view your result!");
}

_func('array');
$r_menu = array_path($menu);
$r_cpanel = array_path($cpanel);
$sys->nav_add('Create Desktop Shortcut');
?>
<form action="" method="POST" name="shortcut" role="form">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Create Desktop Shortcut</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label>Admin Menu Shortcuts</label>
				<div class="input-group">
					<select name="menus[]" id="menus" multiple="true" size="10" class="form-control">
						<?php
						foreach ($r_menu as $i => $title)
						{
							?>
							<option value="<?php echo $i; ?>"<?php echo $menu[$i]['is_shortcut'] ? ' selected' : ''; ?>><?php echo $title; ?></option>
							<?php
						}
						?>
					</select>
					<div class="input-group-addon">
						<input onclick="var v=$(this).parent().prev().get(0);for(i=0; i<v.options.length; i++)v.options[i].selected=this.checked;" type="checkbox"></div>
				</div>
				<p class="help-block">
					Select which menu you want to create short on your admin desktop
					<span class="text-info">
						PS: Press and Hold the command (CMD) / control (Ctrl) button on your keyboard to select multiple options
					</span>
				</p>
			</div>
			<div class="form-group">
				<label>Control Panel Shortcuts</label>
				<div class="input-group">
					<select name="cpanels[]" id="cpanels" multiple="true" size="10" class="form-control">
						<?php
						foreach ($r_cpanel as $i => $title)
						{
							?>
							<option value="<?php echo $i; ?>"<?php echo $cpanel[$i]['is_shortcut'] ? ' selected' : ''; ?>><?php echo $title; ?></option>
							<?php
						}
						?>
					</select>
					<div class="input-group-addon">
						<input onclick="var v=$(this).parent().prev().get(0);for(i=0; i<v.options.length; i++)v.options[i].selected=this.checked;" type="checkbox"></div>
				</div>
				<p class="help-block">
					Select which control panel you want to create short on your admin desktop
					<span class="text-info">
						PS: Press and Hold the command (CMD) / control (Ctrl) button on your keyboard to select multiple options
					</span>
				</p>
			</div>
		</div>
		<div class="panel-footer">
			<span type="button" class="btn btn-default btn-sm" onclick="document.location.href='<?php echo $return; ?>';">
				<span class="glyphicon glyphicon-chevron-left"></span>
			</span>
			<button type="submit" name="shortcut_submit_update" value="SAVE" class="btn btn-primary btn-sm">
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