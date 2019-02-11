<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

if(!empty($_POST['template_name']))
{
	$q = "UPDATE bbc_config SET params='".json_encode($_POST['template_name'])."' WHERE name='template' AND module_id=0";
	$db->Execute($q);
	$_CONFIG['template'] = $_POST['template_name'];
	$sys->clean_cache();
	echo msg("template has been updated.");
}
$q = "SELECT * FROM bbc_template ORDER BY name ASC";
$r_tpl = $db->getAssoc($q);

$header = array('#', 'Template', 'Action', 'Synced from', 'Installed');
$body   = array();
$link   = $Bbc->mod['circuit'].'.block';
$path   = _URL.'modules/_cpanel/admin/images/icon_';

foreach($r_tpl AS $i => $dt)
{
	$checked = ($dt['name'] == $_CONFIG['template']) ? ' checked="checked"' : '';
	$sync    = !empty($r_tpl[$dt['syncron_to']]) ? $r_tpl[$dt['syncron_to']]['name'] : '';
	$action  = '<a href="'.$Bbc->mod['circuit'].'.template&act=update&id='.$i.'" title="update">'.icon('edit', 'Update Template').'</a>';
	if (empty($sync))
	{
		$add = ($checked) ? '" class="admin_link' : '&template_id='.$i;
		$action .= <<<EOT
<a href="{$link}{$add}" title="Block Manager"> <img src="{$path}block.png" /> </a>
<a href="{$link}&act=block_position{$add}" title="Block Position"> <img src="{$path}block_position.png" /> </a>
<a href="{$link}&act=theme{$add}" title="Block Themes"> <img src="{$path}theme.png" /> </a>
EOT;
	}
	$body[]  = array(
		'<input type="radio" name="template_name" value="'.$dt['name'].'"'.$checked.' id="template'.$i.'" />'
	, '<label for="template'.$i.'">'.tip($dt['name'], image(_ROOT.'templates/'.$dt['name'].'/thumbnail.png')).'</label>'
	, $action
	, $sync
	, date("D, M jS Y H:i:s", strtotime($dt['installed']))
	);
}
?>
<form action="" method="POST" class="form-inline" role="form">
	<?php echo table($body, $header); ?>
	<button type="submit" class="btn btn-primary"><?php echo icon('save'); ?> Set Default Template</button>
	<button type="reset" class="btn btn-warning"><?php echo icon('repeat'); ?> RESET</button>
	<button type="button" class="btn btn-default" onclick="document.location.href='<?php echo $Bbc->mod['circuit']; ?>.template&act=scan'"><?php echo icon('import'); ?> Scan New Template</button>
</form>