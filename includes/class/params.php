<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

/*	CHECK OUT THE SAMPLE HOW TO USE AT THE BOTTOM OF THIS FILE */
class params
{
	var $table;
	var $default;
	var $config;
	var $name;
	var $title;
	var $id;
	var $controller_id;
	var $is_loaded;
	var $init;
	var $delim_file    = '|';
	var $is_language   = false;
	var $isFormRequire = false;
	var $row_count     = 0;
	var $def_files_tot = 5;

	function __construct($params=array(), $db = '')
	{
		$this->init   = false;
		$this->lang_r = lang_assoc();
		if(is_array($params) && !empty($params)) $this->set($params, $db);
	}
	function set($params, $db = '')
	{
		if(!is_array($params) || empty($params)) return false;
		if(empty($db)) {
			global $db;
		}
		$this->db					= $db;
		$this->title			= isset($params['title']) ? $params['title'] : 'Add / Edit table';
		$this->table			= @$params['table'];
		$this->text_id		= isset($params['text_id']) ? $params['text_id'] : false ;
		$this->config_pre	= is_array(@$params['config_pre']) ? $this->set_param($params['config_pre']) : array();
		$this->config			= $this->set_param($params['config']);
		$this->name				= isset($params['name']) ? $params['name'] : 'params';
		$this->config_post= is_array(@$params['config_post']) ? $this->set_param($params['config_post']) : array();
		$this->table_id		= intval(@$params['id']);
		$this->primary		= isset($params['primary']) ? $params['primary'] : 'id';
		$this->default		= array();
		$this->pre_func		= @$params['pre_func'];
		$this->post_func	= @$params['post_func'];
		$this->set_encode(true);
		$this->init				= true;
	}
	function set_encode($bool = false)
	{
		$this->is_encode = $bool;
	}
	function set_param($arr)
	{
		$fields = array();
		foreach((array)$arr AS $title => $dt)
		{
			$dt['title'] = isset($dt['title']) ? $dt['title'] : $title;
			$field = array(
				'type' => strtolower(@$dt['type'])
			,	'text' => isset($dt['text']) ? lang($dt['text']) : @lang($dt['title'])
			,	'add'	 => @lang($dt['add'])
			,	'tips' => @lang($dt['tips'])
			,	'help' => @lang($dt['help'])
			,	'attr' => (isset($dt['attr']) && substr($dt['attr'],0,1) != ' ') ? ' '.$dt['attr'] : @$dt['attr']
			,	'language'	=> (isset($dt['language']) && $dt['language']) ? true : false
			,	'default'		=> trim(@$dt['default'])
			,	'mandatory' => @intval($dt['mandatory'])
			,	'checked'		=> isset($dt['checked']) ? strtolower($dt['checked']) : 'any'
			);
			switch($dt['type'])
			{
				case 'files':
					$field['max'] = (intval($dt['max']) > 0) ? $dt['max'] : $this->def_files_tot;
				case 'file':
					$path = empty($dt['path']) ? _ROOT.'images/uploads/' : $dt['path'];
					if(!empty($dt['option'])) {
						if(is_dir($dt['option']))	$path = $dt['option'];
						elseif(is_dir(_ROOT.$dt['option']))	$path = _ROOT.$dt['option'];
					}
					$field['path'] = $path;
				break;
				case 'checkbox':
					$dt['option'] = isset($dt['option']) ? $dt['option'] : array('1' => $field['text']);
					if (!is_array($dt['option'])) {
						$dt['option'] = array(1 => $dt['option']);
					}
				case 'radio':
				case 'select':
					$field['is_arr']= isset($dt['is_arr']) ? $dt['is_arr'] : false;
					$field['delim']	= isset($dt['delim']) ? $dt['delim'] : '<br />';
					$field['option'] = $this->set_param_option($dt['option']);
					if(strstr($field['default'], ';')) {
						$r = explode(';', $field['default']);
						if(count($r) > 0) {
							$field['default'] = array();
							foreach($r AS $value) {
								$value = trim($value);
								if(!empty($value))
									$field['default'][] = $value;
							}
						}
					}
				break;
				case 'captcha':
					$field['match'] = isset($dt['match']) ? $dt['match'] : 1;
					$field['mandatory'] = 1;
				break;
				case 'textarea':
					$field['format'] = isset($dt['format']) ? $dt['format'] : 'none';
				case 'text':
				default:
				break;
			}
			if(!$this->is_language && $field['language']) $this->is_language = true;
			$fields[$dt['title']] = $field;
		}
		return $fields;
	}
	function set_param_option($option)
	{
		if(is_string($option))	$option = trim($option);
		if(is_array($option)) {
			$output = $option;
		}elseif(preg_match('#select .*? from #is', $option)) {
			$output = $this->db->getAll($option);
		}elseif(is_array(json_decode($option,1))) {
			$output = json_decode($option,1);
		}else{
			$r = explode(';', $option);
			$output = array();
			foreach((array)$r AS $value) {
				$value = trim($value);
				if($value != '')
					$output[] = $value;
			}
		}
		return $output;
	}
	function clean_newline($input)
	{
		if (is_array($input))
		{
			foreach ($input as $key => $value)
			{
				$input[$key] = call_user_func([$this, __METHOD__], $value);
			}
		}else{
			$input = preg_replace("~\r?\n+~s", '\r\n', $input);
		}
		return $input;
	}
	function show()
	{
		if(!$this->init) return false;
		$output = $this->action($this->name);
		if($this->table_id && $this->is_updated)
		{
			$q = "SELECT * FROM ".$this->table." WHERE ".$this->primary."=".$this->table_id;
			$this->default = $this->db->getRow($q);
			$this->default[$this->name] = $this->db->Affected_rows() ? $this->default[$this->name] : array();
			$this->default[$this->name] = $this->clean_newline($this->default[$this->name]);
			$this->default[$this->name] = @json_decode($this->default[$this->name], 1);
			if($this->text_id)
			{
				$q = "SELECT * FROM ".$this->table."_text WHERE $this->text_id=$this->table_id";
				$r = $this->db->getAll($q);
				foreach($r AS $d)
				{
					$lang_id = $d['lang_id'];
					foreach($d AS $f => $v)
					{
						if($f != 'lang_id' && $f != $this->text_id)
							$this->default[$f][$lang_id] = $v;
					}
				}
			}
			if($this->is_encode)
			{
				$this->default[$this->name] = urldecode_r($this->default[$this->name]);
			}
		}
		ob_start();
		$this->show_param($this->config_pre, $this->default, '');
		$this->show_param($this->config, $this->default[$this->name], $this->name);
		$this->show_param($this->config_post, $this->default, '');
		if ($this->isFormRequire)
		{
			link_js(_LIB.'pea/includes/formIsRequire.js');
		}
		$form = ob_get_contents();
		ob_end_clean();
		$title = lang($this->title);
		if (!empty($_GET['return']))
		{
			if (!empty($title))
			{
				$GLOBALS['sys']->nav_add($title);
			}
			$footer =	$GLOBALS['sys']->button($_GET['return']);
		}else $footer = '';
		$cls = $this->isFormRequire ? ' class="formIsRequire"' : '';
		$output .='
<form action="" method="post" enctype="multipart/form-data" role="form"'.$cls.'>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">'.$title.'</h3>
		</div>
		<div class="panel-body">
			'.$form.'
		</div>
		<div class="panel-footer">
			'.$footer.'
			<button type="submit" name="params_'.$this->table.'_submit" value="'.lang('SAVE')
			.	'" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-floppy-disk"></span> '. lang('SAVE') .'</button>
			<button type="reset" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-repeat"></span> '.lang('Reset').'</button>
		</div>
	</div>
</form>
';
		return $output;
	}

