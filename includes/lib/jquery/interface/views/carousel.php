<div id="<?php echo $divid;?>">
<?php foreach($content AS $key => $item) {	?>
		<<?php echo $params['items'];?> href="<?php echo $item['image'];?>" title="<?php echo $item['title'];?>" rel="imagebox"><img src="<?php echo $item['thumb'];?>" width="100%" /></a>
<?php }	?>
</div>
<script type="text/javascript">
	$(document).ready(
		function() {
			$('#<?php echo $divid;?>').Carousel(<?php echo $param;?>);
			$.ImageBox.init({
					loaderSRC: '<?php echo $loaderSRC;?>',
					closeHTML: '<img src="<?php echo $closeHTML;?>" />'
			});
		});
</script>