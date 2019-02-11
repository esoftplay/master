<?php if (!defined('_VALID_BBC')) exit('No direct script access allowed');?>

<div class="list-group">
	<ul class="list-unstyled">
<?php
foreach ($output['data'] as $data)
{
	?>
	  <li>
			<a href="<?php echo $data['href'] ?>" class="list-group-item" title="<?php echo lang('Chat with').$data['name'];?>">
				<img src="<?php echo $data['src'];?>" alt="<?php echo lang('Chat with').' '.$data['name'];?>"/><?php echo $data['name'];?>
			</a>
		</li>
	<?php
}
if ($output['show_js'])
{
	?>
<script type="text/javascript">
function ym_chat(a) {
	if(a == '') return true;
	b = this.open("<?php echo $output['js_link'] ?>"+a, "chat_"+a,"width=240, height=385, align=top, scrollbars=no, status=no, resizable=no");
	b.window.focus();
	return false;
};
</script>
<?php
}
?>
	</ul>
</div>
<?php
if (!empty($output['address']))
{
	echo '<div>'.nl2br($output['address']).'</div>';
}
