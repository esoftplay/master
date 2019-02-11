<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->stop();
?>
<html lang="en">
	<head><?php echo $sys->meta();?></head>
	<body style="background: #ffffff;text-align: left;">
		<div>
			<?php echo msg(@$_SESSION['detail_mail'], 'success');?></div>
	</body>
</html>
