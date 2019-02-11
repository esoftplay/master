<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$r = config('mobile', 'subdomain');
$sys->stop();
$_URL      = config('mobile', 'subdomain') ? 'http://m.'.preg_replace('~^(www\.)?~','',config('site', 'url'))._URI : _URL;
$links     = $content = array();
$links[]   = $_URL;
$content[] = '<url><loc>'.$_URL.'</loc><priority>1.0</priority><mobile:mobile/></url>';

/*=====================================
 * CREATE CONTENTS
 *===================================*/
$q = 'SELECT c.`id`, c.`created`, c.`modified`, c.`revised`, t.`title` FROM `bbc_content` AS c LEFT JOIN `bbc_content_text` AS t
ON(t.`content_id`=c.`id` AND t.`lang_id`='.lang_id().') WHERE c.`publish`=1 ORDER BY `id` DESC LIMIT 0, 50';
$r = $db->cacheGetAll($q);
foreach($r AS $d)
{
	$link = content_link($d['id'], $d['title'],false,$_URL);
	if(!in_array($link, $links))
	{
		$modified	= $d['revised'] > 0 ? $d['modified'] : $d['created'];
		$links[]	= $link;
		$content[]= '<url><loc>'.htmlentities($link).'</loc><lastmod>'.date('Y-m-d', strtotime($modified)).'</lastmod><mobile:mobile/></url>';
	}
}

/*=====================================
 * CREATE CATEGORIES
 *===================================*/
$q = 'SELECT c.`id`, t.`title` FROM `bbc_content_cat` AS c LEFT JOIN `bbc_content_cat_text` AS t
ON(t.`cat_id`=c.`id` AND t.`lang_id`='.lang_id().') WHERE c.`publish`=1 ORDER BY `id` ASC';
$r = $db->cacheGetAll($q);
foreach($r AS $d)
{
	$link = content_cat_link($d['id'], $d['title'],$_URL);
	if(!in_array($link, $links))
	{
		$prior = $link == $_URL ? '1.0' : '0.6';
		$links[] = $link;
		$content[] = '<url><loc>'.htmlentities($link).'</loc><mobile:mobile/></url>';
	}
}

/*=====================================
 * CREATE YOUR OWN MENUS
 *===================================*/
$r = $sys->menu_get_all();
foreach($r AS $d)
{
	$link = content_menu_link($d['link'], $d['seo'], $_URL);
	if(!in_array($link, $links))
	{
		$prior = $link == $_URL ? '1.0' : '0.6';
		$links[] = $link;
		$content[] = '<url><loc>'.htmlentities($link).'</loc><mobile:mobile/></url>';
	}
}
function content_menu_link($link, $seo, $main_url)
{
	if(empty($link)){
		$output = $main_url;
	}else
	if(preg_match('~^(?:ht|f)tps?://~', $link)){
		$output = $link;
	}else
	if(_SEO and !empty($seo)){
		$output = $main_url.$seo.'.html';
	}else{
		$output = $main_url.$link;
	}
	return $output;
}
$sys->stop();
include tpl('mobilesitemap.html.php');