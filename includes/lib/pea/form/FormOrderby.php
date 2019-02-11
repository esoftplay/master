<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// SAMPLE HOW TO USE
// $form->add->addInput('orderby','orderby');
// $form->add->input->orderby->setAddCondition('WHERE cat_id=2');
// $form->add->input->orderby->setDB('db1');											# diisi string untuk database lain berdasarkan urutan di config.php

// $form->roll->addInput('orderby','orderby');
// $form->roll->input->orderby->setTitle('Ordered');


class FormOrderby extends Form
{
	var $table; //table pada databases
	var $intMax; // mendapatkan maksimal yang ada
	var $intNow; // mendapatkan order yang sekarang
	var $intUp; //mendapatkan orderyang di atasnya
	var $intDown;  //mendapatkan orderyang di bawahnya
	var $str_table_id;
	var $intNowNew; //yang udah di replace
	var $intUpNew; //orderby yang di atasnya yang udah di kurangi satu
	var $intId;
	var $refName;
	var $addCondition;

	function __construct()
	{
		$this->type = 'orderby';
		$this->setIsNeedDbObject(true);
		$this->setIsIncludedInUpdateQuery( true );
		$this->setAlign( 'center' );
	}

	// untuk mereplace method parent karena ada action tambahan
	function setActionType( $str_action_type )
	{
		$this->actionType= $str_action_type;
		switch ($this->actionType)
		{
			case 'edit':
				$this->setIsNeedDbObject(false);
				$this->setIsIncludedInUpdateQuery(false);
				$this->setIsIncludedInSelectQuery(false);
				$this->setIsInsideRow(false);
				break;
			case 'add':
				$this->setIsInsideRow(false);
				break;
		}
	}

	function setAddCondition($sql_where = '')
	{
		if (!preg_match('~^\s{0,}where ~is', $sql_where))
		{
			$sql_where = 'WHERE '.$sql_where;
		}
		$this->addCondition = $sql_where;
	}

	function addOrderby( $title, $value = '' )
	{
		$this->OrderbyTitle[]		= $title;
		$this->OrderbyValue[] 	= $value;
	}

	function addOrderbyArray( $arrTitle, $arrValue )
	{
		$i = 0;
		foreach( $arrTitle as $title)
		{
			$this->addOrderby( $title, $arrValue[$i] );
			$i++;
		}
	}

	function setTypeOrder($type){
		$this->type_order=$type;
	}

	function setReferenceField($ref_field = '')
	{
		if(!empty($ref_field)){
			$this->ref_field = $ref_field;
		}else{
			$this->ref_field = '';
		}
	}

	function getIdName()
	{
		return $this->IdName="".$this->formName."_".$this->tableId."";
	}

	function getMax($id)
	{
		global $Bbc;
		// untuk mendapatkan apaling besar di orderby
		if(!isset($Bbc->orderby[$this->formName][$this->name]['max']))
		{
			$sqlCondition = $this->getMaxMinCondition($id);
			$sql 	= "SELECT  MAX(".$this->fieldName.") FROM ". $this->tableName ." ". $sqlCondition;
			$this->intMax= $this->db->GetOne( $sql );
			$Bbc->orderby[$this->formName][$this->name]['max']=$this->intMax;
		}
		else {
			$this->intMax=$Bbc->orderby[$this->formName][$this->name]['max'];
		}
		return $this->intMax;
	}
	function getMaxMinCondition($id)
	{
		if(!empty($this->ref_field))
		{
			$q = "SELECT $this->ref_field FROM $this->tableName WHERE $this->tableId='$id'";
			$tmp = $this->db->getOne($q);
			if(empty($this->sqlCondition)){
				$output = " WHERE $this->ref_field='$tmp'";
			}elseif(preg_match('~where~is', $this->sqlCondition)){
				$output = str_replace("where ", "WHERE $this->ref_field='$tmp' AND ", strtolower($this->sqlCondition));
			}else{
				$output = "WHERE $this->ref_field='$tmp' ";
			}
		}else{
			$output = $this->sqlCondition;
		}
		return $output;
	}
	function getMin($id)
	{
		global $Bbc;
		// untuk mendapatkan yang paling kecil
		if(!isset($Bbc->orderby[$this->formName][$this->name]['min']))
		{
			$sqlCondition = $this->getMaxMinCondition($id);
			$sql 	= "SELECT  MIN(".$this->fieldName.") FROM ". $this->tableName ." ". $sqlCondition;
			$this->intMin= $this->db->GetOne( $sql );
			$Bbc->orderby[$this->formName][$this->name]['min']=$this->intMin;
		}else{
			$this->intMin=$Bbc->orderby[$this->formName][$this->name]['min'];
		}
		return $this->intMin;
	}

	function getNow($id)
	{
		// untuk mendapatkan orderby yang di miliki melalui data yang di di ambil dari $id yang merupak id dari field orderby
		$sql 	= "SELECT ".$this->fieldName." FROM ". $this->tableName." WHERE ".$this->tableId."=".$id."";

		$result = $this->db->Execute($sql);

		$num_rows_count = $this->db->Affected_rows();

		if( $num_rows_count == 1 )
		{
			$intNow	= $this->db->GetOne( $sql );
			$this->intNow= ( $intNow == '') ? 0 : $intNow;
			$this->inIdNow=$id;
		}
		return $this->intNow;
	}

