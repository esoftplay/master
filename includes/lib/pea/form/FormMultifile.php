<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
EXAMPLE: (yang diawali # adalah optional)
$form->edit->addInput('images','multifile');
#form->edit->input->images->setTitle('Other Images');
$form->edit->input->images->setFolder($Bbc->mod['dir'].$id.'/');
#form->edit->input->images->setResize(640);
#form->edit->input->images->setThumbnail(150, $prefix = 'thumb' || 'thumb/');
#form->edit->input->images->setWatermark($absolute_path_to_watermark_image_png, $position_or_param='center'); // position = center, top-left, top-right, bottom-left, bottom-right
#form->edit->input->images->setFirstField('image'); // ini akan memasukkan secara otomatis image pertama kedalam field di DB

// JIKA MENGGUNAKAN RELATION TABLE SEPERTI DI BAWAH MAKA 'images' AKAN MENJADI FIELD DENGAN NAMA BEBAS
// ATAU TIDAK MENJADI FIELD DALAM TABLE DATABASE LAGI SEHINGGA TIDAK DI QUERY DALAM `SELECT`
#form->edit->input->images->setRelationTable('gallery_image');
#form->edit->input->images->setRelationField('gallery_id', 'image,title,description'); // input ke 2 adalah field2 apa yang dipakai (opsi nya hanya ada 3)
#form->edit->input->images->setRelationCondition('publish=1'); // penambahan field secara manual

// HARUS ADA FIELD `images` (->images->) JIKA TIDAK MENGGUNAKAN RELATION TABLE
// SEDANGKAN TYPE FIELD == `images` text NOT NULL
*/
include_once _PEA_ROOT.'form/FormMulticheckbox.php';
class FormMultifile extends FormMulticheckbox
{
	var $folder;
	var $folderUrl;
	var $isFolderSet = false;
	var $actionURL;
	var $params;
	var $relationTable;
	var $relationTableId;
	var $relationField;
	var $addCondition;
	var $arrExt;
	var $isOrderby;
	var $deleted;
	var $mainTableId;
	var $firstField;
	var $isFieldTitleExist       = true;
	var $isFieldDescriptionExist = true;

	function __construct()
	{
		global $Bbc;
		$this->type    = 'multifile';
		$this->params  = array();
		$this->deleted = array();
		$this->setDelimiter();
		$this->setIsNeedDbObject( true );
		$this->setIsIncludedInSelectQuery( true );
		$this->setIsIncludedInDeleteQuery(true); // agar getDeleteSQL($ids) di eksekusi sebelum dataRow dihapus
		$this->setFolder($Bbc->mod['dir'].'0/');
		$this->setCaption('Add or Drop image here');
		$this->setAllowedExtension();
		$this->isFolderSet = false;
	}
	function setParent($obj)
	{
		$this->parent = $obj;
	}
	function setFolder( $str_folder='', $str_folder_url='' )
	{
		if(empty($str_folder_url))
		{
			if(preg_match("#^"._ROOT."#is", $str_folder))
			{
				$str_folder     = preg_replace("#^"._ROOT."#", '', $str_folder);
				$str_folder_url = _URL.$str_folder;
			}else{
				$str_folder_url = _URL.$str_folder;
			}
		}
		if (substr($str_folder, -1) != '/')
		{
			$str_folder .= '/';
		}
		if (substr($str_folder_url, -1) != '/')
		{
			$str_folder_url .= '/';
		}
		$this->params['path'] = array(
			'folder' => $str_folder,
			'tmp'    => menu_save($str_folder).'/',
			);
		$this->folder_tmp  = _CACHE.$this->params['path']['tmp'];
		$this->folder      = _ROOT.$str_folder;
		$this->folderUrl   = $str_folder_url;
		$this->isFolderSet = true;
	}
	function setActionURL($url='')
	{
		$this->actionURL = $url;
	}
	function setResize( $size, $is_resize = true )
	{
		if ($is_resize)
		{
			$this->params['resize'] = $size;
		}else{
			unset($this->params['resize']);
		}
	}
	function setThumbnail( $sizes, $prefix = 'thumb')
	{
		if(substr($prefix, -1) != '/')
		{
			$prefix .= '_';
			$is_dir = 0;
		}else{
			$is_dir = 1;
		}
		$this->params['thumbnail'] = array(
			'size'   => $sizes,
			'prefix' => $prefix,
			'is_dir' => $is_dir,
			);
	}
	/*
	$params = array(
    'wm_vrt_alignment' => 'middle', // top, middle, bottom
    'wm_hor_alignment' => 'center', // left, center, right
    'wm_opacity'       => 50,
    'wm_x_transp'      => 4,
    'wm_y_transp'      => 4,
    );
	*/
	function setWatermark( $image, $params = array() )
	{
		if(is_file($image))
		{
			$arr = array(
			  'wm_type'					=> 'overlay'
			, 'wm_overlay_path' => $image
			);
			if (is_string($params))
			{
				$params = array('wm_position' => $params);
			}
			$this->params['watermark'] = array_merge($arr, $params);
		}else{
			unset($this->params['watermark']);
		}
	}
	function setFirstField($fieldName)
	{
		$this->firstField = $fieldName;
	}
	function setRelationTable( $str_relation_table )
	{
		if(!empty($str_relation_table))
		{
			$this->relationTable	= $str_relation_table;
			$r = $this->db->getCol("SHOW FIELDS FROM ".$this->relationTable);
			if (in_array('orderby', $r))
			{
				$this->isOrderby = true;
			}
			$this->relationTableId = $r[0];
			$this->setIsIncludedInSelectQuery( false );
		}
	}
	function setRelationField( $str_main_id_field, $str_reference_image_field='image,title,description' )
	{
		if (empty($this->relationTable))
		{
			die( 'FormMulticheckbox:: tentukan setRelationTable( $str_relation_table ) terlebih dahulu baru menentukan field yang digunakan di tabel relasi' );
		}
		$this->relationField['main'] = $str_main_id_field;
		if (!is_array($str_reference_image_field))
		{
			$fields = array();
			$r      = explode(',', $str_reference_image_field);
			foreach ($r as $f)
			{
				if (!empty($f))
				{
					$fields[] = $f;
				}
			}
		}else{
			$fields = $str_reference_image_field;
		}
		$this->isFieldTitleExist       = in_array('title', $fields);
		$this->isFieldDescriptionExist = in_array('description', $fields);
		if ($this->isOrderby && !in_array('orderby', $fields))
		{
			$fields[] = 'orderby';
		}
		$this->relationField['fields'] = $fields;
	}
	function getDataFromRelationTable($str_value)
	{
		$output = array();
		if ($this->actionType=='add')
		{
			return (array)$this->defaultValue;
		}
		if(!empty($this->relationTable))
		{
			if (empty($this->relationField['fields']) || empty($this->relationField['main']))
			{
				die( 'FormMultifile:: setRelationField($str_main_id_field, $str_reference_image_field) harus diset untuk menentukan table dan field yang digunakan.' );
			}
			if (!empty($str_value))
			{
				$q = 'SELECT '. implode(', ', $this->relationField['fields'])
						.' FROM '. $this->relationTable
						.' WHERE '.$this->relationField['main'].'='.intval($str_value);
				$q = $this->getTableCondition($q, $this->sqlRelationCondition);
				if ($this->isOrderby && !preg_match('~ order by ~is', $q))
				{
					$q .= ' ORDER BY orderby ASC';
				}
				$output	= $this->db->getAll( $q );
			}
		}
		if(empty($output))
		{
			$output = @config_decode($str_value, 1);
		}
		return $output;
	}
	function setAllowedExtension( $arr_allowed_extension = array('jpg', 'gif', 'png', 'bmp') )
	{
		$this->arrExt        = $arr_allowed_extension;
		$this->params['ext'] = $this->arrExt;
	}
	function getMainTableId($i)
	{
		if ( !is_numeric($i) )
		{
			// UPDATE SQL DARI EDIT / ADD FORM
			$query       = 'SELECT '.$this->tableId.' FROM '.$this->tableName .' '. $this->sqlCondition;
			$this->mainTableId = $this->actionType == 'add' ? 0 : (!empty($this->parent->arrResult[$this->tableId]) ? intval($this->parent->arrResult[$this->tableId]) : intval($this->db->getOne($query)));
		}else{
			// UPDATE SQL DARI ROLL FORM
			$this->mainTableId = @intval($_POST[$this->formName.'_'.$this->tableId][$i]);
		}
		return $this->mainTableId;
	}
	function getPostData($i='')
	{
		$mainTableId = $this->getMainTableId($i);
		if (!$this->isFolderSet)
		{
			global $Bbc;
			$this->setFolder($Bbc->mod['dir'].$mainTableId);
		}
		$output   = array();
		$post     = @$_POST[$this->name];
		$imgField = !empty($this->relationField['fields']) ? reset($this->relationField['fields']) : 'image';
		$orderby  = 1;
		if (is_numeric($i))
		{
			$post = $post[$i];
		}
		if (!empty($post['order']))
		{
			if (!empty($this->folder) && !file_exists($this->folder))
			{
				_func('path', 'create', $this->folder);
			}
			$fields = !empty($this->relationField['fields']) ? $this->relationField['fields'] : array('image','title','description','orderby');
			unset($fields[0]);
			$k = array_search('orderby', $fields);
			if ($k)
			{
				unset($fields[$k]);
			}
			foreach ($post['order'] as $j => $f)
			{
				$dt = array($imgField => $f);
				if (!empty($dt[$imgField]))
				{
					foreach ($fields as $f)
					{
						$dt[$f] = @$post[$f][$j];
					}
					if ($this->isOrderby)
					{
						$dt['orderby'] = $orderby;
					}
					$output[] = $dt;
					$orderby++;
				}
			}
		}
		if (!empty($this->firstField))
		{
			$this->parent->addExtraField($this->firstField, @$output[0][$imgField]);
		}
		return $output;
	}
	function getRollUpdateSQL( $i='' )
	{
		$name        = $this->name;
		$output      = '';
		$post        = $this->getPostData($i);
		$mainTableId = $this->mainTableId;
		if($this->relationTable)
		{
			$imgField = !empty($this->relationField['fields']) ? reset($this->relationField['fields']) : 'image';
			$add_sql  = !empty($this->sqlRelationCondition) ? ', '.implode(', ', $this->sqlRelationCondition) : '';
			$query    = ' '. $this->relationTable.' WHERE '.$this->relationField['main'].'='.$mainTableId;
			$query    = $this->getTableCondition($query,$this->sqlRelationCondition);
			$before   = $this->db->getAssoc("SELECT * FROM ".$query);
			$images   = $image_before = $ids_before = array();
			foreach ($post as $dt)
			{
				$images[] = $dt[$imgField];
			}
			if (!empty($before))
			{
				foreach ($before as $id => $data)
				{
					$image_before[] = $data[$imgField];
					if (!in_array($data[$imgField], $images))
					{
						$this->db->Execute("DELETE FROM {$this->relationTable} WHERE {$this->relationTableId}={$id}");
						@unlink($this->folder.$data[$imgField]);
						if (!empty($this->params['thumbnail']))
						{
							@unlink($this->folder.$this->params['thumbnail']['prefix'].$data[$imgField]);
						}
					}else{
						// cari id nya di DB
						foreach ($post as $dt)
						{
							if ($dt[$imgField] == $data[$imgField])
							{
								$ids_before[$data[$imgField]] = $id;
								break;
							}
						}
					}
				}
			}
			foreach ($post AS $dt)
			{
				$image = $dt[$imgField];
				$sql   = ' '.$this->relationTable.' SET '.$this->relationField['main'].'='.$mainTableId.$add_sql;
				foreach ((array)$dt as $field => $value)
				{
					$sql .= ', `'.$field.'`=\''.$this->cleanSQL($value).'\'';
				}
				if (!in_array($image, $image_before))
				{
					if (file_exists($this->folder_tmp.$image) && !file_exists($this->folder.$image))
					{
						if(@rename($this->folder_tmp.$image, $this->folder.$image))
						{
							$this->db->Execute('INSERT INTO '.$sql);
							if (!empty($this->params['thumbnail']))
							{
								$thumbnail = $this->params['thumbnail']['prefix'].$image;
								if ($this->params['thumbnail']['is_dir'] && !is_dir($this->folder.$this->params['thumbnail']['prefix']))
								{
									_func('path', 'create', $this->folder.$this->params['thumbnail']['prefix']);
								}
								@rename($this->folder_tmp.$thumbnail, $this->folder.$thumbnail);
							}
						}
					}
				}else{
					$this->db->Execute('UPDATE '.$sql.' WHERE '.$this->relationTableId.'='.@intval($ids_before[$image]));
				}
			}
		}else{
			$query  = 'SELECT '.$this->fieldName.' FROM '.$this->tableName .' '. $this->sqlCondition;
			$before = config_decode($this->db->getOne($query), 1);
			$images = array();
			foreach ($post as $dt)
			{
				$images[] = $dt['image'];
			}
			if (!empty($before))
			{
				foreach ($before as $dt)
				{
					if (!in_array($dt['image'], $images))
					{
						@unlink($this->folder.$dt['image']);
						if (!empty($this->params['thumbnail']))
						{
							@unlink($this->folder.$this->params['thumbnail']['prefix'].$dt['image']);
						}
					}
				}
			}
			if (!empty($images))
			{
				foreach ($images as $image)
				{
					if (file_exists($this->folder_tmp.$image) && !file_exists($this->folder.$image))
					{
						@rename($this->folder_tmp.$image, $this->folder.$image);
						if (!empty($this->params['thumbnail']))
						{
							$thumbnail = $this->params['thumbnail']['prefix'].$image;
							if ($this->params['thumbnail']['is_dir'] && !is_dir($this->folder.$this->params['thumbnail']['prefix']))
							{
								_func('path', 'create', $this->folder.$this->params['thumbnail']['prefix']);
							}
							@rename($this->folder_tmp.$thumbnail, $this->folder.$thumbnail);
						}
					}
				}
			}
			$output = "`".$this->fieldName."` = '".$this->cleanSQL(config_encode($post))."', ";
		}
		_func('path');
		$r = path_list($this->folder_tmp);
		if (empty($r) || (!empty($this->params['thumbnail']['is_dir']) && $r == array(trim($this->params['thumbnail']['prefix']), '/')))
		{
			path_delete($this->folder_tmp);
		}
		return $output;
	}
	function getAddAction($db, $Insert_ID)
	{
		if ($Insert_ID > 0)
		{
			if (file_exists($this->folder) && preg_match('~/0/?$~', $this->folder))
			{
				$this->newFolder = preg_replace('~0/?$~s', $Insert_ID.'/', $this->folder);
				if (!file_exists($this->newFolder))
				{
					@rename($this->folder, $this->newFolder);
				}
			}
		}
	}
	function getAddSQL()
	{
		$name         = $this->name;
		$add_sql      = !empty($this->sqlRelationCondition) ? ', '.implode(', ', $this->sqlRelationCondition) : '';
		$pendingQuery = array();
		$postData     = $this->getPostData();
		if($this->relationTable)
		{
			$pendingQuery[]		= '_PENDING_QUERY';
			foreach ($postData as $data)
			{
				$fields = array();
				$sql = $add_sql;
				foreach ($data as $field => $value)
				{
					$fields[] = "`{$field}`='".$this->cleanSQL($value)."'";
				}
				if (!empty($fields))
				{
					$sql .= ', '.implode(', ', $fields);
				}
				$pendingQuery[] = 'INSERT INTO '.$this->relationTable.' SET '.$this->relationField['main'].'=\'_INSERT_ID\''.$sql;
			}
		}else{
			$pendingQuery['into']  = $this->fieldName .", ";
			$pendingQuery['value'] = "'". $this->cleanSQL(config_encode($postData)) ."', ";
		}
		_func('path');
		if (!empty($postData))
		{
			$imgField = !empty($this->relationField['fields']) ? reset($this->relationField['fields']) : 'image';
			if (file_exists($this->folder))
			{
				foreach ($postData as $data)
				{
					$file = $data[$imgField];
					if(@rename($this->folder_tmp.$file, $this->folder.$file))
					{
						if (!empty($this->params['thumbnail']))
						{
							if (!empty($this->params['thumbnail']['is_dir']))
							{
								path_create($this->params['thumbnail']['prefix']);
							}
							@rename($this->folder_tmp.$this->params['thumbnail']['prefix'].$file, $this->folder.$this->params['thumbnail']['prefix'].$file);
						}
					}
				}
				path_delete($this->folder_tmp);
			}else{
				@rename($this->folder_tmp, $this->folder);
			}
		}else{
			path_delete($this->folder_tmp);
		}
		return $pendingQuery;
	}
	function getDeleteSQL($ids)
	{
		$sql = '';
		ids($ids);
		if (!empty($ids))
		{
			$files = array();
			if ($this->relationTable)
			{
				$imgField = !empty($this->relationField['fields']) ? reset($this->relationField['fields']) : 'image';
				$sql      = "DELETE FROM ". $this->relationTable." WHERE ".$this->relationField['main']." IN ({$ids})";
				$sql      = $this->getTableCondition($sql, $this->sqlRelationCondition);
				$q        = preg_replace('~^DELETE ~', 'SELECT '.$imgField.' ', $sql);
				$files    = $this->db->getCol($q);
			}else{
				$files = @$_POST[$this->name]['order'];
			}
			if (!empty($files))
			{
				foreach ($files as $file)
				{
					@unlink($this->folder.$file);
					if (!empty($this->params['thumbnail']))
					{
						@unlink($this->folder.$this->params['thumbnail']['prefix'].$file);
					}
				}
				if ($this->isFolderSet)
				{
					_func('path');
					$r = explode(',', $ids);
					foreach ($r as $d)
					{
						path_delete($this->folder.$d);
					}
				}
			}
		}
		return $sql;
	}
	function getReportOutput( $str_value = '' )
	{
		$out	= $this->getDataFromRelationTable($str_value);
		if (!empty($out) && is_array($out))
		{
			$fields = array_keys(reset($out));
			$out    = table($out, $fields);
		}
		return $out;
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if (!$this->isFolderSet)
		{
			global $Bbc;
			$path = $Bbc->mod['dir'];
			if ($this->isIncludedInSelectQuery)
			{
				$i     = $this->parent->arrResult[$this->tableId];
				$path .= intval($i);
			}else{
				$path .= intval($str_value);
			}
			$this->setFolder($path);
		}
		if ($this->isPlaintext)
		{
			return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		}
		$extra  = str_replace(' class="form-control"', '', $this->extra.' '. $str_extra);
		$values = $this->getDataFromRelationTable($str_value);
		$path   = str_replace(_ROOT, _URL, _CACHE).$this->params['path']['tmp'];
		ob_start();
		$this->params['folder'] = $this->folder;
		$this->params['expire'] = strtotime('+2 HOUR');
		link_js(_PEA_URL.'includes/FormMultifile.js', false);
		link_css(_PEA_URL.'includes/FormMultifile.css', false);
		$extra .= ' data-name="'.$this->name.'"';
		$extra .= ' data-path="'.$path.'"';
		$extra .= ' data-params="'.encode(json_encode($this->params)).'"';
		if ($this->isFieldTitleExist)
		{
			$extra .= ' data-title';
		}
		if ($this->isFieldDescriptionExist)
		{
			$extra .= ' data-description';
		}
		if (!empty($this->actionURL))
		{
			$extra .= ' data-action="'.$this->actionURL.'"';
		}
		?>
<div class="file-uploader"<?php echo $extra; ?>>
	<noscript> <p>Please enable JavaScript to use file uploader.</p> </noscript>
	<div class="hidden">
		<?php
		foreach ($values as $data)
		{
			$extra = '';
			if ($this->isFieldTitleExist)
			{
				$extra .= ' data-title="'.htmlentities($data['title']).'"';
			}
			if ($this->isFieldDescriptionExist)
			{
				$extra .= ' data-description="'.htmlentities($data['description']).'"';
			}
			echo '<img src="'.$this->folderUrl.$data['image'].'"'.$extra.' />';
		}
		?>
	</div>
</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}