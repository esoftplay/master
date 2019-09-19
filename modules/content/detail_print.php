<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if(!$id) $sys->denied();

$data = content_fetch($id, true);
if(empty($data)) $sys->denied();

$config = $data['config'];
if($data['publish'] && @$config['print'])
{
	meta_title($data['title'], 2);
	meta_desc($data['description'], 2);
	meta_keyword($data['keyword'], 2);
	$sys->set_layout('blank.php');
	if($config['title'])
	{
		?>
		<h1><?php echo $data['title'];?></h1>
		<?php
	}
	if(	$config['created'] || $config['author'] )
	{
		?>
		<div class="text text-muted">
			<?php echo ($config['created']) ? '<span class="created pull-right">'.lang('created').' : '.content_date($data['created']).'</span>' : '';?>
			<?php echo ($config['author']) ? '<span class="author pull-left">'.lang('author').' : '.$data['created_by_alias'].'</span>' : '';?>
			<div class="clearfix"></div>
		</div>
		<?php
	}
	$image = (!empty($data['is_popimage']) && !empty($data['image'])) ? content_src($data['image'], false, true) : '';
	if(!empty($image))
	{
		echo image($image, '', ' class="img img-responsive img-fluid" alt="'.$data['title'].'" title="'.$data['title'].'"');
	}
	echo '<div class="text text-justify">'.$data['content'].'</div>';
	echo ($config['modified']) ? '<span class="text text-muted pull-right">'.lang('Last modified').' : '.content_date($data['modified']).'</span>' : '';
	?>
	<br class="clearfix" />
	<nav>
		<ul class="pager">
			<li><a href="" onClick="window.print();return false;"><?php echo icon('print').' '.lang('print');?></a></li>
			<li><a href="" onClick="window.close();return false;"><?php echo icon('remove-circle').' '.lang('close');?></a></li>
		</ul>
	</nav>
		<?php
}
