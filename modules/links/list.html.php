<ul class="list-group">
<?php	foreach($r AS $dt)
		{
			$title = image($Bbc->mod['dir'].$dt['image']);
			if(empty($title)) $title = $dt['title'];	?>
			<li class="list-group-item"><a href="<?php echo $dt['link'];?>" title="<?php echo $dt['title'];?>"><?php echo $title;?></a></li>
<?php	}	?>
</ul>