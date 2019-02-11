<?php
if (!empty($data['video']))
{
	include_once _ROOT.'modules/content/constants.php';
	?>
	<div class="form-group">
		<center>
			<?php echo str_replace('{code}', $data['video'], _VIDEO_EMBED); ?>
		</center>
		<?php
		if (!empty($data['caption']))
		{
			?>
			<div class="help-block"><?php echo $data['caption']; ?></div>
			<?php
		}
		?>
	</div>
	<?php
}