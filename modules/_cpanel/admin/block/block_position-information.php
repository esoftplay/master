<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

function list_repair($text, $arr, $sprtor = '<br />')
{
	$out = array();
	$r = repairExplode($text);
	foreach($r AS $i) {
		$out[] = (!is_numeric($i)) ? $i : $arr[$i];
	}
	return implode($sprtor, $out);
}

// FETCH ALL BLOCKS POSITION
$q = "SELECT * FROM bbc_block_position";
$r_position = $db->getAssoc($q);
// FETCH ALL AVAILABLE BLOCKS
$q = "SELECT id, name FROM bbc_block_ref";
$r_ref = $db->getAssoc($q);
// FETCH ALL BLOCKS THEMES
$q = "SELECT id, name FROM bbc_block_theme WHERE template_id={$template_id} ORDER BY `name` ASC";
$r_theme = $db->getAssoc($q);
// FETCH ALL MENUS
$q = "SELECT id, title FROM bbc_menu AS m LEFT JOIN bbc_menu_text AS t 
		ON (m.id=t.menu_id AND lang_id=".lang_id().") WHERE is_admin=0 ORDER BY cat_id, par_id, orderby ASC";
$r_menu = $db->getAssoc($q);
$r_menu['-1'] = 'Home';
// FETCH ALL MODULES
$q = "SELECT id, name FROM bbc_module ORDER BY name";
$r_module = $db->getAssoc($q);
// FETCH ALL USER GROUPS
$q = "SELECT id, name FROM bbc_user_group WHERE is_admin=0";
$r_group = $db->getAssoc($q);
// FETCH ALL LANGUAGE
$r_lang = lang_assoc();

if (empty($r_theme))
{
	$q = "INSERT INTO `bbc_block_theme` (`template_id`, `name`, `content`, `active`) VALUES ({$template_id}, 'none', '[title][content]', 1)";
	if($db->Execute($q))
	{
		$i = $db->Insert_ID();
		$r_theme[$i] = 'none';
	}
	
}