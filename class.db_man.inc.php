<?php

include("class.db_tool.inc.php");

class db_man {
	
	var $db_tool;
	var $user_id = 0;
	var $m_quotes = false;
	
	function db_man($user_id) {
		$this->user_id = $user_id;
		$this->db_tool = new db_tool("popper.inc.php");		
		$this->magic_quotes = get_magic_quotes_gpc();
	}
	
	// Strip slashes
	function stripper($x) {
		if (is_array($x)) {
			foreach($x as $key => $val) {
				$x[$key] = stripslashes($val);
			}
		}
		else {
			$x = stripslashes($x);
		}
		
		return $x;
	}
	
	
	function get_error() {
		return $this->db_tool->db_error();
	}
	
	// The first argument it the table name
	// followed by a variable size of column name and finally the where clause
	// i.e. quick_select("mails", "id", "mail", "from_name", "id='$id'");
	function quick_select($table) {
		$num_args = func_num_args();
		$args = func_get_args();
		$query = "SELECT ";
		for($i = 2; $i < $num_args - 1; $i++) {
			$query .= func_get_arg($i).", ";
		}
		// Remoe the last ", "
		$query = substr($query, 0, strlen($query) - 2);
		
		$query .= " WHERE ".func_get_arg($i++);
		$query .= " AND user_id = '$this->user_id'";
		
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		
		while($row = $this->db_tool->fetch_array($res)) {
			$ret[] = $row;
		}
		
		return $ret;
	}
	
	
	// Get the user id of a given name
	function get_userid($name) {
		$query = "SELECT id FROM users WHERE STRCMP(name, '$name') = 0";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}

		$row = $this->db_tool->fetch_row($res);

