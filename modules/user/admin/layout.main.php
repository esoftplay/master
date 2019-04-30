<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->set_layout('main.php');
$sys->link_set($sys->template_url.'css/home.css', 'css');
$sys->link_js($sys->template_url.'js/home.js');

// SET MENU...
$allmenus = json_encode(menu_admin());
if (!empty($Bbc->shortcut))
{
	echo '<dl id="x-shortcuts">';
	foreach ($Bbc->shortcut as $menu)
	{
		$src = $sys->template_url.'images/bogus_shortcut.png';
		if (!empty($menu[2]))
		{
			if (is_file(_ROOT.'modules/_cpanel/admin/images/'.$menu[2]))
			{
				$src = _URL.'modules/_cpanel/admin/images/'.$menu[2];
			}else
			if (is_file(_ROOT.'modules/_cpanel/admin/images/icon_'.$menu[2]))
			{
				$src = _URL.'modules/_cpanel/admin/images/icon_'.$menu[2];
			}
		}
		?>
    <dt id="shortcut-<?php echo $menu[0]; ?>" title="<?php echo $menu[1]; ?>">
			<a href="#">
				<img src="<?php echo $src; ?>" />
				<div><?php echo $menu[1]; ?></div>
			</a>
    </dt>
		<?php
	}
	echo '</dl>';
}
$_SESSION[bbcAuth]['Alert'] = array();
unset($_SESSION[bbcAuth]['Alert']);
?>
<a id="x-powered" href="<?php echo _URL; ?>user/help" onclick="window.open(_URL + 'user/help', 'help', 'width=800, height=600, align=top, scrollbars=yes, status=no, resizable=yes');; return false;">
	<img src="<?php echo $sys->template_url; ?>images/button-help.png" ext:qtip="display manual help" />
</a>
<script type="text/javascript">
	var menuInfo=<?php echo $allmenus;?>;
</script>
<iframe src="" name="hidden_frame" style="display:none;" frameborder=0></iframe>
<div id="notif_badge" class="dropup">
	<span id="notif_badge_count" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">0</span>
	<div class="list-group" aria-labelledby="notif_badge_count">
		<ul class="dropdown-menu"></ul>
	</div>
</div>