<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function survey_sess($index, $data)
{
	$_SESSION['survey'][$index] = $data;
}
function survey_path($dir, $i='', $act='')
{
	if(!empty($dir))
	{
		global $Bbc;
		$output = $Bbc->mod['root'].'custom/'.$dir.'/index_'.$i.$act.'.php';
	}else $output = '';
	return $output;
}
function survey_repair($id)
{
	global $db;
	$id = intval($id);
	$q = "SELECT * FROM survey_question AS q LEFT JOIN survey_question_text AS t ON
		(q.id=t.question_id AND t.lang_id=".lang_id().") WHERE q.id=$id";
	$data = $db->getRow($q);
	if(!$db->Affected_rows())
	{
		return false;
	}else{
		$q = "SELECT id, voted FROM survey_question_option WHERE question_id=$id";
		$o = $db->getAssoc($q);
		$q = "SELECT id, option_ids FROM survey_posted_question WHERE question_id=$id";
		$r = $db->getAssoc($q);
		$t = array();
		$v = array();
		$k = array_keys($o);
		foreach($r AS $p_id => $p)
		{
			$t[] = $p_id;
			$arr = repairExplode($p);
			$arr_new = array();
			foreach($arr AS $i)
			{
				if(in_array($i, $k))
				{
					$arr_new[] = $i;
					$v[$i] = isset($v[$i]) ? ($v[$i]+1) : 1;
				}
			}
			if($arr != $arr_new)
			{
				$q = "UPDATE survey_posted_question SET option_ids='".implode(',', $arr_new)."' WHERE id=$p_id";
				$db->Execute($q);
			}
		}
		foreach($o AS $id => $voted)
		{
			$voted_new = @intval($v[$id]);
			if($voted != $voted_new)
			{
				$q = "UPDATE survey_question_option SET voted=$voted_new WHERE id=$id";
				$db->Execute($q);
			}
		}
		$total_voter = 0;
		if(count($t) > 0)
		{
			$q = "SELECT COUNT(DISTINCT posted_id) FROM survey_posted_question WHERE id IN (".implode(',', $t).")";
			$total_voter = $db->getOne($q);
		}
		if($total_voter != $data['voted'])
		{
			$q = "UPDATE survey_question SET voted=$total_voter WHERE id=".$data['id'];
			$db->Execute($q);
		}
	}
	return true;
}
function survey_reset($id)
{
	global $db;
	$id = intval($id);
	$q = "SELECT * FROM survey_question AS q LEFT JOIN survey_question_text AS t ON
		(q.id=t.question_id AND t.lang_id=".lang_id().") WHERE q.id=$id";
	$data = $db->getRow($q);
	if(!$db->Affected_rows())
	{
		return false;
	}else{
		$q = "SELECT posted_id FROM survey_posted_question WHERE question_id=$id";
		$posted_ids = $db->getCol($q);
		if(!empty($posted_ids))
		{
			$q = "SELECT id, question_ids FROM survey_posted WHERE id IN (".implode(',', $posted_ids).")";
			$r = $db->getAssoc($q);
			$posted_id_delete = array();
			foreach($r AS $posted_id => $question_ids)
			{
				$question_ids = repairExplode($question_ids);
				$question_ids_new = array();
				foreach($question_ids AS $i)
				{
					if($i != $id) $question_ids_new[] = $i;
				}
				if(empty($question_ids_new)) $posted_id_delete[] = $posted_id;
				else {
					$q = "UPDATE survey_posted SET question_ids='".implode(',', $question_ids_new)."' WHERE id=$posted_id";
					$db->Execute($q);
				}
			}
			if(!empty($posted_id_delete))
			{
				$q = "DELETE FROM survey_posted WHERE id IN (".implode(',', $posted_id_delete).")";
				$db->Execute($q);
			}
		}
		$q = "DELETE FROM survey_posted_question WHERE question_id=$id";
		$db->Execute($q);
		$q = "UPDATE survey_question_option SET voted=0 WHERE question_id=$id";
		$db->Execute($q);
		$q = "UPDATE survey_question SET voted=0 WHERE id=$id";
		$db->Execute($q);
	}
	return true;
}

function survey_option($type, $id, $arr)
{
	$default = '';
	switch($type)
	{
		case 'checkbox':
			echo lang($type.' option').'<br />';
			foreach((array)$arr AS $dt)
			{
				$checked = ($dt['checked']) ? ' checked="checked"' : '';
				?>
				<label for="options[<?php echo $id;?>][<?php echo $dt['id'];?>]">
					<input type="checkbox" name="options[<?php echo $id;?>][]" value="<?php echo $dt['id'];?>" 
					id="options[<?php echo $id;?>][<?php echo $dt['id'];?>]"<?php echo $checked;?>>	<?php echo $dt['title'];?>
				</label><br />
				<?php
			}
			break;
		case 'multiple':
			$r_option = $default = array();
			foreach((array)$arr AS $dt){
				$r_option[] = array($dt['id'], $dt['title']);
				if($dt['checked'])	$default[] = $dt['id'];
			}
			echo lang($type.' option').'<br />';
			?>
			<select name="options[<?php echo $id;?>][]" id="options[<?php echo $id;?>]" multiple="multiple" size="10">
				<?php echo createOption($r_option, $default);?>
			</select>
			<?php
			break;
		case 'radio':
			echo lang($type.' option').'<br />';
			foreach((array)$arr AS $dt)
			{
				$checked = ($dt['checked']) ? ' checked="checked"' : '';
				?>
				<label for="options[<?php echo $id;?>][<?php echo $dt['id'];?>]">
					<input type="radio" name="options[<?php echo $id;?>][]" value="<?php echo $dt['id'];?>" 
					id="options[<?php echo $id;?>][<?php echo $dt['id'];?>]"<?php echo $checked;?>>	<?php echo $dt['title'];?>
				</label><br />
				<?php
			}
			break;
		case 'select':
			$r_option = array();
			foreach((array)$arr AS $dt)
			{
				$r_option[] = array($dt['id'], $dt['title']);
				if($dt['checked'])	$default = $dt['id'];
			}
			echo lang($type.' option');
			?>
			<select name="options[<?php echo $id;?>][]" id="options[<?php echo $id;?>][]">
				<?php echo createOption($r_option, $default);?>
			</select>
			<?php
			break;
		case 'text':
			echo '<input type="text" name="options['.$id.'][]" id="options['.$id.'][]" value="'.$default.'" />';
			break;
		case 'none':
			break;
	}
}