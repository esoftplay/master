<ul class="list-group">
	<?php
	foreach((array)$tags AS $data)
  {
    $link = content_tag_link($data['id'], $data['title']);
    ?>
    <li class="list-group-item">
      <a href="<?php echo content_tag_link($data['id'], $data['title']); ?>" title="<?php echo $data['title']; ?>"> #<?php echo $data['title']; ?></a></li>
    <?php
	}
  ?>
</ul>