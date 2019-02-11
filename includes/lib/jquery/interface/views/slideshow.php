<div id="<?php echo $divid;?>" class="slideShow"></div>
<script type="text/javascript">
	$(document).ready(
		function() {
			$.slideshow(<?php echo $param;?>);
			$('a').ToolTip({className: 'inputsTooltip',position: 'mouse'});
		});
</script>