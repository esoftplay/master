<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
	<head><?php echo $sys->meta();?></head>
	<body style="background: #fff;" onload="var a = document.getElementById('login-loading');if(a){a.style.display='none';}var b = document.getElementById('login-form');if(b){b.style.display='block';document.login_form.usr.focus();}">
		<?php echo trim($Bbc->content);?>
	</body>
</html>
