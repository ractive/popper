<?php

/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

include("class.pop3.inc.php");
include("class.mailer.inc.php");
include("config.inc.php");
include("class.db_tool.inc.php");

// Include the missing function array_diff for PHP 4.0.0
if ($GLOBALS["is_php400"]) {
	include("diff.inc.php");
}

class popper {
	var $db_tool;
	var $name = "";
	var $userid = 0;
	var $db;
	var $folder = "";
	var $cur_message_id = 0;
	var $cur_address_id = 0;
	var $cur_account_id = 0;
	var $cur_mailer = 0;
	var $month = 0;

	var $del;
	var $to;
	var $folders = array(
			"inbox" => array(
				"name" => "Inbox",
				"pic" => "inbox.gif",
				"cur_page" => 0
			),
			"outbox" => array(
				"name" => "Outbox",
				"pic" => "outbox.gif",
				"cur_page" => 0
			),
			"sent" => array(
				"name" => "Sent mail",
				"pic" => "sent.gif",
				"cur_page" => 0
			),
			"bin" => array(
				"name" => "Recycle bin",
				"pic" => "bin.gif",
				"cur_page" => 0
			),
			"drafts" => array(
				"name" => "Drafts",
				"pic" => "drafts.gif",
				"cur_page" => 0
			),
			"cal" => array(
				"name" => "Calendar",
				"pic" => "cal.gif",
				"cur_page" => 0
			)
		);
	var $config = array();

	function popper() {
		$this->db_tool = new db_tool("popper.inc.php");
		$this->folder = "inbox";
	}

	function get_userid($name = "") {
		if (empty($name)) {
			$name = $this->name;
		}
		$query = "SELECT id FROM users WHERE STRCMP(name, '$name') = 0";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "DB Error");
			return;
		}

		$row = $this->db_tool->fetch_array($res);

		if (isset($row[id]))
			return $row[id];
		else
			return 0;
	}

	function get_list_db() {
		$doc_root = getenv("DOCUMENT_ROOT");
		require("$doc_root/../popper.inc.php");
		$this->db = mysql_connect($host, $user, $pass)
			or die ("Could not connect");

		$query = "use $dbname";
		mysql_query($query, $this->db)
			or die ("Could not open mailinglist database");

		return $this->db;
	}

	function set_user($name) {
		$this->name = $name;
		$this->userid = $this->get_userid();
		$this->load_config();
		if ($this->config["auto_check"] == 1) {
			$this->get_mails();
		}
	}
	
	function load_config() {
		$query = "SELECT * FROM conf WHERE user_id = '$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "The user-configuration could not be fetched");
			return;
		}
		
		$row = $this->db_tool->fetch_array($res);
		$this->config["keep"] = $row["keep"];
		$this->config["server_del"] = $row["server_del"];
		$this->config["auto_check"] = $row["auto_check"];
		$this->config["signature"] = $row["signature"];
		$this->config["app_sig"] = $row["app_sig"];								
		$this->config["send_direct"] = $row["send_direct"];
		$this->config["mails_per_page"] = $row["mails_per_page"];
		$this->config["mail_rows"] = $row["mail_rows"];
		$this->config["add_address"] = $row["add_address"];
	}

	function show_in_window($form, $title="", $status_msg="", $msgboxtext = "", $msgboxhead="") {
		global $top_part, $bottom_part;

		include($top_part);
		include("titlebar.inc.php");
		include("childwindow.inc.php");
		include("statusbar.inc.php");
		include($bottom_part);
	}
	function show_form($form, $status_msg = "") {
		global $account_id, $addr_id;

		if (isset($account_id) && !empty($account_id)) {
			$this->cur_account_id = $account_id;
		}
		if (isset($addr_id) && !empty($addr_id)) {
			$this->cur_address_id = $addr_id;
		}
		global $top_part, $bottom_part;
		include($top_part);
		include("titlebar.inc.php");
		if(file_exists($form.".toolbar.inc.php")) {
			include($form.".toolbar.inc.php");
		}
		include($form.".form.inc.php");
		include("statusbar.inc.php");
		include($bottom_part);
	}

	function show($folder = "", $msg = "") {
		global $mail_id;

		if (isset($mail_id) && !empty($mail_id)) {
			$this->cur_message_id = $mail_id;
		}
		else {
			$this->cur_message_id = 0;
			$this->cur_mailer = 0;
		}
		if (empty($folder)) {
			$folder = "inbox";
		}
		$this->folder = $folder;
		$this->show_form("main", $msg);
	}

	function has_account() {
		$query = "SELECT id FROM accounts WHERE user_id='$this->userid'";
		$res = $this->db_tool->db_query($query);
		return $this->db_tool->affected_rows($res) > 0;
	}

	function update_pageno($page, $mail_count) {
		$cur_page = $this->folders["$this->folder"]["cur_page"];
		if ($page == "next" && (($cur_page + 1) * $this->config["mails_per_page"]) < $mail_count) {
			$cur_page++;
		}
		else if ($page == "prev" && $cur_page != 0) {
			$cur_page--;
		}
		$this->folders["$this->folder"]["cur_page"] = $cur_page;
	}

	function get_pageno() {
		return $this->folders["$this->folder"]["cur_page"];
	}

	function folder_selected($folder) {
		if ($this->folder == $folder) {
			echo(" style=\"background-color: Blue; color: White;\" ");
		}
	}

	function address_selected($address) {
		if ($this->cur_address_id == $address) {
			echo(" style=\"background-color: Blue; color: White;\" ");
		}
	}
	function mail_selected($message) {
		if ($this->cur_message_id == $message) {
			echo(" style=\"background-color: Blue; color: White;\" ");
		}
	}

	function account_selected($acc_id) {
		if ($this->cur_account_id == $acc_id) {
			echo(" style=\"background-color: Blue; color: White;\" ");
		}
	}

	function show_addresses() {
		global $addr_id;

		if (isset($addr_id) && !empty($addr_id)) {
			$this->cur_address_id = $addr_id;
		}
		$query = "SELECT id, name, email FROM addresses WHERE user_id = '$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "DB Error");
			return;
		}
		if ($this->db_tool->affected_rows() < 1) {
			echo("No addresses found...");
			return;
		}

		echo("<table width=\"100%\">\n");
		echo("<tr><th align=\"left\">Name</th><th align=\"left\">E-Mail</th></tr>\n");
		while($row = $this->db_tool->fetch_array($res)) {
			echo("<tr><td>\n");
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=addressbook&addr_id=$row[id]\" title=\"Show details\"");
			$this->address_selected($row[id]);
			echo(">".$this->extract_disp_name(stripslashes($row[name]))."</a></td>\n");
			echo("<td><a href=\"$GLOBALS[PHP_SELF]?action=newmail&addr_id=$row[id]\" title=\"Send mail\">".stripslashes($row["email"])."</a></td></tr>\n");
		}
		echo("</table>\n");
	}

	function show_address() {
		global $addr_id;

		if (isset($addr_id) && !empty($addr_id)) {
			$this->cur_address_id = $addr_id;
		}
		$query = "SELECT * FROM addresses WHERE user_id='$this->userid' AND id='$this->cur_address_id'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "DB Error");
			return;
		}
		if ($this->db_tool->affected_rows() != 1) {
			echo("Click on a name to see the details...<br><br><br><br><br><br><br><br><br><br><br>");
			return;
		}
		$row = $this->db_tool->fetch_array($res);
