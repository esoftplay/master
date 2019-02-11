<?php if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$count = count($output['images']);
if ($count > 0)
{
  $style = !empty($output['config']['fixsize']) ? ' style="width:'.@$output['cat']['width'].'px;height:'.@$output['cat']['height'].'px;overflow:hidden;"' : '';
  ?>
  <div id="imageslider<?php echo $block->id; ?>"<?php /*echo $style;*/ ?> class="carousel slide" data-ride="carousel">
  <?php
  if (!empty($output['config']['indicator']) && $count > 1)
  {
    echo '<ol class="carousel-indicators">';
    foreach ($output['images'] as $key => $value)
    {
      $cls = $key ? '' : ' class="active"';
      echo '<li data-target="#imageslider'.$block->id.'" data-slide-to="'.$key.'"'.$cls.'></li>';
    }
    echo '</ol>';
  }
  echo '<div class="carousel-inner">';
  foreach ($output['images'] as $key => $dt)
  {
    $cls = $key ? '' : ' active';
    ?>
    <div class="item<?php echo $cls; ?>">
      <?php echo !empty($dt['link']) ? '<a href="'.$dt['link'].'" title="'.$dt['title'].'">' : ''; ?>
      <img src="<?php echo $dt['image']; ?>" alt="<?php echo $dt['title'] ?>" title="<?php echo $dt['title'] ?>" />
      <?php echo !empty($output['config']['caption']) ? '<div class="carousel-caption"><h3>'.$dt['title'].'</h3></div>' : ''; ?>
      <?php echo !empty($dt['link']) ? '</a>' : ''; ?>
    </div>
    <?php
  }
  echo '</div>';
  if (!empty($output['config']['control']) && $count > 1)
  {
    ?>
    <a class="left carousel-control" href="#imageslider<?php echo $block->id; ?>" role="button" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left"></span>
    </a>
    <a class="right carousel-control" href="#imageslider<?php echo $block->id; ?>" role="button" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right"></span>
    </a>
    <?php
  }
  ?>
  </div>
  <?php
}