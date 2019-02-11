<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

include 'menu_fetch.php';
if (count($r_lang) == 1) {
	$r_lang[lang_id()]['title'] = 'Menu Title';
}
?>
<script type="text/javascript">
	var all_menus = <?php echo $all_menus;?>;
	var lang_ids	=	[<?php echo implode(',', array_keys($r_lang));?>];
	var lang_id		= <?php echo lang_id();?>;
</script>
<div class="panel panel-default">
	<div class="panel-heading">
		Create Public Menu of this Content Type
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
			<div class="help-block">new menu will be placed after selection</div>
		</div>
		<div class="form-group">
			<label>Menu Title</label>
<?php	foreach($r_lang AS $lang_id => $dt) {	?>
			<input type="text" id="menu_title_<?php echo $lang_id;?>" placeholder="<?php echo $dt['title'];?>" class="form-control menu_create" />
<?php	}	?>
		</div>
		<div class="form-group">
			<label>SEO Link</label>
			<input type="text" id="menu_seo" class="form-control menu_create" />
		</div>
		<input type="button" value="Create" onClick="menu_create('', <?php echo $data['id'];?>);" class="btn btn-default" />
		<div id="menu_exists">
			<?php
			$q	= "SELECT id, cat_id FROM bbc_menu
						WHERE `link`='index.php?mod=content.type&id=".$id."'
						AND is_admin=0 ORDER BY cat_id, par_id, orderby ASC";
			$r	= $db->getAssoc($q);
			$av_menu = array();
			foreach($r AS $i => $j)
			{
				$menu					= $r_menu[$j][$i];
				$menu['code'] = 'old';
				$av_menu[]		= $menu;
			}
			if(!empty($av_menu))
			{
				$_SESSION['type_menu_exists'] = $av_menu;
			}else{
				unset($_SESSION['type_menu_exists']);
			}
			include 'type_edit-menu-available.php';
			?>
		</div>
	</div>
</div>