	function show_param($arr, $config = array(), $name = 'params')
	{
		$i = $this->row_count;
		if(is_array($arr) AND count($arr) > 0)
		{
			$img = _class('images', _URL.'images/');
			foreach($arr AS $id => $data)
			{
				$cls           = 'form-group';
				$text          = (!empty($data['text'])) ? $data['text'] : ucwords($id);
				// $text         .= !empty($data['mandatory']) ? ' <small style="font-weight: lighter;">*</small>' : '';
				$data['attr'] .= !empty($data['mandatory']) ? ' req="'.$data['checked'].' true"' : ($data['checked']!='any' ? 'req="'.$data['checked'].' false"' : '');
				if (!$this->isFormRequire && $data['type']!='plain' && (!empty($data['mandatory']) || $data['checked']!='any'))
				{
					$this->isFormRequire = true;
				}

				if(isset($data['force'])) $value = $data['force'];
				else $value= isset($config[$id]) ? $config[$id] : @$data['default'];
				$input= $opt = '';
				switch($data['type'])
				{
					case 'radio':
						$cls = 'radio';
						$out = array();
						foreach((array)$data['option'] AS $key => $val)
						{
							if(empty($opt))	$opt = ($key!=0) ? 'key' : 'value';
							$key = ($opt == 'key') ? $key : $val;
							$select	= ($key==$value) ? ' checked="checked"' : '';
							$out[] = '<label><input type="radio" name="'.$this->set_field_name($name, $id).'" value="'.htmlentities($key).'" id="'.$id.$key.'"'.$select.$data['attr'].' />'.$val.'</label>';
						}
						$input .= implode($data['delim'], $out);
					break;
					case 'select':
						if(!empty($data['is_arr']))
						{
							$add =  '[]';
							$data['attr'] .= ' multiple="multiple"';
							$value = is_array($value) ? $value : repairExplode($value, $data['delim']);
						}else{
							$add = '';
						}
						$input .= '<select id="'.$this->set_field_name($name, $id).$add.'" name="'.$this->set_field_name($name, $id).$add.'"'.$data['attr'].' class="form-control">'.createOption($data['option'], $value).'</select>';
						if(isset($data['is_arr']) && $data['is_arr'])
						{
							if (!empty($data['add']))
							{
								$data['add'] = '<div class="input-group-addon">'.$data['add'].'</div>';
							}
							$input = '<div class="input-group">'.$input.'<div class="input-group-addon"><input type="checkbox" onclick="var v=$(this).parent().prev().get(0);for(i=0; i < v.options.length; i++)v.options[i].selected = this.checked;"></div>'.@$data['add'].'</div>';
							unset($data['add']);
						}
					break;
					case 'checkbox':
						$cls = 'checkbox';
						if(count($data['option']) > 1)
						{
							$out = array();
							$value	= is_array($value) ? $value : array($value);
							foreach((array)$data['option'] AS $key => $val)
							{
								$select	= in_array($key, $value) ? ' checked="checked"' : '';
								$out[] = '<label><input type="checkbox" name="'.$this->set_field_name($name, $id).'[]" value="'.$key.'" id="'.$id.$key.'"'.$select.$data['attr'].' /> '.$val.'</label>';
							}
							if (empty($data['delim']))
							{
								$data['delim'] = ' ';
							}
							$input .= implode(@$data['delim'], $out);
						}else{
							$data['option']	= is_array($data['option']) ? $data['option'] : array(1 => $data['option']);
							list($key, $val)= each($data['option']);
							$select	= $key==$value ? ' checked="checked"' : '';
							$input .= '<label><input type="checkbox" name="'.$this->set_field_name($name, $id).'" value="'.htmlentities($key).'" id="'.$id.$key.'"'.$select.$data['attr'].' /> '.$val.'</label>';
						}
					break;
					case 'custom':
					case 'text':
						if($data['language'])
						{
							$r_tmp = array();
							if (!is_array($value))
							{
								$tmp = array();
								foreach ($this->lang_r as $l)
								{
									$tmp[$l['id']] = $value;
								}
								$value = $tmp;
							}
							foreach((array)$this->lang_r AS $l)
							{
								$r_tmp[]= '<input type="text" name="'.$this->set_field_name($name, $id.']['.$l['id']).'" value="'.@htmlentities($value[$l['id']]).'" title="'.$l['title'].'" class="form-control"'.$data['attr'].' />';
							}
							$input .= implode('<br />', $r_tmp);
						}else
							$input .= '<input type="text" name="'.$this->set_field_name($name, $id).'" value="'.htmlentities($value).'" class="form-control"'.$data['attr'].' />';
					break;
					case 'textarea':
						if ($data['language'] && !is_array($value))
						{
							$tmp = array();
							foreach ($this->lang_r as $l)
							{
								$tmp[$l['id']] = $value;
							}
							$value = $tmp;
						}
						if($data['format'] == 'html' || $data['format'] == 'code')
						{
							_func('editor');
							$data['attr'] = is_array($data['attr']) ? $data['attr'] : array('attr' => $data['attr']);
						}
						if($data['format'] == 'code')
						{
							if($data['language'])
							{
								$values = array();
								foreach($this->lang_r AS $d)
								{
									$values[$d['id']] = array($d['title'], (isset($value[$d['id']]) ? $value[$d['id']] : '' ));
								}
							}else{
								$values = $value;
							}
							$input .= editor_code($this->set_field_name($name, $id), $values, $data['attr']);
						}else{
							if($data['language'])
							{
								$r_tmp = array();
								foreach((array)$this->lang_r AS $l)
								{
									if($data['format'] == 'html')	$r_tmp[$l['title']]= editor_html($this->set_field_name($name, $id.']['.$l['id']), $value[$l['id']], $data['attr']);
									else	$r_tmp[$l['title']]= '<textarea name="'.$this->set_field_name($name, $id.']['.$l['id']).'" class="form-control"'.$data['attr'].' />'.@htmlentities($value[$l['id']]).'</textarea>';
								}
								$input .= tabs($r_tmp);
							}else{
								if($data['format'] == 'html')	$input .= editor_html($this->set_field_name($name, $id), $value, $data['attr']);
								else $input .= '<textarea name="'.$this->set_field_name($name, $id).'" class="form-control"'.$data['attr'].' />'.htmlentities($value).'</textarea>';
							}
						}
					break;
					case 'files':
						$data['path']	= $this->repair_path(@$data['path']);
						$data['max']	= ($data['max'] > 0) ? intval($data['max']) : $this->def_files_tot;
						$img->setPath($data['path']);
						$r = repairExplode($value, $this->delim_file);
						$count_r			= count($r);
						$files_i			= 0;
						$files_field	= $this->set_field_name($name, $id);
						$files_prefix	= $this->table.'_'.$name.'_'.$id;
						$input .= '<div id="'.$files_prefix.'_document_id">';
						for($i=0; $i < $data['max']; $i++ )
						{
							$files_i= $i+1;
							$images	= $image = '';
							if(isset($r[$i]))
							{
								$image = $r[$i];
								$img->setimages($image);
								$images = $img->show();
							}
							link_js('includes/system/params.js', false);
							$style = (!empty($images) || $i == $count_r) ? '' : ' style="display: none;"';
							$input .= '<div id="'.$files_prefix.$files_i.'ID"'.$style.'>';
							$input .= '<input type="file" name="'.$files_field.'['.$files_i.']" onChange="addFiles(\''.$files_field.'\', '.$files_i.', \''.$files_prefix.'\');" class="form-control">';
							if(!empty($images))
							{
								$files_text	= $image.' ('.@round(filesize($data['path'].$image)/1000).' Kb)';
								$input .= ' -&gt; '.tip($files_text, $images);
							}
							if(is_file($data['path'].$image))
							{
								$input .= '<label><input type="checkbox" name="_delete'.$files_field.'['.$files_i.']" value="1"> Delete File</label>';
							}
							$input .= '</div>';
						}
						$input .= '</div><script type="text/JavaScript">var '.$files_prefix.'_max = '.$data['max'].';</script>';
					break;
					case 'file':
						$data['path']	= $this->repair_path(@$data['path']);
						$images = image($data['path'].$value, '', ' class="img-responsive img-thumbnail"');
						if(!empty($images))
						{
							$input .= '<br />'.$images.'<br />';
						}
						if(is_file($data['path'].$value))
						{
							$input .= $value.' ('.round(filesize($data['path'].$value)/1000).' Kb) '
										 .	'<label><input type="checkbox" name="_delete'.$this->set_field_name($name, $id).'" value="1"> Delete File</label><br />';
							$data['attr'] = preg_replace('~(\s?req=".*?")~', '', $data['attr']);
						}
						$input .= '<input type="file" name="'.$this->set_field_name($name, $id).'" value="" class="form-control"'.$data['attr'].' />';
					break;
					case 'captcha':
						$input .= '<div class="g-recaptcha" data-sitekey="6LdjLAkTAAAAAJi4m2cz_8akfTE0rDT0jd5pKO2n"></div><script src="https://www.google.com/recaptcha/api.js"></script>';
						if (!empty($data['add']))
						{
							$input .= '<div class="input-group-addon">'.$data['add'].'</div>';
							unset($data['add']);
						}
					break;
					case 'plain':
						$input .= (!empty($data['add'])) ? $value : '<div class="input-group">'.$value.'</div>';
					break;
					case 'hidden':	break;
					default:
						$input .= 'UNSUPPORTED INPUT TYPE...';
					break;
				}
				if($data['type'] != 'hidden')
				{
					if(!empty($data['help']))
					{
						$text .= ' '.help('<span style="font-weight: lighter;">'.$data['help'].'</span>');
					}
					$add = !empty($data['tips']) ? '<p class="help-block">'.$data['tips'].'</p>' : '';
					if ($cls != 'form-group')
					{
						if(isset($data['add']))
						{
							if (!empty($data['add']))
							{
								$input .= '<div class="input-group-addon">'.$data['add'].'</div>';
							}
							$input = '<div class="input-group">'.$input.'</div>';
						}
						echo '<div class="form-group"><label>'.$text.'</label><div class="'.$cls.'">'.$input.'</div>'.$add.'</div>';
					}else{
						if (!empty($data['add']))
						{
							$input .= '<div class="input-group-addon">'.$data['add'].'</div>';
							$input = '<div class="input-group">'.$input.'</div>';
						}
						echo '<div class="'.$cls.'"><label>'.$text.'</label>'.$input.$add.'</div>';
					}
				}
			}
			$this->row_count = $i;
		}
	}

