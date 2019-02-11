<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$av_menu	= (array)@$_SESSION['type_menu_exists'];
if(!empty($_POST['title'][lang_id()]) && !empty($_POST['seo']))
{
	$av_menu[]	= array(
		'code'	=>	'new'
	,	'id'		=> '0'
	,	'par_id'=>	$_POST['par_id']
	,	'cat_id'=>	$_POST['cat_id']
	,	'title'	=>	$_POST['title'][lang_id()]
	,	'seo'		=>	$_POST['seo']
	,	'orderby'=>	$_POST['orderby']
	,	'link'	=>	'none'
	,	'active'=>	2
	,	'titles'=>	$_POST['title']
	);
	$_SESSION['type_menu_exists'] = $av_menu;
}
if(isset($_GET['del_id']))
{
	if(!empty($av_menu[$_GET['del_id']]))
	{
		if($av_menu[$_GET['del_id']]['code'] == 'new')
		{
			unset($av_menu[$_GET['del_id']]);
		}else{
			$av_menu[$_GET['del_id']]['code'] = 'delete';
		}
		$_SESSION['type_menu_exists'] = $av_menu;
	}
}
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
								[<a href="#" onclick="return menu_delete(<?php echo $i;?>);" title="delete menu"><?php echo icon('remove'); ?></a>]
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
