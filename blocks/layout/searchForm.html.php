<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$placeholder = lang($config['caption']);
$value       = '';
if (!empty($_SESSION['currSearch']))
{
	$placeholder = $_SESSION['currSearch'];
	$value       = $_SESSION['currSearch'];
}
?>
<form method="post" class="form-inline" id="block_search<?php echo $block->id ?>" action="" role="form">
	<div class="input-group">
    <input type="text" class="form-control input-sm" name="keyword" value="<?php echo $value; ?>" placeholder="<?php echo $placeholder;?>" />
    <span class="input-group-btn">
	    <button class="btn btn-default input-sm" type="submit"><?php echo icon('search');?></button>
	  </span>
  </div>
</form>
<script type="text/javascript">
	_Bbc(function($){
		$("#block_search<?php echo $block->id ?>").submit(function(e){
			e.preventDefault();
			var a = $('input[name="keyword"]');
			if (a.val()=="") {
				alert("<?php echo lang('Please Insert Keyword!'); ?>");
				a.focus();
			}else{
				var b = _URL+'search.htm';
				var c = encodeURIComponent(a.val());
				if (c.length>12) {
					b += '?id=';
				}else{
					b += '/';
				}
				b += c;
				document.location.href = b;
			}
		})
	});
</script>