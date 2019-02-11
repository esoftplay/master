<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$category_id = @intval($_GET['id']);
$type_id = @intval($_GET['type_id']);

/*=====================================================
 * INCLUDE ALL JAVASCRIPT...
 *====================================================*/
$sys->link_js('category.js');
$form_config = _class('bbcconfig');

/*=====================================================
 * FETCH ALL LANG...
 *====================================================*/
$r_lang = lang_assoc();

/*=====================================================
 * FETCH ALL MENU IN JAVASCRIPT...
 *====================================================*/
include 'menu_fetch.php';

/*=====================================================
 * FETCH DEFAULT CONFIG...
 *====================================================*/
switch($Bbc->mod['task'])
{
	case 'category_sub':
		$base_link = $Bbc->mod['circuit'].'.'.$type_id.'_category_sub';
		$sub_content = true;
		$q_list = ($category_id) ? '' : 'AND type_id='.$type_id;
	break;
	default:
		$base_link = $Bbc->mod['circuit'].'.category';
		$sub_content = false;
		$q_list = '';
	break;
}
$def_config = content_type($type_id, 'list');
link_js(_URL.'includes/lib/pea/includes/FormFile.js');
$tab_category = array();
if($category_id > 0)
{
	$prefix = 'edit_';
	include 'category-form.php';
	$tab_category[$category_form_title] = $category_form;
}

$prefix = 'add_';
include 'category-form.php';
include 'category-list.php';

$tab_category[$category_list_title] = $category_list;
$tab_category[$category_form_title] = $category_form;

$is_checked = @$_SESSION['content_category_showtree'] ? true : false;
$col        = $is_checked ? 9 : 12;
?>
<div class="col-md-<?php echo $col ?>">
	<div class="panel-heading">
		<h3 class="panel-title">
			<label><input type="checkbox" id="show_tree"<?php echo is_checked($is_checked); ?> /> Show Category Tree</label>
		</h3>
	</div>
	<?php echo tabs($tab_category); ?>
	<script type="text/javascript">
		var all_menus = <?php echo $all_menus;?>;
		var lang_ids	=	[<?php echo implode(',', array_keys($r_lang));?>];
		var lang_id		= <?php echo lang_id();?>;
		var menu_delimiter="<?php echo menu_delimiter(); ?>";
	</script>
</div>
<?php
if ($is_checked)
{
	?>
	<div class="col-md-3">
		<?php
		include 'category-showtree.php';
		?>
	</div>
	<?php
}