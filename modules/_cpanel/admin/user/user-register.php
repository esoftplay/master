<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

if(!empty($_GET['temp_id']) && $_GET['temp_id'] > 0)
{
	$q = "SELECT code FROM bbc_account_temp WHERE id=".$_GET['temp_id'];
	$c = $db->getOne($q);
	if($db->Affected_rows())
	{
		redirect(_URL.'user/register-validate/'.$c);
	}
}

$form2 = _lib('pea',  $str_table = "bbc_account_temp" );
$form2->initRoll( 'WHERE 1 ORDER BY id DESC', 'id' );
$form2->roll->setFormName( 'registrant' );

$form2->roll->addInput( 'email', 'sqllinks' );
$form2->roll->input->email->setTitle( 'Approved' );
$form2->roll->input->email->setGetName( 'temp_id' );
$form2->roll->input->email->setExtra( 'target="_blank"' );
$form2->roll->input->email->setLinks( $Bbc->mod['circuit'].'.user' );

$form2->roll->addInput( 'name', 'sqlplaintext' );
$form2->roll->input->name->setTitle( 'Name' );

$form2->roll->addInput( 'date', 'datetime' );
$form2->roll->input->date->setTitle( 'Date' );
$form2->roll->input->date->setPlaintext( true );

$form2->roll->addInput( 'active', 'checkbox' );
$form2->roll->input->active->setTitle( 'Active' );
$form2->roll->input->active->setCaption( 'Active' );
