<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$file  = new user_upload(isset($_GET['qqfile']));
if (!empty($_GET['token']))
{
  $param = json_decode(decode(str_replace(' ', '+', $_GET['token'])), 1);
}else{
  $get = $_GET;
  unset($get['mod'], $get['qqfile'], $get['id']);
  $param = json_decode(decode(str_replace(' ', '+', implode('', $get))), 1);
}
if ($file && !empty($param['expire']) && $param['expire'] > time())
{
  $file->path = _CACHE.@trim($param['path']['tmp']);
  $extAllowed = $param['ext'];
  $sizeLimit  = 1024*1024*1024;
  if (!file_exists($file->path))
  {
  	_func('path', 'create', $file->path);
  }
  $file->path .= substr($file->path, -1)!='/' ? '/':'';
  if (!is_writable($file->path))
  {
    output_json(array('error' => "Server error. Upload directory isn't writable."));
  }
	if (!$file)
	{
		output_json(array('error' => 'No files were uploaded.'));
	}
	$size = $file->getSize();
	if ($size == 0)
	{
		output_json(array('error' => 'File is empty or too large!'));
	}
	if ($size > $sizeLimit)
	{
		output_json(array('error' => 'File is too large'));
	}
	$pathinfo = pathinfo($file->getName());
	$filename = menu_save($pathinfo['filename']);
	$ext      = $pathinfo['extension'];
	if($extAllowed && !in_array(strtolower($ext), $extAllowed))
	{
		$these = implode(', ', $extAllowed);
		output_json(array('error' => 'File has an invalid extension, it should be one of these '. implode('/',$extAllowed)));
	}
	while (file_exists($file->path . $filename . '.' . $ext))
	{
		$filename .= rand(10, 99);
	}
	if (!empty($param['folder']) && is_dir($param['folder']))
	{
		while (file_exists($param['folder'] . $filename . '.' . $ext))
		{
			$filename .= rand(10, 99);
		}
	}
  $fileresult = $file->path . $filename . '.' . $ext;
  if ($file->save($file->path . $filename . '.' . $ext))
  {
  	$img = _class('image_lib');
  	if (!empty($param['resize']))
  	{
  		list($width, $height) = image_size($param['resize'], true);
  		$img->initialize(array(
  			'source_image' => $fileresult,
  			'width'        => $width,
  			'height'       => $height,
  			));
  		$img->resize();
  	}
  	if (!empty($param['watermark']))
  	{
      if (!empty($param['watermark']['wm_overlay_path']) && file_exists($param['watermark']['wm_overlay_path']))
      {
        $watermark = array(
          'wm_type'          => 'overlay',
          'source_image'     => $fileresult,
          'wm_overlay_path'  => $param['watermark']['wm_overlay_path'],
          'wm_vrt_alignment' => 'middle', // top, middle, bottom
          'wm_hor_alignment' => 'center', // left, center, right
          'wm_opacity'       => 50,
          'wm_x_transp'      => 4,
          'wm_y_transp'      => 4,
          );
        switch (@$param['watermark']['wm_position'])
        {
          case 'top-left':
            $watermark['wm_vrt_alignment'] = 'top';
            $watermark['wm_hor_alignment'] = 'left';
            break;
          case 'top-right':
            $watermark['wm_vrt_alignment'] = 'top';
            $watermark['wm_hor_alignment'] = 'right';
            break;
          case 'bottom-left':
            $watermark['wm_vrt_alignment'] = 'bottom';
            $watermark['wm_hor_alignment'] = 'left';
            break;
          case 'bottom-right':
            $watermark['wm_vrt_alignment'] = 'bottom';
            $watermark['wm_hor_alignment'] = 'right';
            break;
        }
        $watermark = array_merge($watermark, $param['watermark']);
    		$img->initialize($watermark);
    		$img->watermark();
      }
  	}
  	if (!empty($param['thumbnail']))
  	{
  		$path = $file->path.$param['thumbnail']['prefix'];
  		if ($param['thumbnail']['is_dir'])
  		{
  			_func('path', 'create', $path);
  		}
  		list($width, $height) = image_size($param['thumbnail']['size'], true);
  		$cfg = array(
  			'source_image' => $fileresult,
  			'width'        => $width,
  			'height'       => $height,
  			// 'create_thumb' => true,
  			'new_image'    => $path.$filename.'.'.$ext,
  			);
  		$img->initialize($cfg);
  		$img->resize();
  	}
  	output_json(array('success'=>1, 'result' => $filename.'.'.$ext));
  }else{
  	output_json(array('error'=> 'Could not save uploaded file.' . 'The upload was cancelled, or server error encountered'));
  }
}
die();
class user_upload
{
	function __construct($is_XML)
	{
		$this->isXML = $is_XML ? true : false;
	}
	public function save($path)
	{
    if ($this->isXML)
    {
      $input = fopen("php://input", "r");
      $temp = tmpfile();
      $realSize = stream_copy_to_stream($input, $temp);
      fclose($input);
      if ($realSize != $this->getSize())
      {
        return false;
      }
      $target = fopen($path, "w");
      fseek($temp, 0, SEEK_SET);
      stream_copy_to_stream($temp, $target);
      fclose($target);
      return true;
    }else{
      if (@is_uploaded_file($_FILES['qqfile']['tmp_name']))
      {
        $done = move_uploaded_file($_FILES['qqfile']['tmp_name'], $path);
        if ($done)
        {
          chmod($path, 0777);
        }
        return $done;
      }
		}
	}
	public function getName()
	{
		return $this->isXML ? $_GET['qqfile'] : $_FILES['qqfile']['name'];
	}
	public function getSize()
	{
		if ($this->isXML)
		{
			if (isset($_SERVER["CONTENT_LENGTH"]))
			{
				return (int)$_SERVER["CONTENT_LENGTH"];
			} else {
				throw new Exception('Getting content length is not supported.');
			}
		}else{
			return @$_FILES['qqfile']['size'];
		}
	}
}