?>
		<table cellpadding="3" cellspacing="3">
			<tr>
				<td><b>Name:</b></td>
				<td><?php echo(stripslashes($row["name"])); ?></td>
			</tr>
			<tr>
				<td><b>Lastname:</b></td>
				<td><?php echo(stripslashes($row["lastname"])); ?></td>
			</tr>
			<tr>
				<td><b>Nickname:</b></td>
				<td><?php echo(stripslashes($row["nickname"])); ?></td>
			</tr>
			<tr>
				<td><b>E-Mail:</b></td>
				<td><?php echo(stripslashes($row["email"])); ?></td>
			</tr>
			<tr>
				<td><b>Address 1:</b></td>
				<td><?php echo(stripslashes($row["address1"])); ?></td>
			</tr>
			<tr>
				<td><b>Address 2:</b></td>
				<td><?php echo(stripslashes($row["address2"])); ?></td>
			</tr>
			<tr>
				<td><b>ZIP:</b></td>
				<td><?php echo(stripslashes($row["zip"])); ?></td>
			</tr>
			<tr>
				<td><b>Town:</b></td>
				<td><?php echo(stripslashes($row["town"])); ?></td>
			</tr>
			<tr>
				<td><b>State:</b></td>
				<td><?php echo(stripslashes($row["state"])); ?></td>
			</tr>
			<tr>
				<td><b>Country:</b></td>
				<td><?php echo(stripslashes($row["country"])); ?></td>
			</tr>
			<tr>
				<td><b>Phone:</b></td>
				<td><?php echo(stripslashes($row["phone"])); ?></td>
			</tr>
			<tr>
				<td><b>Mobile-phone:</b></td>
				<td><?php echo(stripslashes($row["mobile"])); ?></td>
			</tr>
			<tr>
				<td><b>Homepage:</b></td>
				<td>
				<?php
					if (!empty($row[homepage])) {
						echo("<a href=\"$row[homepage]\">".stripslashes($row["homepage"])."</a>");
					}
				?>
				</td>
			</tr>
		</table>
<?php
	}

	function show_account() {

		global $account_id;

		if (isset($account_id) && !empty($account_id)) {
			$this->cur_account_id = $account_id;
		}

		$query = "SELECT * FROM accounts WHERE user_id = '$this->userid' AND id = '$this->cur_account_id'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "DB Error");
			return;
		}
		if ($this->db_tool->affected_rows() != 1) {
			echo("Click on a name to see the details...<br><br><br><br><br><br><br><br><br><br><br>");
			return;
		}
		$row = $this->db_tool->fetch_array($res);
?>
		<table cellpadding="3" cellspacing="3">
			<tr>
				<td>Account name:</td>
				<td><?php echo(stripslashes($row["name"]));?></td>
			</tr>
			<tr>
				<td>From name:</td>
				<td><?php echo(stripslashes($row["fromname"]));?></td>
			</tr>
			<tr>
				<td>Reply to email:</td>
				<td><?php echo(stripslashes($row["replyto"]));?></td>
			</tr>
			<tr>
				<td>POP-Server:</td>
				<td><?php echo(stripslashes($row["popserver"]));?></td>
			</tr>
			<tr>
				<td>POP Loginname:</td>
				<td><?php echo(stripslashes($row["poplogin"]));?></td>
			</tr>
		</table>
		<br><br><br>
