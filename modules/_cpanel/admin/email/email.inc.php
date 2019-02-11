<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$keyword['module_id'] = $sys->module_id;
if(!empty($_GET['act']))
{
  $sys->nav_add('Edit');
  include dirname(__FILE__).'/email_form.php';
  echo $form1->edit->getForm();
}else{
  include dirname(__FILE__).'/emailSearch.php';
  include dirname(__FILE__).'/email_list.php';
  echo $form->roll->getForm();
}