	function action($name)
	{
		$output = '';
		$this->is_updated = false;
		if($name == $this->name)
		{
			$q = "SELECT * FROM ".$this->table." WHERE ".$this->primary."=".$this->table_id;
			$this->default = $this->db->getRow($q);
			$this->table_id = @intval($this->default[$this->primary]);
			$this->default[$this->name] = $this->db->Affected_rows() ? $this->default[$this->name] : array();
			$this->default[$this->name] = $this->clean_newline($this->default[$this->name]);
			$this->default[$this->name] = @json_decode($this->default[$this->name], 1);
			if($this->is_encode)
			{
				$this->default[$this->name]	= urldecode_r($this->default[$this->name]);
			}
			if($this->text_id)
			{
				$q = "SELECT * FROM ".$this->table."_text WHERE ".$this->text_id."=".$this->table_id;
				$r = $this->db->getAll($q);
				foreach($r AS $d)
				{
					$lang_id = $d['lang_id'];
					foreach($d AS $f => $v)
					{
						if($f != 'lang_id' && $f != $this->text_id)
							$this->default[$f][$lang_id] = $v;
					}
				}
			}

			if(isset($_POST['params_'.$this->table.'_submit']))
			{
				$fields = array();
				$this->is_updated = true;
				$msg	= '';
				/*==================================
				 * PROSES DULU CAPTCHA NYA JIKA ADA
				 * LALU DI HAPUS AGAR TIDAK DI PROSES
				 *================================*/
				$r_config = array($this->config, $this->config_pre, $this->config_post);
				foreach ($r_config as $i => $config)
				{
					foreach ($config as $name => $param)
					{
						if (strtolower($param['type']) == 'captcha')
						{
							$c = _lib('captcha');
							if(!$c->Validate())
							{
								$this->is_updated = false;
								$msg .= lang($param['text']).lang(' must not empty').'<br />';
							}
							unset($r_config[$i][$name]);
							break;
						}
					}
					if (!$this->is_updated) {
						break;
					}
				}
				$tmp    = array();
				$tmp[2] = $this->action_query($fields, $r_config[0], $this->default[$this->name], $this->name);
				$tmp[1] = $this->action_query($fields, $r_config[1], $this->default);
				$tmp[3] = $this->action_query($fields, $r_config[2], $this->default);
				$msg   .= $tmp[1].$tmp[2].$tmp[3].$this->action_func($this->pre_func);
				if($this->is_updated)
				{
					$query = $query_text = array();
					foreach($fields AS $field => $value)
					{
						if(is_array($value))
						{
							foreach($value AS $l_id => $v)
							{
								$query_text[$l_id][] = "`$field`='$v'";
							}
						}else{
							$value = $this->clean_newline($value);
							$query[] = "`{$field}`='{$value}'";
						}
					}
					$set_query = implode(', ', $query);
					$success   = false;
					if($this->table_id)
					{
						$q = "UPDATE ".$this->table." SET $set_query WHERE ".$this->primary."=".$this->table_id;
						$success = $this->db->Execute($q);
					} else {
						$q = "INSERT INTO ".$this->table." SET $set_query";
						$success = $this->db->Execute($q);
						$this->table_id = $this->db->Insert_ID();
					}
					if($success && count($query_text) > 0 && $this->text_id)
					{
						$q = "SELECT lang_id FROM ".$this->table."_text WHERE ".$this->text_id."=".$this->table_id;
						$lang_ids = $this->db->getCol($q);
						foreach($query_text AS $lang_id => $r)
						{
							if(in_array($lang_id, $lang_ids))
							{
								$q = "UPDATE ".$this->table."_text SET ".implode(',', $r)." WHERE lang_id=$lang_id AND ".$this->text_id."=".$this->table_id;
							}else{
								$q = "INSERT INTO ".$this->table."_text SET ".implode(',', $r).", lang_id=$lang_id, ".$this->text_id."=".$this->table_id;
							}
							$this->db->Execute($q);
						}
					}
					$msg .= $this->action_func($this->post_func);
					if($success) $output = msg('Success to Update Data.');
					else $output = msg('Failed to Update Data.');
					$this->is_updated = $success;
				}
				if(!empty($msg) && !$this->is_updated)
				{
					$output = msg($msg, 'danger');
					$this->default = array_merge($this->default, $_POST);
				}
			}
		}
		return $output;
	}
	function action_query(&$post_out, $config, $default, $prefix = '')
	{
		$output = '';
		/*==================================
		 * PROSES SEMUA FIELDS KECUALI CAPTCHA
		 * (KECUALI JIKA ADA CAPTCHA LEBIH DARI SATU)
		 *================================*/
		if(is_array($config) && $this->is_updated)
		{
			$post = array();
			foreach((array)$config AS $name => $param)
			{
				if(empty($default[$name])) $default[$name] = @$param['default'];
				switch($param['type'])
				{
					case 'files':
						if($this->is_updated)
						{
							$post[$name]	= array();
							$param['path']= $this->repair_path(@$param['path']);
							$file_from		= repairExplode($default[$name], $this->delim_file);
							$file_name 		= empty($prefix) ? $_FILES[$name] : $_FILES[$prefix][$name];
							$is_del				= empty($prefix) ? $_POST['_delete'.$name] : $_POST['_delete'.$prefix][$name];
							$tmp_id 			= 0;
							foreach((array)$file_name['name'] AS $id => $file)
							{
								if(isset($is_del[$id]) && $is_del[$id] == '1')
								{
									@chmod($param['path'].$file_from[$tmp_id], 0777);
									@unlink($param['path'].$file_from[$tmp_id]);
								}else
								if(empty($file))
								{
									$post[$name][] = @$file_from[$tmp_id];
								}else{
									if(empty($prefix))
									{
										$tmp_file = $this->do_upload($param['path'], $_FILES[$name], $id);
									} else {
										$tmp_file = $this->do_upload($param['path'], $_FILES[$prefix][$name], $id);
									}
									$post[$name][] = empty($tmp_file) ? @$file_from[$tmp_id] : $tmp_file;
									if($tmp_file != $file_from[$tmp_id] && is_file($param['path'].$file_from[$tmp_id]))
									{
										@chmod($param['path'].$file_from[$tmp_id], 0777);
										@unlink($param['path'].$file_from[$tmp_id]);
									}
								}
								$tmp_id++;
							}
							$t_post = array();
							foreach($post[$name] AS $file)
							{
								if(is_file($param['path'].$file))
									$t_post[] = $file;
							}
							$post[$name] = repairImplode($t_post, $this->delim_file);
						}
						$param['checked'] = 'any';
					break;
					case 'file':
						if($this->is_updated)
						{
							$param['path'] = $this->repair_path(@$param['path']);
							$del = empty($prefix) ? @$_POST['_delete'.$name] : @$_POST['_delete'.$prefix][$name];
							if($del == '1')
							{
								@chmod($param['path'].$default[$name], 0777);
								@unlink($param['path'].$default[$name]);
								$post[$name] = '';
							}else{
								$file_name = empty($prefix) ? $_FILES[$name]['name'] : $_FILES[$prefix]['name'][$name];
								if(empty($file_name))
								{
									$post[$name] = $default[$name];
								} else {
									if(empty($prefix))
									{
										$out = $this->do_upload($param['path'], $_FILES[$name]);
									} else {
										$out = $this->do_upload($param['path'], $_FILES[$prefix], $name);
									}
									if($out)
									{
										$post[$name] = $out;
										if($post[$name] != $default[$name] && is_file($param['path'].$default[$name]))
										{
											@chmod($param['path'].$default[$name], 0777);
											@unlink($param['path'].$default[$name]);
										}
									}else{
										@unlink($param['path'].$default[$name]);
										$post[$name] = '';
									}
								}
							}
						}
						$param['checked'] = 'any';
					break;
					case 'plain':
					case 'hidden':	$post[$name] = $default[$name];	break;
					case 'captcha': // captcha tetap di check untuk jaga2 jika ada captcha lebih dari satu
						$post[$name] = (empty($prefix)) ? $_POST[$name] : $_POST[$prefix][$name];
						if(!empty($post[$name]))
						{
							$captcha = _lib('captcha');
							if (!$captcha->Validate($post[$name], $param['match'])) {
								$this->is_updated = false;
								$output .= lang('Incorrect').' '.$param['text'].'<br />';
							}
						}
					break;
					case 'checkbox':
						if(!is_array($param['option']) || count($param['option']) == 1)
						{
							$posting = (empty($prefix)) ? @$_POST[$name] : @$_POST[$prefix][$name];
							$post[$name] = !empty($posting) ? $posting : 0;
							break;
						}
					case 'text':
					case 'textarea':
						$post[$name] =(empty($prefix)) ? $_POST[$name] : $_POST[$prefix][$name];
						if($param['language'])
						{
							$last = ''; $t_p = array();
							$p = $post[$name];
							foreach($this->lang_r AS $l)
							{
								if(isset($p[$l['id']]) && !empty($p[$l['id']])) {
									$i_p	= $p[$l['id']];
									$last = $p[$l['id']];
								}else $i_p = $last;
								$t_p[$l['id']] = $i_p;
							}
							$post[$name] = $t_p;
						}
					break;
					case 'select':
						if($param['is_arr'])
						{
							if((empty($prefix)))
							{
								$_POST[$name] = is_array($_POST[$name]) ? $_POST[$name] : array();
								$_POST[$name] = repairImplode($_POST[$name], $param['delim']);
							}else{
								$_POST[$prefix][$name] = is_array($_POST[$prefix][$name]) ? $_POST[$prefix][$name] : array();
								$_POST[$prefix][$name] = repairImplode($_POST[$prefix][$name]);
							}
						}
					case 'custom':
					default:
						$post[$name] =(empty($prefix)) ? $_POST[$name] : $_POST[$prefix][$name];
					break;
				}
				if($param['type']!='plain' && $param['mandatory'] && empty($post[$name]))
				{
					$this->is_updated = false;
					$output .= $param['text'].lang(' must not empty').'<br />';
				}
				// VALIDATION...
				if($param['type'] == 'custom' || $param['type'] == 'captcha') {
					unset($post[$name]);
				}
				if($this->is_updated
				&& $param['type'] != 'custom'
				&& $param['type'] != 'plain'
				&& !empty($post[$name]))
				{
					$this->is_updated = $this->action_query_check($post[$name], @$param['checked']);
					if(!$this->is_updated) {
						$output .= $param['text'].lang(' is not a valid').' '.$param['checked'].'<br />';
					}
				}
			}
			if(empty($prefix)) {
				$post_out = array_merge($post_out, $post);
			}else{
				if($this->is_encode) {
					$post = urlencode_r($post);
				}
				if(!empty($post))
				{
					$post_out[$prefix] = json_encode($post);
				}
			}
		}
		return $output;
	}
	function action_query_check($field, $type)
	{
		if(is_array($field))
		{
			foreach($field AS $f) {
				return $this->action_query_check($f, $type);
			}
		}else{
			switch($type)
			{
				case 'email':
					$output = is_email($field);
				break;
				case 'url':
					$output = is_url($field);
				break;
				case 'phone':
					$output = is_phone($field);
				break;
				case 'number':
					$output = preg_match('#^[0-9]+$#s', $field);
				break;
				default:
					$output = true;
				break;
			}
			return $output;
		}
	}
	function action_func($func_name='')
	{
		$output = '';
		if(!empty($func_name) && $this->is_updated)
		{
			if(function_exists($func_name))
			{
				$output = call_user_func_array( $func_name, array(&$this) );
			}else{
				$output = lang('Function').' '.$func_name.' '.lang('is not exists');
			}
		}
		return $output;
	}

