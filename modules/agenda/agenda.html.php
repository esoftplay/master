<h1><?php echo lang('Past Events');?></h1>
<ul class="comment_list">
	<?php
	foreach((array)$r AS $data)
	{
		?>
		<li>
			<div class="comment_list-content">
				<a href="<?php echo content_link($data['content_id'], $data['title']);?>">
					<b><?php echo $data['title'];?></b>
				</a>
			</div>
		</li>
		<?php
	}
	?>
	<div class="clearfix"></div>
</ul>
<?php
echo page_list($t, $Agenda->limit, $page, 'page_e', seo_uri('page_e'));