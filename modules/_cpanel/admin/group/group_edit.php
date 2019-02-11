<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

_func('array');
function repairImplodeVar($arr, $r_selected)
{
	$output = array();
	$r = is_array($r_selected) ? $r_selected : array();
	array_unshift($arr, array('all', 'All'));
	foreach($arr AS $data)
	{
		$selected = in_array($data[0], $r) ? '1' : '0';
		$output[] = '["'.$data[0].'", "'.$data[1].'", "'.$selected.'"]';
	}
	return $output;
}
ob_start();
$_GET['id'] = @intval($_GET['id']);
if(isset($_POST['name']))
{
	$id = @intval($_POST['id']);
	$_POST['score']         = @intval($_POST['score']);
	$_POST['is_customfield']= @intval($_POST['is_customfield']);
	$_POST['is_admin']			= @intval($_POST['is_admin']);
	$_POST['menus']					= @is_array($_POST['menus']) ? $_POST['menus'] : array();
	if(in_array('all', $_POST['menus'])) $_POST['menus'] = array('all');
	$_POST['cpanels']	= @is_array($_POST['cpanels']) ? $_POST['cpanels'] : array();
	if(in_array('all', $_POST['cpanels'])) $_POST['cpanels'] = array('all');

	$q = "SELECT 1 FROM bbc_user_group WHERE id=".$id;
	$exist = $db->getOne($q);
	$add_msg = '';
	if($exist > 0)
	{
		$q="UPDATE bbc_user_group
				SET `name`	      = '".$_POST['name']."'
				,	`desc`		      = '".$_POST['desc']."'
				,	`menus`		      = '".repairImplode($_POST['menus'])."'
				,	`cpanels`	      = '".repairImplode($_POST['cpanels'])."'
				,	`score`         = ".$_POST['score']."
				,	`is_customfield`= ".$_POST['is_customfield']."
				,	`is_admin`			= '".$_POST['is_admin']."'
				WHERE `id`	= $id
				";
		$succeed = $db->Execute($q);
	}else{
		$q="INSERT INTO bbc_user_group
				SET `name`	      = '".$_POST['name']."'
				,	`desc`		      = '".$_POST['desc']."'
				,	`menus`		      = '".repairImplode($_POST['menus'])."'
				,	`cpanels`	      = '".repairImplode($_POST['cpanels'])."'
				,	`score`         = ".$_POST['score']."
				,	`is_customfield`= ".$_POST['is_customfield']."
				,	`is_admin`			= '".$_POST['is_admin']."'
				";
		$succeed = $db->Execute($q);
		if(@$_POST['is_customfield'] == '1') $add_msg = '<br />Please <a href="'.site_url($Bbc->mod['circuit'].'.'.$Bbc->mod['task'].'&act=edit&id='.$db->Insert_ID()).'">click this link !</a> to manage user field.';
	}
	$Msg = ($succeed) ? '<font color="black">Succeed to update data.</font>'.$add_msg : '<font color="red">Failed to update data.</font>';
	echo msg($Msg);
	unset($_POST);
}
/*====================================*
 * GET GROUP MAIN DATA...
 *====================================*/
$q = "SELECT * FROM bbc_user_group WHERE id=".$_GET['id'];
$data = $db->getRow($q);
/*====================================*
 * GET MENU LINKS...
 *====================================*/
$r_menu_public = $r_menu_admin = array();
$q = "SELECT m.id, m.par_id AS par_id, t.title
			, IF(m.is_admin='0', CONCAT(c.name,': '), '') AS cat_name
			, m.is_admin
			FROM bbc_menu AS m
			LEFT JOIN bbc_menu_text AS t ON (m.id=t.menu_id AND lang_id=".lang_id().")
			LEFT JOIN bbc_menu_cat AS c ON(m.cat_id=c.id)
			WHERE m.active=1 AND protected=1 ORDER BY c.orderby, m.par_id, m.orderby ASC";
$r = $db->getAll($q);
foreach($r AS $dt)
{
	if($dt['is_admin']) $r_menu_admin[] = $dt;
	else $r_menu_public[] = $dt;
}
$data['menus'] = repairExplode($data['menus']);

# GET ARRAY FOR PUBLIC MENU...
$r_menu_public = repairImplodeVar(array_option($r_menu_public, 0, @$r_menu_public[0]['cat_name']), $data['menus']);

# GET ARRAY FOR ADMIN MENU...
$r_menu_admin = repairImplodeVar(array_option($r_menu_admin, 0, ''), $data['menus']);

/*====================================*
 * GET CPANEL LINKS...
 *====================================*/
