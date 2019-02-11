<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
$q = "SELECT * FROM contact WHERE id=$id";
$data = $db->getRow($q);
if(!$db->Affected_rows()) redirect($Bbc->mod['circuit'].'.posted');
$r_param = urldecode_r(config_decode($data['params']));
?>
<table class="table table-hover">
	<thead>
		<tr>
			<td colspan=2><strong>Contact Detail</strong></td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td width="150px">Name</td>
			<td><?php echo $data['name'];?></td>
		</tr>
		<?php
		foreach((array)$r_param AS $title => $value)
		{
			?>
			<tr>
				<td><?php echo $title;?></td>
				<td><?php echo $value;?></td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td>Email</td>
			<td><?php echo $data['email'];?></td>
		</tr>
		<tr>
			<td>Posted</td>
			<td><i><?php echo date( 'D - M jS, Y H:i:s', strtotime($data['post_date']));?></i></td>
		</tr>
		<tr>
			<th>Message</th>
			<td><?php echo nl2br($data['message']);?></td>
		</tr>
		<tr>
			<th>Answer</th>
			<td><?php echo nl2br($data['answer']);?></td>
		</tr
		<tr>
			<td>Answered on</td>
			<td><i><?php echo date( 'D - M jS, Y H:i:s', strtotime($data['answer_date']));?></i></td>
		</tr
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">
				<span type="button" class="btn btn-default btn-sm" onclick="document.location.href='<?php echo $Bbc->mod['circuit'].'.posted';?>';"><span class="glyphicon glyphicon-chevron-left"></span></span>
			</td>
		</tr>
	</tfoot>
</table>
