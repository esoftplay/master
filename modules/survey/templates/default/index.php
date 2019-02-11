<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><?php echo $sys->meta();?></head>
<body>
<div class="mainPage">
	<div class="header">
		<div class="headerCell">
			<a href="<?php echo _URL;?>" class="siteLogo"></a>
			<h1><?php echo config('site', 'title');?></h1>
		</div>
	</div>
	<div class="wrapper">
		<div class="content"><?php echo trim($Bbc->content);?></div>
	</div>
	<div class="footer">
		<div class="footerCell">
			<?php echo config('site','footer');?>
		</div>
		<p>&nbsp;</p>
	</div>
</div>
</body>
</html>
