<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
EXAMPLE: (yang diawali # adalah optional)
$form->edit->addInput('image','file');
#form->edit->input->image->setTitle('Image');
#form->edit->input->image->setFolder($Bbc->mod['dir']);
#form->edit->input->image->setAllowedExtension(array('jpg', 'jpeg', 'gif', 'png', 'bmp'));
#form->edit->input->image->setResize(535);
#form->edit->input->image->setThumbnail(120, $prefix = 'thumb', $is_dir = true);
#form->edit->input->image->setWatermark($image_path, $param_in_image_lib = array());
#form->edit->input->image->setImageHover(); // image hanya muncul jika di hover
#form->edit->input->image->setImageClick(); // image muncul sebagai thumbnail jika di click muncul popup nya
*/

class FormFile extends Form
{
	var $isUniqueFileName;
	var $isImageHover;
	var $isImageClick;
	var $folder;
	var $folderUrl;
	var $maxFileSize;
	var $uploadStatus;
	var $rez_width;
	var $rez_height;
	var $prefixFileName;
	var $arrImageExt;			// extension sehingga dianggap image, sehingga di output bisa sbg <img ser
	var $is_resize = false;
	var $is_thumbnail = false;
	var $is_watermark = false;
	var $thumb_prefix	= '';
	var $chmod = 0777;

	function __construct()
	{
		global $Bbc;
		$this->type = 'file';
		$this->setIsNeedDbObject( true );
		$this->setAllowedExtension();
		$this->setMaxFileSize();
		$this->setImageExtension();
		$this->setDeleteButton();
		$this->isUniqueFileName = true;
		$this->setIsIncludedInSearch( false );
		$this->setIsIncludedInDeleteQuery(true); // agar getDeleteSQL($ids) di eksekusi sebelum dataRow dihapus
		$this->setImageHover( false );
		$this->setFolder( $Bbc->mod['dir'], $Bbc->mod['image'], false );
	}

	function setFolder( $str_folder='', $str_folder_url='', $create_ifnotexists = true )
	{
		if(empty($str_folder_url))
		{
			if(preg_match("#^"._ROOT."#is", $str_folder))
			{
				$str_folder_url = _URL.preg_replace("#^"._ROOT."#", '', $str_folder);
			}else{
				$str_folder_url = _URL.$str_folder;
				$str_folder			= _ROOT.$str_folder;
			}
		}
		if($create_ifnotexists && !is_dir($str_folder))
		{
			_func('path');
			path_create($str_folder);
		}
		$this->folder		= $str_folder;
		$this->folderUrl= $str_folder_url;
	}

	function setUniqueFileName( $str_prefix = '' )
	{
		$this->isUniqueFileName	= true;
		$this->prefixFileName	= $str_prefix;
	}

	function setResize( $width, $height=0, $is_resize = true )
	{
		if (func_num_args()==1)
		{
			list($width, $height) = image_size($width, true);
		}
		$this->is_resize	= $is_resize;
		$this->rez_width	= $width;
		$this->rez_height	= ($height > 0 ) ? $height : $this->rez_width;
	}
	function setThumbnail( $sizes, $prefix = 'thumb', $is_dir=true )
	{
		$sizes = image_size($sizes, true);
		$this->thumb_cfg = array(
		  'width'		=>	$sizes[0]
		, 'height'	=>	$sizes[1]
		);
		$prefix .= $is_dir ? '/' : '_';
		$this->thumb_prefix	= $prefix;
		$this->is_thumbnail = true;
	}
	function setWatermark( $image = '', $params = array() )
	{
		if(is_file($image))
		{
			$arr = array(
			  'wm_type'					=> 'overlay'
			, 'wm_overlay_path' => $image
			);
			$this->watermark_param = array_merge($arr, $params);
			$this->is_watermark = true;
		}
	}

	// untuk ngeset nama file setelah diupload menjadi apa, biarkan jika tidak perlu
	function setFileName( $str_file_name = '' )
	{
		$this->isUniqueFileName	= false;
		$this->fileName	= $str_file_name;
	}

	// untuk ngeset chmod setelah diupload, default 0644
	function setChmod( $int_chmod = '' )
	{
		$this->chmod	= $int_chmod;
	}

	// untuk ngeset besar file maksimum
	function setMaxFileSize( $int_max_file_size = -1 )
	{
		$this->maxFileSize	= $int_max_file_size;
	}

	// untuk ngeset apakah image hanya tampil saat mouseover
	function setImageHover( $bool = true )
	{
		$this->isImageHover = $bool;
	}

	// untuk ngeset apakah image hanya tampil saat mouseover
	function setImageClick( $bool = true )
	{
		$this->isImageClick = $bool;
	}

	// untuk ngeset extensi apa aja yang boleh di upload
	// berupa array
	// contoh: $this->setAllowedExtension( array('jpg', 'gif') );
	function setAllowedExtension( $arr_allowed_extension = array('jpg', 'gif', 'png', 'bmp') )
	{
		$this->arrAllowedExtension	= $arr_allowed_extension;
	}

	function setImageExtension( $arr_image_extension = array() )
	{
		if ( empty( $arr_image_extension ) )
			$arr_image_extension	= array( 'jpg', 'jpeg', 'gif', 'png', 'bmp' );
		$this->arrImageExt	= $arr_image_extension;
	}

	function setDeleteButton( $value = 'Delete File' )
	{
		$this->deleteButton = new stdClass;
		$this->deleteButton->value	= $value;
	}

	function getFileExtension( $file_name )
	{
		if (preg_match('~\.([^\.]+)$~is', $file_name, $match))
		{
			return strtolower($match[1]);
		}else return '';
	}

	function uploadFile( $oldFileName='', $i = '' )
	{
		include_once( _PEA_ROOT.'phpUploadFile.php');
		$upload	= new phpUploadFile( $this->name, $this->folder, @$this->fileName, $this->chmod
								, $this->is_resize, $this->rez_width, $this->rez_height );
		$upload->setAllowedExtension( $this->arrAllowedExtension );
		$upload->setMaxFileSize( $this->maxFileSize );
		if ( $i != '' || $i == '0' )
		{
			$upload->setArrayPostName( $i );
		}

		if ( $this->isUniqueFileName )
		{
			$upload->setUniqueFileNameOn( $prefix = $this->prefixFileName );
		}
		if($this->is_thumbnail)
		{
			$upload->setThumbnail($this->thumb_cfg, $this->thumb_prefix);
		}
		if($this->is_watermark)
		{
			$upload->setWatermark($this->watermark_param);
		}

		if ( !$this->isUniqueFileName )
		{
			$this->deleteFile( $this->folder, $oldFileName );
		}
		$upload_ok = $upload->do_upload();
		if ( $upload_ok )
		{
			$this->uploadStatus = $upload->getError();
			if ( $this->isUniqueFileName )
			{
				$this->deleteFile( $this->folder, $oldFileName );
			}
			return $file_name = $upload->getFileNameOkUploaded();
		}
		else
		{
			$this->status = $upload->getError();
			return false;
		}
	}

	function deleteFile( $filePath, $file )
	{
		@chmod( $filePath.$file, 0777 );
		@unlink( $filePath.$file );
		if($this->is_thumbnail){
			@chmod( $filePath.$this->thumb_prefix.$file, 0777 );
			@unlink( $filePath.$this->thumb_prefix.$file );
		}
	}

	function getShowFile( $fieldName='', $fileName='', $func_name='', $add_input='' )
	{
		$out   = '';
		$isUrl = false;
		if (!empty($fileName))
		{
			if (is_url($fileName))
			{
				$fileUrl  = $fileName;
				$fileName = 'online_thumb.jpg';
				$file     = _ROOT.'images/loading.gif';
				$isUrl    = true;
				$this->setPlaintext(true);
			}else{
				if (is_file(_ROOT.$fileName))
				{
					$file    = _ROOT . $fileName;
					$fileUrl = _URL . $fileName;
					$this->setPlaintext(true);
				}else{
					$file    = $this->folder . $fileName;
					$fileUrl = $this->folderUrl . $fileName;
				}
			}
			$btn   = '';
			$title = '';
			$nobr  = 0;
			if ( file_exists($file) && is_file( $file ) )
			{
				$size = '';
				if (!$isUrl)
				{
					$size  = file_size($file);
					$size  = !empty($size) ? ' &raquo; '.$size : '';
					if (preg_match('~([^/]+)$~', $fileName, $m))
					{
						$fileName = $m[1];
					}
				}
				$fileExt = strtolower( $this->getFileExtension( $fileName ) );
				if ( in_array( $fileExt, $this->arrImageExt ) )
				{
					if (!empty($size))
					{
						@list($w,$h) = @getimagesize($file);
						if (!empty($w) && !empty($h))
						{
							$fileName .= ' ('.money($w).' x '.money($h).' px)';
						}
					}
					$title = " <em>". $fileName .$size .'</em>';
					$cls   = $this->isImageClick ? ' class="img-thumbnail img-responsive formFile-clickable"' : ' class="img-thumbnail img-responsive"';
					$out  .= '<img src="'.$fileUrl.'"'.$cls.' title="'.trim(strip_tags($title)).'" />';
				}	else
				if ( $fileExt == 'swf' )
				{
					$title = " <em>". $fileName .$size .'</em>';
					$size  = $this->isImageClick ? array(640,480) : array(300,200);
					$out  .= '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="'.$size[0].'" height="'.$size[1].'">';
					$out  .= '	<param name="movie" value="'. $fileUrl .'">';
					$out  .= '	<param name="quality" value="high">';
					$out  .= '	<embed src="'. $fileUrl .'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="'.$size[0].'" height="'.$size[1].'" />';
					$out  .= '</object>';
					if ($this->isImageClick)
					{
						$out = '<button type="button" class="btn btn-default btn-sm formFile-clickable" data-modal="'.htmlentities($out).'" title="'.htmlentities($title).'"><span class="glyphicon glyphicon-film"></span></button>';
					}
				}	else {
					$i = _func('content', 'format', $fileExt);
					if (!empty($i))
					{
						$i .= '-';
					}
					$out .= '<h1>'.icon('fa-file-'.$i.'o', $i).'<br /><small><a href="'.$fileUrl.'" target="_blank">'.$fileName.$size.'</a></small></h1>';
					$nobr = 1;
				}
				if (!$this->isPlaintext)
				{
					$btn = <<<EOT
<input type="hidden" name="id_image_{$func_name}" value="0">
<input type="hidden" name="{$fieldName}" value="{$fileName}">
<input type="hidden" name="{$this->formName}_file_delete_image{$add_input}" value="0">
<button type="button" name="submit_delete_{$this->name}" class="btn btn-danger btn-sm" value="{$this->deleteButton->value}"
 onclick="if(confirm('Are you sure want to delete this file ?')){this.form.id_image_{$func_name}.value=1;this.form.reset();this.form.submit();return false;} return false;">
	<span class="glyphicon glyphicon-trash"></span>
</button>
EOT;
				}else $btn = '';
				if($this->isImageHover)
				{
					$out = $btn.tip($title, $out);
				} else {
					if (!$this->isImageClick)
					{
						if (!empty($out) && empty($nobr))
						{
							$out .= '<br />';
						}
						$out .= $btn.$title;
					}else{
						link_js(_PEA_URL.'includes/FormFile.js', false);
						if (!$this->isPlaintext) {
							$out .= $btn.$title;
						}
					}
				}
			}
		}
		return $out;
	}

	function getRollUpdateSQL( $i='' )
	{
		$submit_delete	= "submit_delete_".$this->name;
		if ( $i == '' && !is_numeric($i) )
		{
			$id_image	= "id_image_".$this->name;
			// dapatkan namaFile yang lama dulu
			$old_sql 	= "SELECT ". $this->fieldName ." FROM ". $this->tableName ."
												". $this->sqlCondition ."";
			$oldFileName= $this->db->GetOne( $old_sql );
			//lakukan upload jika tombol submit delete file tidak dipencet
			if ( @$_POST[$id_image] != '1' )
			{
				$fileName	= $this->uploadFile( $oldFileName );
				// jika berhasil upload atau submit delete di klik, file lama di hapus
				if ( !$fileName )
					$fileName	= $oldFileName;
			} else {
				$this->deleteFile( $this->folder, $oldFileName );
				$fileName	= '';
			}
		} else {
			$id_image	= "id_image_".$this->name."__". $i;

			// dapatkan namaFile yang lama dulu
			$old_sql 	= "SELECT ". $this->fieldName ." FROM ". $this->tableName
								.	" WHERE ". $this->tableId ." ='". $_POST[$this->formName."_".$this->tableId][$i] ."'";
			$oldFileName= $this->db->GetOne( $old_sql );

			//lakukan upload jika tombol submit delete file tidak dipencet
			if ( @$_POST[$id_image] != '1' )
			{
				$fileName	= $this->uploadFile( $oldFileName, $i );
				// jika berhasil upload atau submit delete di klik, file lama di hapus
				if ( !$fileName )
					$fileName	= $oldFileName;
			} else {
				$this->deleteFile( $this->folder, $oldFileName );
				$fileName	= '';
			}
		}
		return $query = "`". $this->fieldName ."` = '". $fileName ."', ";
	}

	function getAddSQL()
	{
		$fileName	= $this->uploadFile();
		if( $fileName )
		{
			$name			= $this->name;
			$out['into']	= $this->fieldName .", ";
			$out['value']	= "'". $fileName ."', ";
		} else {
			$out['into']	= $this->fieldName .", ";
			$out['value']	= "'', ";
		}
		return $out;
	}
	function getDeleteSQL($ids)
	{
		$sql = '';
		ids($ids);
		if (!empty($ids))
		{
			$old_sql 	= "SELECT ". $this->fieldName ." FROM ". $this->tableName .	" WHERE ". $this->tableId ." IN ({$ids})";
			$files = $this->db->getCol( $old_sql );
			foreach ($files as $afile)
			{
				$file = $this->folder.$afile;
				@unlink($file);
				if ($this->is_thumbnail)
				{
					$file = preg_replace('~(/)([^/]+)$~s', '/'.$this->thumb_prefix.'$2', $file);
					@unlink($file);
				}
			}
		}
		return $sql;
	}

	function getPlaintexOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		return $this->getReturn($this->getShowFile( $str_name, $str_value ));
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( $this->isPlaintext ) return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		$name	 = ( $str_name == '' ) ? $this->name : $str_name;
		$extra = $this->extra .' '. $str_extra;
		if(!is_dir($this->folder))
		{
			_func('path', 'create', $this->folder);
		}
		if ( preg_match( "~\]~s", $str_name ) )
		{
			if ( preg_match( "/(\w.*?)\[(\d{1,})\]\$/", $str_name, $match ) >= 1 )
			{
				$func_name	= $match[1] ."__". $match[2];
			}
		} else {
			$func_name	= $str_name;
		}
		$add_input = isset($match[2]) ? '['.$match[2].']' : '';
		$out	= $this->getShowFile( $name, $str_value, $func_name, $add_input );
		return $this->getReturn($out).'<input name="'.$name.'" type="file" size="'.$this->size.'"'.$extra.'>';
	}
}