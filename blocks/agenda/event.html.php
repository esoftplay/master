<?php
foreach((array)@$r AS $i => $data)
{
	?>
	<h3><a href="<?php echo content_link($data['content_id'], $data['title']); ?>"><?php echo $data['title'];?></a></h3>
	<small>
		<?php
		$i_start = strtotime($data['start_date'].' '.$data['start_hour'].':'.$data['start_minute']);
		$i_end	 = strtotime($data['end_date'].' '.$data['end_hour'].':'.$data['end_minute']);
		echo date($time_format, $i_start);
		if($i_end > $i_start)	echo ' - '.date($time_format, $i_end);
		?>
	</small>
	<p><?php echo $data['intro'];?></p>
	<div class="clearfix"></div>
	<?php
}
?>
<a href="index.php?mod=agenda.<?php echo strtolower(agenda_cat($config['type']));?>" class="link_more"><?php echo lang('Archieves');?></a>
