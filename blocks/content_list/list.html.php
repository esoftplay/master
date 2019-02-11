<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

echo '<ul class="list-group">';
foreach ($output['data'] as $data)
{
	?>
	<li class="list-group-item">
		<a title="<?php echo $data['title'];?>" href="<?php echo $data['href']; ?>">
			<?php echo $data['title']; ?>
		</a>
	</li>
	<?php 
}
?>
</ul>
<a href="<?php echo $output['href'];?>" title="<?php echo $output['title'];?>" class="btn btn-default btn-xs pull-right"><?php echo $output['title'];?></a>
<div class="clearfix"></div>