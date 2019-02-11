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
		<?php
		echo $sys->nav_show();
		echo $Bbc->content;
		if($db->debug)
		{
			?>
			<div class="clearfix"></div>
			<div class="predebug">&nbsp;</div>
			<div class="debug" data-toggle="modal" href="#debug-content" style="position: fixed"></div>
				<div class="modal fade" id="debug-content" tabindex="-1">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-body">
								<?php
								echo '<code>'.print_r($Bbc->debug, 1).'</code>';
								?>
							</div>
						</div>
					</div>
				</div>
			<?php
		}
		?>
		<div id="loading">Loading...</div>
		<script src="<?php echo $sys->template_url;?>bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="<?php echo $sys->template_url;?>js/index.js" type="text/javascript"></script>
	</body>
</html>