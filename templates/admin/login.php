<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><?php echo $sys->meta();?></head>
<body style="background: #fff;" onload="var a = document.getElementById('login-loading');if(a){a.style.display='none';}var b = document.getElementById('login-form');if(b){b.style.display='block';document.login_form.usr.focus();}">
	<?php echo trim($Bbc->content);?>
</body>
</html>
