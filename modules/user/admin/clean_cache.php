<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

_func('path');
path_delete(_CACHE);
path_create(_CACHE);
file_write(_CACHE.'index.html', '');
$alert = 'Success to clean system cache.';
?>
<script type="text/javascript">
	var is_alert = true;
	// if (window.parent.length) {
	// 	if (window.parent.Ext) {
	// 		window.parent.Ext.MessageBox.alert('Status', "<?php echo $alert; ?>");
	// 		is_alert = false;
	// 	};
	// }
	if (is_alert) {
		alert("<?php echo $alert; ?>");
	};
</script>
<?php
die();
