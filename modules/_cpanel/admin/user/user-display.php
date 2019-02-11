<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$tabs = array(
	'User List'	=> $form->roll->getForm(),
	'Add Users'	=> $userEdit
	);
if (config('rules', 'register_auto')!='1')
{
	$tabs['Registrant'] = $form2->roll->getForm();
}
echo tabs($tabs);
