<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$av_menu= @$_SESSION[$prefix.'content_category_menu'];
$active	= array('unpublished', 'published', 'new');
if(!empty($av_menu))
{
	?>
	<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">Existing Menu</h3>
	  </div>
		<div class="panel-body">
			<table class="table">
				<?php
				foreach((array)$av_menu AS $i => $dt)
				{
					$dt['link'] = ($dt['link'] == 'none') ? 'not created' : '<a href="'._URL.$dt['seo'].'.html" target="_blank">'.$dt['seo'].'</a>';
					if($dt['code'] != 'delete')
					{
						?>
						<tr>
							<th>title</th>
							<td>: <?php echo $dt['title'];?></td>
						</tr>
						<tr>
							<th>link</th>
							<td>: <?php echo $dt['link'];?></td>
						</tr>
						<tr>
							<th>status</th>
							<td>: <?php echo $active[$dt['active']];?>
								[<a href="#" onclick="return menu_delete('<?php echo $prefix;?>', <?php echo $i;?>);" title="delete menu"><?php echo icon('remove'); ?></a>]
							</td>
						</tr>
						<tr>
							<td colspan=2>&nbsp;</td>
						</tr>
						<?php
					}
				}
				?>
			</table>
		</div>
	</div>
	<?php
}