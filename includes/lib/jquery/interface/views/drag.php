<div id="<?php echo $divid;?>"><?php echo $content;?></div>
<script type="text/javascript">
$(document).ready(
	function(){$('#<?php echo $divid;?>').Draggable(<?php echo $param;?>);
	}
);
</script>