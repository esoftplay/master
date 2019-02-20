<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*=============================================
 * sample $config = array(
 *									'toolbar'         => 'Basic' || 'Full'
 *								, 'DefaultLanguage' => 'en'
 *								, 'path'            => 'images/modules/content' -> path to upload images if empty it will go to "images/uploads"
 *								, 'width'           => '100%'
 *								, 'height'          => '120px'
 *								);
/*============================================*/
function editor_html($name, $value = '', $config = array(), $is_inline = false)
{
	global $sys;
	$sys->link_js(_URL.'includes/lib/ckeditor/ckeditor.js', false);
	ob_start();
	if(is_object($config))
	{
		$config = get_object_vars($config);
	}
	if(!empty($config['ToolbarSet']))
	{
		$config['toolbar'] = $config['ToolbarSet'];
		unset($config['ToolbarSet']);
	}
	if(!empty($config['Config']))
	{
		$config = array_merge($config, $config['Config']);
		unset($config['Config']);
	}
	$func   = $is_inline ? 'inline' : 'replace';
	$value2 = htmlentities($value, ENT_COMPAT, 'UTF-8', FALSE);
	if (!empty($value2))
	{
		$value = $value2;
	}
	$attr = '';
	if (!empty($config['attr']))
	{
		$attr = $config['attr'];
		unset($config['attr']);
	}
	$attr .= ' rel="ckeditor"';
	if (!empty($config['path']))
	{
		$path = str_replace(array(_URL, _ROOT), array('',''), $config['path']);
		if (!empty($path) && is_dir(_ROOT.$path))
		{
			$config['path'] = $path;
			$attr          .= ' data-path="'.$path.'"';
		}else unset($config['path']);
	}
	?>
	<textarea id="<?php echo $name;	?>" name="<?php echo $name;	?>"<?php echo $attr; ?>><?php echo $value;?></textarea>
	<script type="text/javascript"> CKEDITOR.<?php echo $func;?>('<?php echo $name;?>',<?php echo json_encode($config); ?>); </script>
	<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
/*=============================================
 * sample:
	$name = array(
		'id'         => 'input.name',
		'fullscreen' => false,
		'word_wrap'  => false,
		'syntax'     => 'php',
		'syntaxes'   => 'php,html,javascript,css',
		);
/*============================================*/
function editor_code($name, $value = '', $option = array(), $meta = true)
{
	$output = '';
	/* SET DEFAULT CONFIG */
	$params = array(
		'id'         => '',
		'fullscreen' => false,
		'word_wrap'  => false,
		'syntax'     => 'html'
		);
	if(is_array($name)) $params = array_merge($params, $name);
	else $params['id'] = $name;
	$attr = '';
	if (!empty($option['attr']))
	{
		$attr = ' '.trim($option['attr']);
		unset($option['attr']);
	}
	$params = array_merge($params, (array)$option);

	// Dulu syntax 'js' sekarang diubah menjadi 'javascript'
	if ($params['syntax'] == 'js')
	{
		$params['syntax'] = 'javascript';
	}
	// Script lawas masih menerima 'syntax_selection_allow' belum menggunakan 'syntaxes' diubah semua ke syntaxes
	if (!empty($params['syntax_selection_allow']) && empty($params['syntaxes']))
	{
		$params['syntaxes'] = $params['syntax_selection_allow'];
	}
	if (!empty($params['syntaxes']))
	{
		$params['syntaxes'] = array_map('trim', explode(',', $params['syntaxes']));
	}else{
		$params['syntaxes'] = array($params['syntax']);
	}

	/* PENENTUAN CONFIG */
	$cfg = array(
		'theme' => 'ace/theme/dreamweaver',
		'mode'  => 'ace/mode/'.$params['syntax'],
		'wrap'  => $params['word_wrap'] ? true : false,
		);
	if (!empty($params['theme']))
	{
		$params['theme'] = 'ace/theme/'.$params['theme'];
	}
	$config = array_merge($cfg, $params);
	$excl   = [
		'allow_toggle',
		'attr',
		'begin_toolbar',
		'EA_load_callback',
		'height',
		'id',
		'is_multi_files',
		'language',
		'show_line_colors',
		'start_highlight',
		'syntax',
		'syntax_selection_allow',
		'syntaxes',
		'toolbar',
		'word_wrap'
	]; // bersihkan key untuk script lawas
	foreach ($excl as $exc)
	{
		unset($config[$exc]);
	}

	/*
	JS Custom:
	- emmet.js
	- ext-options.js
	- ext-settings_menu.js
	- mode-php.js
	- mode-php_laravel_blade.js
	- theme-esoftplay.js

	*/
	link_js(_FUNC.'js/editor/ace.js', $meta);
	link_js(_FUNC.'js/editor/emmet.js', $meta);
	link_js(_FUNC.'js/editor/init.js', $meta);
	link_js(_FUNC.'js/editor/ext-emmet.js', $meta);
	link_js(_FUNC.'js/editor/ext-language_tools.js', $meta);
	link_js(_FUNC.'js/editor/ext-settings_menu.js', $meta);
	global $Bbc;
	if (empty($Bbc->editor_code_id))
	{
		$Bbc->editor_code_id = 0;
	}
	$index = menu_save($params['id']);
	if (is_array($value))
	{
		$tabs = array();
		foreach ($value as $key => $val)
		{
			$name = $index.$key;
			$Bbc->editor_code_id++;
			$tabs[$key] = '<textarea id="input_'.$name.'" name="'.$params['id'].'['.$key.']" class="form-control" '.$attr.'>'.htmlentities($val, ENT_COMPAT, 'UTF-8', FALSE).'</textarea>'
							. '<div rel="editor_code" id="editor_'.$name.'"'
							.	' data-id="'.$name.'"'
							.	' data-syntax="'.$params['syntax'].'"'
							.	' data-syntaxes="'.htmlentities(json_encode($params['syntaxes'])).'"'
							.	' data-config="'.htmlentities(json_encode($config)).'"'
							.	' data-options="'.htmlentities(json_encode($option)).'"'
							.	'></div>'
							. '<script type="text/javascript">var editor'.$Bbc->editor_code_id.' = aceEditor(document.getElementById("editor_'.$name.'"));</script>';
		}
		$output .= tabs($tabs);
	}else{
		$Bbc->editor_code_id++;
		$output .= '<textarea id="input_'.$index.'" name="'.$params['id'].'" class="form-control" '.$attr.'>'.htmlentities($value, ENT_COMPAT, 'UTF-8', FALSE).'</textarea>'
						. '<div rel="editor_code" id="editor_'.$index.'"'
						.	' data-id="'.$index.'"'
						.	' data-syntax="'.$params['syntax'].'"'
						.	' data-syntaxes="'.htmlentities(json_encode($params['syntaxes'])).'"'
						.	' data-config="'.htmlentities(json_encode($config)).'"'
						.	' data-options="'.htmlentities(json_encode($option)).'"'
						.	'></div>'
						. '<script type="text/javascript">if(typeof aceEditor!="undefined"){var editor'.$Bbc->editor_code_id.' = aceEditor(document.getElementById("editor_'.$index.'"));}</script>';
	}
	return $output;
}