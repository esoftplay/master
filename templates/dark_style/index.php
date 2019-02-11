<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $sys->meta();?>
</head>
<body>
	<div class="container">
		
		<div class="row top">
			<div class="prev">
				<?php echo $sys->block_show('intro');?>
			</div>
			<div class="head">
				<div class="col-md-9 col-sm-9">
					<?php echo $sys->block_show('logo');?>
				</div>
				<div class="col-md-3 col-sm-3">
					<?php echo $sys->block_show('header');?>
				</div>
			</div>
			<div class="menu_top">
				<?php echo $sys->block_show('top');?>
			</div>
		</div>
		
		<div class="row main">
			<div class="col-md-9">
				<div class="col-md-9 col-xs-12 content">
					<div class="content_main">
						<?php echo $sys->block_show('content_top');?>
						<?php echo trim($Bbc->content);?>
						<?php echo $sys->block_show('content');?>
					</div>					
					<div class="content_bottom">
						<?php echo $sys->block_show('content_bottom');?>
					</div>
				</div>
				<div class="col-md-3 menu_left">
					<?php echo $sys->block_show('left');?>
				</div>
			</div>
			
			<div class="col-md-3 col-xs-12">
				<?php echo $sys->block_show('right');?>
			</div>
		</div>
		<div class="clearfix"></div>
		
		<div class="row">
			<div class="menu_bottom">
				<?php echo $sys->block_show('bottom');?>
				<div class="clearfix"></div>
				<div class="footer">
					<?php echo config('site','footer');?>
					<?php echo $sys->block_show('footer');?>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php echo $sys->block_show('debug');?>
	<script src="<?php echo _URL;?>templates/admin/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>