	function set_field_name($name, $id)
	{
		if(!empty($name)) return $name.'['.$id.']';
		else return $id;
	}
	function repair_path($path = '', $default = 'images/')
	{
		$path = !empty($path) ? $path : $default;
		if(preg_match('~^'.preg_quote(_URL, '~').'~s', $path))
			$path = preg_replace('~^'.preg_quote(_URL, '~').'~s', '', $path);
		if(!preg_match('~^'.preg_quote(_ROOT, '~').'~s', $path))
			$path = _ROOT.$path;
		if(!file_exists($path))
		{
			_func('path');
			path_create($path);
		}
		return $path;
	}
	function do_upload($path, $files, $name = '')
	{
		$output = false;
		$ftemp  = empty($name) ? $files['tmp_name'] : $files['tmp_name'][$name];
		$fname  = empty($name) ? $files['name'] : $files['name'][$name];
		if($this->do_upload_ok($fname))
		{
			if (is_uploaded_file($ftemp))
			{
				$output = $this->do_upload_check($path, $fname);
				if (move_uploaded_file($ftemp, $path.$output))
				{
					@chmod($path.$output, 0777);
				}else $output = false;
			}
		}
		return $output;
	}
	function do_upload_ok($name)
	{
		$restricted = array('php','phps','php3','php4','phtml','pl','p');
		preg_match('~\.([a-z0-9]+)[^\.]{0,}$~is', $name, $match);
		return (!empty($match[1]) && !in_array(strtolower($match[1]), $restricted));
	}
	function do_upload_check($path, $name)
	{
		if(!is_file($path.$name)) {
			return $name;
		}else{
			$ext	= strrchr($name, '.');
			$name = rand().$ext;
			return $this->do_upload_check($path, $name);
		}
	}
}
/*
$config = array(
	'characters' => array(
		'text'      => 'Sample Text Input',
		'tips'      => 'text to display under the input in small font-size',
		'add'       => 'additional text after the input',
		'help'      => 'popup tips to display right after the title',
		'type'      => 'text', // type of input
		'language'  => true, 	// is input support multiple language, default value is false
		'attr'      => ' size="40"', // additional attribute for the input
		'default'   => 'insert default value',
		'mandatory' => 1, // is this field must be filled in (compulsory). Eg. 1 or 0
		'checked'   => 'any'	// validate input before it save in database eg. 'any' || 'email' || 'url' || 'phone' || 'number' default is 'any'
		),
	'plain' => array(
		'text'    => 'Sample Plaintext',
		'tips'    => 'text to display under the input in small font-size',
		'add'     => 'additional text after the input',
		'help'    => 'popup tips to display right after the title',
		'type'    => 'plain',
		'default' => 'this is the text'
		),
	'radio' => array(
		'text'      => 'Sample Radio input',
		'tips'      => 'text to display under the input in small font-size',
		'add'       => 'additional text after the input',
		'help'      => 'popup tips to display right after the title',
		'type'      => 'radio',
		'delim'     => "<br />\n",
		'option'    => array('yes', 'no'),
		'default'   => '0',
		'mandatory' => 1, // is this field must be filled in (compulsory). Eg. 1 or 0
		'checked'   => 'any'	// validate input before it save in database eg. 'any' || 'email' || 'url' || 'phone' || 'number' default is 'any'
		),
	'select' => array(
		'text'      => 'Sample Select input',
		'tips'      => 'text to display under the input in small font-size',
		'add'       => 'additional text after the input',
		'help'      => 'popup tips to display right after the title',
		'type'      => 'select',
		'is_arr'    => true,			// if this is true, user has multiple selection
		'option'    => array(1 => 'yes', 0 => 'no'),
		'default'   => 'no',
		'mandatory' => 0, // is this field must be filled in (compulsory). Eg. 1 or 0
		'checked'   => 'any'	// validate input before it save in database eg. 'any' || 'email' || 'url' || 'phone' || 'number' default is 'any'
		),
	'checkbox'	=> array(
		'text'      => 'Sample Checkbox input',
		'tips'      => 'text to display under the input in small font-size',
		'add'       => 'additional text after the input',
		'help'      => 'popup tips to display right after the title',
		'type'      => 'checkbox',
		'delim'     => "<br />\n",
		'option'    => array(1 => 'yes', 0 => 'no'), // leave it empty or unset for one checkbox and value
		'default'   => 1,
		'mandatory' => 0, // is this field must be filled in (compulsory). Eg. 1 or 0
		'checked'   => 'any',	// validate input before it save in database eg. 'any' || 'email' || 'url' || 'phone' || 'number' default is 'any'
		),
	'checkbox2'	=> array(
		'text'      => 'Sample Checkbox with one option',
		'tips'      => 'text to display under the input in small font-size',
		'add'       => 'additional text after the input',
		'help'      => 'popup tips to display right after the title',
		'type'      => 'checkbox',
		'option'    => 'activate',
		'default'   => 1,
		'mandatory' => 0, // is this field must be filled in (compulsory). Eg. 1 or 0
		'checked'   => 'any'	// validate input before it save in database eg. 'any' || 'email' || 'url' || 'phone' || 'number' default is 'any'
		),
	'textarea'	=> array(
		'text'      => 'Sample textarea input',
		'tips'      => 'text to display under the input in small font-size',
		'add'       => 'additional text after the input',
		'help'      => 'popup tips to display right after the title',
		'type'      => 'textarea',
		'language'  => true, 			// is input support multiple language, default value is false
		'default'   => 'sfdghgfhg',// default value
		'mandatory' => 0,				// is this field must be filled in (compulsory). Eg. 1 or 0
		'format'    => 'none', 		// what format you want to use eg. none | code | html
		'checked'   => 'any'			// validate input before it save in database eg. 'any' || 'email' || 'url' || 'phone' || 'number' default is 'any'
		),
	'file'			=> array(
		'text'    => 'Sample input for single file',
		'tips'    => 'text to display under the input in small font-size',
		'add'     => 'additional text after the input',
		'help'    => 'popup tips to display right after the title',
		'type'    => 'file',
		'default' => 'sfdghgfhg',
		'path'    => 'images/uploads/'
		),
	'files'			=> array(
		'text'    => 'Sample input for multiple files',
		'tips'    => 'insert files to uploaded',
		'add'     => 'additional text after the input',
		'help'    => 'popup tips to display right after the title',
		'type'    => 'files',
		'default' => '',
		'max'     => '5',
		'path'    => 'images/upload'
		),
	'captcha'		=> array(
		'text'  => 'Sample captcha for validation',
		'tips'  => 'Insert shown code in image as validation',
		'add'   => 'additional text after the input',
		'help'  => 'popup tips to display right after the title',
		'type'  => 'captcha',
		'match' => 0 // '1' for Case Insensitive
		)
	);
//CHANGE 'default' TO 'force' IF IN EDIT CONDITION...
// SAMPLE VARIABLE TO INSERT...
$params = array(
	'title'				=> 'Header of form or title',
	'table'				=> 'bbc_content',				// what table to insert or edit
	'text_id'			=> 'bbc_content_text',	// leave it blank or unset if multi language is not in use
	'config_pre'	=> array(),							// this is the input fields to diplay before config fields (must be available in table of current database)
	'config'			=> $config,							// variable above
	'name'				=> 'params',						// what field name which is use in database table
	'config_post'	=> array(),							// this is the input fields to diplay after config fields (must be available in table of current database)
	'primary'			=> 'id',								// field name for primary field in database table, default value is id
	'id'					=> 0										// insert value for the primary field, if you leave it blank or 0 that would be Insert data
	);
# PS: PRIMARY FIELD OF TABLE MUST BE NAMED 'id' TO MAKE THIS CLASS WORK PROPERLY
$p = _class('params',$params);
echo $p->show();
*/
