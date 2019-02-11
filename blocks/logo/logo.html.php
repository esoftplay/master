<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

if(!empty($config['is_link']))
{
?>
	<a href="<?php echo $output['link'];?>" title="<?php echo $output['title'];?>"<?php echo $output['attribute']; ?>>
		<?php echo image($output['image'], $output['size'], 'alt="'.$output['title'].'" title="'.$output['title'].'"');?>
	</a>
<?php
}else{
	echo image($output['image'], $output['size'], 'alt="'.$output['title'].'" title="'.$output['title'].'"'.$output['attribute']);
}