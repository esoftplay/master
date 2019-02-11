<?php
// UPLOAD FILE
class phpUploadFile {
	var $post_name;
	var $folder;
	var $file_name_uploaded;
	var $file_name_sukses_uploaded;	//nama file yang telah berhasil diuplaod, di chmod dan direname manjadi ini
	var $chmod;
	var $is_resize;
	var $arr_extension_filter;
	var $file_name_uploaded_prefix;				//public digunakan kl unique_file_name true, ini prefix kl make uniquefilename
	var $unique_file_name;
	var $file_size_max;			// max file size in bytes
	var $error_code;			// kode error : file_type_unallowed, file_size_max_exceeded, file_post_un_uploaded, ok
	var $arrayPostName;
	var $is_thumbnail = false;
	var $is_watermark = false;
	//konstruktor
	function __construct($post_name, $folder, $file_name_uploaded = "", $chmod = 0664, $is_resize = false, $width = 0, $height = 0)
	{
		$this->file_name_uploaded = $file_name_uploaded;
		$this->post_name	= $post_name;
		$this->folder			= $folder;
		$this->chmod			= $chmod;
		$this->is_resize	= $is_resize;
		$this->rez_width	= $width;
		$this->rez_height	= $height;
		$this->image			= _class('image_lib');
		$this->arr_extension_filter	= array();
		$this->unique_file_name	= false;
		$this->file_size_max 		= -1;
		$this->error_code			= '';
		$this->arrayPostName		= '';
		$this->isArrayPostName 	= false;
	}

	// untuk ngeset nama array belakang dari variabel POST
	// misalkan post name nya adalah: $_POST[image][1];
	// maka cara nggunakan class ini adl:
	// $this->phpUploadFile($post_name= 'image', $folder, $file_name_uploaded = "", $chmod = 0664)
	// $this->setArrayPostName( '1' );
	function setArrayPostName( $str_array_opst_name = '' )
	{
		$this->arrayPostName = $str_array_opst_name;
		if ( $this->arrayPostName != '' || $this->arrayPostName == '0' )
		{
			$this->isArrayPostName = true;
		}
	}

	//public
	function setAllowedExtension( $arr_extension_filter )
	{
		$this->arr_extension_filter = $arr_extension_filter;
	}

	//public
	function setUniqueFileNameOn( $prefix = "" )
	{
		$this->unique_file_name = true;
		$this->file_name_uploaded_prefix = $prefix;
	}

	//public
	function setMaxFileSize($file_size_max = -1) // max file size in bytes
	{
		$this->fileSizeMax = $file_size_max;
	}

	//public
	function getFileNameOkUploaded()
	{
		return $this->file_name_sukses_uploaded;
	}

	//private
	function getFileExtension($file_name)
	{
		if (preg_match('#\w.*\.(\w.*)$#', $file_name, $match))
		{
			$ext = strtolower($match[1]);
			return $ext;
		}else return "";
	}

	//private
	// untuk filter extensi file yang diperbolehkan
	function filteringExtension($post)
	{
		$post 		= $_FILES[$this->post_name];
		if (!empty($this->arr_extension_filter))
		{
			$name = ( !$this->isArrayPostName ) ? $post['name'] : $post['name'][$this->arrayPostName];
			$ext = $this->getFileExtension( $name );
			if (in_array($ext, $this->arr_extension_filter))
				return true;
			else return false;
		}else return true;
	}

	//private
	//mengembalikan filename diserver dari file yang akan diupload
	function getFileNameUploaded()
	{
		$post 		= $_FILES[$this->post_name];
		$name 		= ( !$this->isArrayPostName ) ? $post['name'] : $post['name'][$this->arrayPostName];

		if ($this->unique_file_name)
		{
			$ext	= $this->getFileExtension( $name );
			$this->file_name_uploaded	= uniqid($this->file_name_uploaded_prefix).".".$ext;
		}
		else
		{
			if ($this->file_name_uploaded == "") $this->file_name_uploaded 	= $name;
			else $this->file_name_uploaded .= ".".$this->getFileExtension( $name );
		}
		return $this->file_name_uploaded;
		//return $name;
	}

	//private
	//kl memenuhi syarat kembalikan true
	function checkFileSize()
	{
		if ($this->file_size_max >= 0)
		{
			$post 		= $_FILES[$this->post_name];
			$size 		= ( !$this->isArrayPostName ) ? $post['size'] : $post['size'][$this->arrayPostName];
			if ($size > $this->file_size_max) return false;
			else return true;
		}else return true;
	}
	function setThumbnail( $params, $prefix )
	{
		$this->thumb_param = $params;
		$this->thumb_prefix= $prefix;
		$this->is_thumbnail = true;
	}
	function setWatermark( $params = array() )
	{
		$this->watermark_param = $params;
		$this->is_watermark = true;
	}

	//fungsi utama untuk upload
	//public
	// mengembalikan true kl berhasil , else false
	function do_upload()
	{
		if (isset($_FILES[$this->post_name]))
		{
			$post 		= $_FILES[$this->post_name];
			$tmp_file	= ( !$this->isArrayPostName ) ? $post['tmp_name'] : $post['tmp_name'][$this->arrayPostName];

			if ( is_uploaded_file( $tmp_file ) )
			{
				if ($this->filteringExtension($post))
				{
					if ($this->checkFileSize())
					{
						$file_name = $this->getFileNameUploaded();
						$dest = $this->folder.$file_name;
						$upload = move_uploaded_file($tmp_file, $dest);
						if ($upload)
						{
							@chmod($dest, $this->chmod);
							$this->file_name_sukses_uploaded = $file_name;
							if($this->is_resize)
							{
								$cfg_resize = array(
									'source_image'=> $dest
								,	'width'				=> $this->rez_width
								,	'height'			=> $this->rez_height
								);
								$this->image->initialize($cfg_resize);
								$this->image->resize();
							}
							if($this->is_watermark)
							{
								$this->watermark_param['source_image'] = $dest;
								$this->image->initialize($this->watermark_param);
								$this->image->watermark();
							}
							if($this->is_thumbnail)
							{
								if(substr($this->thumb_prefix, -1) == '/' && !is_dir($this->folder.$this->thumb_prefix))
								{
									@mkdir($this->folder.$this->thumb_prefix, 0777);
								}
								$config = array_merge($cfg_resize, $this->thumb_param);
								$config['create_thumb'] = TRUE;
								$config['new_image']		= $this->folder.$this->thumb_prefix.$file_name;
								$this->image->initialize($config);
								$this->image->resize();
								@chmod($this->folder.$this->thumb_prefix.$file_name, $this->chmod);
							}
							return true;
						}
						else $this->error_code .= 'move_upload_file_failed';
					} else $this->error_code .= 'file_size_max_exceeded';
				} else $this->error_code .= 'file_type_unallowed';
			}// end if is_uploaded_file
#			else $this->error_code .= 'file_upload_from_post_failed';
		}// end if isset
		else {
#			$this->error_code .= 'file_post_un_uploaded';
			return true;
		}
		return false;
	}//end function do_upload


	//public
	function getError()
	{
		return $this->error_code;
	}
}// end class upload

?>