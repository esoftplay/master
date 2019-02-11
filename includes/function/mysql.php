<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

function mysql_dump($r_tables = array())
{
	$output = '';
	$r_tables = is_array($r_tables) ? $r_tables : array($r_tables);
	if($r_tables)
	{
		global $db;
		$r_all = $db->getCol("SHOW TABLES");
		$r_table = array();
		foreach($r_all AS $tbl)
		{
			if(in_array($tbl, $r_tables))
			{
				$r_table[] = $tbl;
			}
		}
		if($r_table)
		{
			foreach($r_table AS $table)
			{
				$output .= "-- \n";
				$output .= "-- Table structure for table `$table` \n";
				$output .= "-- \n\n";
				$output .= "DROP TABLE IF EXISTS `$table`;\n";
				$output .= "CREATE TABLE `$table` (\n";
				$r = $db->getAll("SHOW FIELDS FROM `$table`");
				foreach($r AS $d)
				{
					$output .= "  `".$d['Field']."` ".$d['Type']."";
					$output .= ($d['Null'] != "YES") ? " NOT NULL" : false;
					$output .= ($d['Default'] != '') ? " default '".$d['Default']."'" : false;
					$output .= (!empty($d['Extra'])) ? " ".$d['Extra']."" : false;
					$output .= ",\n";
				}
				$output = preg_replace("~,\n$~", "", $output);
				// Save all Column Indexes in array
				unset($index);
				$r = $db->getAll("SHOW KEYS FROM `$table`");
				foreach((array)$r AS $d)
				{
					if (($d['Key_name'] == 'PRIMARY') AND ($d['Index_type'] == 'BTREE')) {
						$index['PRIMARY'][$d['Key_name']][] = $d['Column_name'];
					}

					if (($d['Key_name'] != 'PRIMARY') AND ($d['Non_unique'] == '0') AND ($d['Index_type'] == 'BTREE')) {
						$index['UNIQUE'][$d['Key_name']][] = $d['Column_name'];
					}

					if (($d['Key_name'] != 'PRIMARY') AND ($d['Non_unique'] == '1') AND ($d['Index_type'] == 'BTREE')) {
						$index['INDEX'][$d['Key_name']][] = $d['Column_name'];
					}

					if (($d['Key_name'] != 'PRIMARY') AND ($d['Non_unique'] == '1') AND ($d['Index_type'] == 'FULLTEXT')) {
						$index['FULLTEXT'][$d['Key_name']][] = $d['Column_name'];
					}
				}
				if (is_array($index))
				{
					foreach ($index as $xy => $columns)
					{
						$output .= ",\n";
						$c = 0;
						foreach ($columns as $column_key => $column_name)
						{
							$c++;
							$column_name = implode('`,`', $column_name);
							$output .= ($xy == "PRIMARY") ? "  PRIMARY KEY  (`{$column_name}`)" : false;
							$output .= ($xy == "UNIQUE") ? "  UNIQUE KEY `{$column_key}` (`{$column_name}`)" : false;
							$output .= ($xy == "INDEX") ? "  KEY `{$column_key}` (`{$column_name}`)" : false;
							$output .= ($xy == "FULLTEXT") ? "  FULLTEXT KEY `{$column_key}` (`{$column_name}`)" : false;
							$output .= ($c < (count($index[$xy]))) ? ",\n" : false;
						}
					}
				}
				$output .= "\n) ENGINE=MyISAM;\n\n";
				// Header
				$limit	= 200;
				$count	= $db->getOne("SELECT COUNT(*) FROM `$table`");
				$loop		= ceil($count/$limit);
				if($count > 0)
				{
					$output .= "-- \n";
					$output .= "-- Dumping data for table `$table` \n";
					$output .= "-- \n\n";
				}
				for($j=0;$j < $loop ; $j++)
				{
					$data	= array();
					$start= $j*$limit;
					$arr	= $db->getAll("SELECT * FROM `$table` LIMIT {$start}, {$limit}");
					if(!empty($arr))
					{
						$i = 0;
						foreach($arr AS $d)
						{
							$fields = array();
							foreach($d AS $f)
							{
								if(is_numeric($f))   $fields[] = $f;
								else $fields[] = "'".str_replace('\"', '"', mysql_real_escape_string($f))."'";
							}
							$i++;
							$data[] = "(".implode(", ", $fields).")";
						}
						$output .= "INSERT INTO `$table` VALUES \n";
						$output .= implode(",\n", $data);
						$output .= ";\n";
					}
				}
				if($count > 0)
				{
					$output .= "\n-- --------------------------------------------------------\n\n";
				}
			}
		}
	}
	return $output;
}