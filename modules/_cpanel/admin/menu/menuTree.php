<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

_func('tree');
$add = (!empty($keyword['cat_id']) && $is_admin==0) ? ' AND cat_id='.$keyword['cat_id'] : '';
$q = "SELECT *, link AS description FROM bbc_menu AS m LEFT JOIN bbc_menu_text AS t ON (m.id=t.menu_id AND lang_id=".lang_id().") WHERE is_admin=$is_admin $add ORDER BY cat_id, par_id, orderby";
$r = $db->getAll($q);
$r_menu = array();
foreach($r as $i => $row)
{
	$row['title'] = ($menu_id==$row['id']) ? '<font class="blocki">'.addslashes($row['title']).'</font>' : addslashes($row['title']);
	$row['link'] = 'JavaScript:placeCat('.$row['id'].')';
	$r_menu[] = $row;
}
$text = $is_admin ? 'Admin' : 'Public';
$title = array($text.' Menu', $mainLink);
$param = array(
	'useIcons' => false
);
echo tree_list($r_menu, $title, $param);
?>
<script type="text/javascript">
	function placeCat(menu_id)
	{
		document.location.href='<?php echo $mainLink;?>&id='+menu_id+'&return='+escape(document.location.href);
	}
</script>
