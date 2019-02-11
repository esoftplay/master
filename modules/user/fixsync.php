<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id    = @intval($_GET['id']);
$limit = 100;
$start = $id*$limit;
$async = $db->getCol("SELECT `id` FROM `bbc_async` WHERE 1 ORDER BY `id` ASC LIMIT {$start},{$limit}");
// $async = $db->getCol("SELECT `id` FROM `bbc_async` WHERE 1 ORDER BY `id` ASC");
if (!empty($async))
{
	foreach ($async as $i)
	{
		_class('async')->fix($i);
	}
	pr($async);
	$id++;
	?>
	<script type="text/javascript">
		setTimeout(function() {
			document.location.href='<?php echo _URL; ?>user/fixsync/<?php echo $id; ?>';
		}, 1000);
		// document.location.href=_URL+'user/fixsync/<?php echo $id; ?>';
	</script>
	<?php
}else{
	echo msg('No data found', 'warning');
}
$sys->set_layout('blank');