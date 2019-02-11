<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

if (!isset($is_admin))
{
  $is_admin = 0;
}
$q = "SELECT id, name FROM bbc_module WHERE active=1 ORDER BY name";
$r_module = $db->getAll($q);
$q = "SELECT id, name, menus FROM bbc_user_group WHERE is_admin={$is_admin} ORDER BY is_admin, score DESC";
$r_group = $db->getAll($q);

function link_parse($url)
{
  global $db;
  if(preg_match('~^#~s', $url))
  {
  	return $url;
  }else
  if(empty($url) || preg_match('~^index\.php\?mod=~', $url))
  {
    return $url;
  }
  $_URL = preg_replace('~//www\.~is', '//', _URL);
  $url  = preg_replace('~//www\.~is', '//', $url);
  $url  = preg_replace('~^'.$_URL.'~is', '', $url);

  if(preg_match('~^(?:ht|f)tps?://([^/]+)~is',$url))
  {
    return $url;// this url is pointing to another site
  }
  $output   = $url;
  $continue = 2;// di path keberapakah url untuk variable nya
  $r = explode('/', $url);
  if(file_exists(_ROOT.'modules/'.$r[0].'/_function.php'))
  {
    include_once _ROOT.'modules/'.$r[0].'/_function.php';
    $func = $r[0].'_url_parse';
    if(function_exists($func))
    {
      $out = $func($url);
      if(!empty($out))
      {
        return $out;
      }
    }
  }
  if(preg_match('~^(.*?)(?:(_|\-)([0-9]+))?\.(html?)$~is', $r[0], $m))
  {
    $output = 'index.php?mod=content.';
    switch($m[4])
    {
      case 'htm':
        switch($m[2])
        {
          case '-':
            $output .= 'list&id='.urlencode($m[3]);
          break;
          case '_':
            $output .= 'detail&id='.urlencode($m[3]);
          break;
          default:
            $output .= $m[1];
          break;
        }
        $continue = 1;
      break;
      case 'html':
        $q = "SELECT `link` FROM `bbc_menu` WHERE seo='".$m[1]."'";
        $output = $db->getOne($q);
        $continue= 0;
      break;
    }
  }else{
    $output = 'index.php?mod='.$r[0];
    if(empty($r[1]))
    {
      $output .= '.main';
      $continue = 0;
    }else
    if(preg_match('~^([^,]+),(.*?)$~is', $r[1], $m))
    {
      $output .= '.main&'.$m[1].'='.urlencode($m[2]);
    }else{
      $output .= '.'.$r[1];
    }
  }
  if($continue)
  {
    for($i=$continue; $i < count($r); $i++)
    {
      if(!empty($r[$i]))
      {
        if(preg_match('~^([^,]+),(.*?)$~is', $r[1], $m))
        {
          $output .= '&'.$m[1].'='.urlencode($m[2]);
        }else{
          $output .= '&id='.urlencode($r[$i]);
        }
      }
    }
  }
  return $output;
}
function menu_update_insert($id=0)
{
  global $db, $is_admin;
  if (empty($id))
  {
    $id = @intval($_GET['id']);
  }
  $menu = $db->getRow("SELECT * FROM bbc_menu WHERE id={$id}");
  $sql  = array();
  if (!empty($menu))
  {
    $link = '';
    if (!empty($menu['link']))
    {
      if (!preg_match('~^index\.php\?mod=~s', $menu['link']))
      {
        $link = link_parse($menu['link']);
        if ($link != $menu['link'])
        {
          $sql[] = "`link`='{$link}'";
        }
      }
    }
    if (!empty($link))
    {
      preg_match('~index\.php\?mod=([^\.]+)~is', $link, $match);
      if (!empty($match[1]))
      {
        $module_id = $db->getOne("SELECT id FROM bbc_module WHERE name='".$match[1]."'");
        if ($module_id!=$menu['module_id'])
        {
          $sql[] = '`module_id`='.intval($module_id);
        }
      }
    }
    if (empty($is_admin))
    {
      $clean = true;
      $title = !empty($_POST['add_title']) ? $_POST['add_title'][lang_id()] : $_POST['edit_title'][lang_id()];
      $seo   = menu_seo($menu['seo'], $title, $id);

      if ($seo != $menu['seo'])
      {
        $sql[] = "`seo`='{$seo}'";
      }
      if (!empty($match[1]))
      {
        if ($match[1]=='content')
        {
          if(preg_match('~mod=content\.(.*?)&id=([0-9]+)~s', $link, $m2))
          {
            switch ($m2[1])
            {
              case 'detail':
                $sql[] = '`is_content`=1';
                $sql[] = '`content_id`='.$m2[2];
                $sql[] = '`is_content_cat`=0';
                $sql[] = '`content_cat_id`=0';
                $clean = false;
                break;
              case 'list':
                $sql[] = '`is_content`=0';
                $sql[] = '`content_id`=0';
                $sql[] = '`is_content_cat`=1';
                $sql[] = '`content_cat_id`='.$m2[2];
                $clean = false;
                break;
            }
          }
        }
      }
      if ($clean)
      {
        $sql[] = '`is_content`=0';
        $sql[] = '`content_id`=0';
        $sql[] = '`is_content_cat`=0';
        $sql[] = '`content_cat_id`=0';
      }
    }
  }
  if (!empty($sql))
  {
    $db->Execute("UPDATE bbc_menu SET ".implode(', ', $sql)." WHERE id={$id}");
  }
  if (isset($_POST['add_orderby']) && $menu['orderby'] == $_POST['add_orderby'])
  {
    $q = "UPDATE bbc_menu SET orderby=(orderby+1)
      WHERE is_admin={$is_admin}
        AND orderby > ".$menu['orderby']."
        AND cat_id   = ".$menu['cat_id']."
        AND par_id   = ".$menu['par_id']."
        AND id      != {$id}";
    $db->Execute($q);
    $q = "UPDATE bbc_menu SET orderby=(orderby-1)
      WHERE is_admin={$is_admin}
        AND orderby <= ".$menu['orderby']."
        AND cat_id   = ".$menu['cat_id']."
        AND par_id   = ".$menu['par_id']."
        AND id      != {$id}";
    $db->Execute($q);
    menu_repair();
  }else{
    $db->cache_clean();
  }
}