	function getUpOrderby()
	{
		// untuk mendapatkan orderby yang di atasnya melalui data yang di kirim lewat $_GET[$this->tableId]
		// $sql 	= "SELECT ".$this->tableId.",".$this->fieldName." FROM ". $this->tableName." WHERE ".$this->fieldName." >= ".$this->intNow." AND ".$this->tableId."<>".$this->inIdNow." ORDER BY ". $this->fieldName ." limit 1";
		$order	= $this->sqlOrder;
		$this->sqlCondition = empty($this->sqlCondition) ? ' WHERE 1' : $this->sqlCondition;
		$sql 	= "SELECT ".$this->tableId.",". $this->fieldName." FROM ". $this->tableName." ".
						$this->sqlCondition
						." AND ".$this->fieldName." >= ".$this->intNow
						." AND ".$this->tableId." <> ". $this->inIdNow
						." $order limit 1";
		$result = $this->db->Execute($sql);

		$num_rows_count = $this->db->Affected_rows();

		if( $num_rows_count == 1 )
		{
			$data= $this->db->getRow($sql);
			@extract($data);
			$_str_table_id = $this->tableId; 	//cuma memudahkan
			$_fieldName    = $this->fieldName;
			$this->intIdUp = $$_str_table_id;
			$this->intUp   = $$_fieldName;
    }
		return $this->intUp;
	}
	function getSqlCondition()
	{
		$pos = strpos( strtolower( $this->sqlCondition ), ' order by ');
		if (is_integer($pos))
		{
			$sql                = substr( $this->sqlCondition, 0, $pos );
			$sqlOrder           = substr( $this->sqlCondition, $pos);
			$this->sqlCondition = " ".$sql;
			$this->sqlOrder     = " ".$sqlOrder;
		}else{
			$this->sqlOrder		= " ORDER BY ". $this->fieldName;
		}
	}

	function getDownOrderby()
	{
		// membalik sqlOrder dulu
		if (preg_match('~ asc~is', $this->sqlOrder))
			$order = preg_replace('~ asc~is', ' desc', $this->sqlOrder);
		elseif (preg_match('~ desc~is', $this->sqlOrder))
			$order = preg_replace('~ desc~is', ' asc', $this->sqlOrder);
		else
			$order = $this->sqlOrder ." desc";

		// untuk mendapatkan orderby yang di bawahnya melalui data yang di kirim lewat $_GET[$this->tableId]
		$this->sqlCondition = empty($this->sqlCondition) ? 'WHERE 1' : $this->sqlCondition;
		$sql 	= "SELECT ".$this->tableId.",". $this->fieldName." FROM ". $this->tableName." ".
						$this->sqlCondition
						." AND ".$this->fieldName." <= ".$this->intNow
						." AND ".$this->tableId." <> ". $this->inIdNow
						." $order limit 1";

		$result = $this->db->Execute($sql);
		$num_rows_count = $this->db->Affected_rows();

		if( $num_rows_count == 1 )
		{
			$data= $this->db->getRow($sql);
			@extract($data);
			$_str_table_id=$this->tableId;
			$_fieldName=$this->fieldName;
			$this->intIdDown=$$_str_table_id;
			$this->intDown=$$_fieldName;
		}
		return $this->intDown;
	}

	function changeToUpOrderby()
	{
		$this->intNowNew=$this->intUp;			//orderby yagn di klik sama dengan yang atasnya
		$this->intUpNew=$this->intNowNew - 1;	//ordrby yang atasnya di kurangi satu
		return $this->intNowNew;
	}

	function changeToDownOrderby()
	{
		$this->intNowNew=$this->intDown;		//orderby yagn di klik sama dengan yang di bawahnya
		$this->intDownNew=$this->intNowNew + 1;	//ordrby yang di bawahnya di ditambah satu
		return $this->intNowNew;
	}

	function UpdateToUpOrderby()
	{
		if (@is_numeric($this->intNowNew) && @is_numeric($this->inIdNow))
		{
			$this->db->Execute("UPDATE ".$this->tableName." SET ". $this->fieldName."=".$this->intNowNew." WHERE ".$this->tableId."=".$this->inIdNow); // yang now lama di di update
		}
		if (@is_numeric($this->intUpNew) && @is_numeric($this->intIdUp))
		{
			$this->db->Execute("UPDATE ".$this->tableName." SET ". $this->fieldName."=".$this->intUpNew." WHERE ".$this->tableId."=".$this->intIdUp); // yang di atasnya di update
		}
	}


