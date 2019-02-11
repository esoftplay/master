<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$menuText = ($menu_id > 0) ? 'Sub Menu' : 'Menu';
$keyword['showTree'] = @intval($keyword['showTree']);
echo '<div class="col-md-'.($keyword['showTree']?9:12).'">';
$tabs = array();
if($menu_id > 0)
{
	$tabs['Edit Menu'] = $formMenu['edit_'];
}
$tabs[$menuText] = $form->roll->getForm();
$tabs['Add '.$menuText] = $formMenu['add_'];

/* CHECK MENU YANG PERTAMA GAK POSISI PROTECTED */
$q        = "SELECT * FROM bbc_menu AS m LEFT JOIN bbc_menu_text AS t ON (m.id=t.menu_id AND lang_id=".lang_id().") WHERE is_admin=0 AND active=1 ORDER BY cat_id, par_id, orderby";
$file     = 'lang/menu_'.lang_id().'.cfg';
if($is_admin)
{
  $r = $db->getAll($q);
}else{
  $r = $db->cache('getAll', $q, $file);
}
$Bbc->menu->all_array = array();
foreach($r AS $dt)
{
  $Bbc->menu->all_array[$dt['id']] = $dt;
}
$first_menu = $sys->menu_fetch('link', '');
if (empty($first_menu))
{
  $first_menu = $sys->menu_fetch('link', 'index.php?mod=', 'like');
}
if (!empty($first_menu['protected']))
{
  echo msg('You must create at least one normal and unprotected menu in public to avoid page looping for unkown page.<br />Please just add a menu in any position which is linked to any module!', 'danger');
}

echo tabs($tabs, 1, 'menutabs'.$menu_id);

if(empty($menu_id))
{
	if (!empty($is_admin))
	{
		echo $sys->button(_URL.'admin/index.php?mod=_cpanel.menu&act=shortcut&return='.urlencode(seo_uri()), 'Create Admin Shortcuts', 'link');
	}else{
		echo $sys->button(_URL.'admin/index.php?mod=_cpanel.menu&act=clean&return='.urlencode(seo_uri()), 'clean sorting menus!', 'wrench');
	}
}
echo '</div>';
if($keyword['showTree'])
{
	echo '<div class="col-md-3">';
	include 'menuTree.php';
	echo '</div>';
}
echo '<div class="clearfix"></div>';
?>
<div class="modal fade" id="module_task" tabindex="-1" role="dialog" aria-labelledby="module_task_title">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="module_task_title"></h4>
      </div>
      <div class="modal-body" id="module_task_body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
