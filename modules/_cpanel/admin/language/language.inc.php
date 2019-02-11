<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$keyword['module_id'] = $sys->module_id;
$path = dirname(__FILE__).'/';
if(!empty($_GET['act']) && $_GET['act'] == 'edit')
{
  $sys->nav_add('Edit');
  $id = intval($_GET['id']);
  include $path.'language-update.php';
  echo $language_update;
}else{
  $id = 0;
  include $path.'language-search.php';
  include $path.'language-update.php';
  include $path.'language-list.php';
  echo $form1->roll->getForm().$language_update;
}
