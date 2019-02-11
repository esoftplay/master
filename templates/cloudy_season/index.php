<!DOCTYPE html>
<html lang="en">
	<head>
		<?php echo $sys->meta();?>
	</head>
	<body>

		<!-- batas awal container -->
		<div class="container">

			<!-- batas awal row atas -->
			<div class="row page_top">

				<!-- batas awal widget_intro -->
				<div class="widget_intro">
					<?php echo $sys->block_show('intro');?>
				</div>
				<!-- batas akhir widget_intro -->

				<!-- batas awal widget_header -->
				<div class="widget_header">

					<!-- batas awal widget_logo -->
					<div class="widget_logo">
						<div class="col-md-12">
							<?php echo $sys->block_show('logo');?>
						</div>
					</div>
					<!-- batas akhir widget_logp -->

					<!-- batas awal widget_search -->
					<div class="widget_search">
						<div class="col-md-12 no-both">
							<!--search-->
							<?php echo $sys->block_show('header');?>
						</div>
					</div>
					<!-- batas akhir widget_search -->

					<!-- batas awal widget_navigasi -->
					<div class="widget_navigasi">
						<div class="col-md-12">
							<div class="block_menu_top">
							  <nav class="navbar navbar-default" role="navigation">
							    <div class="navbar-header">
							      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-menu-top">
							        <span class="icon-bar"></span>
							        <span class="icon-bar"></span>
							        <span class="icon-bar"></span>
							      </button>
							    </div>
							    <div class="collapse navbar-collapse navbar-menu-top text-left">
							      <?php echo $sys->block_show('top');?>
							    </div>
							  </nav>
							</div>
						</div>
					</div>
					<!-- batas akhir widget_navigasi -->

				</div>
				<!-- batas akhir widget_header -->

			</div>
			<!-- batas akhir row atas -->

			<!-- batas awal row tengah -->
			<div class="row page_middle">


				<!-- batas awal col-md-9 -->
				<div class="col-md-9">

					<!-- batas awal widget_content -->
					<!-- <div class="widget_content"> -->

						<!-- batas awal content -->
						<div class="col-md-9">
							<!-- tengah -->
							<div class="content_page">
							<?php echo $sys->block_show('content_top');?>
								<?php echo trim($Bbc->content);?>
							</div>
							<div class="clearfix"></div>
							<!-- panel -->
							<?php echo $sys->block_show('content_bottom');?>
							<div class="clearfix"></div>
						</div>
						<!-- batas akhir content -->

					<!-- </div> -->
					<!-- batas akhir widget_content -->

					<!-- batas awal widget_panel_right -->
					<!-- <div class="widget_panel_right"> -->

						<!-- batas awal panel_right -->
						<div class="col-md-3">
				<!-- kanan -->
				<!-- panel panel -->


					<!-- panel-panel -->
					<?php echo $sys->block_show('right');?>

				</div>
				<!-- batas akhir content_kiri -->

			</div>
			<!-- batas akhir row tengah -->

				<!-- batas awal content_kanan -->
				<div class="col-md-3">
				<div class="widget_left">

					<!-- batas awal widget_left_panel -->
					<?php echo $sys->block_show('left');?>
					<!-- batas akhir widget_left_panel -->

				</div>
			</div>
				<!-- batas akhir widget_left -->

			<div class="clearfix"></div>
		</div>
			<!-- batas awal row bawah -->
			<div class="row page_bottom">

			<!-- batas awal page_bottom -->
				<div class="page_bottom">

					<!-- batas awal widget_bottom -->
					<div class="widget_bottom">

						<?php echo $sys->block_show('bottom');?>

					</div>
					<!-- batas akhir widget_bottom -->

					<div class="clearfix"></div>

					<!-- batas awal widget_footer -->
					<div class="widget_footer">
						<?php echo config('site','footer');?>
						<?php echo $sys->block_show('footer');?>
					</div>
					<!-- batas akhir widget_footer -->

				</div>
				<!-- batas akhir page_bottom -->

			</div>
			<!-- batas akhir row bawah -->
			<div class="clearfix"></div>
		</div>
	</div>
		<!-- batas akhir container -->
		<!-- jQuery -->
<!--
		<script src="//localhost/master/templates/admin/bootstrap/js/bootstrap.min.js"></script>
		 -->
		<script src="<?php echo _URL; ?>templates/admin/bootstrap/js/bootstrap.min.js"></script>
		<!-- Bootstrap JavaScript -->

			<?php echo $sys->block_show('debug');?>
	</body>
</html>