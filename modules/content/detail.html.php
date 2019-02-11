<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

echo !empty($config['title']) ? '<h1>'.$data['title'].'</h1>' : '';
if(	!empty($config['created']) || !empty($config['author']) )
{
	?>
	<div class="text text-muted">
		<?php echo ($config['author']) ? '<span class="author pull-left">'.lang('author').$data['created_by_alias'].'</span>' : '';?>
		<?php echo ($config['created']) ? '<span class="created pull-right">'.lang('created').content_date($data['created']).'</span>' : '';?>
		<div class="clearfix"></div>
		<br />
	</div>
	<?php
}
?>
<article class="text text-justify">
	<?php
	include tpl('detail_header'.$data['kind_id'].'.html.php');
	echo $data['content'];
	?>
</article>
<br />
<?php
if( !empty($config['tag']) )
{
	$r = content_category($data['id'], $config['tag_link']);
	if(!empty($r))
	{
		echo '<div class="text text-left">'.lang('Tags').' '.implode(' ', $r).'</div>';
	}
}
if(empty($data['revised']))
{
	$config['modified'] = 0;
}
echo @$config['modified'] ? '<span class="text text-muted pull-right">'.lang('Last modified').content_date($data['modified']).'</span>' : '';
?>
<div class="clearfix"></div>
<br />
<input type="hidden" value="<?php echo $data['id'];?>" id="content_value_id">
<?php
if( !empty($config['rating'])
	|| !empty($config['rating_vote'])
	|| !empty($config['print'])
	|| !empty($config['email'])
	|| !empty($config['pdf'])
	|| !empty($config['share'])
	|| !empty($edit_data) )
{
	$sys->link_js('detail.js', false);
	$tbl = $config['rating_vote'] ? 'bbc_content' : '';
	$tbl_id = $config['rating_vote'] ? $data['id'] : '';
	?>
	<div class="col-md-6 no-both">
		<?php echo $config['rating'] ? rating($data['rating'], $tbl, $tbl_id) : ''; ?>
	</div>
	<div class="col-md-6 no-left text-right">
		<div class="btn-group">
			<?php
			if(!empty($edit_data))
			{
				?>
				<a href="<?php echo $Bbc->mod['circuit'].'.posted_form&id='.$data['id'];?>" class="btn btn-default btn-sm">
					<?php echo icon('edit',lang('edit content')); ?>
				</a>
				<?php
			}
			if (!empty($config['pdf']))
			{
				?>
				<a class="btn btn-default btn-sm" id="icon_pdf">
					<?php echo icon('fa-file-pdf-o',lang('convert to pdf')); ?>
				</a>
				<?php
			}
			if (!empty($config['email']))
			{
				?>
				<a class="btn btn-default btn-sm" id="icon_mail">
					<?php echo icon('fa-envelope',lang('tell friend')); ?>
				</a>
				<?php
			}
			if (!empty($config['print']))
			{
				?>
				<a class="btn btn-default btn-sm" id="icon_print">
					<?php echo icon('fa-print',lang('print preview')); ?>
				</a>
				<?php
			}
			?>
			<div class="clearfix"></div>
		</div>
	</div>
	<?php
	if(!empty($config['share']))
	{
		$sys->meta_add('<link rel="image_src" href="'.content_src($data['image'], false, true).'" />
		<meta property="og:title" content="'.$data['title'].'" />
		<meta property="og:type" content="article" />
		<meta property="og:url" content="'.content_link($data['id'], $data['title']).'" />
		<meta property="og:image" content="'.content_src($data['image'], false, true).'" />
		<meta property="og:site_name" content="'.config('site', 'url').'"/>
		<meta property="og:description" content="'.$data['description'].'" />');
		?>
		<br class="clearfix" />
		<div class="addthis_toolbox addthis_default_style">
			<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
			<a class="addthis_button_tweet"></a>
			<a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
			<a class="addthis_counter addthis_pill_style"></a>
		</div>
		<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4edbbcb7772a1026"></script>
		<?php
	}
}
$cfg = array(
	'table'    => 'bbc_content_comment',
	'field'    => 'content',
	'id'       => $data['id'],
	'type'     => $config['comment'],
	'list'     => $config['comment_list'],
	'link'     => content_link($data['id'], $data['title']),
	'form'     => $config['comment_form'],
	'emoticon' => $config['comment_emoticons'],
	'captcha'  => $config['comment_spam'],
	'approve'  => $config['comment_auto'],
	'alert'    => $config['comment_email'],
	'admin'    => $edit_data ? 1 : 0
	);
echo _class('comment', $cfg)->show();
