<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$_search = array(
	'name'	=>	$Bbc->mod['task'],
	'link'	=>	$Bbc->mod['circuit'].'.'.$Bbc->mod['task'],
	'module'=>	'_cpanel',
	'keyword'=>	'search'
);
if(isset($_POST[$_search['keyword']]))
{
	$keyword = array();
	if(!empty($_POST['group_id'])){
		$keyword['group_id']= $_POST['group_id'];
	}
	if(!empty($_POST['keyword'])){
		$keyword['keyword']	= $_POST['keyword'];
	}
	if($_POST['Submit']=='reset'){
		unset($keyword);
	}
	$_SESSION[$_search['module']][$_search['name']] = $keyword;
	redirect($_search['link']);
}else{
	$keyword = @$_SESSION[$_search['module']][$_search['name']];
}

$q = "SELECT id, name AS title, '0' AS par_id, IF(is_admin=0, 'public : ', 'admin : ') AS cat_name
			FROM bbc_user_group ORDER BY is_admin ASC, score DESC";
$r_group_array = $db->getAll($q);
?>
<form method="POST" action="" name="search" class="form-inline pull-right" role="form">
	<div class="form-group">
		<select name="group_id" class="form-control">
		<option value="">select user group</option>
		<?php echo createOption($r_group_array, $keyword['group_id']);?>
		</select>
	</div>
	<div class="form-group">
		<input name="keyword" type="text" value="<?php echo @htmlentities($keyword['keyword']);?>" class="form-control" title="keyword" placeholder="keyword">
	</div>
		<input type="hidden" name="<?php echo $_search['keyword'];?>" value="1">
	<button type="submit" name="Submit" value="search" class="btn btn-default">
		<span class="glyphicon glyphicon-search" title="rch"></span>
	</button>
	<button type="submit" name="Submit" value="reset" class="btn btn-default">
		<span class="glyphicon glyphicon-remove-circle" title="ove circle"></span>
	</button>
</form>
