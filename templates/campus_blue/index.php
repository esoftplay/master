<!DOCTYPE html>
<html lang="en">
	<head><?php echo $sys->meta();?>
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="widget_intro">
					<?php echo $sys->block_show('intro');?>
				</div>
				<div class="widget_header">
					<div class="col-md-6">
						<?php echo $sys->block_show('header');?>
					</div>
					<div class="col-md-6">
						<?php echo $sys->block_show('logo');?>
					</div>
				</div>
				<div class="widget_top">
					<?php echo $sys->block_show('top');?>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row content">
				<div class="col-md-9">
					<div class="col-md-8">
						<?php echo $sys->block_show('content_top');?>
						<?php echo trim($Bbc->content);?>
						<?php echo $sys->block_show('content_bottom');?>
					</div>
					<div class="col-md-4">
						<?php echo $sys->block_show('left');?>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="col-md-3">
					<?php echo $sys->block_show('right');?>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="page_bottom">
					<?php echo $sys->block_show('bottom');?>
					<div class="clearfix"></div>
					<div class="widget_footer">
						<?php echo config('site','footer');?>
					</div>
					<?php echo $sys->block_show('footer');?>
				</div>
			</div>
			<div class="clearfix"></div>
			<a id="to-top" class="dark" style="bottom: 17px;"> <i class="glyphicon glyphicon-chevron-up"></i></a>
		</div>
		<script src="<?php echo _URL;?>templates/admin/bootstrap/js/bootstrap.min.js"></script>
		<?php
		$sys->link_js($sys->template_url.'js/application.js', false);
		echo $sys->block_show('debug');
		?>
	</body>
</html>
