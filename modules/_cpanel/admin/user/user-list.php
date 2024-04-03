<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );


$is_push = 0;
if (defined('_FCM_SENDER_ID'))
{
	$tables = $db->getCol("SHOW TABLES LIKE 'bbc_user_push_topic_list'");
	if (!in_array('bbc_user_push_topic_list', $tables))
	{
		$is_push = 2;
	}else{
		$is_push = 1;
	}
}


if(!empty($keyword))
{
	$arr = array();
	if(isset($keyword['keyword'])){
		$q = "SELECT user_id FROM bbc_account WHERE MATCH ( `username`, `name`, `email`, `params` )
					AGAINST ('\"".$keyword['keyword']."\"' IN BOOLEAN MODE) LIMIT 0, 31";
		$ids = array_merge(array(0), $db->getCol($q));
		if (count($ids) > 30)
		{
			$arr[] = "LOWER(username) LIKE '%".strtolower($keyword['keyword'])."%'";
		}else{
			$arr[] = "(LOWER(username) LIKE '%\"".strtolower($keyword['keyword'])."\"%' OR id IN (".implode(',', $ids)."))";
		}
	}
	if(isset($keyword['group_id'])){
		$arr[] = "group_ids LIKE '%,".$keyword['group_id'].",%'";
	}
	if(count($arr) > 0)	$add_sql = 'WHERE '.implode(' AND ', $arr);
	else $add_sql = '';
}else{
	$add_sql = 'WHERE 1 ORDER BY id DESC';
}
$form = _lib('pea',  'bbc_user' );
$form->initRoll( $add_sql );

$form->roll->setFormName('listuser');
$form->roll->setDeleteTool(config('rules', 'disable_user_del') ? false : true);

$form->roll->addInput('user_id','sqlplaintext');
$form->roll->input->user_id->setTitle('ID');
$form->roll->input->user_id->setFieldName('id AS user_id');
$form->roll->input->user_id->setDisplayColumn(false);

$form->roll->addInput( 'username', 'sqllinks' );
$form->roll->input->username->setTitle( 'Username' );
$form->roll->input->username->setLinks( $Bbc->mod['circuit'].'.user&act=edit' );

$form->roll->addInput('name', 'selecttable');
$form->roll->input->name->setTitle('Name');
$form->roll->input->name->setFieldName('id AS name');
$form->roll->input->name->setReferenceTable('bbc_account');
$form->roll->input->name->setReferenceField( 'name', 'user_id' );
$form->roll->input->name->setPlainText(true);

$form->roll->addInput('group_ids','multicheckbox');
$form->roll->input->group_ids->setTitle('Group');
$form->roll->input->group_ids->setReferenceTable('bbc_user_group');
$form->roll->input->group_ids->setReferenceField('name', 'id');
$form->roll->input->group_ids->setRelationTable(false);
$form->roll->input->group_ids->setPlainText(true);
$form->roll->input->group_ids->setDelimiter(', ');
$form->roll->input->group_ids->setDisplayColumn(false);

if (!empty($is_push))
{
	$form->roll->addInput( 'topics', 'sqllinks' );
	$form->roll->input->topics->setTitle( 'Topics' );
	$form->roll->input->topics->setFieldName( 'id AS topics' );
	$form->roll->input->topics->setLinks('index.php?mod=_cpanel.user&act=fcm_topic');
	$form->roll->input->topics->setHelp('list of notification topics which user subscribed to');
	$form->roll->input->topics->setModal(true);
	$form->roll->input->topics->setDisplayColumn(false);
	$form->roll->input->topics->setDisplayFunction(function($user_id) {
		global $db;
		$topics = $db->getOne("SELECT COUNT(`list_id`) FROM `bbc_user_push_topic_list` WHERE `user_id`={$user_id}");
		return money($topics, $is_shorten= false);
	});
}

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

// echo $form->roll->getForm();