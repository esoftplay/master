<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// CHECK SESSION...
if(!isset($sess['index']) || empty($sess['index'])) redirect($Bbc->mod['circuit']);
if(!isset($sess['index_2']) || empty($sess['index_2'])) redirect($Bbc->mod['circuit'].'.index_2');
if(!empty($sess['index_3_id']))
{
	$user->id = intval($sess['index_3_id']);
}
if($user->id > 0)
{
	$q = "SELECT * FROM bbc_account WHERE user_id=".$user->id;
	$data = $db->getRow($q);
	$data['params'] = config_decode($data['params']);
	$sess['index_4']= $data;
	survey_sess('index_4', $data);
	$_POST['Submit'] = 'Submit &#187;';
}else
if(empty($sess['index_4']) && !$user->id)
{
	redirect($Bbc->mod['circuit'].'.index_4');
}

if(isset($_POST['Submit']) && !empty($_POST['Submit']))
{
#	$db->debug=1;
	$input = $sess;
	$params= array();
	//INSERT USER DATA...
	$data = $input['index_4'];
	$data['params'] = serialize(urlencode_r($data['params']));
	$data = addslashes_r($data);
	$q = "INSERT INTO survey_posted
		SET `user_id`='".$user->id."'
		, `lang_id`  ='".lang_id()."'
		, `name`     ='".$data['name']."'
		, `email`    ='".$data['email']."'
		, `params`   ='".$data['params']."'
		, `date`     = NOW()
		, `publish`  ='".$config['publish']."'
	";
	$db->Execute($q);
	$posted_id = $db->Insert_ID();

	// GET QUESTIONS... 
	$question_ids		= implode(',', (array)$input['index']);
	$q = "SELECT q.id, t.title FROM survey_question AS q LEFT JOIN survey_question_text AS t
			ON (q.id=t.question_id AND t.lang_id=".lang_id().")
			WHERE q.id IN (".$question_ids.") ORDER BY orderby";
	$question_titles= $db->getAssoc($q);
	$q = "UPDATE survey_posted SET question_ids='$question_ids'
			, question_titles='".addslashes(implode('<br />', $question_titles))."'
			WHERE id=$posted_id";
	$db->Execute($q);

	// INSERT POSTED QUESTION...
	$data = $input['index_2'];
	$option_ids = array();
	foreach((array)$data AS $question_id => $dt)
	{
		$q = "SELECT type, file FROM survey_question WHERE id=$question_id";
		$d = $db->getRow($q);
		$is_execute = true;
		if($d['type'] == 'custom')
		{
			$filepath = survey_path($d['file'], 5);
			if(file_exists($filepath))
			{
				$is_execute = false;
				include $filepath;
			}
		}
		if($is_execute)
		{
			$q = "SELECT title FROM survey_question_option_text WHERE option_id IN (".implode(',', (array)$dt['ids']).") AND lang_id=".lang_id();
			$titles = $db->getCol($q);
			$titles = addslashes_r($titles);
			$q = "INSERT INTO survey_posted_question
			SET posted_id		= $posted_id
			, question_id		= $question_id
			, question_title= '".addslashes(@$question_titles[$question_id])."'
			, option_ids		= '".implode(',', (array)$dt['ids'])."'
			, option_titles	= '".implode('<br />', (array)$titles)."'
			, note					= '".addslashes($dt['notes'])."'
			";
			$db->Execute($q);
			$option_ids = array_merge($option_ids, $dt['ids']);
		}
	}
	$q = "UPDATE survey_question SET voted=(voted+1) WHERE id IN (".$question_ids.")";
	$db->Execute($q);
	if(!empty($option_ids))
	{
		$q = "UPDATE survey_question_option SET voted=(voted+1) WHERE id IN (".implode(',', $option_ids).")";
		$db->Execute($q);
	}
	//	SEND MAIL...
	$params = $input['index_4'];
	$emails = array($input['index_4']['email']);
	if($config['alert'])
	{
		$emails[] = is_email($config['email']) ? $config['email'] : config('email', 'address');
	}
	$sys->mail_send($emails, 'entry', $params);
	$_SESSION['survey'] = array('message' => $sys->text_replace(lang('thank you'), $params));
	redirect($Bbc->mod['circuit'].'.index_6');
}

$data = $sess['index_4'];
?>
<form action="" method="post">
	<div class="formItem">
		<h3><?php echo lang('Profile Detail');?></h3>
		<table class="table">
			<tr>
				<td style="width: 150px;"><?php echo lang('Name');?></td>
				<td><?php echo $data['name'];?></td>
			</tr>
			<?php
			foreach((array)$data['params'] AS $field => $value)
			{
				?>
				<tr>
					<td><?php echo lang($field);?></td>
					<td><?php echo $value;?></td>
				</tr>
				<?php
			}
			?>
			<tr>
				<td><?php echo lang('Email');?></td>
				<td><?php echo $data['email'];?></td>
			</tr>
		</table>
	</div>
	<p class="button">
		<input type="Button" value="&#171; Back" class="btn" onClick="window.history.go(-1);" />
		<input type="submit" name="Submit" value="Submit &#187;" class="btn" />
	</p>
</form>
