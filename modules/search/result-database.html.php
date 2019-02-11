<br>
<div class="well">
<ul class="list-unstyled">
<?php if(!empty($result))	{	
		foreach((array)$result AS $d)	{	?>
	<li>
		<div class="text-primary">
			<i class="glyphicon glyphicon-link"></i>&nbsp; <a href="<?php echo $d['link'];?>"><?php echo $d['title'];?></a>
		</div>
		<div>
			<?php echo $d['description'];?>
		</div>
		<br>
	</li>
<?php 	}
	 }else{	?>
	<li>
		<strong><?php echo $keyword;?></strong> <?php echo lang('not found');?>
	</li>
<?php }	?>
</ul>
</div>
<div>
	<?php echo page_list($total, $limit, @$_GET['page'], 'page' );?>
</div>