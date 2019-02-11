<ul id="<?php echo $htmlid;?>">
<?php foreach($content AS $key => $item) {	?>
		<li class="sortableitem"><a href="#<?php echo $key;?>" class="tabs"><?php echo $key;?></a></li>
<?php }	?>
</ul>
<?php foreach($content AS $key => $item) {	?>
	<div id="<?php echo $key;?>"><?php echo $item;?></div>
<?php }	?>
<script type="text/javascript">
	$(document).ready(
		function() {
			$('ul#<?php echo $htmlid;?>').Sortable(<?php echo $param;?>);
		});
</script>