<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($cat['list']))
{
	?>
	<ul class="list-inline">
		<?php
		foreach($cat['list'] AS $data)
		{
			if (!empty($data['image']))
			{
				$link = content_link($data['id'], $data['title']);
				?>
				<li class="col-md-4 col-sm-4">
					<a href="<?php echo $link;?>" title="<?php echo $data['title'];?>">
					<?php echo content_src($data['image'], true, false); ?>
					</a>
				</li>
				<?php
			}
		}
		?>
	</ul>
	<?php
}