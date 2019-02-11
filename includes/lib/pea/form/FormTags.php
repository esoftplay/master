<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
EXAMPLE: (yang diawali # adalah optional)
$form->edit->addInput('NAMABEBAS','tags');
#form->edit->input->NAMABEBAS->setTitle('Content Tags');
#form->edit->input->NAMABEBAS->setLinks($Bbc->mod['circuit'].'.tag&id=');		# jika ini digunakan maka semua tag yang muncul akan mengarah ke link tertentu
#form->edit->input->NAMABEBAS->setAllowNew(true);														# apakah form ini bisa membuat tag baru ataukah tidak
#form->edit->input->NAMABEBAS->setURL($Bbc->mod['circuit'].'.tag');					# digunakan untuk mengarahkan ke url lain selain defaultnya ( jika ini digunakan maka otomatis ->setAllowNew==false )
$form->edit->input->NAMABEBAS->setReferenceTable('bbc_content_tag');				# menentukan table reference yaitu table Tags nya
$form->edit->input->NAMABEBAS->setReferenceField('title','id');							# menentukan field yang digunakan untuk table reference
#form->edit->input->NAMABEBAS->setReferenceCount(true||'total_fieldname');	# setiap data yang masuk ke dalam tag akan dihitung jumlahnya untuk memudahkan dalam listing tag dgn kolom jumlah di peaRoll
$form->edit->input->NAMABEBAS->setReferenceCondition('lang_id='.lang_id());	# jika ada tambahan dalam pencarian di table reference (bisa banyak)
$form->edit->input->NAMABEBAS->setRelationTable('bbc_content_tag_list');		# masukkan nama table untuk relasi untuk hubungan many to many table
$form->edit->input->NAMABEBAS->setRelationField('content_id', 'tag_id');		# $input1 adalah field yg menghubungkan table relasi dgn table utama, $input2 adalah field yang menghubungkan dengan table reference
#form->edit->input->NAMABEBAS->setRelationTableId('list_id');								# Jika relation table menggunakan field auto_increment maka masukkan nama field di sini untuk memudahkan dalam menambah dan menghapus row
#form->edit->input->NAMABEBAS->setDB('db1');																# diisi string untuk database lain berdasarkan urutan di config.php

## LIHAT STRUKTUR TABLE DATABASE DI BAWAH UNTUK CONTOHNYA
## Contoh bikin form tanpa PEA _ROOT.'modules/user/tags.php';
*/
include_once _PEA_ROOT.'form/FormMulticheckbox.php';
class FormTags extends FormMulticheckbox
{
	var $links           = '';
	var $url             = '';
	var $isTotalCount    = false;
	var $isAllowedNew    = false;
	var $fieldRefTotal   = 'total';
	var $fieldRefUpdated = '';
	var $fieldRefCreated = '';
	function __construct()
	{
		$this->type = 'tags';
		$this->setDelimiter(',');
		$this->isAllowedNew = _ADMIN ? true : false;
		$this->setIsNeedDbObject( true );
		$this->setIsIncludedInSelectQuery( false );
		$this->setIsIncludedInDeleteQuery(true); // agar getDeleteSQL($ids) di eksekusi sebelum dataRow dihapus
		$this->setIsIncludedInSearch( false );
	}
	function setLinks( $str_links = '' )
	{
		$this->links = $str_links;
	}
	function setURL( $str_url = '' )
	{
		$this->url = site_url($str_url);
		$this->setAllowNew(false);
	}
	function setAllowNew($boolean = true)
	{
		if (!empty($this->url))
		{
			if ($boolean)
			{
				$this->isAllowedNew = $boolean;
			}else{
				$this->isAllowedNew = false;
			}
		}else{
			$this->isAllowedNew = $boolean;
		}
	}
	function setReferenceTable( $str_reference_table )
	{
		$this->referenceTable	= $str_reference_table;
		$fields = $this->db->getCol("SHOW FIELDS FROM {$this->referenceTable}");
		if (in_array('total', $fields))
		{
			$this->setReferenceCount('total');
		}
		if (in_array('created', $fields))
		{
			$this->fieldRefCreated = 'created';
		}
		if (in_array('updated', $fields))
		{
			$this->fieldRefUpdated = 'updated';
		}
	}
	function setReferenceCount($bool_or_string = true)
	{
		if (is_string($bool_or_string))
		{
			$this->isTotalCount    = true;
			$this->fieldRefTotal = $bool_or_string;
		}else{
			$this->isTotalCount = $bool_or_string ? true : false;
		}
	}
	function getDataOfReferenceTable($str_value)
	{
		if (empty($this->referenceTable) || empty($this->referenceField['value']) || empty($this->referenceField['label']))
		{
			die( __METHOD__.':: setReferenceTable($str_reference_table) dan setReferenceField($str_reference_label_field, $str_reference_value_field) harus diset untuk menentukan table dan field yang digunakan.' );
		}
		$out = array();
		$str = $this->getValue($str_value);
		if (!empty($str))
		{
			$sql = "SELECT ". $this->referenceField['value'] ." AS `value`, "
					. $this->referenceField['label'] ." AS `label`"
					. "	FROM ". $this->getTableCondition($this->referenceTable, $this->sqlReferenceCondition);
			$sql = $this->getTableCondition($sql, array($this->referenceField['value'].' IN ('.$str.')'));
			if (!preg_match('~\s+order\s+by\s+~is', $sql))
			{
				$sql .= ' ORDER BY `label` ASC';
			}
			$result	= $this->db->getAssoc( $sql );
			$ids = explode($this->delimiter, $str);
			foreach ($ids as $id)
			{
				if (isset($result[$id]))
				{
					$out[$id] = $result[$id];
				}
			}
		}
		return $out;
	}
	function getValue($value)
	{
		if (is_array($value))
		{
			$value = implode($this->delimiter, $value);
		}
		$str = preg_replace('~[^0-9'.$this->delimiter.']+~s', $this->delimiter, $value);
		$r   = array_unique(explode($this->delimiter, $str));
		return trim(implode($this->delimiter, $r), $this->delimiter);
	}
	function getToken()
	{
		$output = array(
			'table'  => $this->getTableCondition($this->referenceTable, $this->sqlReferenceCondition),
			'field'  => $this->referenceField['label'],
			'id'     => $this->referenceField['value'],
			'expire' => strtotime('+2 HOURS'),
			);
		if (!empty($this->links))
		{
			$output['links'] = $this->links;
		}
		if (!empty($this->db_str))
		{
			$output['db'] = $this->db_str;
		}
		return encode(json_encode($output));
	}
	function updateReferenceData($ref_id)
	{
		if (is_numeric($ref_id) && $ref_id > 0)
		{
			$sql = array();
			if ($this->isTotalCount)
			{
				$query = "SELECT COUNT(*) FROM {$this->relationTable} WHERE ".$this->relationField['reference'].'=\''.$ref_id.'\'';
				$query = $this->getTableCondition($query,$this->sqlRelationCondition);
				$sql[] = $this->fieldRefTotal.'='.intval($this->db->getOne($query));
			}
			if (!empty($this->fieldRefUpdated))
			{
				$sql[] = $this->fieldRefUpdated.'=NOW()';
			}
			if (!empty($sql))
			{
				$this->db->Execute("UPDATE {$this->referenceTable} SET ".implode(', ', $sql)." WHERE ".$this->referenceField['value'].'='.$ref_id);
			}
		}
	}
	function getRollUpdateSQL( $i='' )
	{
		$name    = $this->name;
		if ( $i == '' && !is_numeric($i) )
		{
			/*==============================================
			UPDATE SQL DARI EDIT FORM
			==============================================*/
			$query       = 'SELECT '.$this->tableId.' FROM '.$this->tableName .' '. $this->sqlCondition;
			$post        = isset($_POST[$name]) ? $_POST[$name] : array();
			$mainTableId = $this->actionType == 'add' ? 0 : (!empty($this->parent->arrResult[$this->tableId]) ? intval($this->parent->arrResult[$this->tableId]) : intval($this->db->getOne($query)));
		}else{
			/*==============================================
			UPDATE SQL DARI ROLL FORM
      ==============================================*/
			$post        = isset( $_POST[$name][$i] ) ? $_POST[$name][$i] : array();
			$mainTableId = @intval($_POST[$this->formName.'_'.$this->tableId][$i]);
		}
		$post = is_array($post) ? array_unique($post) : array($post);
		$newTags = array();
		$add_sql = !empty($this->sqlRelationCondition) ? ', '.implode(', ', $this->sqlRelationCondition) : '';
		if (empty($this->relationTableId))
		{
			$old_ids = $this->getDataFromRelationTable($mainTableId);
			foreach( $post AS $id )
			{
				if (is_numeric($id))
				{
					if (!in_array($id, $old_ids))
					{
						$q = 'INSERT INTO '.$this->relationTable.' SET '.$this->relationField['main'].'='.$mainTableId
								.', '.$this->relationField['reference'].'=\''. $id.'\''.$add_sql;
						$this->db->Execute( $q );
						$this->updateReferenceData($id);
					}
				}else{
					$newTags[] = $id;
				}
			}
			foreach ($old_ids as $id)
			{
				if (!in_array($id, $post))
				{
					$sql = 'DELETE FROM '. $this->relationTable.' WHERE '.$this->relationField['main'].'='.$mainTableId;
					$sql = $this->getTableCondition($sql,$this->sqlRelationCondition);
					$sql = $this->getTableCondition($sql,array($this->relationField['reference'].'='.$id));
					$this->db->Execute( $sql );
					$this->updateReferenceData($id);
				}
			}
		}else{
			$q       = 'SELECT '.$this->relationTableId.', '.$this->relationField['reference'].' FROM '.$this->relationTable;
			$c       = array_merge($this->sqlRelationCondition, array($this->relationField['main'].'='.$mainTableId));
			$old_ids = $this->db->getAssoc($this->getTableCondition($q,$c));
			// HAPUS ID YANG ADA DI TABLE RELATION TAPI TIDAK ADA DI POSTINGAN
			foreach ($old_ids as $tableId => $ref_id)
			{
				if (!in_array($ref_id, $post))
				{
					if (!is_numeric($tableId)) {
						$tableId = "'{$tableId}'";
					}
					$this->db->Execute("DELETE FROM {$this->relationTable} WHERE {$this->relationTableId}={$tableId}");
					$this->updateReferenceData($ref_id);
				}
			}
			// INSERT ID YANG TIDAK ADA DI TABLE RELATION TAPI ADA DI POSTINGAN
			foreach( $post as $ref_id )
			{
				if(!in_array($ref_id, $old_ids))
				{
					if (is_numeric($ref_id))
					{
						$q = 'INSERT INTO '.$this->relationTable.' SET '.$this->relationField['main'].'='.$mainTableId
							.', '.$this->relationField['reference'].'=\''.$ref_id.'\''.$add_sql;
						$this->db->Execute($q);
						$this->updateReferenceData($ref_id);
					}else{
						$newTags[] = $ref_id;
					}
				}
			}
		}
		if (!empty($newTags) && $this->isAllowedNew)
		{
			$this->db->cache_clean();
			foreach ($newTags as $tag)
			{
				$q = 'INSERT INTO '.$this->referenceTable.' SET '.$this->referenceField['label'].'=\''.$this->cleanSQL($tag).'\'';
				if ($this->isTotalCount)
				{
					$q .= ', '.$this->fieldRefTotal.'=1';
				}
				if (!empty($this->fieldRefCreated))
				{
					$q .= ', '.$this->fieldRefCreated.'=NOW()';
				}
				if (!empty($this->fieldRefUpdated))
				{
					$q .= ', '.$this->fieldRefUpdated.'=NOW()';
				}
				$q = $this->getTableCondition($q, $this->sqlReferenceCondition, 'INSERT');
				if($this->db->Execute($q))
				{
					$ref_id = $this->db->Insert_ID();
					if (!empty($ref_id))
					{
						$this->db->Execute("INSERT INTO {$this->relationTable} SET ".$this->relationField['reference'].'='.$ref_id.', '.$this->relationField['main'].'='.$mainTableId);
					}
				}
			}
		}
		return '';
	}
	function getAddSQL()
	{
		$add_sql      = !empty($this->sqlRelationCondition) ? ', '.implode(', ', $this->sqlRelationCondition) : '';
		$pendingQuery = array();
		$post         = isset($_POST[$this->name]) && is_array($_POST[$this->name]) ? array_unique($_POST[$this->name]) : array();
		if (!empty($post))
		{
			$newTags        = array();
			$pendingQuery[] = '_PENDING_QUERY';
			if (!empty($this->sqlReferenceCondition))
			{
				$add_sql2 = ' AND '.implode(' AND ', $this->sqlReferenceCondition);
			}else{
				$add_sql2 = '';
			}
			foreach ($post as $ref_id)
			{
				if (is_numeric($ref_id))
				{
					$pendingQuery[] = 'INSERT INTO '.$this->relationTable
					.' SET '.$this->relationField['main'].'=\'_INSERT_ID\''
					.", `".$this->relationField['reference']."`='{$ref_id}'"
					.$add_sql;
					if ($this->isTotalCount && !empty($this->fieldRefUpdated))
					{
						$q = 'UPDATE '.$this->referenceTable.' SET ';
						if ($this->isTotalCount)
						{
							$q .= '`'.$this->fieldRefTotal.'`=('.$this->fieldRefTotal.'+1)';
						}
						if (!empty($this->fieldRefUpdated))
						{
							if ($this->isTotalCount)
							{
								$q .= ', ';
							}
							$q .= '`'.$this->fieldRefUpdated.'`=NOW()';
						}
						$q .= ' WHERE `'.$this->referenceField['value'].'`='.$ref_id.$add_sql2;
						$pendingQuery[] = $q;
					}
				}else{
					$newTags[] = $ref_id;
				}
			}
			if (!empty($newTags) && $this->isAllowedNew)
			{
				$this->db->cache_clean();
				foreach ($newTags as $tag)
				{
					$q = 'INSERT INTO '.$this->referenceTable.' SET '.$this->referenceField['label'].'=\''.$this->cleanSQL($tag).'\'';
					if ($this->isTotalCount)
					{
						$q .= ', '.$this->fieldRefTotal.'=1';
					}
					if (!empty($this->fieldRefCreated))
					{
						$q .= ', '.$this->fieldRefCreated.'=NOW()';
					}
					if (!empty($this->fieldRefUpdated))
					{
						$q .= ', '.$this->fieldRefUpdated.'=NOW()';
					}
					$q = $this->getTableCondition($q, $this->sqlReferenceCondition, 'INSERT');
					if($this->db->Execute($q))
					{
						$ref_id = $this->db->Insert_ID();
						if (!empty($ref_id))
						{
							$pendingQuery[] = 'INSERT INTO '.$this->relationTable
							.' SET '.$this->relationField['main'].'=\'_INSERT_ID\''
							.", `".$this->relationField['reference']."`='{$ref_id}'"
							.$add_sql;
						}
					}
				}
			}
		}
		return $pendingQuery;
	}
	function getDeleteSQL($ids)
	{
		$sql = '';
		ids($ids);
		if (!empty($ids))
		{
			if ($this->relationTable)
			{
				$sql = "DELETE FROM ". $this->relationTable." WHERE ".$this->relationField['main']." IN ({$ids})";
				$sql = $this->getTableCondition($sql, $this->sqlRelationCondition);
				if ($this->isTotalCount)
				{
					$q = "SELECT ".$this->relationField['reference']." FROM {$this->relationTable} WHERE ".$this->relationField['main']." IN ({$ids})";
					$q = $this->getTableCondition($q, $this->sqlRelationCondition);
					$ref_ids = $this->db->getCol($q);
					if (!empty($ref_ids))
					{
						if ($ref_ids == array_unique($ref_ids))
						{
							$q = "UPDATE {$this->referenceTable} SET {$this->fieldRefTotal}=({$this->fieldRefTotal} - 1)";
							if (!empty($this->fieldRefUpdated))
							{
								$q .= ", {$this->fieldRefUpdated}=NOW()";
							}
							$q .= " WHERE ".$this->referenceField['value']." IN (".implode(',', $ref_ids).")";
							$q = $this->getTableCondition($q, $this->sqlReferenceCondition);
							$this->db->Execute($q);
						}else{
							foreach ($ref_ids as $id)
							{
								$q = "UPDATE {$this->referenceTable} SET {$this->fieldRefTotal}=({$this->fieldRefTotal} - 1)";
								if (!empty($this->fieldRefUpdated))
								{
									$q .= ", {$this->fieldRefUpdated}=NOW()";
								}
								$q .= " WHERE ".$this->referenceField['value']."={$id}";
								$q = $this->getTableCondition($q, $this->sqlReferenceCondition);
								$this->db->Execute($q);
							}
						}
					}
				}
			}
		}
		return $sql;
	}
	function getReportOutput( $str_value = '' )
	{
		$relation_ids = $this->getDataFromRelationTable($str_value);
		$output       = $this->getDataOfReferenceTable($relation_ids);
		return implode($this->delimiter.' ', $output);
	}
	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if (empty($this->referenceTable))
		{
			die( __METHOD__.':: harus menentukan setRelationTable( $str_relation_table ) untuk menentukan field yang digunakan di tabel relasi' );
		}
		if (empty($this->relationField))
		{
			die( __METHOD__.':: tentukan setRelationField( $str_main_id_field, $str_reference_id_field ) terlebih dahulu setelah menentukan setRelationTable( $str_relation_table )' );
		}
		if ($this->isPlaintext)
		{
			return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		}
		$name  = ( $str_name == '' ) ? $this->name : $str_name;
		$extra = $this->extra.' '.$str_extra;
		$ids   = $this->getDataFromRelationTable($str_value);
		$array = $this->getDataOfReferenceTable($ids);
		$attr  = 'name="'.$name.'"';
		if ($this->isAllowedNew)
		{
			$attr .= ' data-isallowednew="1"';
		}
		if (!empty($this->links))
		{
			$attr .= ' data-href="'.$this->links.'"';
		}
		if (!empty($this->url))
		{
			$attr .= ' data-url="'.$this->url.'"';
		}
		link_js(_PEA_URL.'includes/FormTags.js', false);
		$extra = str_replace('"form-control"', '"form-control tags"', $extra);
		ob_start();
		?>
		<div <?php echo $extra; ?>>
			<span>
				<?php
				foreach ($array as $i => $t)
				{
					$link = !empty($this->links) ? 'href="'.$this->links.$i.'"' : '';
					?>
					<input type="hidden" name="<?php echo $name; ?>[]" value="<?php echo $i; ?>" title="<?php echo $t; ?>" />
					<?php
				}
				?>
			</span>
			<span data-token="<?php echo $this->getToken(); ?>" <?php echo $attr; ?> contenteditable></span>
		</div>
		<?php
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
}

/*
## TINGGAL GANTI `module` MENJADI NAMA TABLE UTAMA DALAM MODULE YANG ANDA BUAT ##

CREATE TABLE `module_tag` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT '',
  `total` int(11) DEFAULT '0',
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `updated` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `total` (`total`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `module_tag_list` (
  `list_id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(255) unsigned DEFAULT '0',
  `module_id` int(255) unsigned DEFAULT '0',
  PRIMARY KEY (`list_id`),
  KEY `tag_id` (`tag_id`),
  KEY `module_id` (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `module_tag_list`
  ADD CONSTRAINT `module_tag_list_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `module_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `module_tag_list`
  ADD CONSTRAINT `module_tag_list_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `module` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

*/