<?php
	}

	function show_accounts() {
		global $account_id;

		if (isset($account_id) && !empty($account_id)) {
			$this->cur_account_id = $account_id;
		}

		$query = "SELECT id, name, popserver FROM accounts WHERE user_id = '$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "DB Error");
			return;
		}

		echo("<table width=\"100%\">\n");
		echo("<tr>");

		echo("<th align=\"left\">Name</th><th align=\"left\">Server</th></tr>\n");
		while($row = $this->db_tool->fetch_array($res)) {
			echo("<tr><td>\n");
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=accounts&account_id=$row[id]\" title=\"Show details\"");
			$this->account_selected($row[id]);
			echo(">".stripslashes($row["name"])."</a></td>\n");
			echo("<td>".stripslashes($row["popserver"])."</td></tr>\n");
		}
		echo("</table>\n");
	}

	function store_address() {
		$GLOBALS["user_id"] = $this->userid;
		$this->db_tool->store("addresses", "user_id", "email", "name", "lastname", "nickname", "address1", "address2", "zip", "town", "state", "country", "phone", "mobile", "homepage", "id='$this->cur_address_id' AND user_id='$this->userid'");
	}

	function store_account() {
		$GLOBALS["user_id"] = $this->userid;
		$this->db_tool->store("accounts", "user_id", "name", "fromname", "replyto", "popserver", "poplogin" ,"poppwd", "id='$this->cur_account_id' AND user_id='$this->userid'");
	}

	function get_account_name($id = "") {
		if(!isset($id) || empty($id)) {
			$id = $this->cur_account_id;
		}
		$query = "SELECT name FROM  accounts WHERE id=$id AND user_id='$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "DB Error");
			return;
		}
		if ($this->db_tool->affected_rows($res) != 1) {
			return "";
		}

		$row = $this->db_tool->fetch_row($res);

		return stripslashes($row[0]);
	}

	function get_address_name($id = "") {
		if(!isset($id) || empty($id)) {
			$id = $this->cur_address_id;
		}
		$query = "SELECT name FROM  addresses WHERE id='$id' AND user_id='$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "DB Error");
			return "";
		}
		if ($this->db_tool->affected_rows($res) != 1) {
			return "";
		}

		$row = $this->db_tool->fetch_row($res);

		return stripslashes($row[0]);
	}

	function del_address() {
		global $ack;
		$name = $this->get_address_name();
		if (!isset($ack) || empty($ack) ) {
			$this->show_in_window("ack", "Delete address $name", "Confirm deletion of address $name");
			return;
		}
		else if($ack == "No") {
			$del_msg = "Deletion cancelled!";
		}
		else {
			$query = "DELETE FROM addresses WHERE id = '$this->cur_address_id' AND user_id = '$this->userid'";
			$res = $this->db_tool->db_query($query);
			if ($res == 0) {
				$this->report_error($this->db_tool->db_error(), "DB Error");
				return;
			}
			if ($this->db_tool->affected_rows() > 0) {
				$del_msg = "The address $name has been deleted!";
			}
			else {
				$del_msg = "The address $name could not be deleted!";
			}
		}

		$this->cur_address_id = 0;

		$this->show_form("addressbook", $del_msg);
	}

	function del_account() {
		global $ack;
		$accountname = $this->get_account_name();
		if (!isset($ack) || empty($ack) ) {
			$this->show_in_window("ack", "Delete account $accountname", "Confirm deletion of account $accountname");
			return;
		}
		else if($ack == "No") {
			$del_msg = "Deletion cancelled!";
		}
		else {
			$query = "DELETE FROM accounts WHERE id = '$this->cur_account_id' AND user_id = '$this->userid'";
			$res = $this->db_tool->db_query($query);
			if ($res == 0) {
				$this->report_error($this->db_tool->db_error(), "DB Error");
				return;
			}
			if ($this->db_tool->affected_rows() > 0) {
				$del_msg = "The account $accountname has been deleted!";
			}
			else {
				$del_msg = "The account $accountname could not be deleted!";
			}
		}

		$this->cur_account_id = 0;

		$this->show_form("accounts", $del_msg);
	}

	function show_folders() {
		$query = "SELECT folder, count(*) as count FROM mails WHERE was_read!=1 AND user_id='$this->userid' GROUP BY folder";
		$res = $this->db_tool->db_query($query);
		if ($res != 0) {
			while($row = $this->db_tool->fetch_array($res)) {
				// Make an key-value array. Key = foldername, value=count of unread mails
				$folder_info[$row[folder]] = $row[count];
			}
		}
		echo("<table width=\"80%\">");
			while(list($key, $val) = each($this->folders)) {
				echo("<tr>\n");
					echo("<td width=\"1%\"><img src=\"graphics/$val[pic]\" alt=\"$val[name]\" title=\"$val[name]\" style=\"vertical-align: middle;\"></td>\n");
					echo("<td><a href=\"$GLOBALS[PHP_SELF]?action=showfolder&folder=$key\"");
					echo($this->folder_selected($key));
					if ($folder_info[$key] > 0) {
						echo("style=\"font-weight: bolder\"");
					}
					echo(">$val[name]");

					if ($folder_info[$key] > 0) {
						echo(" (".$folder_info[$key].")");
					}
					echo("</a><br></td>\n");
				echo("</tr>\n");
			}
		echo("</table>");
	}

	function count_mails() {
		$query = "SELECT COUNT(*) FROM mails WHERE user_id='$this->userid' AND folder='$this->folder'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "DB Error");
			return;
		}
		$row = $this->db_tool->fetch_row($res);
		return $row[0];
	}

	function report_error($err_msg, $head) {
		$this->messagebox($err_msg, "ERROR", $head);
	}

	function show_folder_content() {
		global $mail_id, $page, $col_width;
		if (empty($this->folder)) {
			$this->folder = "inbox";
		}
		/*if (!isset($mail_id) || $mail_id == 0) {
			$this->cur_message_id = 0;
		}*/

		while(1) {
			$pageno = $this->get_pageno() * $this->config["mails_per_page"];
			$mails = $this->config["mails_per_page"];
			$query = "SELECT id, tos, froms, IF(LENGTH(subject) > $col_width, CONCAT(LEFT(subject, $col_width), ' ...'), subject) as subject, is_mime, date_format(date, 'd.m.Y H:i:s') as date, date as date_raw, was_read FROM mails WHERE folder = '$this->folder' AND user_id = '$this->userid' ORDER BY date_raw DESC LIMIT $pageno, $mails";
			$res = $this->db_tool->db_query($query);
			if ($res == 0) {
				$this->report_error($this->db_tool->db_error(), "DB Error");
				return;
			}
			// See how much mails we got
			if (($count = $this->db_tool->affected_rows()) == 0) {
				if ($this->get_pageno() > 0) {
					// The user probably delete the last mail on the page.
					// Make a new SELECT on the previous page
					$mail_count = $this->count_mails();
					$this->update_pageno("prev", $mail_count);
					continue;
				}
				else {
					echo (" No mails in this folder");
					echo(str_repeat("<br>", $this->config["mails_per_page"] - 1));
					return;
				}
			}

			break;
		}

		echo("<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">");
		echo("<tr style=\"text-align: left; background-color: silver; color: inherit;\">\n");
		echo("<th style=\"border: thin outset silver; width: 20px; text-align: center;\"><img src=\"graphics/attachment_small.gif\" alt=\"Att.\" align=\"center\"></th><th style=\"border: thin outset silver;\" width=\"33%\">");
		if ($this->folder == "outbox" || $this->folder == "sent") {
			echo("To");
		}
		else {
			echo("From");
		}
		echo("</th><th style=\"border: thin outset silver;\" width=\"40%\">Subject</th><th style=\"border: thin outset silver;\">Date</th></tr>\n");

		// Now fill in the mails
		while ($row = $this->db_tool->fetch_array($res)) {
			echo("<tr");
			// If the mail was not read, display it bold
			if (!$row["was_read"]) {
				echo(" style=\"font-weight: bolder;\"");
			}
			echo(">\n<td style=\"width: 20px; text-align: center;\">\n");
			if ($row[is_mime]) {
				//echo("<a href=\"$GLOBALS[PHP_SELF]?action=getattachment&mail_id=$row[id]&att_id=2\" title=\"Get attachment\" target=\"top_\"><img src=\"graphics/attachment_small.gif\" alt=\"Get attachment\" align=\"center\" border=\"0\" style=\"vertical-align: middle;\"></a>\n");
				echo("<img src=\"graphics/attachment_small.gif\" alt=\"Get attachment\" align=\"center\" border=\"0\" style=\"vertical-align: middle;\">\n");				
			}
			echo("</td><td>\n<a href=\"$GLOBALS[PHP_SELF]?action=showfolder&folder=$this->folder&mail_id=$row[id]\"\n");
			$this->mail_selected($row[id]);
			// If the mail was not read, display it bold (also the link)
			if (!$row["was_read"]) {
				echo(" style=\"font-weight: bolder;\"");
			}
			else {
				echo(" style=\"font-weight: normal;\"");
			}
			echo(">");
			$mailer = new mailer(0);

			if ($this->folder == "outbox" || $this->folder == "sent") {
				$out = htmlspecialchars($this->extract_disp_name($row["tos"]));
			}
			else {
				$out = htmlspecialchars($this->extract_disp_name($row["froms"]));
			}

			// Cut the name if it's too long
			if (strlen($out) > $col_width) {
				$out = substr($out, 0, $col_width)."...";
			}
			echo($out);
			
			//$subject = $mailer->headers->charset_decode($row["subject"]);
			$subject = $mailer->charset_decode(stripslashes($row["subject"]));
			echo("</a></td>\n<td>".htmlspecialchars($subject));
			echo("</td>\n<td>".$row[date]."</td>");
			echo("</tr>\n");
		}
		echo("</table>");

		// If we have less then $minlines mails, we fill the rest with line-breaks

		if ($count < $this->config["mails_per_page"]) {
			echo(str_repeat("<br>", $this->config["mails_per_page"] - $count));
		}

	}

	function extract_disp_name($name) {
		// Check for an address like "Foobar" <foo@bar.com>
		// and only return the Name part to display
		$name = stripslashes($name);
		$mail = new mailer(0);
		// Decode the charset
		$cont = $mail->charset_decode($name);
		
		// Strip out header comments
		$cont = $mail->strip_comment($cont);

		// Get the parts that are either quoted or in angle brackets
		preg_match("/(\"(.*?)\"\s*)*<(.*?)>/", $cont, $matches);

		// We prefer the quoted representation
		if (!empty($matches[2])) {
			return $matches[2];
		}
		// Then the string in brackets
		if (!empty($matches[3])) {
			return $matches[3];
		}
		// If nothing did match, we just return the original header content

		return $cont;
	}

	function show_sources() {
		$GLOBALS[show_sources] = true;
		$this->show_form("main", "The sources of this E-mail");
	}

	function show_mail_content() {
		if ($this->cur_message_id == 0) {
			echo("\r\n\r\n  ======== No message selected ========");
			return;
		}
		$content = "";
		if (isset($GLOBALS[show_sources]) && $GLOBALS[show_sources] == true) {
			$query = "SELECT mail FROM mails WHERE id='$this->cur_message_id' AND user_id='$this->userid'";
			$res = $this->db_tool->db_query($query);
			if ($res == 0) {
				$content = "\r\n\r\n  ======== ERROR while trying to get mail! ========";
			}
			else {
				$row = $this->db_tool->fetch_row($res);
				$content = $row[0];
			}
		}
		else {
			if ($this->cur_mailer == 0 || $this->cur_mailer->id != $GLOBALS[mail_id]) {
				$this->cur_mailer = new mailer($this->userid);

				if (!$this->cur_mailer->load($this->cur_message_id)) {
					$content = "\r\n\r\n  ======== ERROR while trying to get mail! ========";
				}
			}
		}

		if (empty($content)) {
			$content = $this->cur_mailer->get_text_body();
		}

		// If we are here, the mail has been read, we tell that the DB
		$this->set_mail_read($this->cur_message_id);

		echo(stripslashes($content));
	}

	function set_mail_read($mail_id) {
		$query = "UPDATE mails SET was_read = 1 WHERE id='$mail_id' AND user_id='$this->userid'";
		$this->db_tool->db_query($query);
	}

	function get_foldername() {
		return $this->folders["$this->folder"]["name"];
	}

	function pwd_check($name, $pwd) {
		// Check if we were called with mailaddress and password to really login.

		if (!empty($name) && !empty($pwd)) {

			$res = $this->db_tool->db_query("SELECT name, pwd FROM users WHERE STRCMP(name, '$name') = 0");
			if ($res == 0) {
				$this->report_error($this->db_tool->db_error(), "DB Error");
				return;
			}
			if ($this->db_tool->affected_rows() == 0) {
				unset($name);
				unset($pwd);
				$this->show_in_window("newuser", "The user $name was not found in the database. Please create an account first.");
			}

			$row = mysql_fetch_array($res)
				or die (mysql_error());

			if ($row[pwd] != $pwd) {
				unset($name);
				unset($pwd);
				$this->show_in_window("login", "Login", "Your password was not correct. Please try again");
			}
			else {
				return 1;
			}
		}

		return 0;
	}

	function create_user() {
		global $name, $email, $pwd, $pwd2;

		if (!isset($name) && !isset($pwd) && !isset($pwd2)) {
			// Seems like new user is called for the first time without any info
			// Just show the form
			$this->show_in_window("newuser", "Create new user");
			exit;
		}

		// Check if the password and the password confirmation are tha same
		if ($pwd != $pwd2) {
			$this->show_in_window("newuser", "Password missmatch", "The passwords did not match, please enter them again");
			return;
		}

		// Don't allow empty passwords
		if (empty($pwd)) {
			$this->show_in_window("newuser", "Empty password", "You HAVE to enter a password");
			return 0;
		}

		// Everything is fine. Now we insert the new user into the DB
		$query = "INSERT INTO users (name, pwd, email) VALUES ('$name', '$pwd', '$email')";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$error = mysql_error();
			if (ereg("^Duplicate entry", $error)) {
				$this->show_in_window("newuser", "Username exists", "The username $name already exists. Please choose another one!");
				return 0;
			}
			else {
				$this->report_error($error, "DB Error");
			}
		}
		$this->userid = $this->db_tool->insert_id();
		$this->set_user($name);
		
		$this->send_confirmation_mail($name, $email);
		
		$query = "INSERT INTO conf (user_id, keep, mails_per_page, mail_rows) VALUES ('$this->userid', 1, 20, 20)";
		$res = $this->db_tool->db_query($query);
		
		return ($this->userid > 0);
	}

	function send_confirmation_mail($name, $email) {
		srand((double)microtime()*1000000);
		$check = md5(rand());
		$server = "http://".getenv("SERVER_NAME");
		$href  = $server.$GLOBALS[PHP_SELF];
		$href .= "?check=$check";

		// Save it in the database
		$query = "INSERT INTO confirmations (user_id, check) VALUES ('$this->userid', '$check')";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error("I could not write your confirmation informations. Please try again later<br><br>".$this->db_tool->db_error(), "DB ERROR");
			return;
		}

		$mailer = new mailer($this->userid);
		$mailer->to($email);
		$mailer->subject("Confirm subscription");
		$mailer->from("webmaster@".getenv("SERVER_NAME"));		
		$mail_text = "Dear $name\r\n\r\nYou requested an account at $server for the Web Mail Reader.\r\n\r\nPlease call the following link with your webbrowser to activate your account:\r\n$href\r\n\r\n(if you can't click it here, you have to copy and paste it to your webbrowser)\r\n\r\nThank you for using this service";
		$mailer->body = $mail_text;
		
		if (!$mailer->send()) {
			$this->report_error("The confirmation mail could not be sent to you. Please try again later", "Mail error");
			return;
		}
	}

	function check_confirmation($check) {
		$query = "SELECT user_id FROM confirmations WHERE check='$check'";
		$res = $this->db_tool->db_query($query);
		$row = $this->db_tool->fetch_array($res);
		$ret = $this->db_tool->affected_rows($res);
		if ($res && $ret) {
			$query = "DELETE FROM confirmations WHERE check='$check'";
			$this->db_tool->db_query($query);
			$query = "UPDATE users SET activated=1 WHERE id='$row[user_id]'";
			$ret &= $this->db_tool->db_query($query);

			if ($ret == 0) {
				$this->report_error("Your account could not be activated. The database refused the request", "DB ERROR");
				exit;
			}			
		}
		
		return $ret;
	}

	function is_activated($name) {
		$id = $this->get_userid($name);
		$query = "SELECT activated FROM users WHERE id='$id'";

		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error("Activation status could not be fetched", "DB ERROR");
			exit;
		}
		$row = $this->db_tool->fetch_array($res);
		
		return $row["activated"];
		
	}

	function messagebox($msgtext, $msgtitle = "", $msghead = "", $msgstatus = "") {
		$this->show_in_window("report", $msgtitle, $msgstatus, $msgtext, $msghead);
	}

	function store_mail($backup = false) {
		global $to_x, $cc_x, $bcc_x, $tos, $ccs, $bccs, $subject, $body, $submit, $replyaccount, $submit, $attachment, $attachment_name, $attachment_size, $attachment_type;

		$body = stripslashes($body);

		if (isset($to_x) || isset($cc_x) || isset($bcc_x)) {
			$backup = true;
		}

		$query = "SELECT fromname, replyto FROM accounts WHERE id = '$replyaccount' AND user_id='$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "Could not fetch account information");
			exit;
		}
		$row = $this->db_tool->fetch_array($res);

		if ($submit == "Send") {
			$folder = "outbox";
		}
		else if ($submit == "Save") {
			$folder = "drafts";
		}
		else {
			$folder = "inbox";
		}

		$mailer = new mailer($this->userid, $replyaccount);
		$mailer->create($tos, $row[replyto], $subject, $body);
		if (isset($ccs) && !empty($ccs)) {
			$mailer->cc($ccs);
		}
		if (isset($bccs) && !empty($bccs)) {
			$mailer->bcc($bccs);
		}	
			
		if (isset($attachment) && !empty($attachment) && $attachment != "none") {
			$mailer->attach_file($attachment, $attachment_name, $attachment_type, "", "", "");
		}

		// We backup the message to use it later
		// This is used to show the "Choose recipients" dialog. The current mail then does not get lost
		if ($backup) {
			if (isset($to_x)) {
				$GLOBALS["field"] = "TO";
			}
			else if (isset($cc_x)) {
				$GLOBALS["field"] = "CC";
			}	
			else if (isset($bcc_x)) {
				$GLOBALS["field"] = "BCC";
			}						
			$this->cur_mailer = $mailer;

			$this->show_in_window("choose", "Please select the recipients for the $GLOBALS[filed]-field");			
			exit;
		}
		else {
			// Does the user want to send it directly?
			if ($this->config["send_direct"] == 1) {
				if ($mailer->send()) {
					// The mail could be sent. Store it in the sent folder
					$folder = "sent";
				}
				else {
					// The mail could not be sent, keep it in the outbox folder
					$folder = "outbox";
				}
				
			}
			if ($mailer->store($folder)) {
				return "The mail was moved to the $folder-folder";
			}
			else {
				return "The mail could not be moved to the $folder-folder";
			}
		}
	}
	function show_attachments() {
		if ($this->cur_message_id == 0) {
			return;
		}
		if ($this->cur_mailer == 0 || $this->cur_mailer->id != $GLOBALS[mail_id]) {		
			$this->cur_mailer = new mailer($this->userid);
			$this->cur_mailer->load($this->cur_message_id);
		}		
		$i = 1;
		while($this->cur_mailer->extract_part($i)) {
			$type = $this->cur_mailer->get_attachment_type($i);
			switch ($type) {
				case "image/gif":
				case "image/jpeg":
				case "image/png":			
				case "image/tiff":			
				case "image/pjpeg":
					$type_pic = "picture.gif";
					break;
				case "video/mpeg":
				case "video/quicktime":
					$type_pic = "movie.gif";
					break;
				case "application/x-zip-compressed":
				case "application/zip":			
					$type_pic = "zip.gif";
					break;
				case "text/plain":
				case "application/rtf":
				case "text/html":
					$type_pic = "text.gif";
					break;
				case "application/pdf":
					$type_pic = "pdf.gif";
					break;
				default:
					$type_pic = "unknown.gif";
			}
			$att_name = $this->cur_mailer->get_attachment_name($i);
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=getattachment&mail_id=$GLOBALS[mail_id]&att_id=$i\" title=\"$att_name\" target=\"top_\" style=\"width: auto;\"><img src=\"graphics/$type_pic\" border=\"0\" alt=\"$att_name\" style=\"vertical-align: top;\"></a>\n");
			$i++;
		}
	}
	
	function show_head() {
		if ($this->cur_message_id == 0) {
			return;
		}
		if ($this->cur_mailer == 0 || $this->cur_mailer->id != $GLOBALS[mail_id]) {
			$this->cur_mailer = new mailer($this->userid);
			$this->cur_mailer->load($this->cur_message_id);
		}

		echo("<b>From:</b> ".htmlspecialchars(stripslashes($this->cur_mailer->from()))."&nbsp;<b>To:</b> ".htmlspecialchars(stripslashes($this->cur_mailer->to())));

		$cc = $this->cur_mailer->cc();
		if (!empty($cc)) {
			echo("&nbsp;<b>Cc:</b> ".htmlspecialchars($this->cur_mailer->cc()));
		}
		$subject = $this->cur_mailer->charset_decode(stripslashes($this->cur_mailer->subject()));
		//$subject = $this->cur_mailer->headers->get_decoded("Subject");
		echo("<br><b>Subject:</b> ".htmlspecialchars($subject));
	}
	
	function get_attachment() {
		global $att_id;
		if (!isset($att_id)) {
			$att_id = 1;
		}
		if ($this->cur_mailer == 0 || $this->cur_message_id != $GLOBALS["mail_id"]) {
			$this->cur_mailer = new mailer($this->userid);
			if (!$this->cur_mailer->load($GLOBALS["mail_id"])) {
				$this->report_error("Could not extract the attachment", "Mail error");
				exit;
			}
		}
		$data = $this->cur_mailer->get_attachment($att_id);
		if ($data === false) {
			$this->report_error("The attachment could not be extracted", "Mail error");
			exit;
		}
		$disp = "Content-Disposition: ".str_replace("\r\n\t", " ", $this->cur_mailer->mimeparts[$att_id]->headers->get("Content-Disposition"));
		$type = "Content-Type: ".str_replace("\r\n\t", " ", $this->cur_mailer->mimeparts[$att_id]->headers->get("Content-Type"));

		$disp = str_replace("attachment;", "inline;", $disp);
		Header($disp);
		Header($type);

		echo($data);

		exit;
	}

	function delete_mail() {
		global $ack;
		if ($this->cur_message_id == 0) {
			$this->show_form("main", "Please select a message to delete first");
			return 0;
		}

		$del_msg = "";

		// When the mail is in the bin folder we ask the user for a confirmation if the wants to delete
		//
		if ($this->folder == "bin") {
			// If $ack is not set, we pop-up the confirmation window
			if (!isset($ack) || empty($ack) ) {
				$this->del = $this->cur_message_id;
				$this->show_in_window("ack", "Delete mail", "Do you really want to delete this mail?");
				return;
			}
			else if($ack == "No") {
				$this->del = "";
				// The user said no :-(
				$del_msg = "Deletion cancelled!";
			}
			else {
				$this->cur_message_id = $this->del;

				if ($this->config["server_del"] == 1) {
					if ($this->trash_mail($this->cur_message_id)) {
						$del_msg = "The mail has been deleted from the server!";
					}
					else {
						$del_msg = "The mail could not be deleted from the server!";
					}
				}
				$this->del = "";
			}
		}
		else {
			if ($this->move_to_bin($this->cur_message_id)) {
				$del_msg = "The mail has been moved to the recycle bin";
			}
			else {
				$del_msg = "The mail could not be moved to the recycle bin!";
			}
		}

		$this->show_form("main", $del_msg);
	}

	function move_to_bin($id) {
		// Move the mail to the bin folder
		$query = "UPDATE mails SET folder='bin' WHERE id='$id' AND user_id='$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "Mail could not be move to the bin folder");
			return false;
		}
		return true;
	}

	function trash_mail($id) {
		// Get the account from this mail
		$query = "SELECT accounts.*, mails.uidl AS uidl FROM accounts LEFT JOIN mails ON mails.acc_id = accounts.id WHERE mails.id = '$id' AND mails.user_id = '$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$row = 0;
		}
		else {
			$row = $this->db_tool->fetch_array($res);
		}
		
		// Don't try to delete a mail from the server if we don't have the uidl
		// or the account information
		
		$d_uidl = stripslashes($row[uidl]);
		
		if ($row == 0 || empty($d_uidl)) {
			$pop_login = false;
		}
		else {
			// Connect to the server
			$pop3 = new pop3("$row[popserver]");
	
			$pop_login = true;
			$count = $pop3->pop_login($row["poplogin"], $row["poppwd"]);
			if ($count < 0 || $count === false) {
				$pop_login = false;
			}
		}

		if ($pop_login && $this->config["server_del"] == 1) {
			// Delete the mail from the POP server

			$server_uidls = $pop3->uidl();
			$del_id = 0;
			// Get the mail ID that corresponds the X-UIDL
			foreach($server_uidls as $sid => $uidl) {
				if ($uidl == $d_uidl) {
					$del_id = $sid;
					break;
				}
			}


			if ($del_id != 0) {
				// Now, delete the mail
				$pop3->delete($del_id);
			}
			
			$pop3->quit();
		}
		
		// Delete the mail from the database
		$query = "DELETE FROM mails WHERE id = '$id' AND user_id = '$this->userid' AND folder='bin'";

		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "DB Error");
			return false;
		}
		$this->cur_message_id = 0;
		$this->cur_mailer = 0;

		return $this->db_tool->affected_rows() > 0;
	}

	function show_recipients() {
		$query = "SELECT id, email, name FROM addresses WHERE user_id='$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "Could not fetch mail to delete");
			return;
		}
		echo('<table width="100%" align="center" border="1">');
		echo('<tr style="text-align: left;"><th>&nbsp;</th><th>Name</th><th>E-Mail</th></tr>');
		while($row = $this->db_tool->fetch_array($res)) {
			echo("<tr>\n<td>\n");
			echo('<input type="checkbox" name="rec[]" value="'.$row[id].'"></td>');
			echo("<td>".htmlspecialchars($row[name])."</td><td>");
			echo(htmlspecialchars($row[email])."</td></tr>\n");
		}
		echo('</table>');
	}

	function delete_mails() {
		global $ack, $delete, $del;

		if (isset($del) && !empty($del)) {
			$this->del = $del;
		}



		if(!(isset($delete) && $delete == "Delete") && !(isset($ack))) {
			$this->del = $del;
			// Show the deletion window if delete is not set
			$this->show_in_window("del", "Delete mails from the folder ".$this->folders[$this->folder][name]);
			return;
		}

		if ($this->folder == "bin") {
			// If $ack is not set, we pop-up the confirmation window
			if (!isset($ack) || empty($ack) ) {
				$this->show_in_window("ack", "Delete mail", "Do you really want to delete these mails?");
				return;
			}
			else if($ack == "No") {
				$this->del = "";
				// The user said no :-(
				$del_msg = "Deletion cancelled!";
			}
			else {
				$del = $this->del;
				if ((count($this->del) > 0)) {
					$all_succ = true;
					foreach($this->del as $d) {
						$all_succ &= $this->trash_mail($d);
					}
					if ($all_succ) {
						$del_msg = "All mails have been deleted";
					}
					else {
						$del_msg = "Not all mails could be deleted";
					}
				}
				$this->del = "";
			}
		}
		else if (count($del) > 0) {
			$all_succ = true;
			foreach($del as $d) {
				$all_succ &= $this->move_to_bin($d);
			}
			if ($all_succ) {
				$del_msg = "All mails have been moved to the Recycle bin";
			}
			else {
				$del_msg = "Not all mails could be moved to the Recycle bin";
			}

		}


		$this->show_form("main", $del_msg);
	}

	// List all mails in the current folder
	function show_del_mails() {
		$decoder = new mimepart;
		$query = "SELECT id, tos, froms, subject, date FROM mails WHERE folder='$this->folder' AND user_id='$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "Could not fetch mail to delete");
			return;
		}
		echo('<table width="100%" align="center" border="1">');
		echo('<tr style="text-align: left;"><th>&nbsp;</th><th>To</th><th>From</th><th>Subject</th></tr>');
		while($row = $this->db_tool->fetch_array($res)) {
			echo("<tr>\n<td>\n");
			echo('<input type="checkbox" name="del[]" value="'.$row[id].'"></td>');
			echo("<td>".htmlspecialchars(stripslashes($decoder->charset_decode($row[tos])))."</td><td>");
			echo(htmlspecialchars(stripslashes($decoder->charset_decode($row[froms])))."</td><td>");
			echo(htmlspecialchars(stripslashes($decoder->charset_decode($row[subject])))."</td>\n</tr>\n");
		}
		echo('</table>');
	}

	function send_and_receive() {
		global $what;

		if (!isset($what) || empty($what)) {
			$this->show($this->folder);
		}
		if ($what == "sendonly") {
			$this->send_outbox();
		}
		else if ($what == "sendandreceive") {
			$msg = $this->send_outbox();
			$this->get_mails();
			$this->show("sent", $msg);
			return;
		}
		else if ($what == "getall") {
			$this->get_mails();
		}
		else {
			// Only get the mails from a specified account
			$this->get_mails($what);
		}

		$this->show($this->folder, $msg);
	}

	function get_mails($account = "") {
		// Make an array with all UIDLs we already have
		$query = "SELECT uidl FROM mails WHERE user_id = '$this->userid' AND folder != 'outbox' AND folder != 'sent'";
		$res = $this->db_tool->db_query($query);
		$uidls = array();

		while($row = $this->db_tool->fetch_array($res)) {
			if (!empty($row["uidl"])) {
				$uidls[] = stripslashes($row["uidl"]);
			}
		}

		if (empty($account)) {
			// Get all accounts of the user
			$query = "SELECT * FROM accounts WHERE user_id = '$this->userid'";
		}
		else {
			// Just get the account with the given name
			$query = "SELECT * FROM accounts WHERE user_id = '$this->userid' AND id = '$account'";
		}

		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "DB Error");
			return;
		}

		while($row = $this->db_tool->fetch_array($res)) {
			$pop3 = new pop3("$row[popserver]");
			$count = $pop3->pop_login($row["poplogin"], $row["poppwd"]);
			if ($count < 0 || $count === false) {
				$error_head = "POP3 ERROR<br>Account $row[name]";
				$error_msg = "POP3 Error message:<br><br>".$pop3->ERROR;
				$this->report_error($error_msg, $error_head);
				continue;
			}

			// Get the UIDLs from the server
			$server_uidls = $pop3->uidl();
			
			// If the server doesn't support the X-UIDL header, just get
			// ALL the mails
			if ($server_uidls === false) {
				$get = $pop3->pop_list();
				array_shift($get);
			}
			else {
				// Remove the first entry. It contains the amount of mails on the server
				// BUT the mail-ids must then be set +1
				array_shift($server_uidls);
	
				// Get the difference between the UIDLS on the server and in the DB
				$get = array_diff($server_uidls, $uidls);			
			}
			$mailer = new mailer($this->userid, $row[id]);
			reset($get);

			foreach($get as $num => $uid) {
				$get_mail = $num + 1;
				$mail = $pop3->get_text($get_mail);

				if(empty($mail)) {
					continue;
				}

				if (!$mailer->store("inbox", $mail)) {
					$this->report_error($mailer->db_tool->db_error(), "Mail could not be stored");
					exit;
				}

				if ($this->config["keep"] != 1) {
					$pop3->delete($get_mail);
				}

				if ($this->config["add_address"]) {
					$this->add_address($mailer);
				}
			}
			
			$pop3->quit();
		}
	}

	function add_address($mailer) {
		$from = addslashes($mailer->headers->get("From"));
		$add = $mailer->get_address($from);
		if (!empty($add["address"])) {
			$query = "SELECT name, email FROM addresses WHERE name='$add[name]' AND email='$add[address]' AND user_id='$this->userid'";
			$res = $this->db_tool->db_query($query);
			if ($this->db_tool->affected_rows() == 0) {
				$GLOBALS["name"] = $add["name"];
				$GLOBALS["email"] = $add["address"];
				$this->store_address();
			}
		}
		
	}

	function show_config() {
		echo('<input type="checkbox" name="keep" value="1"');
		if ($this->config[keep] == 1) {
			echo(' checked');
		}
		echo('> Keep mails on server<br>');
		
		echo('<input type="checkbox" name="server_del" value="1"');
		if ($this->config[server_del]) {
			echo(' checked');
		}		
		echo('> Remove mails from server after deletion (from the recycle bin)<br>');
		
		echo('<input type="checkbox" name="auto_check" value="1"');
		if ($this->config[auto_check]) {
			echo(' checked');
		}			
		echo('> Check for mails after login<br>');
		
		echo('<input type="checkbox" name="send_direct" value="1"');		
		if ($this->config[send_direct]) {
			echo(' checked');
		}			
		echo('> Send mails direcly. Don\'t keep them in the outbox.<br>');		

		echo('<input type="checkbox" name="add_address" value="1"');		
		if ($this->config[add_address]) {
			echo(' checked');
		}			
		echo('> Add e-mail addresses of incoming e-mails to the address-book.<br>');	
		echo('<hr>Display <input name="mails_per_page" value="'.$this->config[mails_per_page].'" size="2"> mails per page<br>');
		echo('Show <input name="mail_rows" value="'.$this->config[mail_rows].'" size="2"> lines in textarea<br>');		
		echo('<hr>Signature:<br><br><textarea name="signature" cols="50" rows="4">');
		echo($this->config[signature]);
		echo('</textarea><br><br>');
		echo('<input type="checkbox" name="app_sig" value="1"');
		if ($this->config[app_sig]) {
			echo(' checked');
		}			
		echo('> Append signature to each message');
		
	}

	function store_config() {
		global $conf_submit, $keep, $server_del, $auto_check, $signature, $app_sig, $send_direct, $mail_rows, $mails_per_page, $add_address;

		if (isset($conf_submit) && $conf_submit == "Store") {
			$query = "UPDATE conf SET send_direct='$send_direct', keep='$keep', server_del='$server_del', auto_check='$auto_check', signature='$signature', app_sig='$app_sig', mails_per_page='$mails_per_page', mail_rows='$mail_rows', add_address='$add_address' WHERE user_id = '$this->userid'";
			$res = $this->db_tool->db_query($query);	
			if ($res != 0) {
				$this->config["keep"] = $keep;
				$this->config["server_del"] = $server_del;
				$this->config["auto_check"] = $auto_check;
				$this->config["signature"] = $signature;
				$this->config["app_sig"] = $app_sig;
				$this->config["send_direct"] = $send_direct;	
				$this->config["mail_rows"] = $mail_rows;
				$this->config["mails_per_page"] = $mails_per_page;
				$this->config["add_address"] = $add_address;
			}
		}
	}

	function start_timer($event) {
		printf("timer: %s\n", $event);
		list($low, $high) = split(" ", microtime());
		$t = $high + $low;
		flush();

		return $t;
 	}

	function next_timer($start, $event) {
		list($low, $high) = split(" ", microtime());
		$t    = $high + $low;
    	$used = $t - $start;
    	printf("timer: %s (%8.4f)\n", $event, $used);
    	flush();

		return $t;
	}

	function send_outbox() {
		$query = "SELECT id FROM mails WHERE user_id='$this->userid' AND folder='outbox'";

		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "DB Error");
			return;
		}

		$count = $this->db_tool->affected_rows();
		
		// Commented the progress stuff out...
		/*$width = 110; //500/$count;
		$send_counter = 0;
		$s = $count > 1 ? "s" : "";
		echo('<span style="color: #3A6EA5;"><h2>Sending '.$count.' mail'.$s.'</h2><b>Please stand by, this can take a while:</b><br>');
		for($i = 0; $i < $count; $i++) {
			$per = sprintf("%d", (($i+1) * 100/($count)));
			echo("<span style=\"border-right: thin solid #3A6EA5; width: ".$width."px; text-align: right;\">$per%</span>\n");
		}
		echo("</span><br>\n");
  		//$t = $this->start_timer("Sending mail");
  		flush();*/

		while($row = $this->db_tool->fetch_array($res)) {
			$mailer = new mailer($this->userid);
			$mailer->load($row[id]);


			//$t = $this->next_timer($t, "Mail: $row[subject]");
			//flush();
			$ret = $mailer->send();
			//echo('<span style="border-right: thin solid #3A6EA5; width: '.$width.'px; text-align: right; background-color: #3A6EA5;">&nbsp;</span>');
			//$mail->print_mail();

			// When the mail could be send, we move the mail to the "sent" folder
			if ($ret) {
				$send_counter++;
				$query = "UPDATE mails SET folder='sent', was_read = 1 WHERE id='$row[id]' AND user_id='$this->userid'";
				$update_res = $this->db_tool->db_query($query);
				if ($update_res == 0) {
					$this->report_error($this->db_tool->db_error(), "DB Error\nMail could not be moved to the sent folder");
					return;
				}
			}
		}
		//echo('<span style="width: '.$width.'px; background-color: #3A6EA5;">&nbsp;</span>');
		//flush();
		//$this->next_timer($t, "fertig");
		$errors = $count - $send_counter;

		$msg = "$send_counter Mails have been sent successfully. $errors Errors!";
	}
}
?>