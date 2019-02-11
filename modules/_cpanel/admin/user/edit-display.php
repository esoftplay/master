<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$tabs = array(
	'Edit User'		=> $userEdit,
	'Edit Contact'=> $form->show()
);
echo implode('', $tabs);
