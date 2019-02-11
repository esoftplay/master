<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<?php echo $sys->meta();?>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="widget_intro">
					<?php echo $sys->block_show('intro');?>
					<div class="clearfix"></div>
				</div>
				<div class="widget_header">
					<?php echo $sys->block_show('logo');?>
					<div class="clearfix"></div>
					<?php echo $sys->block_show('header');?>
				</div>
	 			<div class="widget_top">
					<?php echo $sys->block_show('top');?>
					<div class="clearfix"></div>
				</div>
			</div>
			<div class="row contentpage">
				<div class="col-md-9">
					<div class="col-md-9">
						<div class="main_content">
							<?php echo $sys->block_show('content_top');?>
							<?php echo trim($Bbc->content);?>
							<div class="clearfix"></div>
						</div>
							<?php echo $sys->block_show('content_bottom');?>
					</div>
					<div class="col-md-3">
							<?php echo $sys->block_show('left');?>
					</div>
				</div>
				<div class="col-md-3">
					<?php echo $sys->block_show('right');?>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>
			<div class="page_bottom">
				<?php echo $sys->block_show('bottom');?>
				<div class="widget_footer">
					<?php echo config('site','footer');?>
					<?php echo $sys->block_show('footer');?>
				</div>
			</div>
		</div>
		<?php echo $sys->block_show('debug');?>
		<script src="<?php echo _URL;?>templates/admin/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	</body>
</html>