	function UpdateToDownOrderby()
	{
		if (@is_numeric($this->intNowNew) && @is_numeric($this->inIdNow))
		{
			$this->db->Execute("UPDATE ".$this->tableName." SET ". $this->fieldName."=".$this->intNowNew." WHERE ".$this->tableId."=".$this->inIdNow); // yang now lama di di update
		}
		if (@is_numeric($this->intDownNew) && @is_numeric($this->intIdDown))
		{
			$this->db->Execute("UPDATE ".$this->tableName." SET ". $this->fieldName."=".$this->intDownNew." WHERE ".$this->tableId."=".$this->intIdDown); // yang di atasnya di update
		}
	}
	function getAll()
	{
		// untuk mendapatkan semua yang ada
		$order=array();
		$sql 	= "SELECT ".$this->tableId.",". $this->fieldName." FROM ". $this->tableName . $this->sqlCondition . $this->sqlOrder;
		$r=$this->db->Execute($sql);
		$i=1;
		while ($data = $this->db->_fetch())
		{
			$order[$i]['id']=$data[$this->tableId];
			$i++;
	  }
		//print_r($order);
		return $order;
	}
	function updateAll()
	{
		// untuk mengurutkan semuanya order berdasasr odrerby
		$data=$this->getAll();
		$total=count($data);
		for($i=1; $i<=$total; $i++)
		{
			$sql= "UPDATE ".$this->tableName." SET ". $this->fieldName." = ".$i." WHERE ".$this->tableId." = ".$data[$i]['id'].""; //yang di atasnya di update
			$this->db->Execute($sql);
		}

	}

	function getRollUpdateSQL( $i = '' )
	{
		$this->getSqlCondition();

		$idName=$this->getIdName();
		$id=$_POST[$idName][$i];
		if (isset( $_POST[$this->name][$i]['up']))
		{
			$this->getNow($id);
			$this->getDownOrderby();

			if( $this->intNow==$this->intDown )
			{
				//$this->getAll();
				$this->updateAll();
				$this->getNow($id);
				$this->getDownOrderby();
			}
			$this->changeToDownOrderby();
			$this->UpdateToDownOrderby();
		}

		if (isset( $_POST[$this->name][$i]['down']))
		{
			$this->getNow($id);
			$this->getUpOrderby();
			if($this->intNow==$this->intUp)
			{
				//$this->getAll();
				$this->updateAll();
				$this->getNow($id);
				$this->getUpOrderby();
			}
			$this->changeToUpOrderby();
			$this->UpdateToUpOrderby();
		}
	}

	function getAddSQL()
	{
		$sql = 'SELECT  MAX('.$this->fieldName.') FROM '. $this->tableName .' '. $this->addCondition;
		$val = intval($this->db->GetOne($sql))+1;

		$out = array(
			'into' 	=> $this->fieldName.', '
		, 'value' => $val.', '
		);
		return $out;
	}

	/////////////////////////////////////////////////////////////////////////////////
	// karena getReportOutput udah sama dengan plaintext yang plain text tidak di set
	/////////////////////////////////////////////////////////////////////////////////
	function getReportOutput( $str_value = '' )
	{
			$out=$str_value;
			return $out;
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		//untuk men set plain text
		if ( $this->isPlaintext ) return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );

		$out = '';
		if($this->actionType=='roll')
		{
			global $Bbc;
			link_js(_PEA_URL.'includes/FormOrderby.js', false);
			$id     = @intval($id);
			$is_min = $str_value > $this->getMin($id) ? 0 : 1;
			$is_max = $str_value < $this->getMax($id) ? 0 : 1;
			$txt    = '<button type="%s" class="btn btn-default btn-xs btn-orderby"><span class="glyphicon glyphicon-arrow-%s"></span></button>';
			$arr    = array(
				'submit" name="'.$str_name.'[$to]" value="[$to]|$to',
				'button|$to text-muted'
				);
			// neither minimum and maximum (same value in all rows)
			if ($is_min && $is_max) {
				$is_min = $is_max = 0;
			}
			$out   .= vsprintf($txt, explode('|', str_replace('$to', 'up'		, $arr[$is_min])));
			if (empty($Bbc->orderby_params[$this->formName]))
			{
				$Bbc->orderby_params[$this->formName] = 1;
				if (empty($this->sqlOrder) || empty($this->sqlCondition))
				{
					$this->getSqlCondition();
				}
				$param = array(
					'tableId'      => $this->tableId,
					'fieldName'    => $this->fieldName,
					'tableName'    => $this->tableName,
					'sqlCondition' => empty($this->sqlCondition) ? 'WHERE 1' : $this->sqlCondition,
					'sqlOrder'     => $this->sqlOrder,
					'expire'       => strtotime('+2 HOUR')
					);
				if (preg_match('~([a-z0-9_\-]+)~is', $param['tableName'], $m))
				{
					$param['tableName'] = $m[1];
				}
				if (!empty($this->db_str))
				{
					$param['db'] = $this->db_str;
				}
				$arr[0] = 'submit" name="'.$str_name.'[$to]" value="[$to]" data-token="'.encode(json_encode($param)).'|$to';
			}
			$out   .= vsprintf($txt, explode('|', str_replace('$to', 'down'	, $arr[$is_max])));
		}else{

		}
		return $out;
	}

}