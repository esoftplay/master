<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

include 'block_position-information.php';
$sys->stop();
// START TO GET DATA...
if($id > 0)
{
	if(isset($_POST['block_ref_id']))
	{
		include_once 'block_position_save.php';
		block_edit($id);
	}
	$q             = "SELECT * FROM bbc_block WHERE id=".$id;
	$data          = $db->getRow($q);
	$q             = "SELECT lang_id, title FROM bbc_block_text WHERE block_id=".$data['id'];
	$data['title'] = $db->getAssoc($q);
	$data['name']  = @$r_ref[$data['block_ref_id']];
}else
if($block_ref_id)
{
	//declare default value
	$data = array(
		'block_ref_id'=> $block_ref_id,
		'name'				=> $r_ref[$block_ref_id],
		'position_id'	=> isset($position_id) ? $position_id : 0,
		'show_title'	=> 1,
		'cache'				=> 0,
		'active'			=> 1,
		'group_ids'		=> 'all',
		'menu_ids'		=> 'all',
		'config'			=> ''
	);
	if(isset($_POST['block_ref_id']))
	{
		include_once 'block_position_save.php';
		block_add();
	}
}
$block_cfg = config_decode($data['config']);
if (count($r_lang)==1)
{
	$r_lang[lang_id()]['title'] = 'Title';
}
/*
DISPLAY BLOCK BY MENU
*/
$q = "SELECT m.id, m.par_id, t.title, CONCAT(c.name, ': ') AS cat_name
FROM bbc_menu AS m LEFT JOIN bbc_menu_text AS t ON (m.id=t.menu_id AND lang_id=".lang_id().")
LEFT JOIN bbc_menu_cat AS c ON (m.cat_id=c.id)
WHERE m.active=1 AND m.is_admin=0 ORDER BY c.orderby, m.par_id, m.orderby
";
$r_menu = $db->getAll($q);
$menus1 = array(
	array('id'=>'all', 'title'=>'All'),
	array('id'=>'', 'title'=>'--------------------------------------------'),
	array('id'=>'-1', 'title'=>'Home'),
	array('id'=>'unassigned', 'title'=>'Unassigned')
);
_func('array');
$menus1 = array_merge($menus1, array_option($r_menu));
$menus2 = array(
	array('id'=>'-1', 'title'=>'Home'),
	array('id'=>'unassigned', 'title'=>'Unassigned')
);
$menus2 = array_merge($menus2, array_option($r_menu));
/*
DISPLAY BLOCK BY USER GROUP
*/
$r_group['unassigned']= 'Not Logged User';
$r_group['logged']		= 'All Logged User';
$r_group['all']				= 'Public';
$r_group = array_reverse($r_group, true);
/*
GET BLOCK INFO
*/
$data['help'] = '';
if (preg_match('~(?:\n|\r)//([^\r\n]+)~', file_read(_ROOT.'blocks/'.$data['name'].'/_switch.php'), $m))
{
	$data['help'] = $m[1];
}
if (!empty($data['help']))
{
	$data['help'] = '<p class="help-block"><i><small><small>'.htmlentities($data['help']).'</small></small></i></p>';
}
?>
<form method="POST" action="<?php echo seo_uri(); ?>" onsubmit="return block_submit(this);" name="block" enctype="multipart/form-data" role="form">
	<div class="panel panel-default">
		<div class="panel-heading">
			<center>
				<h3 class="panel-title">
					<?php echo $data['name'];?>
				</h3>
			</center>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label>Block Title</label>
				<div class="input-group checkbox">
					<?php
					foreach($r_lang AS $lang_id => $dt)
					{
						?>
						<input type="text" name="title[<?php echo $lang_id;?>]" value="<?php echo @htmlentities($data['title'][$lang_id]);?>" title="<?php echo $dt['title'];?>" placeholder="<?php echo $dt['title'];?>" class="form-control" />
						<?php
					}
					?>
					<div class="input-group-addon">
						<label><input name="show_title" type="checkbox"  value="1"<?php echo is_checked(@$data['show_title'], 1, true);?> /> Show Title</label>
					</div>
					<div class="input-group-addon">
						<label><input name="active" type="checkbox" value="1"<?php echo is_checked($data['active']);?> /> Publish</label>
					</div>
				</div>
				<?php echo $data['help']; ?>
			</div>
			<?php
			if($data['position_id']==0)
			{
				?>
				<div class="form-group">
					<label>Block Position</label>
					<select name="position_id" class="form-control"><?php echo createOption($r_position, $data['position_id']);?></select>
				</div>
				<?php
			}else{
				?>
				<input type="hidden" name="position_id" value="<?php echo $data['position_id'];?>">
				<?php
			}
			?>
			<div class="form-group">
				<label>Block Theme</label>
				<select name="theme_id" class="form-control"><?php echo createOption($r_theme, @$data['theme_id']);?></select>
				<p class="help-block">This is the custom view to wrap your selected template below, you can create your own theme in <a href="index.php?mod=_cpanel.block&act=theme" class="admin_link">Control Panel / Block Manager / Block Theme</a> and customize the style on <a href="index.php?mod=_cpanel.template&act=editCSS" class="admin_link">Control Panel / Site Template / Edit CSS Style</a></p>
			</div>
			<?php
			$_path = _ROOT.'blocks/'.$data['name'].'/';
			$r_tpl = tpl_scan($_path, $_CONFIG['template']);
			if (empty($block_cfg['template']))
			{
				if (!empty($block_cfg['task']))
				{
					$block_cfg['template'] = $block_cfg['task'];
				}else
				if (!empty($block_cfg['layout']))
				{
					$block_cfg['template'] = $block_cfg['layout'];
					if ($data['name']=='menu')
					{
						$block_cfg['template'] = 'menu-'.$block_cfg['template'];
					}
				}
			}
			if (count($r_tpl) >= 2)
			{
				?>
				<div class="form-group">
					<label>Block Template</label>
					<select name="config[template]" class="form-control"><?php echo createOption($r_tpl, @$block_cfg['template']);?></select>
					<p class="help-block">Select template file you want to use to display this block which is created by your web designer</p>
				</div>
				<?php
			}else{
				?>
				<input type="hidden" name="config[template]" value="<?php echo @$block_cfg['template']; ?>" />
				<?php
			}
			$help_id = 'block_help_'.$data['block_ref_id'].'_'.$data['position_id'].'_'.@$data['id'];
			$_file    = $_path.'_setting.php';
			if(is_file($_file))
			{
				$_setting = array();
				include $_file;
				_func('_setting');
				if (!empty($_setting['template']))
				{
					unset($_setting['template']);
				}
				$form_title = 'Additional Parameter <span id="'.$help_id.'" data-name="'.$data['name'].'"></span>';
				_setting($_setting, $block_cfg, $form_title);
			}
			$panel_id = 'form-advance_'.$data['block_ref_id'].'_'.$data['position_id'].'_'.@$data['id'];
			?>
			<script type="text/javascript">
				_Bbc(function($){
					if ($("#<?php echo $help_id; ?>").length) {
						var a = $("#<?php echo $help_id; ?>");
						$.ajax({
						  url: _URL+"user/help/"+a.data("name")+"?dataType=json",
						  context: document.body,
						  global: false,
						  success: function(b) {
						  	if (b.ok) {
						  		a.removeAttr("id");
						  		a.html('<a href="/" title="Bantuan Additional Parameters"> <span class="glyphicon glyphicon-question-sign" /></a>');
						  		a.on("click", function(e){
						  			e.preventDefault();
						  			window.open(_URL + 'user/help/'+$(this).data("name"), 'help', 'width=800, height=600, align=top, scrollbars=yes, status=no, resizable=yes');
						  			return false;
						  		});
						  	}else{
						  		a.remove();
						  	}
						  }
						});
					}
				});
			</script>
			<div class="panel-group" id="accordion<?php echo $panel_id;?>">
				<div class="panel panel-default">
				  <div class="panel-heading">
				    <h4 class="panel-title" data-toggle="collapse" data-parent="#accordion<?php echo $panel_id;?>" href="#pea_isHideToolOn<?php echo $panel_id;?>" style="cursor: pointer;">
				    	Advance Panel
				    </h4>
				  </div>
				  <div id="pea_isHideToolOn<?php echo $panel_id;?>" class="panel-collapse collapse on">
				    <div class="panel-body">
							<div class="form-group">
								<label>Block Cache duration</label>
								<div class="input-group">
									<input type="number" name="cache" value="<?php echo @$data['cache'];?>" class="form-control">
									<div class="input-group-addon">Second(s)</div>
								</div>
								<p class="help-block">Insert the number for how long this block will use system cache. Or leave it blank if cache is not used. Any file with heavy codes is extremely recommended to use the cache Eg. 900</p>
							</div>
							<div class="form-group">
								<label>Title Link</label>
								<input type="text" name="link" value="<?php echo @$data['link'];?>" class="form-control">
								<p class="help-block">Insert any URL if you want to link the title to somewhere, or leave it empty if it doesn't link to anywhere</p>
							</div>
							<div class="form-group">
								<label>Display Block By Menu [ Display on: | Hide on: ]</label>
								<div class="input-group">
									<select name="menu_ids[]" id="menu_ids" size="10" class="form-control" multiple="multiple">
										<?php echo createOption($menus1, repairExplode(@$data['menu_ids']));?>
									</select>
									<div class="input-group-addon">
										<input onclick="var v=$(this).parent().prev().get(0);for(i=0; i &lt; v.options.length; i++)v.options[i].selected=this.checked;" type="checkbox">
									</div>
									<select name="menu_ids_blocked[]" id="menu_ids_blocked" size="10" class="form-control" multiple="multiple">
										<?php echo createOption($menus2, repairExplode(@$data['menu_ids_blocked']));?>
									</select>
									<div class="input-group-addon">
										<input onclick="var v=$(this).parent().prev().get(0);for(i=0; i &lt; v.options.length; i++)v.options[i].selected=this.checked;" type="checkbox">
									</div>
								</div>
								<p class="help-block">
									You can display this block by visitor current menu, if you select to display in all menu then you can define which menu you want to hide this block on the right selection menu
								</p>
							</div>
							<div class="form-group">
								<label>Display Block By Module [ Display on: | Hide on: ]</label>
								<div class="input-group">
									<select name="module_ids_allowed[]" id="modules1" size="10" class="form-control" multiple="multiple">
										<?php echo createOption($r_module, repairExplode(@$data['module_ids_allowed']));?>
									</select>
									<div class="input-group-addon">
										<input onclick="var v=$(this).parent().prev().get(0);for(i=0; i &lt; v.options.length; i++)v.options[i].selected=this.checked;" type="checkbox">
									</div>
									<select name="module_ids_blocked[]" id="modules2" size="10" class="form-control" multiple="multiple">
										<?php echo createOption($r_module, repairExplode(@$data['module_ids_blocked']));?>
									</select>
									<div class="input-group-addon">
										<input onclick="var v=$(this).parent().prev().get(0);for(i=0; i &lt; v.options.length; i++)v.options[i].selected=this.checked;" type="checkbox">
									</div>
								</div>
								<p class="help-block">
									Please select "All" in "Display Block By Menu" before using this option which is you can display this block on specified module. Display on `modulename` is on the left side, Hide on `modulename` is on the right
								</p>
							</div>
							<div class="form-group">
								<label>Display Block By User Privileges</label>
								<select name="group_ids[]" id="group_ids" size="5" class="form-control" multiple="multiple">
									<?php echo createOption($r_group, repairExplode(@$data['group_ids']));?>
								</select>
								<p class="help-block">
									Option after "<?php echo $r_group['unassigned']; ?>" are public user groups which is you can specify <a href="index.php?mod=_cpanel.group" class="admin_link">here</a> and every single <a href="index.php?mod=_cpanel.user" class="admin_link">public user</a> must have at least one user group
								</p>
							</div>
						</div>
				  </div>
				</div>
			</div>
		</div>
		<input type="hidden" name="block_ref_id" value="<?php echo $data['block_ref_id'];?>">
		<input type="hidden" name="id" value="<?php echo @intval($data['id']);?>">
		<div class="panel-footer">
			<?php
			if (!empty($_GET['return']))
			{
				?>
				<span type="button" class="btn btn-default btn-sm" onclick="document.location.href='<?php echo $_GET['return'];?>';"><span class="glyphicon glyphicon-chevron-left"></span></span>
				<?php
			}
			?>
			<button type="submit" name="submit_update" value="SAVE" class="btn btn-primary btn-sm">
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