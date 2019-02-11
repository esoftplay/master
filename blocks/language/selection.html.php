<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

?>
<select class="form-control" onchange="return ch_lang(this.value);">
  <?php
  foreach ((array)$r as $lang_id => $dt)
  {
    $active = $dt['active'] ? ' selected' : '';
    ?>
    <option value="<?php echo $dt['link'] ?>"<?php echo $active; ?>><?php echo $dt['title'] ?></option>
    <?php
  }
  ?>
</select>
<script type="text/javascript">
function ch_lang(a)
{
  document.location.href = a;
  return false;
};
</script>
