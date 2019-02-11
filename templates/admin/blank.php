<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
	<head><?php echo $sys->meta();?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<?php echo $Bbc->content;?>
		<div id="loading">Loading...</div>
		<script src="<?php echo _URL;?>templates/admin/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="<?php echo _URL;?>templates/admin/js/index.js" type="text/javascript"></script>
	</body>
</html>