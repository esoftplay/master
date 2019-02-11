<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

include_once _CLASS.'getdirectory.php';
class xmlArray
{
	var $baseroot = _ROOT;
	var $baseurl	= _URL;
	var $tag			= 'dataXml';
	var $root;
	var $url;
	var $path;
	var $output;
	function __construct($path='')
	{
		$this->root = $this->baseroot;
		$this->url	= $this->baseurl;
		$this->setpath($path);
	}
	function setpath($path)
	{
		if(!empty($path)){
			if(preg_match('#^'.addslashes($this->baseroot).'#is', $path)){
				$path = str_replace($this->baseroot, '', $path);
			}
			if(preg_match('#^'.addslashes($this->baseurl).'#is', $path)){
				$path = str_replace($this->baseurl, '', $path);
			}
		}
		$this->path = $path;
		if(!empty($this->path))
		{
			$this->path = (substr($this->path, 0, 1)=='/') ? substr($this->path, 1) : $this->path;
			if(substr($this->path, -1)!='/'){
				$this->path .= '/';
			}
		}
		$this->url	= $this->baseurl.$this->path;
		$this->root = $this->baseroot.$this->path;
	}
	function xmlfetch($id = 0, $where= '*')
	{
		$file = $id.'.xml';
		$output = array();
		if(is_file($this->root.$file)){
			$xml	= $this->xmlGrab($file);
			$out	= $this->xml2array($xml);
			if($where == '*'){
				$output = $out;
			}else{
				$fields = explode(',', $where);
				foreach($fields AS $field){
					$id = trim($field);
					$output[$id] = $out[$id];
				}
			}
		}
		return $output;
	}
	function xmlGrab($file)
	{
		$output = '';
		$_file = $this->root.$file;
		if(is_file($_file)){
			$_f = fopen($_file, 'r+');
			$output = fread($_f,filesize($_file));
			fclose($_f);
		}
		return $output;
	}
	function xml2array($xml)
	{
		$arr = $this->xmlparsing($xml);
		$output = $this->arrayparsing($arr[$this->tag]);
		return $output;
	}
	function arrayparsing($arr)
	{
		if(is_array($arr['_c']) && count($arr['_c']) > 0){
			$output = array();
			foreach($arr['_c'] AS $id => $dt){
				if($id == 'itemXML'){
					foreach($dt AS $data){
						$output[] = $this->arrayparsing($data);
					}
				}else{
					if(is_array($dt['_c']) && count($dt['_c']) > 0){
						$output[$id] = $this->arrayparsing($dt);
					}else{
						$output[$id] = $this->unhtmlentities($dt['_v']);
					}
				}
			}
			return $output;
		}else{
			return $arr['_v'];
		}
		
	}
	function xmlparsing(&$string)
	{
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, $string, $vals, $index);
		xml_parser_free($parser);
		$mnary=array();
		$ary=&$mnary;
		foreach ($vals as $r)
		{
		  $t=$r['tag'];
		  if ($r['type']=='open') {
		    if (isset($ary[$t])) {
		      if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
		      $cv=&$ary[$t][count($ary[$t])-1];
		    } else $cv=&$ary[$t];
		    if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
		    $cv['_c']=array();
		    $cv['_c']['_p']=&$ary;
		    $ary=&$cv['_c'];
		  } elseif ($r['type']=='complete') {
		    if (isset($ary[$t])) { // same as open
		      if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
		      $cv=&$ary[$t][count($ary[$t])-1];
		    } else $cv=&$ary[$t];
		    if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
		    $cv['_v']=(isset($r['value']) ? $r['value'] : '');
		  } elseif ($r['type']=='close') {
		    $ary=&$ary['_p'];
		  }
		}    
		$this->_del_p($mnary);
		return $mnary;
	}
	function xmlInsert($arr=array(), $name='')
	{
		$output = 0;
		if(is_array($arr) AND count($arr) > 0)
		{
			$xml	= '<'.$this->tag.'>';
			if(!empty($name)){
				$id = $name;
			}else{
				$id		= $this->Last_ID($this->path)+1;
			}
			$xml .= $this->array2xml($arr);
			$xml .= '</'.$this->tag.'>';
			$file = fopen($this->root.$id.'.xml',"w+");
			fputs($file, $xml);
			fclose($file);
			$output = $id;
		}
		return $output;
	}
	function xmlUpdate($data, $file_id)
	{
		$tmp = $this->xmlfetch($file_id);
		$new = array();
		foreach($tmp AS $id => $dt){
			if(isset($data[$id]))	$new[$id] = $data[$id];
			else $new[$id] = $dt;
		}
		$output = $this->xmlInsert($new, $file_id);
		return $output;
	}
	function xmlDelete($file)
	{
		$out = 0;
		if(!empty($file)){
			if(is_array($file)){
				foreach($file AS $dt){
					if(is_file($this->path.$dt)){
						$bool = unlink($this->path.$dt);
						$out += $bool ? 1 : 0;
					}
				}
			}else{
				if(is_file($this->path.$file)){
					$bool = unlink($this->path.$file);
					$out += $bool ? 1 : 0;
				}
			}
		}
		return $out;
	}
	function _del_p(&$ary) {
		foreach ($ary as $k=>$v) {
		  if ($k==='_p') unset($ary[$k]);
		  elseif (is_array($ary[$k])) $this->_del_p($ary[$k]);
		}
	}
	function array2xml($arr = array())
	{
		$this->output = '';
		$output = '';
		if(is_array($arr)){
			$id0 = 0;
			foreach($arr AS $id => $data){
				if(is_int($id)){
					if($id=='0'){
						$key = 'itemXML';
						$id0 = $id;
					}elseif($id0==($id-1)){
						$key = 'itemXML';
						$id0 = $id;
					}
				}else{
					$key = $id;
				}
				if(is_array($data)){
					$output .= '<'.$key.'>'.$this->array2xml($data).'</'.$key.'>';
				}else{
					$output .= '<'.$key.'>'.htmlentities($data).'</'.$key.'>';
				}
			}
		}
		return $output;
	}
	function unhtmlentities($cadena)
	{
		$cadena = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $cadena);
		$cadena = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $cadena);
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
		return strtr($cadena, $trans_tbl);
	}
	function Last_ID($path = '')
	{
		$path = !empty($path) ? $path : $this->path;
		$d = new getDirectory($path, $this->baseroot);
		$r = $d->dirList();
		foreach($r AS $file) {
			if(preg_match('/\.xml/i', $file)) {
				$i = str_replace('.xml', '', $file);
				$i = intval($i);
				if($i > $output) {
					$output = $i;
				}
			}
		}
		return $output;
	}
	function ins2ary(&$ary, $element, $pos) {
		$ar1=array_slice($ary, 0, $pos); $ar1[]=$element;
		$ary=array_merge($ar1, array_slice($ary, $pos));
	}
}
?>