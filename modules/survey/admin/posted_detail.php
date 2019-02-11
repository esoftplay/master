<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Detail Posted');

$id = @intval($_GET['id']);
$q = "SELECT user_id FROM survey_posted WHERE id=$id";
$user_id = $db->getOne($q);
$params = array(
	'title'       => 'User Profile',
	'table'       => 'survey_posted',
	'config_pre'  => array(),
	'config'      => user_field($user_id),
	'config_post' => array(),
	'name'        => 'params',
	'id'          => $id
);

$params['config_pre'] = array(
	'name' => array(
		'text'      => 'Name',
		'type'      => 'text',
		'attr'      => 'size="30"',
		'mandatory' => '1'
	),
	'email' => array(
		'text'      => 'Email',
		'type'      => 'text',
		'mandatory' => '1',
		'attr'      => 'size="30"',
		'checked'   => 'email'
	)
);
$params['config_post'] = array(
	'date' => array(
		'text' => 'Date Time',
		'type' => 'plain'
	),
	'publish' => array(
		'text' => 'Publish',
		'type' => 'checkbox'
	)
);
$form = _class('params');
$form->set($params);
#$form->set_encode(false);
echo $form->show();

$_url = seo_url();
$r    = $db->getAll("SELECT * FROM `survey_posted_question` WHERE `posted_id`={$id} ORDER BY id ASC");
foreach ($r as $data)
{
	$answer    = !empty($data['option_titles']) ? $data['option_titles'] : $data['option_ids'];
	$questions = explode('<br />', $data['question_title']);
	$answers   = explode('<br />', $answer);
	?>
	<div class="list-group">
		<?php
		foreach ($questions as $i => $question)
		{
			?>
			  <div class="list-group-item">
				  <a href="<?php echo $Bbc->mod['circuit'].'.question_detail&id='.$data['question_id'].'return='.urlencode($_url); ?>" style="float: right;"><?php echo icon('check'); ?></a>
			    <h4 class="list-group-item-heading"><?php echo $question; ?></h4>
			    <p class="list-group-item-text"><?php echo @$answers[$i]; ?></p>
			  </div>
			<?php
		}
		if (!empty($data['note']))
		{
			?>
			<div class="list-group-item">
				<blockquote>
				  <p><?php echo $data['note']; ?></p>
				</blockquote>
			</div>
			<?php
		}
		?>
	</div>
	<?php
}

$form = _lib('pea',  'survey_posted_question' );

$form->initRoll("posted_id=$id ORDER BY id", 'id' );
$form->roll->setSaveTool(false);
$form->roll->setDeleteTool(false);

$form->roll->addInput('question_title','sqlplaintext');
$form->roll->input->question_title->setTitle('question');

$form->roll->addInput('option_titles','sqlplaintext');
$form->roll->input->option_titles->setTitle('Answer');
$form->roll->input->option_titles->setFieldName("IF(option_titles!='',option_titles,option_ids) AS option_titles");

$form->roll->addInput('note','sqlplaintext');
$form->roll->input->note->setTitle('Notes');

$form->roll->addInput('link1','editlinks');
$form->roll->input->link1->setTitle('Detail');
$form->roll->input->link1->setCaption('detail');
$form->roll->input->link1->setFieldName('question_id');
$form->roll->input->link1->setLinks( $Bbc->mod['circuit'].'.question_detail');

$form->roll->onDelete('_posted_question_delete', $form->roll->getDeletedId(), false);
// echo $form->roll->getForm();

function _posted_question_delete($ids)
{
	global $db;
	if(count($ids) > 0)
	{
		$q = "SELECT posted_id, question_id, option_ids FROM survey_posted_question WHERE id IN(".implode(',', $ids).")";
		$r = $db->getAll($q);
		$p_ids = array();
		foreach($r AS $d)
		{
			if($d['option_ids'])
			{
				$q = "UPDATE survey_question_option SET voted=(voted-1) WHERE id IN (".$d['option_ids'].")";
				$db->Execute($q);
			}
			$p_ids[$d['posted_id']][] = $d['question_id'];
		}
		if( count($p_ids) > 1 )
		{
			foreach($p_ids AS $posted_id => $question_ids)
			{
				$q = "SELECT question_ids, question_titles FROM survey_posted WHERE id=$posted_id";
				$r = $db->Execute($q);
				foreach($r AS $d)
				{
					$question_id_new     = $question_title_new = array();
					$question_ids_old    = explode(',', $d['question_ids']);
					$question_titles_old = explode('<br />', $d['question_titles']);

					foreach($question_ids_old AS $i => $c)
					{
						if(!in_array($c, $question_ids))
						{
							$question_id_new[]		= $c;
							$question_title_new[]	= $question_titles_old[$i];
						}
					}
				}
				if(count($question_id_new) > 0)
				{
					$q = "UPDATE survey_posted SET question_ids='".implode(',', $question_id_new)."'
							, question_titles='".addslashes(implode('<br />', $question_title_new))."' WHERE id=$posted_id";
					$db->Execute($q);
				}
			}
		}
		$q = "DELETE FROM survey_posted_question WHERE id IN(".implode(',', $ids).")";
		$db->Execute($q);
	}
}
