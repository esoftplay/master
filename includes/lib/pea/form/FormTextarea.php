<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

class FormTextarea extends Form
{
	var $nl2br = true;
	var $isHtmlEditor = false;
	var $isCodeEditor = false;
	var $param;
	var $paramCode;
	var $lang_r;
	function __construct()
	{
		$this->type = 'textarea';
		$this->setSize();
		$this->setToolbar();
		$this->lang_r = lang_assoc();
	}

	function setSize( $rows = '2', $cols = '45' )
	{
		if(empty($this->param))
		{
			$this->param = new stdClass();
		}
		$this->param->Height = $rows;
		$this->param->Width = $cols;
	}

	function setNl2br( $nl2br = false )
	{
		$this->nl2br	= $nl2br;
		// $t = debug_backtrace();
		// var_dump($this->nl2br, $t[0]['file'].':'.$t[0]['line']);
		// pr($t[0]['file'].':'.$t[0]['line'], __FILE__.':'.__LINE__);
	}

	function getRollUpdateSQL( $i='' )
	{
		// pr($this->nl2br, $_POST[$this->name], __FILE__.':'.__LINE__);
		if ( $i == '' && !is_int($i) )
			$val = ( $this->nl2br ) ? nl2br( @$_POST[$this->name] ) : @$_POST[$this->name];
		else
			$val = ( $this->nl2br ) ? nl2br( @$_POST[$this->name][$i] ) : @$_POST[$this->name][$i];
		if(!$this->isHtmlEditor AND !$this->nl2br)
		{
			// $val = preg_replace("~<br\s{0,}/?".">~is", "", $val);
		}
		return $query = "`". $this->fieldName ."` = '". $this->cleanSQL($val) ."', ";
	}
	function setCodeEditor($bool_is_code_editor = true, $syntax= 'css' /*css|html|js|javascript|php|python|vb|xml|c|cpp|sql|basic|pas|brainfuck*/)
	{
		if ( $bool_is_code_editor )
		{
			$this->setNl2br( false );
			$this->paramCode['syntax'] = $syntax;
			$this->setSize('200px', '100%');
			$this->isHtmlEditor = false;
		}
		$this->isCodeEditor = $bool_is_code_editor;
	}
	function setParam($arr)
	{
		if(!empty($arr))
		{
			foreach($arr AS $i => $d)
			{
				$this->paramCode[$i] = $d;
			}
		}
	}
	// untuk ngeset apakah inputnya mau html editor ato enggak
	function setHtmlEditor( $bool_is_html_editor = true )
	{
		if ( $bool_is_html_editor )
		{
			$this->setNl2br( false );
			$this->setExtra( " style=\"width:100%\" " );
			$this->isCodeEditor = false;
		}
		$this->isHtmlEditor	= $bool_is_html_editor;
	}
	function setToolbar($config='Default')
	{
		$config = ($config != 'Default') ? 'Basic' : $config;
		$this->param->ToolbarSet = $config;
	}
	function getAddSQL()
	{
		$text = $_POST[$this->name];
		if($this->nl2br && !preg_match("#<br\s{0,}/?>#is", $text))
			$text = nl2br($text);
		$out['into']	= '`'.$this->fieldName .'`, ';
		$out['value']	= "'". $text ."', ";
		return $out;
	}

	function getEntityOutput( $str_value = '' )
	{
		if ( $this->nl2br )
		{
			$out	= preg_replace( "#<br\s{0,}/?>#is", "", $str_value );
		}else{
			$out	= $str_value;
		}
		$out	= htmlentities(preg_replace( "#\r\n#is", "\n", $out ));
		return $out;
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( $this->isPlaintext ) return $this->getPlaintexOutput( current((array)@$str_value), $str_name, $str_extra );

		$name	 = ( $str_name == '' ) ? $this->name : $str_name;
		$extra = $str_extra;
		$mlang = count($this->lang_r) > 1 ? true : false;
		$out 		= '';
		if($this->isCodeEditor)
		{
			_func('editor');
			foreach($this->param AS $i => $d)
			{
				$r[strtolower($i)] = $d;
			}
			$config = array_merge($r, $this->paramCode);
			if (!empty($extra))
			{
				$config['attr'] = $extra;
			}
			if($this->isMultiLanguage)
			{
				$values = array();
				foreach($this->lang_r AS $d)
				{
					$values[$d['id']] = array($d['title'], (isset($str_value[$d['id']])?$str_value[$d['id']]:''));
				}
				$out = editor_code($name, $values, $config);
			}else{
				$out = editor_code($name, $str_value, $config);
			}
		}else
		if ( $this->isHtmlEditor )
		{
			_func('editor');
			$this->param->Height = ($this->param->Height < 200 and $this->param->ToolbarSet != 'Basic') ? 200 : $this->param->Height;
			$this->param->Width  = ($this->param->ToolbarSet == 'Basic') ? $this->param->Width :'100%';
			$this->param->attr   = $extra;
			if($this->isMultiLanguage)
			{
				$r = array();
				foreach($this->lang_r AS $d)
				{
					$value = isset($str_value[$d['id']]) ? $str_value[$d['id']] : '';
					$r[$d['title']] = editor_html($name.'['.$d['id'].']', $value, $this->param);
				}
				$out = tabs($r, 0);
			}else{
				$out = editor_html($name, $str_value, $this->param);
			}
		}else{
			if($this->isMultiLanguage)
			{
				$r = array();
				foreach($this->lang_r AS $d)
				{
					$alt   = $mlang ? $d['title'] : $this->title;
					$value = isset($str_value[$d['id']]) ? $str_value[$d['id']] : '';
					$extra = preg_replace(array('~\s{0,}title=".*?"~is', '~\s{0,}placeholder=".*?"~is'), '', $extra);
					$r[$d['title']] = '<textarea name="'.$name.'['.$d['id'].']" id="'.$name.'['.$d['id'].']" cols="'.$this->param->Width.'" rows="'.$this->param->Height.'" '.$extra.' title="'.$alt.'" placeholder="'.$alt.'">'.$this->getEntityOutput($value).'</textarea>';
				}
				$out = implode('<br />', $r);
			}else{
				$out = '<textarea name="'. $name .'" id="'. $name .'" cols="'.$this->param->Width.'" rows="'.$this->param->Height.'" '.$extra.'>'. $this->getEntityOutput($str_value) .'</textarea>';
			}
		}
		return $out;
	}
}