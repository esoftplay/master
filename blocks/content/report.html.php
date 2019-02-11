<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

foreach((array)$cat['list'] AS $data)
{
	$link = content_link($data['id'], $data['title']);
	$img  = !empty($data['image']) ? content_src($data['image'], ' class="img-with-animation" data-animation="fade-in" title="'.$data['title'].'"', true) : '';
	?>
	<div class="col-md-6 col-sm-6 col-xs-6">
		<center>
			<a href="<?php echo $link; ?>" target="_self" class="center">
				<?php echo $img; ?>
			</a>
			<span><?php echo $data['title']; ?></span>
		</center>
		<br>
	</div>
	<?php
}