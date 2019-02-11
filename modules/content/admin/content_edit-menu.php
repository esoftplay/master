<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

?>
<div class="panel panel-default">
	<div class="panel-heading">
		Create Menu of this Content
	</div>
	<div class="panel-body">
		<div class="form-group">
			<label>Menu Position</label>
			<select id="menu_cat_id" class="form-control">
				<?php echo createOption(@$r_position, '');?>
			</select>
		</div>
		<div class="form-group">
			<label>Parent Menu</label>
			<select id="menu_par_id" class="form-control"></select>
		</div>
		<div class="form-group">
			<label>Place Menu After</label>
			<select id="menu_orderby" class="form-control"></select>
		</div>
		<div class="form-group">
			<label>Menu Title</label>
<?php	foreach($r_lang AS $lang_id => $dt) {	?>
			<input type="text" id="menu_title_<?php echo $lang_id;?>" placeholder="<?php echo $dt['title'];?>" class="form-control menu_create" data-prefix="<?php echo $form_act; ?>" data-id="<?php echo $data['id']; ?>" />
<?php	}	?>
		</div>
		<div class="form-group">
			<label>SEO Link</label>
			<input type="text" id="menu_seo" class="form-control menu_create" data-prefix="<?php echo $form_act; ?>" data-id="<?php echo $data['id']; ?>" />
		</div>
		<input type="button" value="Create" onClick="menu_create('<?php echo $form_act;?>', <?php echo @intval($data['id']);?>);" class="btn btn-default" />
		<hr />
		<div id="menu_exists">
			<?php
			$q="SELECT id, cat_id FROM bbc_menu
					WHERE is_content=1 AND content_id=".$data['id']."  ORDER BY cat_id, par_id, orderby";
			$r = $db->getAssoc($q);
			$av_menu = array();
			foreach($r AS $id => $cat_id)
			{
				$dt = $r_menu[$cat_id][$id];
				$dt['code'] = 'old';
				$av_menu[] = $dt;
			}
			if(!empty($av_menu))
				$_SESSION[$form_act.'content_menus_exists'] = $av_menu;
			else
				unset($_SESSION[$form_act.'content_menus_exists']);
			include 'content_edit-menu-exists.php';
			?>
		</div>
	</div>
</div>