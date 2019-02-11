<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

_func('content');
_func('smiley');
foreach ($$r_list as $data)
{
	$data['content'] = str_replace(array("\\'", '\\"'), array("'",'"'), $data['content']);
	if (empty($data['image']))
	{
		$data['image'] = $sys->avatar($data['email'], 1);
	}
	?>
	<div class="media" id="comment_media_<?php echo $data['id']; ?>">
	  <div class="media-left">
	  	<img src="<?php echo $data['image']; ?>" alt="<?php echo $data['name']; ?>" title="<?php echo $data['name']; ?>" class="media-object" style="width: 60px;" />
	  </div>
	  <div class="media-body">
			<?php
			$edit = '';
			if (!empty($config['admin']) && !empty($config['form']))
			{
				if ($data['publish'])
				{
					$class = 'ok';
					$title = lang('Click to unpublish');
				}else{
					$class = 'remove';
					$title = lang('Click to publish');
				}
				$edit .= ' <a href="#status" onclick="return comment_publish(this);" title="'.$title.'" data-status="'.$data['publish'].'">'.icon($class, $title).'</a>';
				$edit .= ' <a href="#delete" onclick="return comment_delete(this);" title="'.lang('Delete this comment').'">'.icon('trash', lang('Delete this comment')).'</a>';
			}
			$name = $data['name'].'<small class="pull-right text-muted"><small>'.content_date($data['date']).$edit.'</small></small>';
			if (!empty($data['website']))
			{
				if (!preg_match('/^(?:ht|f)tps?:\/\//is', $data['website']))
				{
					$data['website'] = 'http://'.$data['website'];
				}
				?>
				<a href="<?php echo $data['website']; ?>" target="_blank"><h4 class="media-heading"><?php echo $name; ?></h4></a>
				<?php
			}else{
				?>
				<h4 class="media-heading"><?php echo $name; ?></h4>
				<?php
			}
			echo smiley_parse($data['content']).'<div class="clearfix"></div>';
			if (!empty($config['form']))
			{
				?>
				<a href="#reply" onclick="return comment_reply(this);" data-name="<?php echo $data['name']; ?>" title="<?php echo lang('Reply Comment'); ?>"><small><?php echo icon('fa-reply', lang('Reply Comment')).' '.lang('Reply Comment'); ?></small></a>&nbsp;&nbsp;
				<?php
			}
			if (!empty($data['reply']))
			{
				?>
				<a href="#replies" onclick="return comment_replies(this);" title="<?php echo lang('View Replies'); ?>"><small> <?php echo icon('fa-comments', lang('View Replies')).' '.items($data['reply'], 'Reply', 'Replies'); ?></small></a>
				<?php
				if ($data['par_id']==0)
				{
					$_GET['par_id'] = $data['id'];
					include 'comment_list.php';
				}
			}
			?>
	  </div>
	</div>
	<?php
}
$page++;
if ($pages > 1 && $page < $pages)
{
	$url = seo_uri();
	if (preg_match('~&page_comment=[0-9]+~', $url))
	{
		$url = preg_replace('~&page_comment=[0-9]+~', '&page_comment='.$page, $url);
	}else{
		$url .= '&page_comment='.$page;
	}
	echo '<a href="#'.$page.'" class="page_ajax_more" data-max="'.$pages.'" onclick="return comment_more(this);" rel="'.$url.'">'.icon('fa-angle-double-down').' '.lang('load more').'</a>';
}