<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$tabs = array(
	'User List'	=> $form->roll->getForm(),
	'Add Users'	=> @$userEdit
	);
if (config('rules', 'register_auto')!='1')
{
	$tabs['Registrant'] = $form2->roll->getForm();
}
echo tabs($tabs);
if (!empty($is_push) && $is_push == 2) // ada di 'user-list.php'
{
	echo $sys->button('index.php?mod=_cpanel.user&act=fcm-activate', 'Activate mobile notification', 'fa-bell');
}