/*
// EXAMPLE HOW TO USE IT

link_js(_URL.'includes/lib/pea/includes/FormMultifile.js', false);
link_css(_URL.'includes/lib/pea/includes/FormMultifile.css', false);
$g_path = 'images/modules/gallery/'.$id.'/';
$g_temp = menu_save($g_path).'/';
$params = array(
  'ext'       => array('jpg', 'gif', 'png', 'bmp'),
  'resize'    => 900,
  'thumbnail' => array(
    'size'   => 204,
    'prefix' => 'thumb_',
    'is_dir' => 0,
    ),
  'watermark' => array(
    'wm_overlay_path'  => _ROOT.'images/logo.png',
    'wm_position'      => 'center', // center, top-left, top-right, bottom-left, bottom-right
#   'wm_type'          => 'overlay',
#   'wm_vrt_alignment' => 'middle', // top, middle, bottom
#   'wm_hor_alignment' => 'center', // left, center, right
#   'wm_opacity'       => 50,
#   'wm_x_transp'      => 4,
#   'wm_y_transp'      => 4,
    ),
  'path'   => array(
    'folder' => $g_path,
    'tmp'    => $g_temp,
  ),
  'folder' => $g_path,
  'expire' => strtotime('+2 HOUR'),
  );
$extra = 'title="click or drag to upload"';
$extra .= ' data-name="gallery"';
$extra .= ' data-path="'.str_replace(_ROOT, _URL, _CACHE).$g_temp.'"';
$extra .= ' data-params="'.encode(json_encode($params)).'"';
#extra .= ' data-title'; ## ini optional mau pakai field ini atau tidak terserah
#extra .= ' data-description'; ## ini optional
$array = $db->getAssoc("SELECT id, image, title, description FROM gallery_image WHERE gallery_id=1 ORDER BY orderby ASC");
?>
<div class="file-uploader"<?php echo $extra; ?>>
  <noscript> <p>Please enable JavaScript to use file uploader.</p> </noscript>
  <div class="hidden">
    <?php
    // attribute data-title dan data-description adalah optional
    foreach ($array as $gal)
    {
      echo '<img src="'._URL.$g_path.$gal['image'].'" data-title="'.htmlentities($gal['title']).'" data-description="'.htmlentities($gal['description']).'" />';
    }
    ?>
  </div>
</div>

*/