<div class="list-group">
<?php
foreach($r AS $dt)
{
	$title = $config['show'] ? image($Bbc->mod['dir'].$dt['image']) : '';
	if(empty($title)) $title = $dt['title'];
	?>
	<a href="<?php echo $dt['link'];?>" title="<?php echo $dt['title'];?>" class="list-group-item" target="_blank">
		<?php echo $title;?>
	</a>
	<?php
}
?>
</div>