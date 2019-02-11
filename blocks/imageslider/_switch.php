<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// Menampilkan data image slider dari module "imageslider" berdasarkan category yang anda pilih
$output              = array();
$config['cat_id']    = @intval($config['cat_id']);
$config['fixsize']   = @intval($config['fixsize']);
$config['caption']   = @intval($config['caption']);
$config['indicator'] = @intval($config['indicator']);
$config['control']   = @intval($config['control']);
$output['config']    = $config;
$output['images']    = array();

$q = "SELECT * FROM imageslider AS i LEFT JOIN imageslider_text AS t ON(t.imageslider_id=i.id AND t.lang_id=".lang_id().") WHERE i.cat_id =".$config['cat_id']." AND i.publish=1 ORDER BY i.orderby ASC";
$r = $db->getAll($q);
if(!empty($r))
{
  $output['id']  = 'imageslider'.$block->id;
  $output['cat'] = $db->cacheGetRow("SELECT * FROM imageslider_cat WHERE id=".$config['cat_id']);
  $dir           = _ROOT.'images/modules/imageslider/';
  $url           = str_replace(_ROOT, _URL, $dir);
  foreach($r AS $img)
  {
    if(is_file($dir.$img['image']))
    {
      $output['images'][] = array(
        'link'        => !empty($img['link']) ? site_url($img['link']) : '',
        'title'       => $img['title'],
        'description' => @$img['description'],
        'image'       => $url.$img['image']
        );
    }
  }
}
include tpl(@$config['template'].'.html.php', 'default.html.php');