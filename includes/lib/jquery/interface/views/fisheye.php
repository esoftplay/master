<div id="<?php echo $divid;?>" class="fisheye">
	<div<?php echo $params['container'];?>>
<?php foreach($content AS $key => $item) {	?>
		<<?php echo $params['items'];?> href="<?php echo $item['link'];?>" class="fisheyeItem"><img src="<?php echo $item['thumb'];?>" width="30" /><<?php echo $params['itemsText'];?>><?php echo $item['title'];?></<?php echo $params['itemsText'];?>></<?php echo $params['items'];?>>
<?php }	?>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(
		function() {
			$('#<?php echo $divid;?>').Fisheye(<?php echo $param;?>);
		});
</script>