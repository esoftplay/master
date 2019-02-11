<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><?php echo $sys->meta();?></head>
<body style="background: #ffffff;">
	<div class="widget_content_page">
		<?php echo trim($Bbc->content);?>
	</div>
	<script src="<?php echo _URL;?>templates/admin/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>