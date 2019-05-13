<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
	<head><?php echo $sys->meta();?></head>
	<body scroll="no">
		<div id="x-desktop">
			<div id="x-menu"></div>
			<div id="x-content">
				<?php echo trim($Bbc->content);?>
				<div id="x-toggle" ext:qtip="toggle left panel menu">
					<span id="x-toggle-tool" class="x-tool x-tool-toggle x-tool-collapse-west" ext:qtip="toggle menu (F3)"></span>
				</div>
			</div>
			<div class="x-clear"></div>
		</div>
		<div id="ux-taskbar">
			<div id="ux-taskbar-start">
			</div>
			<div id="ux-taskbuttons-panel"></div>
			<div class="x-clear"></div>
		</div>
	</body>
</html>