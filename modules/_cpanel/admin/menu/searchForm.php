<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

if(isset($_GET['showTree']))
{
	if($_GET['showTree']=='true')	$_SESSION['showTree']='1';
	else														unset($_SESSION['showTree']);
	redirect();
}
$arr = array(
	array('PUBLIC', $Bbc->mod['circuit'].'.menu'),
	array('ADMIN', $Bbc->mod['circuit'].'.menu&is_admin=1'),
	array('<input type="checkbox" name="showTree" value="" id="showTree"'.is_checked(@$_SESSION['showTree'])
		. ' onClick="document.location.href=\''.$mainLink.'&showTree=\'+this.checked;return false;"> '
		. '<label for="showTree" style="font-weight: normal;margin:0;color: #333;">Show Menu Tree</label>', '#')
);
$def = $arr[$is_admin][1];
$tab_link = tab_link($arr, $def);

$form = _lib('pea','bbc_menu');
$form->initSearch();
if(empty($is_admin))
{
	$form->search->addInput('cat_id','selecttable');
	$form->search->input->cat_id->addOption('Select Position', '');
	$form->search->input->cat_id->setReferenceTable('bbc_menu_cat ORDER BY orderby ASC');
	$form->search->input->cat_id->setReferenceField('name', 'id');
}
$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('seo,link', true);

$form->search->addExtraField('is_admin', $is_admin);
$form->search->addExtraField('par_id', $menu_id);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();

if (!empty($_SESSION['showTree']))
{
	$keyword['showTree'] = '1';
}

echo $tab_link;
echo $form->search->getForm();