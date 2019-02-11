<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$r_menu = array(); // used to declare to get deletedId in recursive...
$form = _lib('pea',  $str_table = "bbc_menu" );

$form->initRoll( $add_sql.' ORDER BY `cat_id`, `orderby` ASC', 'id' );

$form->roll->setLanguage('menu_id');

$form->roll->addInput( 'title', 'sqllinks' );
$form->roll->input->title->setLinks( $mainLink );
$form->roll->input->title->setLanguage();

if (!empty($is_admin))
{
	$form->roll->addInput( 'link', 'sqlplaintext' );
}else{
	$form->roll->addInput('seo','texttip');
	$form->roll->input->seo->setTitle('Link');
	$form->roll->input->seo->setTemplate('{link}');
	if (empty($keyword['cat_id']) && empty($menu_id))
	{
		$form->roll->addInput( 'cat_id', 'selecttable' );
		$form->roll->input->cat_id->setTitle( 'Position' );
		$form->roll->input->cat_id->setReferenceTable( 'bbc_menu_cat ORDER BY orderby ASC' );
		$form->roll->input->cat_id->setReferenceField( 'name', 'id' );
		$form->roll->input->cat_id->setPlaintext( true );
	}
}

$q = "SELECT DISTINCT cat_id  FROM bbc_menu AS m LEFT JOIN bbc_menu_text AS t ON (m.id=t.menu_id AND lang_id=".lang_id().") $add_sql";
$db->Execute($q);
if($db->Affected_rows() > 1 && !$is_admin)
{
	$_t = 'sqlplaintext';
}else{
	$_t = 'orderby';
}
$form->roll->addInput( 'orderby', $_t );
$form->roll->input->orderby->setTitle( 'Order' );
if ($_t == 'sqlplaintext')
{
	$form->roll->input->orderby->addHelp("To change the order of this menu list, you need to select position on the search form above !");
}

$form->roll->addInput( 'active', 'checkbox' );
$form->roll->input->active->setTitle( 'Publish' );
$form->roll->input->active->setCaption( 'publish' );

if ($is_admin)
{
	// $form->roll->setDisableInput('delete', 'index.php?mod=_cpanel.main', '=', 'link');
	$_i = $db->getOne("SELECT `id` FROM `bbc_menu` WHERE `link`='index.php?mod=_cpanel.main' AND `is_admin`=1 ");
	$form->roll->setDisableInput('delete', $_i);
}

$form->roll->onSave('menu_repair');
$form->roll->onDelete('doDeleteMenu');
function doDeleteMenu($ids)
{
	if(count($ids > 0))
	{
		menu_delete($ids);
	}
}
