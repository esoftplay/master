<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

// UPDATE ORDERBY OF BLOCK
if ( ! function_exists('get_template_id'))
{
	function get_template_id($template)
	{
		include_once _SYS.'layout.blocks.php';
		$b = new blockSystem();
		return $b->get_template_id($template);
	}
}
global $_CONFIG;
$template_id = empty($template_id) ? get_template_id($_CONFIG['template']) : $template_id;
$q = "SELECT id, orderby, position_id FROM bbc_block WHERE template_id=$template_id ORDER BY position_id, orderby ASC ";
$r_block = $db->getAll($q);
$position_id = 0;
foreach($r_block AS $block)
{
	if($position_id != $block['position_id'])
	{
		$i = 1;
	}else{
		$i++;
	}
	if($block['orderby'] != $i)
	{
		$q = "UPDATE bbc_block SET `orderby`=$i WHERE id=".$block['id'];
		$db->Execute($q);
	}
	$position_id = $block['position_id'];
}
