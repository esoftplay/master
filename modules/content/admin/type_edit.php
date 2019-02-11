<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

ob_start();
include_once '../_config.php';
$id = @intval($_GET['id']);
$sys->link_js('type_edit.js');
if(isset($_POST['submit_update']))
{
	$dt = array(
	  'title'  => $_POST['title']
	, 'detail' => json_encode($_POST['detail'])
	, 'list'   => json_encode($_POST['list'])
	, 'menu_id'=> @intval($_POST['menu_id'])
	, 'active' => @intval($_POST['active'])
	);
	$add_msg = '';
	if($id > 0)
	{
		$q = "SELECT menu_id FROM bbc_content_type WHERE id=$id";
		$menu_id = $db->getOne($q);
		if($menu_id)
		{
			if(!$dt['menu_id'])
			{
				_func('menu');
				menu_delete($menu_id);
				$add_msg = ' <a href="'._URL._ADMIN.'" target="_parent">click here..!</a> to get update';
			}else{
				$dt['menu_id'] = $menu_id;
			}
		}else{
			if($dt['menu_id'])
			{
				$dt['menu_id'] = content_type_menu_create($id, $dt['title'], $Bbc->mod['circuit'].'.'.$id.'_content_sub');
				$add_msg = ' <a href="'._URL._ADMIN.'" target="_parent">click here..!</a> to get update';
			}
		}
		$q = "UPDATE bbc_content_type
		SET `title`	= '".$dt['title']."'
		, `detail`	= '".$dt['detail']."'
		, `list`		= '".$dt['list']."'
		, `menu_id`	= '".$dt['menu_id']."'
		, `active`	= '".$dt['active']."'
		WHERE id=$id
		";
		$db->Execute($q);
		echo msg('Success updating data.'.$add_msg);
	}else{
		$q = "INSERT INTO bbc_content_type
		SET `title`	= '".$dt['title']."'
		, `detail`	= '".$dt['detail']."'
		, `list`		= '".$dt['list']."'
		, `menu_id`	= '".$dt['menu_id']."'
		, `active`	= '".$dt['active']."'
		";
		$db->Execute($q);
		if($dt['menu_id'])
		{
			$id = $db->Insert_ID();
			$dt['menu_id'] = content_type_menu_create($id, $dt['title'], $Bbc->mod['circuit'].'.'.$id.'_content_sub');
			$q = "UPDATE bbc_content_type SET menu_id=".$dt['menu_id']." WHERE id=$id";
			$db->Execute($q);
			$add_msg = ' <a href="'._URL._ADMIN.'" target="_parent">click here..!</a> to get update';
		}
		echo msg('Succees to add data.'.$add_msg);
	}
	if(!empty($_SESSION['type_menu_exists']))
	{
		_func('menu');
		foreach((array)$_SESSION['type_menu_exists'] AS $menu)
		{
			if($menu['code']=='delete')
			{
				menu_delete($menu['id']);
			}else
			if($menu['code']=='new')
			{
				$q="INSERT INTO bbc_menu
						SET par_id		= '".$menu['par_id']."'
						, module_id		= '".$sys->module_id."'
						, seo					= '".menu_seo($menu['seo'], $menu['title'])."'
						, link				= 'index.php?mod=content.type&id={$id}'
						, orderby			= '".$menu['orderby']."'
						, cat_id			= '".$menu['cat_id']."'
						, is_content	= 0
						, content_id	= 0
						, protected		= 0
						, is_admin		= 0
						, active			= 1
				";
				if($db->Execute($q))
				{
					$menu_id = $db->Insert_ID();
					$q = "SELECT lang_id FROM bbc_menu_text WHERE menu_id=$menu_id";
					$r_key_lang = $db->getCol($q);
					// INSERT TITLE
					foreach((array)$menu['titles'] AS $lang_id => $title)
					{
						if(in_array($lang_id, $r_key_lang))
						{
							$q = "UPDATE bbc_menu_text SET title	= '{$title}'
							WHERE menu_id={$menu_id} AND lang_id={$lang_id}";
						}else{
							$q = "INSERT INTO bbc_menu_text
							SET menu_id = {$menu_id}
							, title			= '{$title}'
							, lang_id		= {$lang_id}
							";
						}
						$db->Execute($q);
					}
					// REPAIR ORDERBY..
					$q="UPDATE bbc_menu SET orderby=(orderby+1)
						WHERE cat_id= ".$menu['cat_id']."
						AND par_id	= ".$menu['par_id']."
						AND is_admin= 0
						AND orderby>=".$menu['orderby']."
						AND id     != ".$menu_id."
					";
					$db->Execute($q);
					menu_repair();
				}
			}
		}
	}
	content_type_refresh();
}
$r_lang 		= lang_assoc();
$r_key_lang = array_keys($r_lang);
$conf_list  = content_config_list();
$conf_det   = content_config_detail();
if($id > 0)
{
	$type_form_title = 'Edit Type';
	$q = "SELECT * FROM bbc_content_type WHERE id=$id";
	$data = $db->getRow($q);
	$data['detail']= config_decode($data['detail']);
	$data['list']  = config_decode($data['list']);
	$data['menu_id'] = $data['menu_id'] ? 1 : 0;
	$sys->nav_add('Edit Type');
}else{
	$type_form_title = 'New Type';
	$data = array(
		'id'			=> 0
	,	'title'		=> ''
	,	'detail'	=> get_config('content', 'detail')
	,	'list'		=> get_config('content', 'list')
	,	'menu_id' => '0'
	,	'active'	=> '1'
	);
	$sys->nav_add('Add Content Type');
}
$cfg  = _class('bbcconfig');
$link = 'index.php?mod=content.config_default';
?>
<form method="POST" action="" name="type" enctype="multipart/form-data">
	<div class="col-md-8">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo $type_form_title; ?></h3>
			</div>
			<script type="text/javascript">
			var menu_delimiter="<?php echo menu_delimiter(); ?>";
			</script>
			<div class="panel-body">
				<div class="form-group">
					<label>Content Type's Title</label>
					<input name="title" type="text" class="form-control" value="<?php echo @$data['title'];?>" />
				</div>
				<div class="form-group">
					<label>Admin Panel</label>
					<div class="checkbox">
						<label>
							<input type="checkbox" name="menu_id" value="1" id="menu_id"<?php echo is_checked($data['menu_id']);?> /> Create Menu
						</label>
					</div>
				</div>
				<div class="panel-group" id="accordionlist">
					<div class="panel panel-default">
					  <div class="panel-heading">
					    <h4 class="panel-title" data-toggle="collapse" data-parent="#accordionlist" href="#pea_isHideToolOnlist" style="cursor: pointer;">
								Content List Parameters
					    </h4>
							<a href="<?php echo $link; ?>" title="Edit Default Value" class="pull-right admin_link" style="margin-top: -18px;">
								<?php echo icon('wrench', 'Edit Default Value'); ?>
							</a>
					  </div>
					  <div id="pea_isHideToolOnlist" class="panel-collapse collapse on">
					    <div class="panel-body">
					    	<?php echo $cfg->show_param($conf_list['config'], $data['list'], 'null', 'list');?>
							</div>
					  </div>
					</div>
				</div>
				<div class="panel-group" id="accordiondetail">
					<div class="panel panel-default">
					  <div class="panel-heading">
					    <h4 class="panel-title" data-toggle="collapse" data-parent="#accordiondetail" href="#pea_isHideToolOndetail" style="cursor: pointer;">
								Content Detail Parameters
					    </h4>
							<a href="<?php echo $link; ?>" title="Edit Default Value" class="pull-right admin_link" style="margin-top: -18px;">
								<?php echo icon('wrench', 'Edit Default Value'); ?>
							</a>
					  </div>
					  <div id="pea_isHideToolOndetail" class="panel-collapse collapse on">
					    <div class="panel-body">
					    	<?php echo $cfg->show_param($conf_det['config'], $data['detail'], 'null', 'detail');?>
							</div>
					  </div>
					</div>
				</div>
				<div class="form-group">
					<label>Content Type's Status</label>
					<div class="checkbox">
						<label>
							<input type="checkbox" name="active" value="1" id="active"<?php echo is_checked($data['active']);?> /> Activate
						</label>
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<?php
				if(!empty($_GET['return']))
				{
					echo '<span type="button" class="btn btn-default btn-sm" onclick="document.location.href=\''.$_GET['return'].'\';"><span class="glyphicon glyphicon-chevron-left"></span></span> ';
				}
				?>
				<button type="submit" name="submit_update" default="true" value="&nbsp;SAVE&nbsp;" class="btn btn-primary btn-sm">
					<?php echo icon('floppy-disk'); ?>
					SAVE
				</button>
				<button type="reset" class="btn btn-warning btn-sm">
					<?php echo icon('repeat'); ?>
					RESET
				</button>
			</div>
		</div>
		<?php
		if ($Bbc->mod['task'] == 'type_edit')
		{
			$c = $db->getOne("SELECT COUNT(*) FROM bbc_content_type WHERE 1");
			if ($c < 2)
			{
				echo $sys->button($Bbc->mod['circuit'].'.type_add','Add Content Type', 'fa-file-text-o');
			}
		}
		?>
	</div>
	<div class="col-md-4">
		<?php include 'type_edit-menu.php'; ?>
	</div>
</form>
<?php
$type_form = ob_get_contents();
ob_end_clean();
