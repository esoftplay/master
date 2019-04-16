<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if(!function_exists('survey_questionery_option'))
{
	function survey_questionery_option($q_id, $o_id, $value, $title)
	{
		if(empty($_POST['options'][$q_id][$o_id])) $i = 3;
		else $i = $_POST['options'][$q_id][$o_id];
		$checked = ($value==$i || $value==3) ? ' checked="checked"' : '';
		$checked = '';
		return '<td><input type="radio" name="options['.$q_id.']['.$o_id.']" value="'.$value.'" title="'.lang($title).'"'.$checked.' /></td>';
	}
}
$q = "SELECT id, question_id, title FROM survey_questionary WHERE question_id=".$data['question_id']." AND publish=1 ORDER BY orderby ASC";
$r = $db->getAll($q);
if($db->Affected_rows())
{
	?>
	<table border="0" class="table">
		<tr>
			<th>#</th>
			<th><?php echo lang('Statements');?></th>
			<th style="width:5px;">1</th>
			<th style="width:5px;">2</th>
			<th style="width:5px;">3</th>
			<th style="width:5px;">4</th>
			<th style="width:5px;">5</th>
		</tr>
	<?php	$i = 0;
	foreach($r AS $d)
	{
		$i++;
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $d['title'];?></td>
			<?php echo survey_questionery_option($data['question_id'], $d['id'], 1, 'Sangat Tidak Setuju');?>
			<?php echo survey_questionery_option($data['question_id'], $d['id'], 2, 'Tidak Setuju');?>
			<?php echo survey_questionery_option($data['question_id'], $d['id'], 3, 'Netral');?>
			<?php echo survey_questionery_option($data['question_id'], $d['id'], 4, 'Setuju');?>
			<?php echo survey_questionery_option($data['question_id'], $d['id'], 5, 'Sangat Setuju');?>
		</tr>
		<?php	}
	echo '</table>';
}
