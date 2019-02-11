<label><?php echo $data['question'];?></label>
<form action="index.php?mod=survey.polling&id=<?php echo $polling_id;?>" method="post" name="polling<?php echo $polling_id;?>">
	<div class="radio">
	<?php
	$i = 0;
	foreach((array)$data['option'] AS $d)
	{
		$checked = (!$i) ? ' checked="true"' : '';
		?>
		<label>
			<input name="option" id="option[<?php echo $d[0];?>]" value="<?php echo $d[0];?>" type="radio"<?php echo $checked;?> />
			<?php echo $d[1];?>
		</label>
		<?php
		$i++;
	}
	?>
	</div>
	<br />
	<div class="ext text-justify">
		<button type="submit" name="Submit" value="<?php echo lang('Submit');?>" class="btn btn-primary btn-xs" role="button">
			<?php echo icon('send').' '.lang('Submit'); ?>
		</button>
		<a href="index.php?mod=survey.polling&id=<?php echo $polling_id;?>" class="text text-primary"><?php echo lang('View Result');?></a>
	</div>
	<div class="clearfix"></div>
</form>
<div class="clearfix"></div>