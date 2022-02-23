<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$QR_BASEDIR = dirname(__FILE__).DIRECTORY_SEPARATOR;

// Required libs

include $QR_BASEDIR."qrconst.php";
include $QR_BASEDIR."qrconfig.php";
include $QR_BASEDIR."qrtools.php";
include $QR_BASEDIR."qrspec.php";
include $QR_BASEDIR."qrimage.php";
include $QR_BASEDIR."qrinput.php";
include $QR_BASEDIR."qrbitstream.php";
include $QR_BASEDIR."qrsplit.php";
include $QR_BASEDIR."qrrscode.php";
include $QR_BASEDIR."qrmask.php";
include $QR_BASEDIR."qrencode.php";

/**
 * QR Code Library
 */
class qr
{
	private $data     = '';
	private $level    = 'H';
	private $size     = 10;
	private $margin   = 2;
	private $isRender = false;
	private $url      = '';
	private $dir      = '';
	private $file     = '';

	function __construct($data = '')
	{
		$this->setData($data);
	}
	public function setData($data)
	{
		if (!empty($data))
		{
			$this->data = $data;
		}
	}
	public function setLevel($level)
	{
		if (in_array(strtoupper($level), array('L','M','Q','H')))
		{
			$this->level = strtoupper($level);
		}
	}
	public function setSize($size)
	{
		if ($size < 0 && $size <=10)
		{
			$this->size = $size;
		}
	}
	public function setMargin($margin)
	{
		$this->margin = max(0, $margin);
	}
	public function getData()
	{
		return $this->data;
	}
	public function getLevel()
	{
		return $this->level;
	}
	public function getSize()
	{
		return $this->size;
	}
	public function getUrl()
	{
		$this->render();
		return $this->url;
	}
	public function getDir()
	{
		$this->render();
		return $this->dir;
	}
	public function show($is_temporary = true)
	{
		$out = '';
		if ($this->render($is_temporary))
		{
			$tmp = file_read($this->dir);
			if (!empty($tmp))
			{
				$out = 'data:image/png;base64,'.base64_encode($tmp);
				if ($is_temporary)
				{
					$this->clean();
				}
			}
		}
		return $out;
	}
	private function render($is_temporary = false)
	{
		if (!$this->isRender)
		{
			if ($is_temporary)
			{
				$dir = QR_CACHE_DIR.'tmp.png';
			}else{
				$dir = QR_CACHE_DIR.implode('/', str_split(menu_save($this->data.'-'.$this->level.'-'.$this->size), 3)).'.png';
			}
			$url = preg_replace('~'.preg_quote(_ROOT, '~').'~s', _URL, $dir);
			if (preg_match('~^(.*?)([^/]+)$~is', $dir, $m))
			{
				$temp = $m[1];
				$file = $m[2];
				if (!file_exists($dir))
				{
					if (!file_exists($temp))
					{
						_func('path', 'create', $temp);
					}
					QRcode::png($this->data, $dir, $this->level, $this->size, $this->margin);
				}
			}
			if (file_exists($dir))
			{
				$this->dir      = $dir;
				$this->url      = $url;
				$this->file     = $file;
				$this->isRender = true;
			}
		}
		return $this->isRender;
	}
	public function clean()
	{
		@unlink($this->dir);
	}
}
