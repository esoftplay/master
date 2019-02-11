<ul class="list-unstyled">
	<?php
	foreach((array) $r AS $lang_id => $dt)
	{
		$active = $dt['active'] ? ' class="active"' : '';
		?>
		<li class="text text-muted"<?php echo $active;?>>
			<a href="#<?php echo $dt['code']; ?>" rel="<?php echo $dt['link']; ?>" onclick="return ch_lang(this.rel);">
				<?php echo $dt['title']; ?>
			</a>
		</li>
		<?php
	}
	?>
</ul>
<script type="text/javascript">
function ch_lang(a)
{
  document.location.href = a;
  return false;
};
</script>
