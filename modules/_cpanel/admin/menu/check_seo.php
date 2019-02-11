<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

if(!empty($_POST['seo']) || !empty($_POST['title']) || isset($_POST['menu_id']))
{
	header('Content-Type: text/xml');
	$output = array('out' => menu_seo($_POST['seo'], $_POST['title'], $_POST['menu_id']), 'valid' => 1);
	output_json($output);
}
die();