$r = array();
foreach($Bbc->menu->cpanel_array AS $dt)
{
	$dt['cat_name'] = '';
	$r[] = $dt;
}
$r_cpanel = repairImplodeVar(array_option($r, 0, ''), @repairExplode($data['cpanels']));
?>
<script type="text/JavaScript" >
	var menuLink = new Array();
	var CpanelLink = new Array();
	menuLink	=[[<?php echo implode(',', $r_menu_public);?>], [<?php echo implode(',', $r_menu_admin);?>]];
	CpanelLink=[<?php echo implode(',', $r_cpanel);?>];
	function checkAdminMenu(is_admin)
	{
		var optMenu = document.getElementById('menus');
		var optCpanel = document.getElementById('cpanels');
		optMenu.options.length = 0;
		for(i=0; i < menuLink[is_admin].length; i++){
			optMenu.options[i] = new Option(menuLink[is_admin][i][1], menuLink[is_admin][i][0]);
			optMenu.options[i].selected = (menuLink[is_admin][i][2]=='1') ? true : false;
		}
		optCpanel.options.length = 0;
		if(is_admin=='1'){
			for(i=0; i < CpanelLink.length; i++){
				optCpanel.options[i] = new Option(CpanelLink[i][1], CpanelLink[i][0]);
				optCpanel.options[i].selected = (CpanelLink[i][2]=='1') ? true : false;
			}
		}
	}
	function formCheck(){
		var passed = false;
		with(document.edit){
			if(name.value==""){
				alert('please insert Group User name.');
				name.focus();
			}else if(is_admin.value == ""){
				alert('Please Select permission access for this group');
				is_admin.focus();
			}else{
				passed = true;
			}
		}
		return passed;
	}
</script>

<form method="POST" action="" name="edit" enctype="multipart/form-data" role="form" onSubmit="return formCheck();">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">User Group</h3>
		</div>
		<div class="panel-body">
			<div class="col-md-6">
				<div class="form-group">
					<label>Group Name</label>
					<input name="name" type="text" value="<?php echo @$data['name'];?>" class="form-control" title="Group Name" placeholder="Group Name">
				</div>
				<div class="form-group">
					<label>Description</label>
					<textarea name="desc" class="form-control" rows=4 cols=35 placeholder="User Group Description"><?php echo @$data['desc'];?></textarea>
				</div>
				<div class="form-group">
					<label>Score</label>
					<input name="score" type="text" value="<?php echo @$data['score'];?>" class="form-control" title="Score" placeholder="Score">
					<p class="help-block">System will take the higher score of group in case the user has multiple groups</p>
				</div>
				<div class="form-group">
					<label>Custom Field</label>
					<div class="radio">
						<label><input name="is_customfield" id="is_customfield1" type="radio" value="1" <?php if(@$data['is_customfield']=='1') echo ' checked';?>> Yes</label>
						<label><input name="is_customfield" id="is_customfield0" type="radio" value="0" <?php if(@$data['is_customfield']!='1') echo ' checked';?>> No</label>
					</div>
					<div class="help-block">
						<a href="index.php?mod=_cpanel.user&act=field" rel="admin_link">Click here!</a> to manage your default user fields.
					</div>
				</div>
				<div class="form-group">
					<label>Permission</label>
					<div class="radio">
						<label><input name="is_admin" id="is_admin0" type="radio" value="0" onClick="checkAdminMenu(this.value);" <?php if(@$data['is_admin']!='1') echo ' checked';?>> Public Access</label>
						<label><input name="is_admin" id="is_admin1" type="radio" value="1" onClick="checkAdminMenu(this.value);" <?php if(@$data['is_admin']=='1') echo ' checked';?>> Admin Access</label>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label>Menu Link</label>
					<div class="input-group">
						<select name="menus[]" multiple="" id="menus" size="10" class="form-control" title="Menu Link" placeholder="Menu Link"></select>
						<div class="input-group-addon">
							<input onclick="var v=$(this).parent().prev().get(0);for(i=0; i &lt; v.options.length; i++)v.options[i].selected=this.checked;" type="checkbox">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label>Cpanel Link</label>
					<div class="input-group">
						<select name="cpanels[]" multiple="" id="cpanels" size="10" class="form-control" title="Menu Link" placeholder="Menu Link"></select>
						<div class="input-group-addon">
							<input onclick="var v=$(this).parent().prev().get(0);for(i=0; i &lt; v.options.length; i++)v.options[i].selected=this.checked;" type="checkbox">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
<?php if (!empty($_GET['return']))
			{
				$sys->nav_add("Edit User Group");
				?>
				<span type="button" class="btn btn-default btn-sm" onclick="document.location.href='<?php echo $_GET['return'] ?>';"><span class="glyphicon glyphicon-chevron-left"></span></span>
				<?php
			} ?>
			<button name="user_update" type="submit" value="&nbsp;SAVE&nbsp;" class="btn btn-primary btn-sm">
				<span class="glyphicon glyphicon-floppy-disk"></span>
				SAVE
			</button>
			<button type="reset" class="btn btn-warning btn-sm" onClick="document.forms.edit.reset(); checkAdminMenu(<?php echo @intval($data['is_admin']);?>);">
				<span class="glyphicon glyphicon-repeat"></span>
				RESET
			</button>
		</div>
	</div>
</form>
<script type="text/JavaScript">
	document.forms['edit'].reset()
	checkAdminMenu(<?php echo @intval($data['is_admin']);?>);
</script>
<?php
$group_edit = ob_get_contents();
ob_end_clean();
