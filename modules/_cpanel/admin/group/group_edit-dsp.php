<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

echo $group_edit;
if(isset($data['is_customfield']) && $data['is_customfield'] == '1')
{
	include 'group_edit-field_form.php';
}