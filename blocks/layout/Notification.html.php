<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->link_css(_URL.'templates/admin/bootstrap/css/alert.css', false);
?>
<div id="notif_badge" class="dropdown">
	<a href="#" class="dropdown-toggle"
		data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<?php
		echo $block->title;
		$block->title = '';
		?>
		<span class="badge pull-right" id="notif_badge_count"></span>
	</a>
	<ul class="dropdown-menu"></ul>
</div>

<script type="text/javascript">
	_Bbc(function($){
		$.getScript(_URL+"templates/admin/bootstrap/js/alert.js", function(){
			window.Alert.init();
		});
	});
</script>
<?php
/*
Example:
#1
var a = window.Alert;
a.init("notif_badge");
#2
var a = window.Alert;
a.setup = function(){
	// do someting to setup display
};
a.init();
#3
-- overwritable method before calling a.init(); see the I overwrite setup:
fail: if fetching is failed,
notify: if new alert is coming,
list: notification badge is clicked,
list_more: click "load more" in notification list,
click: notification item in the list is clicked

*/