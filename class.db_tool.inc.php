<?php

/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

class db_tool {

	var $db_data_inc = "";			// contains the vars $host, $user, $pass
	var $db_link;
	// Overridable methods
	//
	// Adapt to other databases
	function db_tool($data_inc) {
		$this->db_data_inc = $data_inc;
	}

	function db_query($query) {
		return mysql_query($query);
	}

	function db_connect($host, $user, $pwd) {
		return @mysql_connect($host, $user, $pwd);
	}

	function fetch_array($res) {
		return mysql_fetch_array($res);
	}

	function fetch_row($res) {
		return mysql_fetch_row($res);
	}

	function insert_id() {
		return mysql_insert_id();
	}

	function affected_rows() {
		return mysql_affected_rows($this->db_link);
	}

	function db_error() {
		return mysql_error();
	}

	function db_data_inc($file) {
		if (empty($file)) {
			// oops!
			return;
		}
		$this->db_data_inc = $file;
	}

	function query($query, $link) {
		return mysql_query($query, $link);
	}

	// Private functions
	//
	// Don't use them in your code


	// Get the link to the db
	function link_db() {
		global $strings;
		$doc_root = getenv("DOCUMENT_ROOT");
		$path = $doc_root;
		if ($GLOBALS["in_docroot"] === false) {
			$path .= "/..";
		}
		$path .= "/".$this->db_data_inc;
		require($path);
		$this->db_link = $this->db_connect($host, $user, $pass)
			or die ($strings["l_ServerConnect"]."<br>".$this->db_error());

		$query = "USE $dbname";
		$this->query($query, $this->db_link)
			or die ($strings["l_OpenDB"]."<br>".$this->db_error());
	}

	// Public functions

	// perform the query
	function db_query($query) {
		$this->link_db();
		return $this->query($query, $this->db_link);
	}

	function insert($tablename /* var. no. of strings of the variable names*/) {
		$num_args = func_num_args();
		$args = func_get_args();
		// Skip the table name as argument
		for($i = 1; $i < $num_args; $i++) {
			$var_name = func_get_arg($i);
			$col_names .= $var_name." ";
			$values .= $GLOBALS["$var_name"]." ";
		}

		$col_names = str_replace(" ", ", ", trim($col_names));
		$values = str_replace(" ", ", ", trim($values));
		$query = "INSERT INTO $tablename ($col_names) VALUES ($values)";
		//echo $query;
		return;
		$res = $this->query($query);
		if ($res == 0) {
			return 0;
		}

		return $this->insert_id();
	}

	function update($tablename, $where /* var. no. of strings of the variable names*/) {
		$num_args = func_num_args();
		$args = func_get_args();
		// Skip the table name and the WHERE clause arguments
		for($i = 2; $i < $num_args; $i++) {
			$var_name = func_get_arg($i);
			$update .= $var_name."=".$GLOBALS["$var_name"]." ";
		}
		$update = str_replace(" ", ", ", trim($update));

		$query = "UPDATE $tablename SET $update WHERE $where";
		//echo $query;
		return;
		$res = $this->query($query);
		if ($res == 0) {
			return 0;
		}

		return $this->insert_id();
	}

	function store($tablename /* var. no. of strings of the variable names*/ /*the last arg is the where clause, pass empty string if it's an insert*/) {
		global $update;

		$num_args = func_num_args();
		$args = func_get_args();

		if (isset($update) && $update == 1) {
			$num_args = func_num_args();
			$args = func_get_args();
			// Skip the table name and the WHERE clause arguments
			for($i = 1; $i < $num_args - 1; $i++) {
				$var_name = func_get_arg($i);
				if (isset($GLOBALS[$var_name])) {
					$update_str .= $var_name."='".$GLOBALS["$var_name"]."', ";
				}
			}

			//$update_str = str_replace("' '", "', '", trim($update_str));
			$update_str = ereg_replace(", $", "", $update_str);
			$where = func_get_arg($num_args - 1);
			$query = "UPDATE $tablename SET $update_str WHERE $where";
			/*echo $query;
			echo("<br>WHERE: $where");*/

			$res = $this->db_query($query);
			if ($res == 0) {
				echo $this->db_error();
				return 0;
			}

			return 1;
		}
		else {
			// Skip the table name as argument
			for($i = 1; $i < $num_args; $i++) {
				$var_name = func_get_arg($i);
				if (isset($GLOBALS[$var_name])) {
					$col_names .= $var_name." ";
					$values .= "'".addslashes($GLOBALS[$var_name])."' ";
				}
			}

			$col_names = str_replace(" ", ", ", trim($col_names));
			$values = str_replace("' '", "', '", trim($values));
			$query = "INSERT INTO $tablename ($col_names) VALUES ($values)";

			$res = $this->db_query($query);
			if ($res == 0) {
				//echo $this->db_error();
				//echo "<br>Errno: ".mysql_errno();
				return 0;
			}

			return $this->insert_id();
		}
	}


}
?>