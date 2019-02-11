<div>
<?php foreach($content AS $key => $item) {	?>
		<a href="<?php echo $item['image'];?>" title="<?php echo $item['title'];?>" rel="imagebox"><img src="<?php echo $item['thumb'];?>" /></a>
<?php }	?>
</div>
<script type="text/javascript">
	$(document).ready(
		function() {
			$.ImageBox.init({
					loaderSRC: '<?php echo $loaderSRC;?>',
					closeHTML: '<img src="<?php echo $closeHTML;?>" />'
			});
		});
</script>