		return $row[0];
	}
	
	// Store the configuration
	function store_config() {
		global $theme, $view, $mailview, $keep, $server_del, $auto_check, $signature, $language, $subject_size, $check_int, $address_size, $app_sig, $send_direct, $mail_rows, $mails_per_page, $add_address, $clean_bin;
		
		$query = "UPDATE conf SET send_direct='$send_direct', keep='$keep', check_int='$check_int', server_del='$server_del', auto_check='$auto_check', signature='$signature', app_sig='$app_sig', mails_per_page='$mails_per_page', mail_rows='$mail_rows', add_address='$add_address', language='$language', subject_size='$subject_size', address_size='$address_size', view='$view', mailview='$mailview', clean_bin='$clean_bin', theme='$theme' WHERE user_id = '$this->user_id'";
		$res = $this->db_tool->db_query($query);
		
		return $res != 0;
	}	
	
	// Get the user configuration of the user with id $id
	function get_config() {
		$query = "SELECT * FROM conf WHERE user_id = '$this->user_id'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		
		$row = $this->db_tool->fetch_array($res);
		
		if ($row == 0) {
			return false;
		}
		
		return $row;
	}
	
	// Return the read status of the message $id
	function get_read_status($id) {
		$query = "SELECT was_read FROM mails WHERE user_id = '$this->user_id' AND id='$id'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		$row = $this->db_tool->fetch_row($res);
		
		return $row[0];
	}
	
	// Determines if the user already set up an account
	function account_present() {
		$query = "SELECT id FROM accounts WHERE user_id='$this->user_id'";
		$res = $this->db_tool->db_query($query);
		return $this->db_tool->affected_rows($res) > 0;	
	}
	
	// Return an assoc. array of all addresses of the current user
	function get_addresses($col_size, $blocked = false) {
		$query = "SELECT id, IF(LENGTH(name) > '".$col_size."', CONCAT(LEFT(name, '".$col_size."'), '...'), name) as name, IF(LENGTH(email) > '".$col_size."', CONCAT(LEFT(email, '".$col_size."'), '...'), email) as email FROM addresses WHERE user_id = '$this->user_id' AND blocked='$blocked'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		
		while ($row = $this->db_tool->fetch_array($res)) {
			// Add the new rot to the array
			$addr[] = $this->stripper($row);
		}
		
		return $addr;
	}
	
	// Return an assoc. array of all account of the current user
	function get_accounts() {
		$query = "SELECT id, name, popserver FROM accounts WHERE user_id = '$this->user_id'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		
		while ($row = $this->db_tool->fetch_array($res)) {
			// Add the new rot to the array
			$acc[] = $this->stripper($row);
		}
		
		return $acc;
	}	

	function get_accounts_full($id) {
		if (empty($id)) {
			// Get all accounts of the user
			$query = "SELECT * FROM accounts WHERE user_id = '$this->user_id'";
		}
		else {
			// Just get the account with the given id
			$query = "SELECT * FROM accounts WHERE user_id = '$this->user_id' AND id = '$id'";
		}

		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		
		while($row = $this->db_tool->fetch_array($res)) {
			$accs[] = $this->stripper($row);
		}
		
		
		return $accs;
	}
	

	// Get *  with id=$id from a given table
	function get($id, $table) {
		$query = "SELECT * FROM $table WHERE user_id='$this->user_id' AND id='$id'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
	
		return $this->stripper($this->db_tool->fetch_array($res));		
	}
	

	// Return the address with id=$id as an assoc. array
	function get_address($id) {
		return $this->get($id, "addresses");
	}

	// Return the account with id=$id as an assoc. array
	function get_account($id) {
		return $this->get($id, "accounts");
	}	
	
	// Tet the 'name' col of a given table
	function get_name($id, $table) {
		$query = "SELECT name FROM $table WHERE id=$id AND user_id='$this->user_id'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}

		$row = $this->db_tool->fetch_row($res);

		return $this->stripper($row[0]);
	}
	
	function get_account_name($id) {
		return $this->get_name($id, "accounts");
	}
	function get_user_name($id) {
		return $this->get_name($id, "users");
	}
	function get_address_name($id) {
		return $this->get_name($id, "addresses");
	}		
	
	// Try to unblock the address with id=$id
	function unblock_addr($id) {
		$query = "UPDATE addresses SET blocked=0 WHERE id = '$id' AND user_id = '$this->user_id' AND blocked=1";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		
		return $this->db_tool->affected_rows($res);
	}
	
	// Delete $id from table $table
	function del($id, $table) {
		$query = "DELETE FROM $table WHERE id = '$id' AND user_id = '$this->user_id'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		return $this->db_tool->affected_rows($res);			
	}
	
	function del_address($id) {
		return $this->del($id, "addresses");
	}
	function del_account($id) {
		return $this->del($id, "accounts");
	}	
	
	// Return an assoc. array with unread mail per folder
	function get_folder_unread() {
		$query = "SELECT folder, count(*) as count FROM mails WHERE was_read='".MAIL_UNREAD."' AND user_id='$this->user_id' GROUP BY folder";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		
		while($row = $this->db_tool->fetch_array($res)) {
			// Make an key-value array. Key = foldername, value=count of unread mails
			$folder_info[stripslashes($row[folder])] = $row[count];
		}
		
		return $folder_info;
	}
	
	// Count the mails of the cur. user in the folder $folder
	function count_mails($folder) {
		$query = "SELECT COUNT(*) FROM mails WHERE user_id='$this->user_id' AND folder='$folder'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		
		$row = $this->db_tool->fetch_row($res);
		
		return $row[0];
	}
	
	// Return an assoc. array of all mails in a folder
	function get_mail_list($folder, $order, $start = -1, $count = -1) {
		$query = "SELECT id, tos, from_name as froms, subject, is_mime, date, was_read, priority FROM mails WHERE folder = '$folder' AND user_id = '$this->user_id' ORDER BY $order";

		if ($start != -1 && $count != -1) {
			$query .= " LIMIT $start, $count";
		}

		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		
		while($row = $this->db_tool->fetch_array($res)) {
			$mails[] = $this->stripper($row);
		}
	
		return $mails;
	}
	
	// Return the mail (text) of a given $id
	function get_mail($id) {
		$query = "SELECT mail FROM mails WHERE id='id' AND user_id='$this->user_id'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		$row = $this->db_tool->fetch_row($res);
		
		return $this->stripper($row[0]);		
	}

	// Return an array of all mail ids in the current folder
	function get_mails_in_folder($folder) {
		// We build an array with all mail ids and pass that to $this->delete_mails()
		$query = "SELECT id FROM mails WHERE user_id='$this->user_id' AND folder='$folder'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}

		if ($this->db_tool->affected_rows($res) == 0) {
			return -1;
		}
		
		// Build the array
		while($row = $this->db_tool->fetch_row($res)) {
			$mails[] = $row[0];
		}

		return $mails;
	}
	
	
	// Get the next id
	function get_next($id, $order) {
		return next_prev($id, true, $order);
	}
	// Get the previous id
	function get_prev($id) {
		return next_prev($id, false, $order);
	}
	
	function next_prev($id, $next, $order) {
		// Get all mail ids

		$query = "SELECT id FROM mails WHERE user_id='$this->user_id' ORDER BY $order";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;	
		}
		if ($this->db_tool->affected_rows($res) == 0) {
			return false;
		}
		
		$old = 0;
		
		// Look for the current id
		while($row = $this->db_tool->fetch_row($res)) {
			if ($row[0] == $id) {
				if ($next) {
					// Get the next entry.
					// This is the new mail_id
					$row = $this->db_tool->fetch_row($res);
					return $row[0];
				}
				else {
					// The old entry is the new mail id
					// It it's null, we are at the top
					if ($old == 0) {
						return $id;
					}
					return $old;
				}
			}
			$old = $row[0];
		}
		
		// The id was not found ?!?!
		return $id;
	}
	
	// Set the status of the mail with id=$id
	function set_mail_status($id, $status) {
		$query = "UPDATE mails SET was_read='$status' WHERE id='$id' AND user_id='$this->user_id'";
		$res = $this->db_tool->db_query($query);
		return $res != 0;
	}
	
	// Check if the password $pwd is correct for user $name
	// Return values:
	//    db error:            false
	//    user doesn't exist:  0
	//    wrong pwd:          -1
	//    ok                   1
	function check_pwd($name, $pwd) {
		$query = "SELECT name, pwd FROM users WHERE STRCMP(name, '$name') = 0";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;	
		}
		
		if ($this->db_tool->affected_rows() == 0) {
			// The user doen't exist
			return 0;
		}
		
		$row = $this->db_tool->fetch_array($res);
		
		if ($row["pwd"] != $pwd) {
			// The password was not correct
			return -1;
		}
		
		// The password is ok
		return 1;
	}

	// Create a new user and the configuration
	// Return values:
	//    db error:            false
	//    user alredy exists:  -1
	//    ok                   new user id
	function create_user($name, $pwd, $email, $activated) {
		$query = "INSERT INTO users (name, pwd, email, activated) VALUES ('$name', '$pwd', '$email', '$activated')";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$error = $this->db_tool->db_error();
			if (ereg("^Duplicate entry", $error)) {
				return -1;
			}
			else {
				return false;
			}
		}
		$this->user_id = $this->db_tool->insert_id();
		
		$query = "INSERT INTO conf (user_id, keep) VALUES ('$this->user_id', 1)";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		
		return $this->user_id;
	}
	
	// Create a random check and insert it in the confirmations table
	function create_conf_check() {
		srand((double)microtime()*1000000);
		$check = md5(rand());

		// Save it in the database
		$query = "INSERT INTO confirmations (user_id, check) VALUES ('$this->user_id', '$check')";
		$res = $this->db_tool->db_query($query);	
		if ($res == 0) {
			return false;
		}
		
		return $check;
	}
	
	// Check if the confirmation is ok
	//    db error:            false
	//    wrong check:         -1  
	//    ok                   1	
	function check_confirmation($check) {
		$query = "SELECT user_id FROM confirmations WHERE check='$check'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		if ($this->db_tool->affected_rows($res) == 0) {
			return -1;
		}
		$row = $this->db_tool->fetch_array($res);
		// Activate the user account
		$query = "UPDATE users SET activated=1 WHERE id='$row[user_id]'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}		
		// Delete the confirmation from the table
		$query = "DELETE FROM confirmations WHERE check='$check'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}		

		return 1;
	}
	
	// Check if the account is activated or not
	function is_activated($name) {
		$query = "SELECT activated FROM users WHERE STRCMP(name, '$name') = 0";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		
		$row = $this->db_tool->fetch_array($res);
		
		return $row["activated"];	
	}
	
	// Get the information to send the password to a user
	function get_pwd_info($name) {
		$query = "SELECT users.email as email, users.pwd as pwd, conf.language as lang FROM users LEFT JOIN conf ON conf.user_id = users.id WHERE users.name='$name'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		// The username could not be found
		if ($this->db_tool->affected_rows($res) != 1) {
			return -1;
		}

		$row = $this->db_tool->fetch_array($res);	
		
		return $this->stripper($row);
	}
	
	// Return an array with the from names of the given id's in $del
	function get_form_names($del) {
		// Build a query string with the ids
		if (is_array($del)) {
			$query = "SELECT froms FROM mails WHERE id IN (";
			foreach($del as $in) {
				$query .= $in.", ";
			}
			// Remove the last comma
			$query = substr($query, 0, strlen($query) - 2);
			$query .= ") AND user_id='$this->user_id'";
		}
		
		$res = $this->db_tool->db_query($query);
		
		if ($res == 0) {
			return false;
		}
		
		// Build the array
		while($row = $this->db_tool->fetch_array($res)) {
			$ret[] = $this->stripper($row);
		}
		
		return $ret;
	}
	
	// Return an array of mail id's that have a from address that is blocked
	function get_blocked_mails($folder) {
		// Get all blocked mail addresses
		$query = "SELECT email FROM addresses WHERE user_id='$this->user_id' AND blocked=1";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;		
		}
		
		// Build the WHERE part for the query
		while ($row = $this->db_tool->fetch_row($res)) {
			$where .= " froms LIKE '%$row[0]%' OR";
		}
		if (!empty($where)) {
			// Remove the last OR
			$where = substr($where, 0, strlen($where) - 3);
		}
		
		// Get all mails that have blocked addrsses
		$query = "SELECT id FROM mails WHERE user_id='$this->user_id' AND folder='$folder' AND ".$where;

		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}

		// Build the array with the ids
		while($row = $this->db_tool->fetch_row($res)) {
			$del[] = $row[0];
		}
		
		return $del;
	}

	// Move mails to folder $folder
	// $id may be a single id or an array of ids
	function move_to($folder, $id) {
		if (is_array($id)) {
			// Build the query string
			$query = "UPDATE mails SET folder='bin' WHERE id IN (";
			foreach($id as $in) {
				$query .= $in.", ";
			}
			// Remove the last comma
			$query = substr($query, 0, strlen($query) - 2);
			$query .= ") AND user_id='$this->user_id'";
		}
		else {
			// Just one id
			$query = "UPDATE mails SET folder='bin' WHERE id='$id' AND user_id='$this->user_id'";
		}
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		
		return true;
	}
	
	// Return an array with all uidls
	function get_uidls() {
		$query = "SELECT uidl FROM mails WHERE user_id = '$this->user_id' AND folder != 'outbox' AND folder != 'sent'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}

		while($row = $this->db_tool->fetch_row($res)) {
			if (!empty($row[0])) {
				$uidls[] = $this->stripper($row[0]);
			}
		}
		
		return $uidls;
	}
	
	// Determine if the user defined blocked senders
	function get_blocked_addresses() {
		$query = "SELECT email FROM addresses WHERE blocked=1 AND user_id='$this->user_id'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			return false;
		}
		
		while($row = $this->db_tool->fetch_row($res)) {
			$ret[] = $this->stripper($row);
		}
		
		return $ret;
		
	}
		
	// Update the uidl of mail with id=$id
	function update_uidl($id, $uidl) {
		// If we use our own calculated uidls, we have to store it in the DB, too
		$query = "UPDATE mails SET uidl='$uidl' WHERE user_id = '$this->user_id' AND id = '$id'";
		$update_res = $this->db_tool->db_query($query);	
		return $update_res != 0;
	}
	
	// Remove everything from the current user in the DB
	function remove_user() {
		// Remove the mails
		$query = "DELETE FROM mails WHERE user_id='$this->user_id'";
		$res = $this->db_tool->db_query($query);
		
		// Remove the configuration
		$query = "DELETE FROM conf WHERE user_id='$this->user_id'";
		$res = $this->db_tool->db_query($query);
		
		// Remove the account
		$query = "DELETE FROM accounts WHERE user_id='$this->user_id'";
		$res = $this->db_tool->db_query($query);
		
		// Remove the addresses
		$query = "DELETE FROM addresses WHERE user_id='$this->user_id'";
		$res = $this->db_tool->db_query($query);
		
		// Remove the user entry				
		$query = "DELETE FROM users WHERE id='$this->user_id'";
		$res = $this->db_tool->db_query($query);
	}
	
	// Update the user's password
	function update_pwd($pwd) {
		$query = "UPDATE users SET pwd='$pwd' WHERE id='$this->user_id'";
		$res = $this->db_tool->db_query($query);
		return $res != 0;
	}
}

?>