<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$id = @intval($_GET['id']);
$block_ref_id = @intval($_GET['ref_id']);
include 'block_position_edit.php';
$sys->stop(false);
$sys->nav_add('Edit Block on "'.$_CONFIG['template'].'"');
?>
<script type="text/javascript">
	_Bbc(function($){
		var a = $("center", $(".panel-heading")).html();
		$(".panel-heading:first").html(a);
		$(".tmppopover").popover();
		$(".tmppopover").removeClass("tmppopover");
	});
</script>