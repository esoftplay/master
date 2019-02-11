<?php
/*
///////// PENGGUNAAN FUNGSI UTAMA SQL PARSER /////////////////////////
//	FUNGSI SQL PARSER
//	SEMENTARA, MUNGKIN SELAMANYA, BARU BISA PARSING NAMA2 FIELDS
//	PENGGUNAAN:

$result = getFieldsFromSQL ( $sql );

//	RESULT BERUPA ARRAY DENGAN INDEX AWAL 0,1,2,3,4, dst... yang masing2 adalah nama2 field dari SQL yang dimasukkan sebagai parameter

*/

//SELECT\s+(DISTINCT|)\s+(.*)\s+FROM\s+(.*)\s+(WHERE|)\s+(.*)\s+(GROUP\s+BY|)\s+(.*)\s+(HAVING|)\s+(.*)\s+(ORDER\s+BY|)\s+(.*)\s+(ASC|DESC)\s+(LIMIT|)\s+(.*)
//			$where = preg_match ( "~where~is", $input ) ? "\sWHERE\s(.*)" : "";
//			$char = "[a-zA-Z,\s\d`]";
//			$regex = "/SELECT\s+(DISTINCT|)(.*)\s+FROM\s+(.*)\s+WHERE\s+(.*|)/is";

///////// BEGIN FUNGSI-FUNGSI UTAMA SQL PARSER /////////////////////////
// trim in array
function arrTrim ( &$item, $key )
{
	$item = trim ( $item );
}

// recover a field with SQL function. e.g: CONCAT(bla bla bla
function arrFuncRecover ( $arr ) // DANANG WIDIANTORO
{
	$output = array();
	foreach($arr AS $field)
	{
		preg_match('~^`?(.*?)(?:(?:\s+|`)+as(?:\s+|`)+([a-z]+)`?)?`?$~is', $field, $m);
		if(isset($m[1]) && !empty($m[1]))
		{
			$tot_m_quote = $tot_n_quote = 0;
			$tot_m = count(explode('(', $m[1]))-1; // total kurung buka (mbukak)
			$tot_n = count(explode(')', $m[1]))-1; // total kurung tutup (nutup)
			$r = explode("'", $m[1]);
			foreach($r AS $i => $d)
			{
				if(($i % 2))
				{
					$tot_m_quote += count(explode('(', $d)) - 1; // total kurung buka dalam quote (di hitung string)
					$tot_n_quote += count(explode(')', $d)) - 1; // total kurung tutup dalam quote (di hitung string)
				}
			}
			$tot_m = $tot_m - $tot_m_quote;
			$tot_n = $tot_n - $tot_n_quote;
			if($tot_n > $tot_m)			die('SQL Parser Error: Kebanyakan kurung tutup di nama field :'.$m[1]);
			elseif($tot_m > $tot_n)	die('SQL Parser Error: Kebanyakan kurung buka di nama field'.$m[1]);
		}
		$output[] = isset($m[2]) ? $m[2] : $m[1];
	}
	return $output;
}

function getWhereClause ( $data )
{
	$regex = "/(.*)WHERE(.*)/is";
}

