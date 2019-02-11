<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

class xss {
	var $use_xss_clean	= TRUE;
	var $xss_hash				= '';
	var $is_load				= FALSE;
	var $name						= 'xss_clean';
	var $unprotected		= array();

	/* never allowed, string replacement */
	var $never_allowed_str = array(
				'information_schema'	=> ''
			,	'document.cookie'	=> '[removed]'
			,	'document.write'	=> '[removed]'
			,	'.parentNode'			=> '[removed]'
			,	'.innerHTML'			=> '[removed]'
			,	'window.location'	=> '[removed]'
			,	'-moz-binding'		=> '[removed]'
			,	'<!--'						=> '&lt;!--'
			,	'-->'							=> '--&gt;'
			,	'<![CDATA['				=> '&lt;![CDATA['
			);
	/* never allowed, regex replacement */
	var $never_allowed_regex = array(
				"javascript\s*:"	=> '[removed]'
			,	"expression\s*\("	=> '[removed]' // CSS and IE
			,	"Redirect\s+302"	=> '[removed]'
			,	'union\s+select.*?$'	=> ''
			,	'select.*?(?:[\s\`]|\*\/){1}from[\s\`].*?$'	=> ''
			,	'update.*?(?:[\s\`]|\*\/){1}set[\s\`].*?$'	=> ''
			,	'delete.*?(?:[\s\`]|\*\/){1}from[\s\`].*?$'	=> ''
			);
	var $protected = array(
				'_SERVER'
			, '_GET'
			, '_POST'
			, '_FILES'
			, '_REQUEST'
			, '_SESSION'
			, '_ENV'
			, 'HTTP_RAW_POST_DATA'
			);

	function __construct()
	{
		$this->_set_unprotected();
	}
	function _set_unprotected()
	{
		$unprotected = @$_SESSION[$this->name]['unprotected'];
		if(is_array($unprotected) && count($unprotected) > 0){
			$this->unprotected = array_merge_recursive($this->unprotected, $unprotected);
		}
		unset($_SESSION[$this->name]['unprotected']);
	}
	function set_unprotected($var )
	{
		$var = strtoupper($var);
		if(in_array($var, $this->protected))
		{
			$out = array($var => 'none');
			$j = func_num_args();
			$i = 1;
			if($j > 1)
			{
				$out = array();
				$o = '$out[\''.$var.'\']';
				while($i < $j)
				{
					$val = func_get_arg($i);
					$o .= "['$val']";
					$i++;
				}
				$o .= " = 'none';";
				eval($o);
			}
			if(!$this->is_load) {
				$this->unprotected = array_merge_recursive($this->unprotected, $out);
			}else{
				$_SESSION[$this->name]['unprotected'] = $out;
			}
		}
	}

	function action()
	{
		foreach ($this->protected as $global)
		{
			if ( !is_array($global))
			{
				$GLOBALS[$global] = $this->_clean_input_data($GLOBALS[$global], @$this->unprotected[$global]);
			}	else {
				$GLOBALS[$global] = array_map(array($this, '_clean_input_data'), $global, $this->unprotected);
			}
		}

		// these are not PHP variables
		unset($_COOKIE['$Version']);
		unset($_COOKIE['$Path']);
		unset($_COOKIE['$Domain']);
		$_COOKIE = $this->_clean_input_data($_COOKIE, @$this->unprotected[$global]);
		$this->is_load = true;
	}

	function _clean_input_data($str, $except = '')
	{
		if (is_array($str))
		{
			$new_array = array();
			foreach ($str as $key => $val)
			{
				$new_array[$this->_clean_input_keys($key)] = $this->_clean_input_data($val, @$except[$key]);
			}
			return $new_array;
		}
		if($except == 'none') return $str;

		// Should we filter the input data?
		if ($this->use_xss_clean === TRUE)
		{
			if (get_magic_quotes_gpc()) {
				$str = stripslashes($str);
			}
			$str = $this->xss_clean($str);
			if (get_magic_quotes_gpc())	{
				$str = addslashes($str);
			}
			// Standardize newlines
			if (strpos($str, "\r") !== FALSE) {
				$str = str_replace(array("\r\n", "\r"), "\n", $str);
			}
		}

		return $str;
	}

	function _clean_input_keys($str)
	{
		 if ( ! preg_match("/^[a-z0-9:_\/-\s]+$/i", $str))
		 {
			exit('Disallowed Key Characters : "'.$str.'"');
		 }

		return $str;
	}

