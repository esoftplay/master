<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
$q = "SELECT SUM(voted) FROM survey_question_option WHERE question_id=$id";
$total = $db->getOne($q);

$q = "SELECT * FROM survey_question AS q LEFT JOIN survey_question_text AS t ON (q.id=t.question_id AND t.lang_id=".lang_id().") WHERE q.id=$id";
$data = $db->getRow($q);

if(empty($data))
{
	redirect($Bbc->mod['circuit'].'.question');
}else{
	$form = _lib('pea', 'survey_question');
	$output = '';
	if($total > 0 || $data['voted'] > 0)
	{
		ob_start();
		if(isset($_POST['submit']))
		{
			switch($_POST['submit'])
			{
				case 'Yes':
					if(survey_reset($id))	echo 'This question has been Reset<br />'.$data['title'];
					else echo 'Failed to repair question';
				break;
				default:
					if(survey_repair($id))	echo 'This question has been repair<br />'.$data['title'];
					else echo 'Failed to repair question';
				break;
			}
		}else{
			?>
			<form method="post" name="reset">
				Are you sure want to reset this question ?
				<p><?php echo $data['title'];?></p>
				<input type="submit" name="submit" value="Yes" class="btn btn-default">
				<input type="submit" name="submit" value="Repair Only" class="btn btn-default">
			</form>
			<?php		}
		$text = ob_get_contents();
		ob_end_clean();
	}else{
		$text = 'this question has no voters.';
	}
	$output = msg($text, '');
	if (!empty($_GET['return']))
	{
		$output .= $sys->button($_GET['return']);
	}
	echo $output;
}
