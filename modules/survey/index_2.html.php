<form action="" method="post">
	<?php
	foreach($question AS $id => $data)
	{
		?>
		<legend><?php echo $data['title'];?></legend>
		<div class="help-block"><?php echo $data['description']; ?></div>
		<?php
		if(isset($output[$id]))
		{
			echo msg($output[$id], '');
		}
		$r_option = (isset($data['option']) && is_array($data['option'])) ? $data['option'] : array();
		$filepath = survey_path($data['file'], 2);
		if($data['type']=='custom' && is_file($filepath))
		{
			include $filepath;
		}else{
			pr($data['type'], $id, $r_option);
			survey_option($data['type'], $id, $r_option);
		}
		if($data['is_note'])
		{
			?>
			<div class="form-group">
				<label><?php echo lang('Insert Notes');?></label>
				<textarea name="notes[<?php echo $id;?>]" class="form-control"><?php echo @$_POST['notes'][$id];?></textarea>
			</div>
			<?php
		}
	}
	?>
	<p class="button">
		<input type="Button" value="&#171; Back" class="btn btn-default btn-secondary" onClick="window.history.go(-1);" />
		<input type="submit" name="Submit" value="Next &#187;" class="btn btn-default btn-secondary" />
	</p>
</form>