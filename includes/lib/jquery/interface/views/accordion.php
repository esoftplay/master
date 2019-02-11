<dl id="<?php echo $divid;?>">
<?php foreach($content AS $key => $item) {	?>
		<<?php echo $params['headerSelector'];?> class="someClass"><?php echo $item['title'];?></<?php echo $params['headerSelector'];?>>
		<<?php echo $params['panelSelector'];?>><p><?php echo $item['content'];?></p></<?php echo $params['panelSelector'];?>>
<?php }	?>
</dl>
<script type="text/javascript">
	$(document).ready(
		function() {
			$('#<?php echo $divid;?>').Accordion(<?php echo $param;?>);
		});
</script>