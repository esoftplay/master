<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$data['id'] = @intval($data['id']);
?>
<div class="panel panel-default">
	<div class="panel-heading">
		Create Menu of this Category
	</div>
	<div class="panel-body">
		<div class="form-group">
			<label>Menu Position</label>
			<select id="<?php echo $prefix;?>menu_cat_id" class="form-control">
				<?php echo createOption(@$r_position, '');?>
			</select>
		</div>
		<div class="form-group">
			<label>Parent Menu</label>
			<select id="<?php echo $prefix;?>menu_par_id" class="form-control"></select>
		</div>
		<div class="form-group">
			<label>Place Menu After</label>
			<select id="<?php echo $prefix;?>menu_orderby" class="form-control"></select>
			<div class="help-block">new menu will be placed after selection</div>
		</div>
		<div class="form-group">
			<label>Menu Title</label>
<?php	foreach($r_lang AS $lang_id => $dt) {	?>
			<input type="text" id="<?php echo $prefix;?>menu_title_<?php echo $lang_id;?>" placeholder="<?php echo $dt['title'];?>" class="form-control menu_create" data-prefix="<?php echo $prefix; ?>" data-id="<?php echo $data['id']; ?>" />
<?php	}	?>
		</div>
		<div class="form-group">
			<label>SEO Link</label>
			<input type="text" id="<?php echo $prefix;?>menu_seo" class="form-control menu_create" data-prefix="<?php echo $prefix; ?>" data-id="<?php echo $data['id']; ?>" />
		</div>
		<input type="button" value="Create" onclick="return menu_create('<?php echo $prefix;?>', <?php echo $data['id'];?>);" class="btn btn-default" />
		<div id="<?php echo $prefix;?>menus">
			<?php
			$q="SELECT id, cat_id FROM bbc_menu
					WHERE is_content_cat=1 AND content_cat_id=".$data['id']."  ORDER BY cat_id, par_id, orderby";
			$r = $db->getAssoc($q);
			$av_menu = array();
			foreach($r AS $id => $cat_id)
			{
				$dt = $r_menu[$cat_id][$id];
				$dt['code'] = 'old';
				$av_menu[] = $dt;
			}
			if(!empty($av_menu))
				$_SESSION[$prefix.'content_category_menu'] = $av_menu;
			else
				unset($_SESSION[$prefix.'content_category_menu']);
			include 'category-form-menu-available.php';
			?>
		</div>
	</div>
</div>