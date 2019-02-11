<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

class bbcconfig
{
	var $table = 'bbc_config';
	var $default;
	var $config;
	var $name;
	var $title;
	var $config_id;
	var $module_id;
	var $init;
	function __construct($config = '', $name = '', $module_id = '', $title = 'Parameters')
	{
		global $db;
		$this->db	=& $db;
		$this->init = false;
		$params		= array(
		  'config'=> $config
		, 'name'	=> $name
		, 'title'	=> $title
		, 'id'		=> $module_id
		);
		$this->set($params);
	}
	function set($params)
	{
		global $sys;
		if(!is_array($params)) return false;
		$this->init		= true;
		$this->config	= $params['config'];
		$this->default= array();
		$this->name		= $params['name'];
		$this->title	= isset($params['title']) ? $params['title'] : 'Parameters';
		$this->module_id = (isset($params['id']) && is_numeric($params['id'])) ? $params['id'] : $sys->module_id;
	}
	function show()
	{
		if(!$this->init) return false;
		$output = $this->action($this->name);
		if($this->config_id && isset($_POST['config_'.$this->name.'_submit']))
		{
			$q = "SELECT params FROM $this->table WHERE id=$this->config_id";
			$this->default = config_decode($this->db->getOne($q));
		}
		ob_start();
		$this->show_param($this->config, $this->default, 'null', $this->name);
		$form = ob_get_contents();
		ob_end_clean();
		$output .='
<form action="" method="post" enctype="multipart/form-data" role="form">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">'.$this->title.'</h3>
		</div>
		<div class="panel-body">
			'.$form.'
		</div>
		<div class="panel-footer">
			<button type="submit" name="config_'.$this->name.'_submit" value="'.lang('SAVE').'"
			 class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-floppy-disk"></span> '. lang('SAVE') .'</button>
			<button type="reset" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-repeat"></span> RESET</button>
		</div>
	</div>
</form>
';
		return $output;
	}
	function action($name)
	{
		$output = '';
		if($name == $this->name)
		{
			global $sys;
			$q = "SELECT id, params FROM $this->table WHERE module_id=$this->module_id AND name='$this->name'";
			$d = $this->db->getRow($q);
			if($this->db->Affected_rows())
			{
				$this->config_id= @$d['id'];
				$this->default	= config_decode($d['params']);
			}else{
				$this->config_id = 0;
				$this->default	 = array();
			}
			if(isset($_POST['config_'.$this->name.'_submit']))
			{
				$post = array();
				unset($img);
				foreach((array)$this->config AS $name => $param)
				{
					switch(strtolower($param['type']))
					{
						case 'file':
							$param['path'] = isset($param['path']) ? $param['path'] : 'images/';
							if(isset($_POST['_delete'][$this->name][$name]) && @$_POST['_delete'][$this->name][$name] == '1')
							{
								if(!preg_match('~^'.addslashes(_ROOT).'~s', $param['path'])) $param['path'] = _ROOT.$param['path'];
								@chmod($param['path'].$this->default[$name], 0777);
								@unlink($param['path'].$this->default[$name]);
								$post[$name] = '';
							}else{
								if(!isset($img))
								{
									$img = _class('images');
									$img->setPath($param['path']);
								}else{
									$img->setPath($param['path']);
								}
								if(empty($_FILES[$this->name]['name'][$name]))
								{
									$post[$name] = @$this->default[$name];
								}else{
									$out = $img->upload_r($_FILES[$this->name], $name);
									if(!empty($out))
									{
										$post[$name] = $out[0];
										if($post[$name] != @$this->default[$name] && @is_file($img->root.$img->path.$this->default[$name]))
										{
											@unlink($img->root.$img->path.$this->default[$name]);
										}
									}else{
										$post[$name] = $this->default[$name];
									}
								}
							}
						break;
						case 'checkbox':
							$data['option'] = @$data['option'];
							if(!is_array($data['option']) || count($data['option']) == 1)
							{
								$post[$name] = isset($_POST[$this->name][$name]) ? $_POST[$this->name][$name] : 0;
								break;
							}
						default:
							$post[$name] = @$_POST[$this->name][$name];
						break;
					}
				}
				$params = config_encode($post);
				if($this->config_id > 0) {
					$q = "UPDATE $this->table SET params='$params' WHERE id=$this->config_id";
					$success = $this->db->Execute($q);
				} else {
					$q = "INSERT INTO $this->table SET params='$params', `name`='$this->name', module_id=$this->module_id";
					$success = $this->db->Execute($q);
					$this->config_id = $this->db->Insert_ID();
				}
				$sys->clean_cache();
				if($success) $output = msg('Success to Update Data.','info');
				else $output = msg('Failed to Update Data.','danger');
			}
		}
		return $output;
	}
	function show_param($arr, $config = array(), $form_title = 'Additional Parameter', $name = 'config')
	{
		global $sys, $Bbc;
		$i = 0;
		if(is_array($arr) AND count($arr) > 0)
		{
			if (empty($Bbc->bbcshowparams))
			{
				$Bbc->bbcshowparams = 1;
			}else{
				$Bbc->bbcshowparams++;
			}
			$panel_id = time().$Bbc->bbcshowparams;
			if ($form_title != 'null')
			{
				?>
				<div class="panel-group" id="title<?php echo $panel_id;?>">
					<div class="panel panel-default">
					  <div class="panel-heading">
					    <h4 class="panel-title" data-toggle="collapse" data-parent="#title<?php echo $panel_id;?>" href="#cfg<?php echo $panel_id;?>" style="cursor: pointer;">
					    	<?php echo $form_title; ?>
					    </h4>
					  </div>
					  <div id="cfg<?php echo $panel_id;?>" class="panel-collapse collapse in">
					  	<div class="panel-body">
				<?php
			}
			foreach($arr AS $id => $data)
			{
				$cls	= 'form-group';
				$text = (isset($data['text'])) ? $data['text'] : ucwords($id);
				$data['attr'] = isset($data['attr']) ? $data['attr'] : '';

				$type = strtolower($data['type']);
				$value= isset($config[$id]) ? $config[$id] : @$data['default'];
				$input= $opt = '';
				switch($type)
				{
					case 'radio':
						$cls = 'radio';
						$out = array();
						foreach((array)$data['option'] AS $key => $val)
						{
							if(empty($opt))	$opt = ($key!=0) ? 'key' : 'value';
							$key = ($opt == 'key') ? $key : $val;
							$select	= ($key==$value) ? ' checked="checked"' : '';
							$out[] = '<label><input type="radio" name="'.$name.'['.$id.']" value="'.htmlentities($key).'" id="'.$name.$id.$key.'"'.$data['attr'].$select.'> '.$val.'</label>';
						}
						if (empty($data['delim']))
						{
							$data['delim'] = ' ';
						}
						$input .= implode(@$data['delim'], $out);
					break;
					case 'select':
						if(isset($data['is_arr']) && $data['is_arr'])
						{
							$id .=  '][';
							$data['attr'] .= ' multiple="multiple"';
						}
						$input .= '<select id="'.$name.'['.$id.']" name="'.$name.'['.$id.']"'.$data['attr'].' class="form-control">'.createOption($data['option'], $value).'</select>';
						if(isset($data['is_arr']) && $data['is_arr'])
						{
							$input = '<div class="input-group">'.$input.'<div class="input-group-addon"><input type="checkbox" onclick="var v=$(this).parent().prev().get(0);for(i=0; i < v.options.length; i++)v.options[i].selected = this.checked;"></div></div>';
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
								$out[] = '<label><input type="checkbox" name="'.$name.'['.$id.'][]" value="'.$key.'" id="'.$name.$id.$key.'"'.$data['attr'].$select.'> '.$val.'</label>';
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
							$input .= '<label><input type="checkbox" name="'.$name.'['.$id.']" value="'.htmlentities($key).'" id="'.$name.$id.$key.'"'.$data['attr'].$select.'> '.$val.'</label>';
						}
					break;
					case 'htmlarea':
						_func('editor');
						$data['attr'] = is_array($data['attr']) ? $data['attr'] : array('attr' => $data['attr']);
						if(!isset($data['attr']['ToolbarSet'])) $data['attr']['ToolbarSet'] = 'Basic';
						$input .= editor_html($name.'['.$id.']', $value, $data['attr']);
					break;
					case 'codearea':
						_func('editor');
						$data['attr'] = is_array($data['attr']) ? $data['attr'] : array('attr' => $data['attr']);
						$input .= editor_code($name.'['.$id.']', $value, $data['attr']);
					break;
					case 'textarea':
						$input .= '<textarea name="'.$name.'['.$id.']"'.$data['attr'].' class="form-control">'.htmlentities($value).'</textarea>';
					break;
					case 'file':
						$data['path'] = isset($data['path']) ? $data['path'] : 'images/';
						$images = image($data['path'].$value);
						$input .= '<div class="clearfix"></div>';
						if(!empty($images))
						{
							$input .= $images.'<br />';
						}
						if(!preg_match('~^'.addslashes(_ROOT).'~s', $data['path']))
							$data['path'] = _ROOT.$data['path'];
						if(is_file($data['path'].$value))
						{
							$size = money(filesize($data['path'].$value)/1000).' kb';
							if (in_array(strtolower(substr($value, -4)), array('.jpg','.gif','.png','.bmp','.jpeg','.ico')))
							{
								list($width, $height) = getimagesize($data['path'].$value);
								if (!empty($height))
								{
									$value .= ' ('.money($width).' x '.money($height).' px)';
								}
							}
							$input .= $value.' &raquo; '.$size
										 .	' <label><input type="checkbox" name="_delete['.$name.']['.$id.']" value="1"> Delete File</label><br />';
						}
						$input .= '<input type="file" name="'.$name.'['.$id.']" value=""'.$data['attr'].' class="form-control">';
					break;
					case 'hidden':
						$input .= '<input type="hidden" name="'.$name.'['.$id.']" value="'.htmlentities($value).'" />';
						break;
					case 'text':
					default:
						$input .= '<input type="text" name="'.$name.'['.$id.']" value="'.htmlentities($value).'"'.$data['attr'].' class="form-control">';
						if($type != 'text')
							$input .= msg('Unsupported type input...', 'warning');
					break;
				}
				if ($type != 'hidden')
				{
					if(isset($data['help']))
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
			if ($form_title != 'null')
			{
				?>
							</div>
					  </div>
					</div>
				</div>
				<?php
			}
		}
	}
}
