<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($data['images']))
{
	$images = @json_decode($data['images'], 1);
	if (!empty($images) && is_array($images))
	{
		$path     = _class('content')->path.$data['id'].'/';
		$indicate = '';
		$inner    = '';
		foreach ($images as $i => $d)
		{
			if (is_file(_ROOT.$path.$d['image']))
			{
				$isActive = '';
				if (empty($hasActive))
				{
					$isActive  = 'active';
					$hasActive = 1;
				}
				$indicate .= '<li data-target="#content-gallery" data-slide-to="'.$i.'" class="'.$isActive.'"></li>';
				$inner .= '<div class="item '.$isActive.'">';
				$inner .= '<center><img src="'._URL.$path.$d['image'].'" alt="'.$d['title'].'" class="img-thumbnail img-responsive"></center>';
				if (!empty($d['title']) || !empty($d['description']))
				{
					$inner .= '<div class="carousel-caption">';
					if (!empty($d['title']))
					{
						$inner .= '<h3>'.$d['title'].'</h3>';
					}
					if (!empty($d['description']))
					{
						$inner .= '<p>'.$d['description'].'</p>';
					}
					$inner .= '</div>';
				}

				$inner .= '</div>';
			}
		}
		if (!empty($inner))
		{
			?>
			<div id="content-gallery" class="carousel slide" data-ride="carousel">
			  <ol class="carousel-indicators"><?php echo $indicate; ?></ol>
			  <div class="carousel-inner" role="listbox"><?php echo $inner; ?></div>
			  <?php
			  if (count($images) > 1)
			  {
			  	?>
				  <a class="left carousel-control" href="#content-gallery" role="button" data-slide="prev">
				    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
				    <span class="sr-only"><?php echo lang('Previous'); ?></span>
				  </a>
				  <a class="right carousel-control" href="#content-gallery" role="button" data-slide="next">
				    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
				    <span class="sr-only"><?php echo lang('Next'); ?></span>
				  </a>
			  	<?php
			  }
			  ?>
			</div>
			<?php
		}
	}
}