<?php
require('php-captcha.inc.php');
if( !empty($_GET['code']) ){
	if( !PhpCaptcha::Validate($_GET['code'], false) ){
		echo 'Error';
	} else {
		echo 'Betul';
	}
}
?>
<br />
<form method="get" action="">
<img src="captcha.php" width="150" height="40" border="1" />
<input type="text" name="code" />
</form>