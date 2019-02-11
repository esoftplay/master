<?php
$image = (!empty($data['is_popimage']) && !empty($data['image'])) ? content_src($data['image'], false, true) : '';
if (!empty($image))
{
	?>
	<div class="form-group">
		<center>
			<img src="<?php echo $image; ?>" alt="<?php echo $data['title']; ?>" title="<?php echo $data['title']; ?>" class="img img-thumbnail" style="width: 100%;" />
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
