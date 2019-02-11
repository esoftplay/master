<ul class="list-unstyled">
	<?php
	foreach((array)$r_list AS $data)
	{
		?>
		<li>
			<div class="blockquote">
				<div class="col-md-2 no-left">
					<?php if($conf['avatar']) echo image($data['image'], '', ' class="img-responsive img-thumbnail"');	?>
				</div>
				<div class="col-md-10 no-left">
					<b><?php echo $data['name'];?></b>
					<span><?php echo date('d M Y | H:i:s', strtotime($data['date']));?></span>
					<p><?php echo $data['message'];?></p>
				</div>
			</div>
			<div class="clearfix"></div>
			<br>
		</li>
		<?php
	}
	?>
	<div class="clearfix"></div>
</ul>
