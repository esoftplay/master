<h1><?php echo $cat['title'];?></h1>
<?php
if($cat['total_page'] > 1)
{
	?>
	<span class="text text-muted pull-right text-justify"><?php echo lang('page').' '.($page+1).' '.lang('of').' '.items($cat['total_page'], 'page');?></span>
	<div class="clearfix"></div>
	<?php
}
?>
<ul class="list-unstyled">
	<?php
	$item = 0;
	foreach((array)$cat['list'] AS $data)
	{
		$item++;
		$edit_data = (content_posted_permission() && $user->id == $data['created_by']) ? 1 : 0;
		$link      = content_link($data['id'], $data['title']);
		?>
		<li class="col-md-4">
			<?php
			if(!empty($config['title']))
			{
				if(!empty($config['title_link']))
				{
					?>
					<a href="<?php echo $link;?>" title="<?php echo $data['title'];?>"><h3><?php echo $data['title'];?></h3></a>
	        <?php
	      }else{
	      	?>
	      	<h3><?php echo $data['title'];?></h3>
	        <?php
	      }
			}
			if(	!empty($config['created']) || !empty($config['author'] ))
			{
				echo (!empty($config['author'])) ? '<span class="text-muted pull-left">'.lang('author').$data['created_by_alias'].'</span>' : '';
				echo (!empty($config['created'])) ? '<span class="text-muted pull-right">'.lang('created').content_date($data['created']).'</span>' : '';
			}
			echo '<div class="row text-justify" style="padding: 0 5px;">';
			echo (!empty($config['thumbnail']) && !empty($data['image'])) ? '<center><a href="'.$link.'">'.content_src($data['image'], ' class="img-thumbnail"', true).'</a></center>' : '';
			echo nl2br(strip_tags($data['intro']));
			echo (!empty($config['read_more'])) ? ' <a href="'.$link.'" class="readmore">'.lang('Read more').'</a>' : '';
			echo '</div>';
			if( !empty($config['tag']) )
			{
				?>
				<div class="text-left">
					<?php
					$r = content_category($data['id'], $config['tag_link']);
					echo lang('Tags').implode(' ', $r);
					?>
				</div>
				<?php
			}
			if(empty($data['revised']))
			{
				$config['modified'] = 0;
			}
			if(!empty($config['rating']) || !empty($config['modified']) || !empty($edit_data))
			{
				if($config['rating'])
				{
					echo rating($data['rating']).'<br />';
				}
				if(!empty($edit_data))
				{
					echo ($config['modified']) ? '<span class="text-muted">'.lang('modified').content_date($data['modified']).'</span>' : '';
					?>
					<a href="<?php echo $Bbc->mod['circuit'].'.posted_form&id='.$data['id'];?>" title="<?php echo lang('edit content');?>"><?php echo icon('edit');?></a>
					<?php
				}	else {
					echo ($config['modified']) ? '<span class="text-muted">'.lang('modified').content_date($data['modified']).'</span>' : '';?>
					<div class="clearfix"></div>
					<?php
				}
			}
			if ($item%3==0)
			{
				echo '<div class="clearfix"></div>';
			}
			?>
		</li>
		<?php
		if ($item%3==0)
		{
			echo '<div class="clearfix"></div><br />';
		}
	}
	?>
</ul>
<div class="clearfix"></div>
<?php
echo page_list($cat['total'], $config['tot_list'], $page, 'page', $cat['link']);
if (!empty($cat['rss']))
{
	?>
	<a href="<?php echo $cat['rss'];?>" class="btn btn-warning btn-sm pull-right" title="<?php echo lang('category feed');?>"><?php echo icon('fa-rss-square');?> <?php echo lang('category feed');?></a>
	<?php
}
?>
<div class="clearfix"></div>