	function xss_clean($str, $is_image = FALSE)
	{
		if (is_array($str))
		{
			while (list($key) = each($str))
			{
				$str[$key] = $this->xss_clean($str[$key]);
			}
			return $str;
		}

		// Remove Invisible Characters (0x1231323232) [pesenane mas ogi]
		$str = $this->_remove_invisible_characters($str);

		// Protect (901119URL5918AMP18930PROTECT8198)
		$str = preg_replace('|\&([a-z\_0-9]+)\=([a-z\_0-9]+)|i', $this->xss_hash()."\\1=\\2", $str);

		 // Validate character entities
		$str = preg_replace('#(&\#?[0-9a-z]{2,})[\x00-\x20]*;?#i', "\\1;", $str);

		 // Validate UTF16 two byte encoding (x00)
		$str = preg_replace('#(&\#x?)([0-9A-F]+);?#i',"\\1\\2;",$str);

		 // Un-Protect GET variables in URLs
		$str = str_replace($this->xss_hash(), '&', $str);

		// <a href="http://%77%77%77%2E%67%6F%6F%67%6C%65%2E%63%6F%6D">Google</a>
		$str = rawurldecode($str);

		// Convert character entities to ASCII
		$str = preg_replace_callback("/[a-z]+=([\'\"]).*?\\1/si", array($this, '_convert_attribute'), $str);

		$str = preg_replace_callback("/<\w+.*?(?=>|<|$)/si", array($this, '_html_entity_decode_callback'), $str);

		// Remove Invisible Characters Again
		$str = $this->_remove_invisible_characters($str);

		// Convert all tabs to spaces
 		if (strpos($str, "\t") !== FALSE)
		{
			$str = str_replace("\t", ' ', $str);
		}

		// Save for later
		$converted_string = $str;

		// Not Allowed Under Any Conditions
		foreach ((array)$this->never_allowed_str as $key => $val) {
			$str = str_replace($key, $val, $str);
		}

		foreach ((array)$this->never_allowed_regex as $key => $val) {
			$str = preg_replace("#".$key."#i", $val, $str);
		}

		// PHP tags safe
		if ($is_image === TRUE)	{
			$str = str_replace(array('<?php', '<?PHP'),  array('&lt;?php', '&lt;?PHP'), $str);
		}	else {
			$str = str_replace(array('<?php', '<?PHP', '<?', '?'.'>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $str);
		}

		// what if j a v a s c r i p t
		$words = array('javascript', 'expression', 'vbscript', 'script', 'applet', 'alert', 'document', 'write', 'cookie', 'window');
		foreach ($words as $word)
		{
			$temp = '';
			for ($i = 0, $wordlen = strlen($word); $i < $wordlen; $i++) {
				$temp .= substr($word, $i, 1)."\s*";
			}

			// "dealer to" does not become "dealerto"
			$str = preg_replace_callback('#('.substr($temp, 0, -3).')(\W)#is', array($this, '_compact_exploded_words'), $str);
		}
		do {
			$original = $str;
			if (preg_match("/<a/i", $str)) {
				$str = preg_replace_callback("#<a\s+([^>]*?)(>|$)#si", array($this, '_js_link_removal'), $str);
			}

			if (preg_match("/<img/i", $str)) {
				$str = preg_replace_callback("#<img\s+([^>]*?)(\s?/?>|$)#si", array($this, '_js_img_removal'), $str);
			}

			if (preg_match("/script/i", $str) OR preg_match("/xss/i", $str)) {
				$str = preg_replace("#<(/*)(script|xss)(.*?)\>#si", '[removed]', $str);
			}
		}
		while($original != $str);

		unset($original);

		// Remove JavaScript Event Handlers
		$event_handlers = array('[^a-z_\-]on\w*','xmlns');

		if ($is_image === TRUE) {
			unset($event_handlers[array_search('xmlns', $event_handlers)]);
		}

		$str = preg_replace("#<([^><]+?)(".implode('|', $event_handlers).")(\s*=\s*[^><]*)([><]*)#i", "<\\1\\4", $str);

		$naughty = 'alert|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|isindex|layer|link|meta|object|plaintext|style|script|textarea|title|video|xml|xss';
		$str = preg_replace_callback('#<(/*\s*)('.$naughty.')([^><]*)([><]*)#is', array($this, '_sanitize_naughty_html'), $str);
		$str = preg_replace('#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $str);

		foreach ($this->never_allowed_str as $key => $val) {
			$str = str_replace($key, $val, $str);
		}

		foreach ($this->never_allowed_regex as $key => $val) {
			$str = preg_replace("#".$key."#i", $val, $str);
		}
		if ($is_image === TRUE) {
			if ($str == $converted_string) {
				return TRUE;
			}	else {
				return FALSE;
			}
		}
		return $str;
	}

	function xss_hash()
	{
		if ($this->xss_hash == '')
		{
			if (phpversion() >= 4.2)
				mt_srand();
			else
				mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

			$this->xss_hash = md5(time() + mt_rand(0, 1999999999));
		}
		return $this->xss_hash;
	}

	function _remove_invisible_characters($str)
	{
		static $non_displayables;

		if ( ! isset($non_displayables))
		{
			// every control character except newline (dec 10), carriage return (dec 13), and horizontal tab (dec 09),
			$non_displayables = array(
				'/%0[0-8bcef]/'	// url encoded 00-08, 11, 12, 14, 15
			,	'/%1[0-9a-f]/'	// url encoded 16-31
			,	'/[\x00-\x08]/'	// 00-08
			,	'/\x0b/'				// 11
			, '/\x0c/'				// 12
			,	'/[\x0e-\x1f]/'	// 14-31
			);
		}

		do
		{
			$cleaned = $str;
			$str = preg_replace($non_displayables, '', $str);
		}
		while ($cleaned != $str);

		return $str;
	}

	function _compact_exploded_words($matches)
	{
		return preg_replace('/\s+/s', '', $matches[1]).$matches[2];
	}

	function _sanitize_naughty_html($matches)
	{
		// encode opening brace
		$str = '&lt;'.$matches[1].$matches[2].$matches[3];

		// encode captured opening or closing brace to prevent recursive vectors
		$str .= str_replace(array('>', '<'), array('&gt;', '&lt;'), $matches[4]);

		return $str;
	}

	function _js_link_removal($match)
	{
		$attributes = $this->_filter_attributes(str_replace(array('<', '>'), '', $match[1]));
		return str_replace($match[1], preg_replace("#href=.*?(alert\(|alert&\#40;|javascript\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si", "", $attributes), $match[0]);
	}

	function _js_img_removal($match)
	{
		$attributes = $this->_filter_attributes(str_replace(array('<', '>'), '', $match[1]));
		return str_replace($match[1], preg_replace("#src=.*?(alert\(|alert&\#40;|javascript\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si", "", $attributes), $match[0]);
	}

	function _convert_attribute($match)
	{
		return str_replace(array('>', '<'), array('&gt;', '&lt;'), $match[0]);
	}

	function _html_entity_decode_callback($match)
	{
		return $this->_html_entity_decode($match[0], 'UTF-8'); // pesanannya mas ayik...
	}

	// with or without semicolons
	function _html_entity_decode($str, $charset='UTF-8')
	{
		if (stristr($str, '&') === FALSE) return $str;

		if (function_exists('html_entity_decode') && (strtolower($charset) != 'utf-8' OR version_compare(phpversion(), '5.0.0', '>=')))
		{
			$str = html_entity_decode($str, ENT_COMPAT, $charset);
			$str = preg_replace('~&#x(0*[0-9a-f]{2,5})~ei', 'chr(hexdec("\\1"))', $str);
			return preg_replace('~&#([0-9]{2,4})~e', 'chr(\\1)', $str);
		}

		// Numeric Entities
		$str = preg_replace('~&#x(0*[0-9a-f]{2,5});{0,1}~ei', 'chr(hexdec("\\1"))', $str);
		$str = preg_replace('~&#([0-9]{2,4});{0,1}~e', 'chr(\\1)', $str);

		// Literal Entities - Slightly slow so we do another check
		if (stristr($str, '&') === FALSE) {
			$str = strtr($str, array_flip(get_html_translation_table(HTML_ENTITIES)));
		}

		return $str;
	}

	function _filter_attributes($str)
	{
		$out = '';

		if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches))
		{
			foreach ($matches[0] as $match) {
				$out .= "{$match}";
			}
		}
		return $out;
	}
}
$xss = new xss();
$xss->action();
//$xss->set_unprotected('_POST', 'var_name', 'var_index_1', 'var_index_2');
?>
