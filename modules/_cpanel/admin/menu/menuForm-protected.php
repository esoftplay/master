<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

if($prefix=='edit_') // form edit menu
{
	$data['group_ids'] = array();
	if($menu_id > 0)
	{
		foreach($r_group AS $dt)
		{
			if(preg_match('~,'.$menu_id.',~s', $dt['menus']) || preg_match('~,all,~is', $dt['menus']))
			{
				$data['group_ids'][] = $dt['id'];
			}
		}
	}
	echo '<input type="hidden" name="protected_from" value="'.intval($data['protected']).'">';
}else
if($data['par_id'] == 0) { // form add menu for non subMenu
		foreach($r_group AS $dt)
		{
			if(preg_match('~,all,~is', $dt['menus']))
			{
				$data['group_ids'][] = $dt['id'];
			}
		}
}
?>
<table border="0" cellpadding="0" width="250px" cellspacing="0">
  <tr>
    <td>
    	<div style="float: left;">
	    	<input type="checkbox" name="protected" value="1" onClick="ThisCheck('<?php echo $prefix;?>group_ids_allowed', this.checked );" 
	    	id="<?php echo $prefix;?>protectedCheck"<?php echo is_checked($data['protected']);?>>
	    	<label for="<?php echo $prefix;?>protectedCheck">Protect This Menu</label>
    	</div>
    	<div style="float: right;">
				<?php echo help('if its checked this menu will be protected by system');?>
			</div>
    </td>
  </tr>
  <tr>
    <td>
    	<div id="<?php echo $prefix;?>group_ids_allowed">
				<select name="group_ids[]" size="8" style="width: 250px;" multiple="multiple">
					<?php echo createOption($r_group, $data['group_ids']);?>
				</select><br />
				<i>select which users are allowed to access</i>
			</div>
		</td>
  </tr>
</table>
<script type="text/JavaScript">
ThisCheck('<?php echo $prefix;?>group_ids_allowed', document.getElementById('<?php echo $prefix;?>protectedCheck').checked);
</script>
