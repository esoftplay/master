<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
	<head><?php echo $sys->meta();?></head>
	<body scroll="no">
		<div id="x-desktop"><?php echo trim($Bbc->content);?></div>
		<div id="ux-taskbar">
			<div id="ux-taskbar-start">
			</div>
			<div id="ux-taskbuttons-panel"></div>
			<div class="x-clear"></div>
		</div>
	</body>
</html>