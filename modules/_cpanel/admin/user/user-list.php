<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

if(!empty($keyword))
{
	$arr = array();
	if(isset($keyword['keyword'])){
		$q = "SELECT user_id FROM bbc_account WHERE MATCH ( `username`, `name`, `email`, `params` )
					AGAINST ('".$keyword['keyword']."' IN BOOLEAN MODE)";
		$ids = array_merge(array(0), $db->getCol($q));
		$arr[] = "(LOWER(username) LIKE '%".strtolower($keyword['keyword'])."%' OR id IN (".implode(',', $ids)."))";
	}
	if(isset($keyword['group_id'])){
		$arr[] = "group_ids LIKE '%,".$keyword['group_id'].",%'";
	}
	if(count($arr) > 0)	$add_sql = "WHERE ".implode(" AND ", $arr);
	else $add_sql = '';
}else{
	$add_sql = '';
}
$form = _lib('pea',  $str_table = "bbc_user" );
$form->initRoll( $add_sql, 'id' );

$form->roll->addInput( 'username', 'sqllinks' );
$form->roll->input->username->setTitle( 'Username' );
$form->roll->input->username->setLinks( $Bbc->mod['circuit'].'.user&act=edit' );

$form->roll->addInput('group_ids','multicheckbox');
$form->roll->input->group_ids->setTitle('Group');
$form->roll->input->group_ids->setReferenceTable('bbc_user_group');
$form->roll->input->group_ids->setReferenceField('name', 'id');
$form->roll->input->group_ids->setRelationTable(false);
$form->roll->input->group_ids->setPlainText(true);
$form->roll->input->group_ids->setDelimiter(', ');

$form->roll->addInput( 'login_time', 'texttip' );
$form->roll->input->login_time->setTitle( 'Info' );
$form->roll->input->login_time->setcaption( 'More Information' );
$form->roll->input->login_time->setTemplate(table(array(
	'Last Login' => '{last_login}',
	'Last IP' => '{last_ip}',
	'Created On' => '{created}',
	)));
$form->roll->input->login_time->setNumberFormat();
$form->roll->input->last_login->setDateFormat();
$form->roll->input->created->setDateFormat();

$form->roll->addInput( 'links1', 'editlinks' );
$form->roll->input->links1->setIcon( 'login' );
$form->roll->input->links1->setTitle( 'Login' );
$form->roll->input->links1->setFieldName( 'id' );
$form->roll->input->links1->setExtra( 'target="_blank"' );
$form->roll->input->links1->setLinks( 'index.php?mod=_cpanel.user&act=force2Login');

$form->roll->addInput( 'active', 'checkbox' );
$form->roll->input->active->setTitle( 'Active' );
$form->roll->input->active->setCaption( 'Active' );

$form->roll->setDisableInput('delete', 1);
$form->roll->onDelete('user_delete', $form->roll->getDeletedId(), $LoadLast = false );
