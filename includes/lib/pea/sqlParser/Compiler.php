<?php

/**
 * A SQL parse tree compiler.
 *
 * @author  John Griffin <jgriffin316@netscape.net>
 * @version 0.1
 * @access  public
 * @package SQL_Parser
 */
class SQL_Compiler {
    var $tree;

// {{{ function SQL_Compiler($array = null)
    function __construct($array = null)
    {
        $this->tree = $array;
    }
// }}}

//    {{{ function getWhereValue ($arg)
    function getWhereValue ($arg)
    {
        switch ($arg['type']) {
            case 'ident':
            case 'real_val':
            case 'int_val':
                $value = $arg['value'];
                break;
            case 'text_val':
                $value = '\''.$arg['value'].'\'';
                break;
            case 'subclause':
                $value = '('.$this->compileSearchClause($arg['value']).')';
                break;
            default:
                return $this->raiseError('Unknown type: '.$arg['type']);
        }
        return $value;
    }
//    }}}

//    {{{ function getParams($arg)
    function getParams($arg)
    {
        for ($i = 0; $i < sizeof ($arg['type']); $i++) {
            switch ($arg['type'][$i]) {
                case 'ident':
                case 'real_val':
                case 'int_val':
                    $value[] = $arg['value'][$i];
                    break;
                case 'text_val':
                    $value[] = '\''.$arg['value'][$i].'\'';
                    break;
                default:
                    return $this->raiseError('Unknown type: '.$arg['type']);
            }
        }
        $value ='('.implode(', ', $value).')';
        return $value;
    }
//    }}}

//    {{{ function compileSearchClause
    function compileSearchClause($where_clause)
    {
        $value = '';
        if (isset ($where_clause['arg_1']['value'])) {
            $value = $this->getWhereValue ($where_clause['arg_1']);
            // if (PEAR::isError($value)) {
			if (!$value) {
                return $value;
            }
            $sql = $value;
        } else {
            $value = $this->compileSearchClause($where_clause['arg_1']);
            // if (PEAR::isError($value)) {
			if (!$value) {
                return $value;
            }
            $sql = $value;
        }
        if (isset ($where_clause['op'])) {
            if ($where_clause['op'] == 'in') {
                $value = $this->getParams($where_clause['arg_2']);
                //if (PEAR::isError($value)) {
				if (!$value) {
                    return $value;
                }
                $sql .= ' '.$where_clause['op'].' '.$value;
            } elseif ($where_clause['op'] == 'is') {
                if (isset ($where_clause['neg'])) {
                    $value = 'not null';
                } else {
                    $value = 'null';
                }
                $sql .= ' is '.$value;
            } else {
                $sql .= ' '.$where_clause['op'].' ';
                if (isset ($where_clause['arg_2']['value'])) {
                    $value = $this->getWhereValue ($where_clause['arg_2']);
                    //if (PEAR::isError($value)) {
					if (!$value) {
                        return $value;
                    }
                    $sql .= $value;
                } else {
                    $value = $this->compileSearchClause($where_clause['arg_2']);
                    // if (PEAR::isError($value)) {
					if (!$value) {
                        return $value;
                    }
                    $sql .= $value;
                }
            }
        }
        return $sql;
    }
//    }}}


// DIEDIT SAMA OGI<br>
// BIAR KELUARNYA GA MURNI STRING< TAPI ARRAY

//    {{{ function compileSelect()
    function compileSelect()
    {
        // save the command and set quantifiers
        $sql = 'select ';
        if (isset($this->tree['set_quantifier'])) {
            $sql .= $this->tree['set_quantifier'].' ';
        }

		$sql = '';
        // save the column names and set functions
        for ($i = 0; $i < sizeof ($this->tree['column_names']); $i++) {
            $column = $this->tree['column_names'][$i];
            if ($this->tree['column_aliases'][$i] != '') {
                $column .= ' as '.$this->tree['column_aliases'][$i];
            }
            $column_names[] = $column;
        }
        for ($i = 0; $i < sizeof ($this->tree['set_function']); $i++) {
            $column = $this->tree['set_function'][$i]['name'].'(';
            if (isset ($this->tree['set_function'][$i]['distinct'])) {
                $column .= 'distinct ';
            }
            if (isset ($this->tree['set_function'][$i]['arg'])) {
                $column .= implode (',', $this->tree['set_function'][$i]['arg']);
            }
            $column .= ')';
            if ($this->tree['set_function'][$i]['alias'] != '') {
                $column .= ' as '.$this->tree['set_function'][$i]['alias'];
            }
            $column_names[] = $column;
        }
        if (isset($column_names)) {
            $sql .= implode (", ", $column_names);
        }

		$result['all_column']	= $sql;

        // save the tables
        $sql .= ' from ';

		$sql = '';
        for ($i = 0; $i < sizeof ($this->tree['table_names']); $i++) {
            $sql .= $this->tree['table_names'][$i];
            if ($this->tree['table_aliases'][$i] != '') {
                $sql .= ' as '.$this->tree['table_aliases'][$i];
            }
            if ($this->tree['table_join_clause'][$i] != '') {
                $search_string = $this->compileSearchClause ($this->tree['table_join_clause'][$i]);
                // if (PEAR::isError($search_string)) {
				if (!$search_string) {
                    return $search_string;
                }
                $sql .= ' on '.$search_string;
            }
            if (isset($this->tree['table_join'][$i])) {
                $sql .= ' '.$this->tree['table_join'][$i].' ';
            }
        }

		$result['all_table']	= $sql;


		$sql	= '';

        // save the where clause
        if (isset($this->tree['where_clause'])) {
            $search_string = $this->compileSearchClause ($this->tree['where_clause']);
            // if (PEAR::isError($search_string)) {
			if (!$search_string) {
                return $search_string;
            }
            $sql .= ' where '.$search_string;
        }

		$result['all_where']	= $sql;

		$sql	= '';

        // save the group by clause
        if (isset ($this->tree['group_by'])) {
            $sql .= ' group by '.implode(', ', $this->tree['group_by']);
        }

		$result['all_group']	= $sql;


		$sql	= '';

        // save the order by clause
        if (isset ($this->tree['sort_order'])) {
            foreach ($this->tree['sort_order'] as $key => $value) {
                $sort_order[] = $key.' '.$value;
            }
            $sql .= ' order by '.implode(', ', $sort_order);
        }

		$result['all_order']	= $sql;

		$sql	= '';

        // save the limit clause
        if (isset ($this->tree['limit_clause'])) {
            $sql .= ' limit '.$this->tree['limit_clause']['start'].','.$this->tree['limit_clause']['length'];
        }

		$result['all_limit']	= $sql;

        return $result;
    }
//    }}}

//    {{{ function compileUpdate()
    function compileUpdate()
    {
        $sql = 'update '.implode(', ', $this->tree['table_names']);

        // save the set clause
        for ($i = 0; $i < sizeof ($this->tree['column_names']); $i++) {
            $set_columns[] = $this->tree['column_names'][$i].' = '.$this->getWhereValue($this->tree['values'][$i]);
        }
        $sql .= ' set '.implode (', ', $set_columns);

        // save the where clause
        if (isset($this->tree['where_clause'])) {
            $search_string = $this->compileSearchClause ($this->tree['where_clause']);
            // if (PEAR::isError($search_string)) {
			if (!$search_string) {
                return $search_string;
            }
            $sql .= ' where '.$search_string;
        }
        return $sql;
    }
//    }}}

//    {{{ function compileDelete()
    function compileDelete()
    {
        $sql = 'delete from '.implode(', ', $this->tree['table_names']);

        // save the where clause
        if (isset($this->tree['where_clause'])) {
            $search_string = $this->compileSearchClause ($this->tree['where_clause']);
            // if (PEAR::isError($search_string)) {
			if (!$search_string) {
                return $search_string;
            }
            $sql .= ' where '.$search_string;
        }
        return $sql;
    }
//    }}}

//    {{{ function compileInsert()
    function compileInsert()
    {
        $sql = 'insert into '.$this->tree['table_names'][0].' ('.
                implode(', ', $this->tree['column_names']).') values (';
        for ($i = 0; $i < sizeof ($this->tree['values']); $i++) {
            $value = $this->getWhereValue ($this->tree['values'][$i]);
            // if (PEAR::isError($value)) {
			if (!$value) {
                return $value;
            }
            $value_array[] = $value;
        }
        $sql .= implode(', ', $value_array).')';
        return $sql;
    }
//    }}}

//    {{{ function compile($array = null)
    function compile($array = null)
    {
        $this->tree = $array;

        switch ($this->tree['command']) {
            case 'select':
                return $this->compileSelect();
                break;
            case 'update':
                return $this->compileUpdate();
                break;
            case 'delete':
                return $this->compileDelete();
                break;
            case 'insert':
                return $this->compileInsert();
                break;
            case 'create':
            case 'drop':
            case 'modify':
            default:
                return $this->raiseError('Unknown action: '.$this->tree['command']);
        }    // switch ($this->tree["command"])

    }
//    }}}


	// ini buatannya oggix
	function raiseError( $error = '' )
	{
		die( "oggixSqlCompiler : $error" );
	}

}
?>