function parseSQL ( $sql )
{
	$sql = get_magic_quotes_gpc() ? stripslashes ( $sql ) : $sql;
	if (	! function_exists ( "arrTrim" ) ||
			! function_exists ( "arrFuncRecover" )
		)
		die ("<strong>DIE</strong>: Fungsi tidak lengkap");

	$result = array ();
	$type = preg_split ( "/\s/", trim ( $sql ), 2 );
	$type = strtolower ( trim ( $type[0] ) );
	$result["type"] = $type;
	switch ( $type )
	{
		case "select":
			$regex = "/SELECT\s+(DISTINCT|DISTINCTROW|ALL|SQL_CALC_FOUND_ROWS|)(.*)\sFROM\s(.*)/is";
			preg_match ( "$regex", $sql, $match );
			$result["selectOption"] = $match[1];
			$result["fieldClause"] = $match[2];
			$afterFrom = $match[3];

			// dapetin SQL syntax apa aja setelah FROM
			$cond = preg_match_all ("/\s(?:WHERE|GROUP\s+BY|HAVING|ORDER\s+BY|LIMIT)\s/is", $afterFrom, $matchFrom);
			if ( count ( $matchFrom[0] ) > 0 )
			{
				$result["conditionClause"] = trim ( substr ( $afterFrom, strpos ( $afterFrom, $matchFrom[0][0] ) ) );

				// dapetin table clause WHERE|GROUP BY|HAVING|ORDER BY|LIMIT
				$result["tableClause"] = trim ( substr ( $afterFrom, 0, strpos ( $afterFrom, $matchFrom[0][0] ) ) );

				// dapetin condiotionnya
				$strRegex = trim(implode ( "(.*)", $matchFrom[0] ) . "(.*)");
				preg_match ( "/$strRegex/is", $result["conditionClause"], $condMatch );
				array_shift( $condMatch );
				if ( count ( $condMatch ) == count ( $matchFrom[0] ) )
				{
					for ($i = 0; $i < count ( $condMatch ); $i ++ )
					{
						$result[strtolower ( preg_replace( "/\s/", "", $matchFrom[0][$i] ) )."Clause"] = preg_replace ( "/\s{2,}/s", " ", trim ( $condMatch[$i] ) );
					}
				}
			}
			else
			{
				$result["tableClause"]		= trim ( $afterFrom );
				$result["conditionClause"]= '';
			}
			$guessTable = preg_split ( "/\s+/", $result["tableClause"], 4 );
			$result["tableName"] = $guessTable[0];
			if ( ! preg_match ("/CROSS|INNER|STRAIGHT_JOIN|LEFT|NATURAL|RIGHT|JOIN/", @$guessTable[1] ) )
				$result["tableAs"] = preg_match ("~as~is", @$guessTable[1] ) ? @$guessTable[2] : @$guessTable[1];

			// generate SELECT CLAUSE
			$result["selectClause"] = 	trim ( strtoupper ( $type ) . " " . $result["selectOption"] ) . " " .
										$result["fieldClause"] .
										" FROM " .
										$result["tableClause"];
			$result["SQLClause"] = $result["selectClause"] . " " . $result["conditionClause"];
		break;

		case "insert":
		break;

		case "update":
		break;

		case "delete":
		break;

		case "replace":
		break;
	}
	return $result;
}

function getFieldsFromSQL ( $sql )
{
	$input = trim ( $sql );
	$result = array ();

	// get type: INSERT / SELECT / DELETE / UPDATE
	list ( $type ) = explode ( " ", $input, 2 );
	switch ( strtolower ( $type ) )
	{
		case "select":
			$regex = "/SELECT\s+(DISTINCT|SQL_CALC_FOUND_ROWS|)(.*)\sFROM\s(.*)/is";
			$suk = preg_match ( "$regex", $input, $match );
			if ( ! $suk ) echo "REGEX ERROR";
			$result["beforeFrom"] = $match[2];
			$result["afterFrom"] = $match[3];

			// dapet fields nya
			$fields_guess = explode (", ", $result["beforeFrom"] );
			if ( function_exists ( "arrTrim" ) )
				array_walk ( $fields_guess, "arrTrim" );
			else
				die ("<strong>DIE</strong>: Fungsi arrTrim tidak ditemukan");

			if ( function_exists ( "arrFuncRecover" ) )
			{
				$result["fields"] = arrFuncRecover ( $fields_guess );
				$return = $result["fields"];
			}
			else
			{
				die ("<strong>DIE</strong>: Fungsi arrFuncRecover tidak ditemukan");
			}
		break;

		case "insert":
		break;

		case "update":
		break;

		case "delete":
		break;

		default:
		break;
	}
	return $return;
}
