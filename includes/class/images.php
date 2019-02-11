<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*
EXAMPLE :
$img = _class('images');
$img->setPath($Bbc->mod['dir']);
$image_name = $img->upload($_FILES['input_name']);
$img->resize(400);
*/
class images
{
	var $root = _ROOT;
	var $url	= _URL;
	var $valid= array ('gif', 'jpg', 'png', 'bmp');
	var $allow= array ('gif', 'jpg', 'png', 'bmp', 'ico', 'swf');
	var $perm	= 0777;
	var $path;
	var $img;
	var $output;
	function __construct($path = '', $img = '')
	{
		if(!empty($path))
			$this->setPath($path, false);
		if(!empty($img))
			$this->setimages($img);
	}
	function setpath($path, $check = true)
	{
		if(!empty($path)) {
			if(preg_match('#^'.addslashes($this->root).'#is', $path)){
				$path = preg_replace('#^'.addslashes($this->root).'#is', '', $path);
			}
			if(preg_match('#^'.addslashes($this->url).'#is', $path)){
				$path = preg_replace('#^'.addslashes($this->url).'#is', '', $path);
			}
			if($check)
			{
				if(!file_exists($this->root.$path))
				{
					_func('path');
					path_create($this->root.$path);
				}
			}
		}
		if(!empty($path)) {
			if(substr($path, 0, 1)=='/') $path = substr($path, 1);
			if(substr($path, -1)!='/'){
				$path .= '/';
			}
		}
		$this->path = $path;
	}
	function setimages($img)
	{
		$this->img = $img ? $img : $this->getUnique('jpg');
	}
	function show($width=0, $height=0, $img='', $extra='')
	{
		$output = '';
		if(empty($img)){
			$img = $this->path.$this->img;
		}else{
			$img = $this->path.$img;
		}
		if(is_file($this->root.$img))
		{
			$ext = $this->getExt($img);
			if(in_array($ext, $this->allow))
			{
				switch($ext)
				{
					case 'swf';
						$output .= "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0' width='".$width."' height='".$height."' ".$extra.">\n";
						$output .= "	<param name='movie' value='".$this->url.$img ."'>\n";
						$output .= "	<param name='quality' value='high'>\n";
						$output .= "	<embed src='". $this->url.$img ."' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width='".$width."' height='".$height."'></embed>\n";
						$output .= "</object>";
					break;
					default:
						if($width == 0 || $height == 0)
							list($w0, $h0) = getimagesize($this->root.$img);
						$width = (intval($width) > 0) ? $width : $w0;
						$height= (intval($height)> 0) ? $height: $h0;
						list($w, $h) = $this->proportionImg($this->root.$img, array($width, $height));
						$output .= '<img src="'.$this->url.$img.'" width="'.$w.'px" height="'.$h.'px" '.$extra.' />';
					break;
				}
			}
		}
		return $output;
	}
	function getExt($img)
	{
		preg_match('~\.([a-z0-9]+)[^\.]{0,}$~is', $img, $match);
		return !empty($match[1]) ? strtolower($match[1]) : '';
	}
	function move($path, $img='', $imgfrom = '')
	{
		if(!empty($imgfrom)) $this->img = $imgfrom;
		if(empty($img)) $img = $this->img;
		$path = preg_replace("#^".addslashes($this->root)."#is", "", $path);
		if(!is_dir($this->root.$path))
			mkdir($this->root.$path, $this->perm);
		if(!is_file($this->root.$path.$img))
		{
			$out = @rename($this->root.$this->path.$this->img, $this->root.$path.$img);
			@chmod($this->root.$path.$img, $this->perm);
			$output = ($out) ? $img : false;
		}else{
			$rand		= rand().'.'.$this->getExt($img);
			$output = $this->move($path, $rand);
		}
		return $output;
	}
	function copying($path, $img='', $imgfrom = '')
	{
		if(!empty($imgfrom)) $this->img = $imgfrom;
		if(empty($img)) $img = $this->img;
		$path = preg_replace("#^".addslashes($this->root)."#is", "", $path);
		if(!is_dir($this->root.$path))
			mkdir($this->root.$path, $this->perm);
		if(!is_file($this->root.$path.$img))
		{
			$out = @copy($this->root.$this->path.$this->img, $this->root.$path.$img);
			@chmod($this->root.$path.$img, $this->perm);
			$output = ($out) ? $img : false;
		}else{
			$rand		= rand().'.'.$this->getExt($img);
			$output = $this->copying($path, $rand);
		}
		return $output;
	}
	function upload_r($imgfile, $name = 'all')
	{
		$output = array();
		$name		= !is_array($name) ? array($name) : array_values($name);
		foreach((array)$imgfile['tmp_name'] as $id => $file)
		{
			if($name[0] == 'all') {
				$insert = true;
			}else{
				$insert = in_array($id, $name) ? true : false;
			}
			if(is_uploaded_file($file) && $insert == true && $this->upload_is_ok($imgfile['name'][$id]))
			{
				if(is_file($this->root.$this->path.$imgfile['name'][$id]))
					@unlink($this->root.$this->path.$imgfile['name'][$id]);
				$success = move_uploaded_file($file, $this->root.$this->path.$imgfile['name'][$id]);
				if($success) {
					@chmod($this->root.$this->path.$imgfile['name'][$id], $this->perm);
					$output[] = $imgfile['name'][$id];
				}
			}
		}
		return $output;
	}
	function upload($imgfile, $imgto='')
	{
		if(is_uploaded_file($imgfile['tmp_name']) && $this->upload_is_ok($imgfile['name']))
		{
			$ext = $this->getExt($imgfile['name']);
			$this->img = $imgto ? $imgto : $this->getUnique( $ext );
			if(!stristr($this->img, '.')){
				$this->img .= '.'.$ext;
			}
			$img_path = $this->root.$this->path;
			$img_dst = $img_path.$this->img;
			move_uploaded_file($imgfile['tmp_name'], $img_dst);
			@chmod($img_dst, $this->perm);
			$output = $this->img;
		}else{
			$output = false;
		}
		return $output;
	}
	function upload_is_ok($name)
	{
		$restricted = array('php','phps','php3','php4','phtml','pl','p');
		return !in_array($this->getExt($name), $restricted);
		return (!empty($match[1]) && !in_array(strtolower($match[1]), $restricted));
	}
	function resize($sizes, $imgdst = '', $compress = 100, $type = 'proportion', $force = false)
	{
		$sizes = image_size($sizes, true);
		$imgfile= $this->root.$this->path.$this->img;
		if(!empty($imgdst)) {
			$imgdst = preg_replace('~^'.preg_quote($this->root, '~').'~is', '', $imgdst);
			$imgdst = preg_replace('~^'.preg_quote($this->path, '~').'~is', '', $imgdst);
			$imgdst = $this->root.$this->path.$imgdst;
		}else{
			$imgdst	= $this->root.$this->path.$this->img;
		}
		$match = false;
		if(is_file($imgfile))
		{
			list($match, $format) = $this->is_validImage($imgfile);
			if($match and !empty($format))
			{
				list($width, $height) = getimagesize($imgfile);
				list($newwidth, $newheight) = $sizes;
				if($newwidth < $width || $newheight < $height)
				{
					chmod($imgfile, $this->perm);
					$sizes = array_values($sizes);
					switch(strtolower($type)){
						case 'stretch':
							list($newwidth, $newheight) = $sizes;
							$dstX = $dstY = $srcX = $srcY = 0;
						break;
						case 'crop':
							list($newwidth, $newheight) = $sizes;
							return $this->cropImage($newwidth, $newheight, $imgfile, $format, $imgdst);
						break;
						case 'proportion':
						default:
							list($newwidth, $newheight) = $this->proportionImg($imgfile, $sizes);
							$dstX = $dstY = $srcX = $srcY = 0;
						break;
					}
					$tumb = ImageCreateTrueColor($newwidth,$newheight);
				  switch($format)
				  {
				  	case 'gif':
				  		$source = imagecreatefromgif($imgfile);
							ImageCopyResized($tumb, $source, $dstX, $dstY, $srcX, $srcY, $newwidth, $newheight, $width, $height);
				  		imagegif($tumb,$imgdst,$compress);
				  	break;
				  	case 'jpg':
				  		$source = imagecreatefromjpeg($imgfile);
							ImageCopyResized($tumb, $source, $dstX, $dstY, $srcX, $srcY, $newwidth, $newheight, $width, $height);
				  		imagejpeg($tumb,$imgdst,$compress);
				  	break;
				  	case 'png':
				  		$source = imagecreatefrompng ($imgfile);
							ImageCopyResized($tumb, $source, $dstX, $dstY, $srcX, $srcY, $newwidth, $newheight, $width, $height);
				  		imagepng($tumb,$imgdst,(ceil($compress/10)-1));
				  	break;
				  	case 'bmp':
				  		$source = imagecreatefromwbmp($imgfile);
							ImageCopyResized($tumb, $source, $dstX, $dstY, $srcX, $srcY, $newwidth, $newheight, $width, $height);
				  		imagewbmp($tumb,$imgdst,$compress);
				  	break;
				  }
				  if($format) {
				  	@chmod($imgdst, $this->perm);
				  }
				}else{
					if($imgfile != $imgdst)
					{
						if (preg_match('~^(.*?)([^/]+)$~is', $imgfile, $source) && preg_match('~^(.*?)([^/]+)$~is', $imgdst, $dest))
						{
							if ($source[1]==$dest[1])
							{
								$imgfile = $source[2];
								$imgdst  = $dest[2];
							}
						}
						if ($force)
						{
							$match = $this->move($this->path, $imgdst, $imgfile);
						}else{
							$match = $this->copying($this->path, $imgdst, $imgfile);
						}
					}else{
						$match = false;
					}
				}
			}
		}
		return $match;
	}
	function cropImage($nw, $nh, $source, $stype, $dest)
	{
		$size = getimagesize($source);
		$w = $size[0];
		$h = $size[1];
		switch($stype) {
			case 'gif':
			$simg = imagecreatefromgif($source);
			break;
			case 'jpg':
			$simg = imagecreatefromjpeg($source);
			break;
			case 'png':
			$simg = imagecreatefrompng($source);
			break;
			case 'bmp':
			$simg = imagecreatefromwbmp($source);
			break;
		}
		$dimg = imagecreatetruecolor($nw, $nh);
		$wm = $w/$nw;
		$hm = $h/$nh;
		$h_height = $nh/2;
		$w_height = $nw/2;
		if($w > $h) {
			$adjusted_width = $w / $hm;
			$half_width = $adjusted_width / 2;
			$int_width = $half_width - $w_height;
			imagecopyresampled($dimg,$simg,-$int_width,0,0,0,$adjusted_width,$nh,$w,$h);
		} elseif(($w < $h) || ($w == $h)) {
			$adjusted_height = $h / $wm;
			$half_height = $adjusted_height / 2;
			$int_height = $half_height - $h_height;
			imagecopyresampled($dimg,$simg,0,-$int_height,0,0,$nw,$adjusted_height,$w,$h);
		} else {
			imagecopyresampled($dimg,$simg,0,0,0,0,$nw,$nh,$w,$h);
		}
		imagejpeg($dimg,$dest,100);
		@chmod($dest, $this->perm);
		return true;
	}
	function delete($img)
	{
		if(is_file($this->root.$this->path.$img)){
			@chmod($this->root.$this->path.$img, $this->perm);
			unlink($this->root.$this->path.$img);
			$output = true;
		}else{
			$output = false;
		}
		return $output;
	}
	function is_validImage($file)
	{
		$format = $this->getExt($file);
	  $match =(in_array($format, $this->valid) and is_file($file)) ? 1 : 0;
	  $output= array($match, $format);
	  return $output;
	}
	function getUnique($ext)
	{
		$rand = rand();
		if(!is_file($this->root.$this->path.$rand.'.'.$ext)){
			return $rand.'.'.$ext;
		}else{
			return $this->getUnique($ext);
		}
	}
	function proportionImg($img_src, $thumbsize)
	{
		list($width, $height) = getimagesize($img_src);
		if(is_array($thumbsize))
		{
			list($max_width, $max_height) = $thumbsize;
			$ok = $ratio = $width_jadi = $height_jadi = array();
			$ok[0] = $ok[1] = 1;
			$ratio[0] = $ratio[1] = 1;
			if ($height > $max_height){
				$ratio[0] = (int)$max_height/$height;
				$height_jadi[0] = $max_height;
				$width_jadi[0]	= $ratio[0]*$width;
			}else{
				$height_jadi[0] = $height;
				$width_jadi[0]	= $width;
			}
			if ($width > $max_width){
				$ratio[1] = (int)$max_width/$width;
				$height_jadi[1] = $ratio[1]*$height;
				$width_jadi[1]	= $max_width;
			}else{
				$height_jadi[1] = $height;
				$width_jadi[1]	= $width;
			}
			if ($width_jadi[0] > $max_width){
				$ok[0] = 0;
			}
			if ($height_jadi[1] > $max_height){
				$ok[1] = 0;
			}
			if ($ok[0] == 1 && $ok[1] == 1){
				$output = array($width_jadi[0], $height_jadi[0] );
			}
			elseif ($ok[0] == 1){
				$output = array($width_jadi[0], $height_jadi[0] );
			}else{
				$output = array($width_jadi[1], $height_jadi[1] );
			}
		}else{
			$imgratio = $width / $height;
				if($imgratio > 1)
				{
					$newwidth = $thumbsize;
					$max = $width;
					$newheight = (int)($thumbsize / $imgratio);
				}else{
					$newheight = $thumbsize;
					$max = $height;
					$newwidth = (int)($thumbsize * $imgratio);
				}
			if ($max < $thumbsize){
				$newwidth = $width;
				$newheight = $height;
			}
			$output = array($newwidth, $newheight);
		}
		return $output;
	}
}