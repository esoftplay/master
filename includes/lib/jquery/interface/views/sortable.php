<?php foreach($content AS $position => $blocks) {	?>
	<div id="<?php echo $position;?>" class="<?php echo $className;?>">
	<?php foreach($blocks AS $id => $data) {	?>
			<div id="<?php echo $id;?>" class="groupItem">
				<div class="itemHeader"><?php echo $data['title'];?><a href="#" class="closeEl">[+]</a></div>
				<div class="itemContent"><?php echo $data['content'];?></div>
			</div>
	<?php }	?>
		<p>&nbsp;</p>
	</div>
<?php }	?>
<script type="text/javascript">
$(document).ready(
	function () {
		$('a.closeEl').bind('click', toggleContent);
		$('div.<?php echo $className;?>').Sortable(
			{
				accept: 'groupItem',
				helperclass: 'sortHelper',
				activeclass : 	'sortableactive',
				hoverclass : 	'sortablehover',
				handle: 'div.itemHeader',
				tolerance: 'pointer',
				onChange : function(ser)
				{
				},
				onStart : function()
				{
					$.iAutoscroller.start(this, document.getElementsByTagName('body'));
				},
				onStop : function()
				{
					$.iAutoscroller.stop();
				}
			}
		);
	}
);
var toggleContent = function(e)
{
	var targetContent = $('div.itemContent', this.parentNode.parentNode);
	if (targetContent.css('display') == 'none') {
		targetContent.slideDown(300);
		$(this).html('[-]');
	} else {
		targetContent.slideUp(300);
		$(this).html('[+]');
	}
	return false;
};
function serialize(s)
{
	serial = $.SortSerialize(s);
	alert(serial.hash);
};
</script>