<!DOCTYPE html>
<html lang="en">
	<head>
		<?php echo $sys->meta();?>
	</head>
	<body>
		<!-- BODY CONTAINER -->
		<div class="body-container">
			<!-- CONTAINER -->
			<div class="container">
				<!-- heading -->
				<div class="row">
					<!-- widget_intro -->
					<div class="widget_intro">
						<?php echo $sys->block_show('intro');?>
					</div>
					<!-- widget_logo -->
					<div class="widget_logo">
						<?php echo $sys->block_show('logo');?>
					</div>
					<!-- widget_header -->
					<div class="widget_header">
						<?php echo $sys->block_show('header');?>
					</div>
					<!-- widget_top -->
					<div class="widget_top">
						<?php echo $sys->block_show('top');?>
					</div>
					<div class="clearfix"></div>
						<?php echo $sys->block_show('content');?>
					<!-- LOGIN -->
					<!-- <div class="col-md-12 block_default">
						<form>
							<div class="col-md-3">
								<label for="inputEmail" class="sr-only">Username</label>
		  					<input id="inputEmail" class="form-control" placeholder="Username" required="" type="username" name="usr">
		  
							</div>
							<div class="col-md-3">
								<label for="inputPassword" class="sr-only">Password</label>
		  					<input id="inputPassword" class="form-control" placeholder="Password" required="" type="password" name="pwd">
		  
							</div>
							<div class="col-md-2">
								<div class="checkbox">
							    <label>
							      <input value="1" name="rememberme" id="_user_remember" type="checkbox">Remember me    </label>
							  </div>
							</div>
							<div class="col-md-2 button">
							  <button class="btn btn-sm btn-primary btn-block" value="Login" type="submit" name="submit">Login</button>
							</div>
							<div class="col-md-2 link">
								 <label>
						    	<input type="hidden" name="url" value="http://localhost/master/">
											<a href="http://localhost/master/user/forget-password">Forget Password ?</a>
											<a href="http://localhost/master/user/register">Register</a>
									</label>
								
							</div>
						</form>
					</div>
					<div class="clearfix"></div> -->
				</div>
				<div class="clearfix"></div>
				<!-- content -->
				<div class="row middle">
					<div class="col-md-9">
						<?php echo $sys->block_show('content_top');?>
						<?php echo trim($Bbc->content);?>
						<div class="clearfix"></div>
						<?php echo $sys->block_show('content_bottom');?>
					</div>
					<div class="col-md-3">
						<!-- PANEL INFO -->
						<?php echo $sys->block_show('left');?>
					</div>

					<?php echo $sys->block_show('right');?>
				</div>
				<!-- footer -->
				<div class="row page_bottom">
					<!-- BOTTOM -->
					<div class="widget_bottom">
						<?php echo $sys->block_show('bottom');?>
					</div>
					<div class="clearfix"></div>
					<!-- FOOTER -->
					<div class="widget_footer">
						<?php echo config('site', 'footer');?>
						<?php echo $sys->block_show('footer');?>
					</div>
				</div>
			</div>
		</div>

		<!-- jQuery -->
		<script src="<?php echo _URL;?>templates/admin/bootstrap/js/bootstrap.min.js"></script>
		<!-- Bootstrap JavaScript -->
		<?php echo $sys->block_show('debug');?>
	</body>
</html>