<div id="title-head" class="">
	<h1 style="margin-bottom: 0px; color: #FFC200;"><?php echo $category['title']; ?></h1>
	<h5><?php echo $category['description']; ?></h5>						
</div>
<hr>
<br>
<div id="list-category">
	
	<?php
	$path = _URL.'images/modules/content/';
	$j = 0;
	foreach ($subcat as $sub)
	{
		$link = content_cat_link($sub['id'], $sub['title']);
		$j++;
		?>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-body">
					<center>
						<h4><a href="<?php echo $link; ?>" title="<?php echo $sub['title']; ?>"><?php echo $sub['title']; ?></a></h4>
						<?php echo image($path.$sub['image'], '', 'style="width: 100%;"'); ?>
						<span><?php echo $sub['description']; ?></span>
					</center>
				</div>
				<div class="panel-footer">
					<small class="pull-left"><?php echo icon('calendar') ?>&nbsp; <?php echo date('F d, Y', strtotime($sub['updated'])); ?></small>
					<small class="pull-right text-right" style="font-size: 16px;"><a href="<?php echo $link; ?>" title="<?php echo $sub['title']; ?>"><?php echo icon('list-alt') ?>&nbsp; <?php echo money($sub['total']); ?></a></small>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<?php
		if (($j%3)==0) {
			echo '<div class="clearfix"></div>';
		}
	}
	if (($j%4)==0) {
		// echo '</div>';
	}
	?>
</div>
<div class="clearfix"></div>
<?php
$paging = page_list($total, $limit, $page, 'page', $link);
if (!empty($paging))
{
	?>
	<hr />
	<div class="container">
		<?php echo $paging; ?>
	</div>
	<?php
}