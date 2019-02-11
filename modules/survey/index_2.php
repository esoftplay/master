<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if(!isset($sess['index']) || empty($sess['index']))
{
	$rr = $db->getCol("SELECT id FROM survey_question WHERE checked=1 AND publish=1 ORDER BY orderby ASC");
	survey_sess('index', $rr);
	$sess['index'] = $rr;
	if(empty($sess['index']))
	{
		redirect($Bbc->mod['circuit']);
	}
}

$ids = $sess['index'];
$q = "SELECT q.id, q.*, t.* FROM survey_question AS q LEFT JOIN survey_question_text AS t
		ON (q.id=t.question_id AND t.lang_id=".lang_id().")
		WHERE q.id IN(".implode(',', $ids).") ORDER BY orderby";
$question = $db->getAssoc($q);
$output		= array();
if(isset($_POST['Submit']))
{
	$input = array();
	foreach((array)$question AS $id => $d)
	{
		if(!empty($_POST['options'][$id]))
		{
			$input[$id] = array(
			  'ids' 	=> $_POST['options'][$id]
			, 'notes'	=> @$_POST['notes'][$id]
			);
		}else{
			$output[$id] = lang('No Selection');
		}
	}
	if( empty($output) )
	{
		survey_sess('index_2', $input);
		redirect($Bbc->mod['circuit'].'.index_3');
	}else{
		echo msg(lang('check input'), lang('error'));
	}
}
$q = "SELECT * FROM survey_question_option AS q LEFT JOIN survey_question_option_text AS t
		ON (q.id=t.option_id AND t.lang_id=".lang_id().")
		WHERE q.question_id IN(".implode(',', $ids).") AND q.publish=1 ORDER BY q.question_id, q.orderby ASC";
$r = $db->getAll($q);
foreach((array)$r AS $d)
{
	$question[$d['question_id']]['option'][] = $d;
}
?>
<form action="" method="post">
<?php
foreach($question AS $id => $data)
{
?>
	<div class="formItem">
		<h3><?php echo $data['title'];?></h3>
		<p><?php echo $data['description'];?></p>
<?php
		if(isset($output[$id]))
		{
			echo msg($output[$id], '');
		}	?>
		<div>
<?php
		$r_option = (isset($data['option']) && is_array($data['option'])) ? $data['option'] : array();
		$filepath = survey_path($data['file'], 2);
		if($data['type']=='custom' && is_file($filepath))
		{
			include $filepath;
		}else{
			survey_option($data['type'], $id, $r_option);
		}	?>
		</div>
<?php if($data['is_note'])
			{	?>
				<p><?php echo lang('Insert Notes');?></p>
				<div>
					<textarea name="notes[<?php echo $id;?>]"><?php echo @$_POST['notes'][$id];?></textarea>			
				</div>
<?php }	?>
	</div>
<?php
}
?>
	<p class="button">
		<input type="Button" value="&#171; Back" class="btn" onClick="window.history.go(-1);" />
		<input type="submit" name="Submit" value="Next &#187;" class="btn" />
	</p>
</form>
