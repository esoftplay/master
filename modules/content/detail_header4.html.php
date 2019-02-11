<?php
if (!empty($data['audio']))
{
	include_once _ROOT.'modules/content/constants.php';
	?>
	<div class="form-group">
		<center>
			<?php echo str_replace('{code}', $data['audio'], _AUDIO_EMBED); ?>
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