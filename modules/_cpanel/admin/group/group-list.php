<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$form = _lib('pea',  $str_table = 'bbc_user_group' );
$form->initRoll( 'WHERE 1 ORDER BY is_admin ASC, score DESC, name ASC', 'id' );

$form->roll->setSaveTool(false);

$form->roll->addInput( 'name', 'sqllinks' );
$form->roll->input->name->setTitle( 'Group' );
$form->roll->input->name->setLinks( $Bbc->mod['circuit'].'.group&act=edit' );

$form->roll->addInput( 'desc', 'text' );
$form->roll->input->desc->setTitle( 'Description' );
$form->roll->input->desc->setPlaintext( true );

$form->roll->addInput( 'score', 'sqlplaintext' );
$form->roll->input->score->setTitle( 'Score' );

$form->roll->addInput( 'is_customfield', 'select' );
$form->roll->input->is_customfield->setTitle( 'Fields' );
$form->roll->input->is_customfield->addOption( 'Default', '0' );
$form->roll->input->is_customfield->addOption( 'Custom', '1' );
$form->roll->input->is_customfield->setPlaintext( true );

$form->roll->addInput( 'is_admin', 'select' );
$form->roll->input->is_admin->setTitle( 'Access' );
$form->roll->input->is_admin->addOption( 'public', '0' );
$form->roll->input->is_admin->addOption( 'admin', '1' );
$form->roll->input->is_admin->setPlaintext( true );

$form->roll->setDisableInput('delete', 1);
$form->roll->onDelete('group_delete');
function group_delete($ids)
{
	global $db;
	$arr = is_array($ids) ? $ids : array();
	if(!empty($arr))
	{
		foreach($arr AS $id)
		{
			$q = "UPDATE bbc_user SET group_ids=REPLACE(group_ids, ',$id,', ',') WHERE group_ids LIKE '%,$id,%'";
			$db->Execute($q);
		}
		$q = "DELETE FROM bbc_user_field WHERE group_id IN (".implode(',', $arr).")";
		$db->Execute($q);
		$q = "UPDATE bbc_user SET group_ids='' WHERE group_ids=','";
		$db->Execute($q);
	}
}
