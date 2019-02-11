<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');?>
<!DOCTYPE HTML>
<html lang="en">
	<head>
		<?php 
		echo $sys->meta();
		$sys->link_css($sys->template_url.'css/logo.css', false);
		?>
	</head>
	<body>
		<div class="container">
			<div class="row page_top">
		    <div class="widget_top">
					<div class="widget_intro">
						<?php echo $sys->block_show('intro');?>
					</div>
					<?php echo $sys->block_show('top');?>
				</div>
				<div class="widget_logo">
					<?php echo $sys->block_show('logo');?>
				</div>
				<div class="widget_header">
					<?php echo $sys->block_show('header');?>
				</div>
			</div>
		  <div class="clearfix"></div>
			<div class="row page_middle">
				<div class="col-md-9">
					<div class="widget_middle">
						<?php
						if($sys->block_show('right'))
						{
							?>
							<div class="col-md-8 widget_content">
								<div class="widget_content_top">
									<?php echo $sys->block_show('content_top');?>
								</div>
								<div class="widget_content_page">
									<?php echo trim($Bbc->content);?>
									<?php echo $sys->block_show('content');?>
								</div>
								<div class="widget_content_bottom">
									<?php echo $sys->block_show('content_bottom');?>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="col-md-4 widget_right">
								<?php echo $sys->block_show('right');?>
							</div>
							<?php
						}else{
							?>
							<div class="widget_content">
								<div class="widget_content_top">
									<?php echo $sys->block_show('content_top');?>
								</div>
								<div class="widget_content_page">
									<?php echo trim($Bbc->content);?>
									<?php echo $sys->block_show('content');?>
								</div>
								<div class="widget_content_bottom">
									<?php echo $sys->block_show('content_bottom');?>
								</div>
								<div class="clearfix"></div>
							</div>
							<?php
						}
						?>
						<div class="clearfix"></div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="widget_left">
						<?php echo $sys->block_show('left');?>
					</div>
				</div>	
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>
	    <div class="row page_bottom">
	    	<div class="widget_bottom">
					<?php echo $sys->block_show('bottom');?>
	      </div>
	      <div class="clearfix"></div>
	      <div class="col-md-12 widget_footer">
					<?php echo $sys->block_show('footer');?>
	        <div class="block_dark-2">
	          <div class="copy">
	            <p><?php echo config('site','footer');?></p>
	          </div>
	        </div>
	      </div>
	    </div>
		</div>
		<?php echo $sys->block_show('debug');?>
		<script src="<?php echo _URL;?>templates/admin/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	</body>
</html>