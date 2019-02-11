<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');?>

<table class="table table-striped table-condensed">
	<tr>
		<td><?php echo lang('Total Visitor');?></td>
		<td>: <?php echo total($output['total_visit'], 'visitor');?></td>
	</tr>
	<tr>
			<td><?php echo lang('Total Member');?></td>
			<td>: <?php echo total($output['total_member'] , 'member');?></td>
	</tr>
	<tr>
				<td><?php echo lang('Online Member');?></td>
				<td>: <?php echo total($output['member_online'], 'user');?></td>
	</tr>
	<tr>
				<td><?php echo lang('Online User');?></td>
				<td>: <?php echo total($output['user_online'], 'user');?></td>
	</tr>
	<tr>
		<td><?php echo lang('Actived');?></td>
		<td>: <?php echo $output['timespan'];?></td>
	</tr>
</table>
