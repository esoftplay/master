<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$tabs = array(
	'List Group'=> $form->roll->getForm(),
	'Add Group'	=> $group_edit
);
echo tabs($tabs);
