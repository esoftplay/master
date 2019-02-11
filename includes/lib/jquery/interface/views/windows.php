<?php foreach($content AS $key => $item) {	?>
	<a href="<?php echo $item['link'];?>" id="<?php echo $a_id;?>" title="<?php echo $item['title'];?>" onClick="return winOpen(this);"><?php echo $item['text'];?></a>
<?php }	?>
<div id="window">
	<div id="windowTop">
		<div id="windowTopContent">title</div>
		<img src="<?php echo $imgurl;?>window_min.jpg" id="windowMin" onClick="winMin();" />
		<img src="<?php echo $imgurl;?>window_max.jpg" id="windowMax" onClick="winMax();" />
		<img src="<?php echo $imgurl;?>window_close.jpg" id="windowClose" onClick="winClose();" />
	</div>
	<div id="windowBottom"><div id="windowBottomContent">&nbsp;</div></div>
	<div id="windowContent"></div>
	<img src="<?php echo $imgurl;?>window_resize.gif" id="windowResize" />
</div>
<script type="text/javascript">
	var now_obj = 'none';
	function winOpen(obj)
	{
		if($('#window').css('display') == 'none') {
			now_obj = obj;
			$('#windowTopContent').html($(obj).attr('title'));
			$(obj).TransferTo({to:'window', className:'transferer2', duration: 400, complete: function(){$('#window').show();}});
			$('#windowContent').load($(obj).attr('href'));
		}
		$(obj).blur();
		return false;
	}
	function winClose() {
		$('#window').TransferTo( { to: now_obj, className:'transferer2', duration: 400}).hide();
	}
	function winMax() {
		var windowSize = $.iUtil.getSize(document.getElementById('windowContent'));
		$('#windowContent').SlideToggleUp(300);
		$('#windowBottom, #windowBottomContent').animate({height: windowSize.hb + 13}, 300);
		$('#window').animate({height:windowSize.hb+43}, 300).get(0).isMinimized = false;
		$('#windowMax').hide();
		$('#windowMin, #windowResize').show();
	}
	function winMin(){
		$('#windowContent').SlideToggleUp(300);
		$('#windowBottom, #windowBottomContent').animate({height: 10}, 300);
		$('#window').animate({height:40},300).get(0).isMinimized = true;
		$('#windowMin').hide();
		$('#windowResize').hide();
		$('#windowMax').show();
	}
	$(document).ready(
		function(){
			$('#window').Resizable({
					minWidth: 200,
					minHeight: 60,
					maxWidth: 700,
					maxHeight: 400,
					dragHandle: '#windowTop',
					handlers: {
						se: '#windowResize'
					},
					onResize : function(size, position) {
						$('#windowBottom, #windowBottomContent').css('height', size.height-33 + 'px');
						var windowContentEl = $('#windowContent').css('width', size.width - 25 + 'px');
						if (!document.getElementById('window').isMinimized) {
							windowContentEl.css('height', size.height - 48 + 'px');
						}
					}
			});
	});
</script>