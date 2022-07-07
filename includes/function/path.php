<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

if (!function_exists('path_list'))
{
	function path_list($path, $order = 'asc')
	{
		$output = array();
		if ($dir = @opendir($path)) {
			while (($data = readdir($dir)) !== false)
			{
				if($data != '.' and $data != '..')
				{
					$output[] = $data;
				}
			}
			closedir($dir);
		}
		if(strtolower($order) == 'desc') rsort($output);
		else sort($output);
		reset($output);
		return $output;
	}
}
if (!function_exists('path_list_r'))
{
	function path_list_r($path, $top_level_only = FALSE)
	{
		if ($fp = @opendir($path))
		{
			$path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			$filedata = array();
			while (FALSE !== ($file = readdir($fp)))
			{
				if (strncmp($file, '.', 1) == 0)
				{
					continue;
				}
				if ($top_level_only == FALSE && @is_dir($path.$file))
				{
					$temp_array = array();
					$temp_array = call_user_func(__FUNCTION__, $path.$file.DIRECTORY_SEPARATOR);
					$filedata[$file] = $temp_array;
				}
				else
				{
					$filedata[] = $file;
				}
			}
			closedir($fp);
			return $filedata;
		}
		return false;
	}
}
if (!function_exists('path_delete'))
{
	function path_delete($path)
	{
		if($path == _ROOT) return false;
		elseif(!preg_match('~^'._ROOT.'~', $path)) return false;
		if (file_exists($path))
		{
			if (@is_dir($path))
			{
				if (substr($path, -1) == '/')
				{
					$path = substr($path, 0, -1);
				}
				@chmod($path,0777);
				if ($handle = @opendir($path))
				{
					while(false !== ($filename = readdir($handle)))
					{
						if ($filename != '.' && $filename != '..')
						{
							call_user_func(__FUNCTION__, $path.'/'.$filename);
						}
					}
					closedir($handle);
				}
				@rmdir($path);
			} else {
				@unlink($path);
			}
		}
	}
}
if (!function_exists('path_create'))
{
	function path_create($path, $chmod = 0777)
	{
		if(!empty($path))
		{
			if(file_exists($path)) $output = true;
			else {
				$output = @mkdir($path, $chmod, true);
				if (!$output)
				{
					$debug = debug_backtrace();
					$file  = $debug[0]['file'];
					$line  = $debug[0]['line'];
					if (defined('_MST'))
					{
						$r = explode('|', _MST);
						foreach ($r as $p)
						{
							$p = trim($p);
							if (!empty($p))
							{
								$file = preg_replace('~^'.preg_quote($p, '~').'~s', '', $file);
							}
						}
						$file = preg_replace('~^'.preg_quote(_ROOT, '~').'~s', '', $file);
					}
					$f = !empty($debug[0]) ? $file.':'.$line : '';
					if (!empty($f))
					{
						error_log(_URL.': '.$f.' Failed path create on '.$path);
					}
				}
			}
		}else{
			$output = false;
		}
		return $output;
	}
}
