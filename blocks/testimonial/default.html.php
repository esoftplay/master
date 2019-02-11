<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($output['data']) && is_array($output['data']))
{
	foreach ($output['data'] as $data )
	{
		echo '<div class="media">';
		if(!empty($output['config']['avatar']) && preg_match('~ src="(.*?)"~', $sys->avatar($data['email']), $match))
		{
			?>
			<div class="media-left">
				<img class="media-object" src="<?php echo $match[1]; ?>" />
			</div>
			<?php
		}
		?>
		<div class="media-body">
			<h4 class="media-heading"><?php echo $data['name'];?></h4>
			<small class="text text-muted"><?php echo date('d M Y | H:i:s', strtotime($data['date']));?></small><br />
			<?php echo $data['message'];?>
		</div>
		<?php
		echo '</div>';
	}
}
if ($output['config']['readmore'])
{
	?>
	<div class="text text-muted text-right"><a href="index.php?mod=testimonial"><?php echo lang('Read more') ?> </a></div><br />
	<div class="clearfix"></div>
	<?php
}