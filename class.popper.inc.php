<?php

/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000-2005 by Jean-Pierre Bergamin

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

if (substr(phpversion(), 0, 5) == "4.0.0") {
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
	var $tmp_files = array();
	var $del;
	var $to;
	var $max_size;
	var $language;
	var $order = "date DESC";
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
		// Create the db_tool object
		$this->db_tool = new db_tool("popper.inc.php");
		
		// Determine the max size of a mail you can
		// store in the DB

		$query = "SHOW VARIABLES";
		$res = $this->db_tool->db_query($query);
		while($row = $this->db_tool->fetch_array($res)) {
			if ($row["Variable_name"] == "max_allowed_packet") {
				$this->max_size = $row["Value"];
				break;
			}
		}

		$this->folder = "inbox";
	}

	function pre_frame() {
		global $mail_id;
		
		if ($this->config["mailview"] != FRAME_VIEW) {
			return;
		}
		
		if (!isset($mail_id) || empty($mail_id)) {
			$mail_id = $this->cur_message_id;
		}
	}
	

	function set_cur() {
		global $mail_id, $load;
		
		if (!isset($mail_id) || $mail_id == 0) {
			$this->cur_message_id = 0;
			unset($this->cur_mailer);
			$this->cur_mailer =0;
		}		
		else if ($load == 1 || ($this->cur_mailer == 0 || (substr($mail_id, -3) != "att" && $this->cur_message_id != $mail_id))) {
			unset($this->att_mailer);
			$this->att_mailer = array();		
			unset($this->cur_mailer);
			$this->cur_mailer = new mailer($this->userid);
			$this->cur_mailer->load($mail_id);
			$this->cur_message_id = $mail_id;
		}
		

		
	}
	

	function unlink_tmp() {
		foreach($this->tmp_files as $file) {
			@unlink($file);
		}
	}

	function get_userid($name = "") {
		global $strings;
		if (empty($name)) {
			$name = $this->name;
		}
		$query = "SELECT id FROM users WHERE STRCMP(name, '$name') = 0";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
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
		global $strings;
		$this->name = $name;
		$this->userid = $this->get_userid();
		$this->load_config();
		if ($this->config["auto_check"] == 1) {
			$this->get_mails();
		}

		$this->update_lang();
	}

	function update_lang() {
		global $poppercookie, $strings, $locale;
		$lang_file = "lang/lang.".$this->config["language"].".inc.php";
		
		if (!file_exists($lang_file)) {
			$lang_file = "lang/lang.english.inc.php";	
		}
		
		require($lang_file);
		
		setlocale(LC_ALL, $locale);
		
		$this->folders["inbox"]["name"] = $strings["l_Inbox"];
		$this->folders["outbox"]["name"] = $strings["l_Outbox"];
		$this->folders["sent"]["name"] = $strings["l_SentMail"];
		$this->folders["bin"]["name"] = $strings["l_RecycleBin"];
		$this->folders["drafts"]["name"] = $strings["l_Drafts"];				
		$this->folders["cal"]["name"] = $strings["l_Calendar"];	
		if ($poppercookie[language]) {
			setcookie("poppercookie[language]", $this->config["language"], time() + 365 * 24 * 3600);
		}
	}
	
	function load_config() {
		global $strings;
		
		$query = "SELECT * FROM conf WHERE user_id = '$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_UserInfoFail"]);
			return;
		}
		
		$row = $this->db_tool->fetch_array($res);
		$this->config["keep"]			= $row["keep"];
		$this->config["server_del"]		= $row["server_del"];
		$this->config["auto_check"]		= $row["auto_check"];
		$this->config["signature"]		= $row["signature"];
		$this->config["app_sig"]		= $row["app_sig"];								
		$this->config["send_direct"]	= $row["send_direct"];
		$this->config["mails_per_page"] = $row["mails_per_page"];
		$this->config["mail_rows"]		= $row["mail_rows"];
		$this->config["add_address"]	= $row["add_address"];
		$this->config["language"]		= $row["language"];
		$this->config["subject_size"]	= $row["subject_size"];
		$this->config["address_size"]	= $row["address_size"];
		$this->config["check_int"]		= $row["check_int"];
		$this->config["view"]			= $row["view"];	
		$this->config["mailview"]		= $row["mailview"];
		$this->config["folder_view_width"]		= $row["folder_view_width"];
	}

	function send_pwd() {
		global $user, $strings;
/*DevNote: roytoo
Confirming the existence of a user account is very bad security.  A more secure scheme:
1.  Have the user enter their user name and an associated account's email address
2.  Attempt to look up the user name and email address
    a.  If valid:	 generate and send the lost password email, proceed to step 3.
	 b.  Not valid: Do not send an email, proceed to step 3.
3.  Display a message in the form "A password reset request has been sent to the email address
    you requested and should arrive soon.  Follow the instructions contained in the email."

For extra security:
1. Never send a user name and password in the same email.  Better yet, never send a password.  
   Instead, send a "change password link" like the confirmation link.
2. The email should also state the IP that the request originated from.
:DevNote*/
		$query = "SELECT users.email as email, users.pwd as pwd, conf.language as lang FROM users LEFT JOIN conf ON conf.user_id = users.id WHERE users.name='$user'";
		$res = $this->db_tool->db_query($query);
		if ($this->db_tool->affected_rows($res) != 1) {
			$this->show_in_window("lostpwd", $strings["l_LostPwd"], $strings["l_UserNotFound"]);
			return;
		}
	
		$row = $this->db_tool->fetch_array($res);

		$lang_file = "lang/lang.".$row["lang"].".inc.php";
		if (!file_exists($lang_file)) {
			$lang_file = "lang/lang.english.inc.php";	
		}
	
		include($lang_file);
		
		$server = getenv("SERVER_NAME");
		
		$mail = $strings["l_LostPwdMail1"]." ".$server;
		$mail .= "\r\nhttp://$server".$GLOBALS["PHP_SELF"];
/*DevNote: roytoo
Masked out for better security.  See prior DevNote.
:DevNote*/
//		$mail .= "\r\n\r\n".$strings["l_UserName"].": ".$user;
		$mail .= "\r\n".$strings["l_Pwd"].": ".stripslashes($row[pwd]);
		$mail .= "\r\n\r\n".$strings["l_LostPwdMail2"];
		
		if (mail($row[email], $strings["l_LostPwd"], $mail, "From: noreply@".$server)) {		
			$this->messagebox($strings[l_LostPwdMail3], $strings[l_LostPwdMail4], $strings[l_LostPwdMail4]);
		}
		else {
			$this->messagebox($strings[l_LostPwdMail5], $strings[l_Error], $strings[l_Error]);			
		}
		
	}
	

	function show_in_window($form, $title="", $status_msg="", $msgboxtext = "", $msgboxhead="") {
		global $top_part, $bottom_part, $strings, $action;
		
		if ($this->config["mailview"] == FRAME_VIEW && $form != "config" && $form != "changepwd" && $form != "newmail") {
			$this->show_small_window($form, $title, $status_msg, $msgboxtext, $msgboxhead);
			return;
		}
		
		if ($form != "choose" && $action != "choosen") {
			$this->set_cur();
		}

		include($top_part);
		include("titlebar.inc.php");
		include("childwindow.inc.php");
		include("statusbar.inc.php");
		include($bottom_part);
	}
	
	function show_small_window($form, $title = "", $status_msg = "", $msgboxtext = "", $msgboxhead = "") {
		global $strings;

		$this->frame_header();
		echo("<table class=\"windowborder\" width=\"50%\" border=\"0\" align=\"center\" style=\"margin-top: 2em;\">");
		echo("<tr class=\"titlebar\"><td>$title</td></tr>");
		echo("<tr class=\"childwindow\"><td>");
		if (file_exists($form.".toolbar.inc.php")) {	
			include($form.".toolbar.inc.php");
		}
		include($form.".form.inc.php");
		echo("</td></tr></table>");					
		$this->frame_footer();
	}
	
	function show_form($form, $status_msg = "") {
		global $account_id, $addr_id, $strings, $action;

		$this->set_cur();

		if (isset($account_id) && !empty($account_id)) {
			$this->cur_account_id = $account_id;
		}
		if (isset($addr_id) && !empty($addr_id)) {
			$this->cur_address_id = $addr_id;
		}
		global $top_part, $bottom_part;
		if ($this->config["mailview"] == FRAME_VIEW && ($form == "addressbook" || $form == "accounts")) {
			$this->frame_header();
			echo("<table class=\"windowborder\" width=\"100%\" border=\"0\" align=\"center\">");
		}
		else {
			include($top_part);
			include("titlebar.inc.php");
		}
		if(file_exists($form.".toolbar.inc.php")) {
			if ($this->folder == "cal") {
				include("cal.toolbar.inc.php");				
			}
			else {
				include($form.".toolbar.inc.php");
			}
		}
		include($form.".form.inc.php");
		if ($this->config["mailview"] == FRAME_VIEW && ($action == "addressbook" || $action == "accounts")) {
			echo("</table></form>");
			$this->frame_footer();
		}		
		else {
			include("statusbar.inc.php");
			include($bottom_part);
		}
	}

	function show($folder = "", $msg = "") {
		global $mail_id;
		
		if (empty($folder)) {
			$folder = "inbox";
		}
		$this->folder = $folder;
		$this->show_form("main", $msg);
	}

	function show_notification_request() {
		global $strings;
		if ($this->cur_mailer == 0) {
			return;
		}
		
		// Is there a notification header?
		$notify = $this->cur_mailer->headers->get("Disposition-Notification-To");
		if (!empty($notify)) {		
			$query = "SELECT was_read FROM mails WHERE user_id = '$this->userid' AND id='$this->cur_message_id'";
			$res = $this->db_tool->db_query($query);
			if ($res != 0 && $row = $this->db_tool->fetch_array($res)) {
				if ($row["was_read"] == MAIL_UNREAD) {
					$options = $this->cur_mailer->get_header("Disposition-Notification-Options");
					if ($options && $options["attribute"] == "important") {
						// We don't know any options...
						// Ignore the notification
						return;
					}
					
					echo("<tr class=\"childwindow\"><td>$strings[l_NotifyQuest]<br>$strings[l_NotifyQuest2]</td>\n");
					echo("<td><form action=\"$GLOBALS[PHP_SELF]\" method=\"GET\">\n<input type=\"submit\" name=\"submit\" value=\"$strings[l_SendNot]\">");
					echo("<input type=\"hidden\" name=\"action\" value=\"notification\"></form>\n</td>\n</tr>\n");
				}
			}
		}
		
	}
	
	function send_notification() {
		global $strings;

		$not_mailer = new mailer($this->userid);
		$notify = $this->cur_mailer->headers->get("Disposition-Notification-To");
		$from = $this->cur_mailer->headers->get("To");

		/*echo "TO: $notify<br>";
		echo("FROM: ".$this->cur_mailer->headers->get("To")."<br>");
		echo("SUBJECT: Re: ".$this->cur_mailer->headers->get("Subject"));
		echo("<br>");*/

		$msg = $strings["l_NotMsg"]."\r\n\r\n";

		$msg .= $strings["l_To"].": $from\r\n";
	
		$msg .= $strings["l_SentAt"].": ".$this->cur_mailer->headers->get("Date")."\r\n";
		$msg .= $strings["l_ReadAt"].": ".strftime("%c", time())."\r\n";
		$msg .= $strings["l_Subject"].": ".$this->cur_mailer->subject()."\r\n\r\n";
		
		$not_mailer->create($notify, $from, $strings["l_NotiSub"].": ".$this->cur_mailer->headers->get("Subject"), $msg);
		$boundary = $not_mailer->create_boundary();
		$not_mailer->headers->put("Content-Type", "multipart/report; report-type=disposition-notification;\tboundary=\"$boundary\"");

		$server = getenv("SERVER_NAME");
		$data = "Reporting-UA: $server; popper\r\n";
		$data .= "Original-Recipient: rfc822; $notify\r\n";
		$data .= "Final-Recipient: rfc822; $notify\r\n";
		$data .= "Original-Message-ID: ".$this->cur_mailer->headers->get("Message-ID")."\r\n";
		$data .= "Disposition: manual-action/MDN-sent-manually; displayed";


		$not_mailer->attach_data($data, "message/disposition-notification", "7bit", "", "inline", false);

		$not_mailer->send();
	}

	function has_account() {
		$query = "SELECT id FROM accounts WHERE user_id='$this->userid'";
		$res = $this->db_tool->db_query($query);
		return $this->db_tool->affected_rows($res) > 0;
	}

	function update_pageno($page, $mail_count) {
		$cur_page = $this->folders["$this->folder"]["cur_page"];
		if ($page == "top") {
			$cur_page = 0;
		}
		else if ($page == "last") {
			$cur_page = floor($mail_count / $this->config["mails_per_page"]);
		}
		else if ($page == "next" && (($cur_page + 1) * $this->config["mails_per_page"]) < $mail_count) {
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
		if (($this->config["mailview"] == IN_VIEW || $this->config["mailview"] == SEP_VIEW) && $this->cur_message_id == $message) {
			echo(" style=\"background-color: Blue; color: White;\" ");
		}
	}

	function account_selected($acc_id) {
		if ($this->cur_account_id == $acc_id) {
			echo(" style=\"background-color: Blue; color: White;\" ");
		}
	}

	function show_addresses($blocked = false) {
		global $addr_id, $strings, $show;

		if ($show == "blocked") {
			$blocked = true;
			echo("<h3>".$strings["l_Blocked"]."</h3>");
		}
		

		if (isset($addr_id) && !empty($addr_id)) {
			$this->cur_address_id = $addr_id;
		}

		$query = "SELECT id, IF(LENGTH(name) > '".$this->config["address_size"]."', CONCAT(LEFT(name, '".$this->config["address_size"]."'), '...'), name) as name, IF(LENGTH(email) > '".$this->config["address_size"]."', CONCAT(LEFT(email, '".$this->config["address_size"]."'), '...'), email) as email FROM addresses WHERE user_id = '$this->userid' AND blocked='$blocked'";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
			return;
		}
		if ($this->db_tool->affected_rows() < 1) {
			echo($strings[l_NoAddress]);
			return;
		}

		echo("<table>\n");
		echo("<tr><th align=\"left\" width=\"50%\">Name</th><th align=\"left\">E-Mail</th></tr>\n");
		while($row = $this->db_tool->fetch_array($res)) {
			echo("<tr><td>\n");
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=addressbook&addr_id=$row[id]");
			if ($blocked) {
				echo("&show=blocked");
			}
			echo("\" title=\"$strings[l_ShowDetails]\"");
			$this->address_selected($row[id]);
			echo(">".$this->extract_disp_name(stripslashes($row[name]))."</a></td>\n");
			echo("<td><a href=\"$GLOBALS[PHP_SELF]?action=newmail&addr_id=$row[id]\" title=\"$strings[l_SendMail]\" target=\"_parent\">".stripslashes($row["email"])."</a></td></tr>\n");
		}
		echo("</table>\n");
	}

	function show_address() {
		global $addr_id, $strings;

		if (isset($addr_id) && !empty($addr_id)) {
			$this->cur_address_id = $addr_id;
		}
		$query = "SELECT * FROM addresses WHERE user_id='$this->userid' AND id='$this->cur_address_id'";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
			return;
		}
		if ($this->db_tool->affected_rows() != 1) {
			echo("$strings[l_ClickName]<br><br><br><br><br><br><br><br><br><br><br>");
			return;
		}
		$row = $this->db_tool->fetch_array($res);
?>
		<table cellpadding="3" cellspacing="3">
			<tr>
				<td><b><?php echo($strings[l_Name]); ?>:</b></td>
				<td><?php echo(stripslashes($row["name"])); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_LastName]); ?>:</b></td>
				<td><?php echo(stripslashes($row["lastname"])); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_NickName]); ?>:</b></td>
				<td><?php echo(stripslashes($row["nickname"])); ?></td>
			</tr>
			<tr>
				<td><b>E-Mail:</b></td>
				<td><?php echo(stripslashes($row["email"])); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_Address]); ?> 1:</b></td>
				<td><?php echo(stripslashes($row["address1"])); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_Address]); ?> 2:</b></td>
				<td><?php echo(stripslashes($row["address2"])); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_ZIP]); ?>:</b></td>
				<td><?php echo(stripslashes($row["zip"])); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_Town]); ?>:</b></td>
				<td><?php echo(stripslashes($row["town"])); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_State]); ?>:</b></td>
				<td><?php echo(stripslashes($row["state"])); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_Country]); ?>:</b></td>
				<td><?php echo(stripslashes($row["country"])); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_Phone]); ?>:</b></td>
				<td><?php echo(stripslashes($row["phone"])); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_MobilePhone]); ?>:</b></td>
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

		global $account_id, $strings;

		if (isset($account_id) && !empty($account_id)) {
			$this->cur_account_id = $account_id;
		}

		$query = "SELECT * FROM accounts WHERE user_id = '$this->userid' AND id = '$this->cur_account_id'";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
			return;
		}
		if ($this->db_tool->affected_rows() != 1) {
			echo("$strings[l_ClickName]<br><br><br><br><br><br><br><br><br><br><br>");
			return;
		}
		$row = $this->db_tool->fetch_array($res);
?>
		<table cellpadding="3" cellspacing="3">
			<tr>
				<td><?php echo($strings[l_AccountName]); ?>:</td>
				<td><?php echo(stripslashes($row["name"]));?></td>
			</tr>
			<tr>
				<td><?php echo($strings[l_From]." ".$strings[l_Name]); ?>:</td>
				<td><?php echo(stripslashes($row["fromname"]));?></td>
			</tr>
			<tr>
				<td><?php echo($strings[l_ReplyTo]); ?>:</td>
				<td><?php echo(stripslashes($row["replyto"]));?></td>
			</tr>
			<tr>
				<td><?php echo($strings[l_POPServer]); ?>:</td>
				<td><?php echo(stripslashes($row["popserver"]));?></td>
			</tr>
			<tr>
				<td><?php echo($strings[l_POPLogin]); ?>:</td>
				<td><?php echo(stripslashes($row["poplogin"]));?></td>
			</tr>
		</table>
		<br><br><br>
<?php
	}

	function show_accounts() {
		global $account_id, $strings;

		if (isset($account_id) && !empty($account_id)) {
			$this->cur_account_id = $account_id;
		}

		$query = "SELECT id, name, popserver FROM accounts WHERE user_id = '$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
			return;
		}

		echo("<table width=\"100%\">\n");
		echo("<tr>");

		echo("<th align=\"left\">$strings[l_Name]</th><th align=\"left\">$strings[l_Server]</th></tr>\n");
		while($row = $this->db_tool->fetch_array($res)) {
			echo("<tr><td>\n");
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=accounts&account_id=$row[id]\" title=\"$strings[l_ShowDetails]\"");
			$this->account_selected($row[id]);
			echo(">".stripslashes($row["name"])."</a></td>\n");
			echo("<td>".stripslashes($row["popserver"])."</td></tr>\n");
		}
		echo("</table>\n");
	}

	function store_address() {
		$GLOBALS["user_id"] = $this->userid;
		$this->db_tool->store("addresses", "user_id", "email", "name", "lastname", "nickname", "address1", "address2", "zip", "town", "state", "country", "phone", "mobile", "homepage", "blocked", "id='$this->cur_address_id' AND user_id='$this->userid'");
	}

	function store_account() {
		$GLOBALS["user_id"] = $this->userid;
		$this->db_tool->store("accounts", "user_id", "name", "fromname", "replyto", "popserver", "poplogin" ,"poppwd", "id='$this->cur_account_id' AND user_id='$this->userid'");
	}

	function get_account_name($id = "") {
		global $strings;
		if(!isset($id) || empty($id)) {
			$id = $this->cur_account_id;
		}
		$query = "SELECT name FROM  accounts WHERE id=$id AND user_id='$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
			return;
		}
		if ($this->db_tool->affected_rows($res) != 1) {
			return "";
		}

		$row = $this->db_tool->fetch_row($res);

		return stripslashes($row[0]);
	}

	function get_address_name($id = "") {
		global $strings;
		
		if(!isset($id) || empty($id)) {
			$id = $this->cur_address_id;
		}
		$query = "SELECT name FROM  addresses WHERE id='$id' AND user_id='$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
			return "";
		}
		if ($this->db_tool->affected_rows($res) != 1) {
			return "";
		}

		$row = $this->db_tool->fetch_row($res);

		return stripslashes($row[0]);
	}

	function show_ack($title = "", $status_msg = "") {
		if ($this->config["mailview"] == FRAME_VIEW) {
			$this->show_small_window("ack", $title, $status_msg);
		}
		else {
			$this->show_in_window("ack", $title, $status_msg);
		}
		
		
	}
	

	function del_address() {
		global $ack, $strings;
		
		// First try to unblock the address
		$query = "UPDATE addresses SET blocked=0 WHERE id = '$this->cur_address_id' AND user_id = '$this->userid' AND blocked=1";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
			return;
		}

		if ($this->db_tool->affected_rows($res) === false) {
			// The address was not blocked. Delete it
			$name = $this->get_address_name();
			if (!isset($ack) || empty($ack) ) {
				$this->show_ack($strings["l_DelAddress"].": $name", $strings["l_DelAddressConf"]);
				return;
			}
			else if($ack == "No") {
				$del_msg = "Deletion cancelled!";
			}
			else {
				$query = "DELETE FROM addresses WHERE id = '$this->cur_address_id' AND user_id = '$this->userid'";
				$res = $this->db_tool->db_query($query);
				if ($res === false) {
					$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
					return;
				}
				if ($this->db_tool->affected_rows() > 0) {
					$del_msg = $strings["l_AddressDel"];
				}
				else {
					$del_msg = $strings["l_AddressDelFail"];
				}
			}
	
			$this->cur_address_id = 0;
		}
		
		$this->show_form("addressbook", $del_msg);
	}

	function del_account() {
		global $ack, $strings;;
		$accountname = $this->get_account_name();
		if (!isset($ack) || empty($ack) ) {
			$this->show_ack($strings["l_DelAccount"]." $accountname", $strings["l_DelAccountConf"]);
			return;
		}
		else if($ack == "No") {
			$del_msg = "Deletion cancelled!";
		}
		else {
			$query = "DELETE FROM accounts WHERE id = '$this->cur_account_id' AND user_id = '$this->userid'";
			$res = $this->db_tool->db_query($query);
			if ($res === false) {
				$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
				return;
			}
			if ($this->db_tool->affected_rows() > 0) {
				$del_msg = $strings["l_AccountDel"];
			}
			else {
				$del_msg = $strings["l_AccountDelFail"];
			}
		}

		$this->cur_account_id = 0;

		$this->show_form("accounts", $del_msg);
	}

	function show_folders() {
		$unread = MAIL_UNREAD;
		$query = "SELECT folder, count(*) as count FROM mails WHERE was_read='$unread' AND user_id='$this->userid' GROUP BY folder";
		$res = $this->db_tool->db_query($query);
		if ($res != 0) {
			while($row = $this->db_tool->fetch_array($res)) {
				// Make an key-value array. Key = foldername, value=count of unread mails
				$folder_info[$row[folder]] = $row[count];
			}
		}
		
		if ($this->config["view"] == MIN_VIEW) {
			$width = "100%";
		}
		else {
			$width="80%";
		}
		
		echo("<table width=\"$width\">");
			while(list($key, $val) = each($this->folders)) {
				echo("<tr>\n");
					if ($this->config["view"] != MIN_VIEW) {
						echo("<td width=\"1%\">");
						echo("<a href=\"$GLOBALS[PHP_SELF]?action=");
						if ($this->config["mailview"] == FRAME_VIEW && $key != "cal" && $this->folder != "cal") {
							echo("list&folder=$key\" target=\"maillist\" ");
						}
						else {
							echo("showfolder&folder=$key\" ");
							echo($this->folder_selected($key));
						}

						if ($folder_info[$key] > 0) {
							echo("style=\"font-weight: bolder\"");
						}
						echo(">");
						echo("<img src=\"graphics/$val[pic]\" alt=\"$val[name]\" title=\"$val[name]\" border=\"0\" ");
						echo("style=\"vertical-align: middle;\"></a></td>\n<td><a href=\"$GLOBALS[PHP_SELF]?action=");
						if ($this->config["mailview"] == FRAME_VIEW && $key != "cal" && $this->folder != "cal") {
							echo("list&folder=$key\" target=\"maillist\" ");
						}
						else {
							echo("showfolder&folder=$key\" ");
							echo($this->folder_selected($key));
						}

						if ($folder_info[$key] > 0) {
							echo("style=\"font-weight: bolder\"");
						}
						
						echo(">$val[name]");
	
						if ($folder_info[$key] > 0) {
							echo(" (".$folder_info[$key].")");
						}
						echo("</a><br></td>\n");
					}
					else {
						echo("<td align=\"center\"><a href=\"$GLOBALS[PHP_SELF]?action=");
						if ($this->config["mailview"] == FRAME_VIEW && $key != "cal" && $this->folder != "cal") {
							echo("list&folder=$key\" target=\"maillist\" ");
						}
						else {
							echo("showfolder&folder=$key\" ");
							echo($this->folder_selected($key));
						}
										
						echo(">");
						echo("<img src=\"graphics/$val[pic]\" alt=\"$val[name]\" title=\"$val[name]\" ");
						echo("style=\"vertical-align: middle;\" border=\"0\"></a></td>");
					}
					
				echo("</tr>\n");
			}
		echo("</table>");
	}

	function count_mails() {
		global $strings;
		
		$query = "SELECT COUNT(*) FROM mails WHERE user_id='$this->userid' AND folder='$this->folder'";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
			return;
		}
		$row = $this->db_tool->fetch_row($res);
		return $row[0];
	}

	function report_error($err_msg, $head) {
		$this->messagebox($err_msg, "ERROR", $head);
		exit;
	}

	function show_frame_list() {
		global $folder;
		
		if (isset($folder) && !empty($folder)) {
			$this->folder = $folder;
		}
		$this->frame_header();
		echo("<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr class=\"window\"><td>");
		$this->show_list_headers();					
		$this->list_mails();
		echo("</td></tr></table>");
		$this->frame_footer();	
	}
	

	function show_list_headers() {
		global $strings;
		
		echo("<form class=\"mail\" action=\"$GLOBALS[PHP_SELF]?action=selected\" method=\"post\">");
		echo("<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">");
		if ($this->config["mailview"] == FRAME_VIEW) {
			echo("<tr class=\"windowtitle\"><td>".$this->get_foldername()."</td></tr>");		
		}
		echo("<tr style=\"text-align: center; background-color: silver; color: inherit;\"><td width=\"100%\" align=\"left\">");
		echo("<input class=\"smallbutton\" type=\"image\" name=\"delete\" value=\"delete\" src=\"graphics/del.gif\" alt=\"$strings[l_DeleteMultiple]\" title=\"$strings[l_DeleteMultiple]\">");
		echo("<input class=\"smallbutton\" type=\"image\" name=\"add_addr\" value=\"add_addr\" src=\"graphics/add_addr.gif\" alt=\"$strings[l_AddAddr]\" title=\"$strings[l_AddAddr]\">");
		echo("<input class=\"smallbutton\" type=\"image\" name=\"block\" value=\"block\" src=\"graphics/stop.gif\" alt=\"$strings[l_Block]\" title=\"$strings[l_Block]\">");
		//echo("<select name=\"move_folder\" style=\"margin: 0, 0, 0, 0; padding: 0, 0, 0, 0; border: none;\"><option>$strings[l_MoveTo]</option><option>lajksdlkj</option></select>");
		echo("</td></tr></table>\n");
		echo("<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">");		
		echo("<tr style=\"text-align: center; background-color: silver; color: inherit;\">\n");
		echo("<th style=\"text-align: center; border: thin outset silver; width: 15px;\">\n&nbsp;</th>\n");
		echo("<th style=\"text-align: center; border: thin outset silver; width: 15px;\">\n<a href=\"$PHP_SELF?action=showfolder&folder=".$this->folder."&order=is_mime\" title=\"$strings[l_Order] $strings[l_Attachment]\"><img src=\"graphics/attachment_small.gif\" alt=\"$strings[l_Order] $strings[l_Attachment]\" align=\"middle\" border=\"0\"></a>\n</th>\n");
		echo("<th style=\"text-align: center; border: thin outset silver; width: 15px;\">\n<a href=\"$PHP_SELF?action=showfolder&folder=".$this->folder."&order=priority\" title=\"$strings[l_Order] $strings[l_Priority]\"><img src=\"graphics/priority.gif\" alt=\"$strings[l_Order] $strings[l_Priority]\" align=\"middle\" border=\"0\"></a></th>\n");
		echo("<th style=\"text-align: left; border: thin outset silver;\" width=\"33%\">");
		echo("<a style=\"font-weight: bolder;\" href=\"$PHP_SELF?action=showfolder&folder=".$this->folder."&order=");
		if ($this->folder == "outbox" || $this->folder == "sent") {
			echo("tos\" title=\"$strings[l_Order] $strings[l_From]\">".$strings[l_To]);
		}
		else {
			echo("froms\" title=\"$strings[l_Order] $strings[l_From]\">".$strings[l_From]);
		}

		if ($GLOBALS[order] == "froms" || $GLOBALS[order] == "tos") {
			if ($desc) {
				echo(" <img src=\"graphics/sort_desc.gif\" alt=\"\" border=\"0\" style=\"margin-left: 30px;\">");
			}
			else {
				echo(" <img src=\"graphics/sort_nodesc.gif\" alt=\"\" border=\"0\" style=\"margin-left: 30px;\">");			
			}
		}		
		echo("</a></th><th style=\"text-align: left; border: thin outset silver;\" width=\"40%\">\n");
		echo("<a style=\"font-weight: bolder;\" href=\"$PHP_SELF?action=showfolder&folder=".$this->folder."&order=subject\" title=\"$strings[l_Order] $strings[l_Subject]\">".$strings[l_Subject]."");
		if ($GLOBALS[order] == "subject") {
			if ($desc) {
				echo(" <img src=\"graphics/sort_desc.gif\" alt=\"\" border=\"0\" style=\"margin-left: 30px;\">");
			}
			else {
				echo(" <img src=\"graphics/sort_nodesc.gif\" alt=\"\" border=\"0\" style=\"margin-left: 30px;\">");			
			}
		}			
		echo("</a></th>\n");
		echo("<th style=\"text-align: left; border: thin outset silver;\"><a style=\"font-weight: bolder;\" href=\"$PHP_SELF?action=showfolder&folder=".$this->folder."&order=date\" title=\"$strings[l_Order] $strings[l_Date]\">".$strings[l_Date]);
		if ($GLOBALS[order] == "date") {
			if ($desc) {
				echo(" <img src=\"graphics/sort_desc.gif\" alt=\"\" border=\"0\" style=\"margin-left: 30px;\">");
			}
			else {
				echo(" <img src=\"graphics/sort_nodesc.gif\" alt=\"\" border=\"0\" style=\"margin-left: 30px;\">");			
			}
		}			
		echo("</a></th></tr>\n");		
		
	}
	
	function list_mails() {
		global $mail_id, $page, $strings, $action;
		
		if (empty($this->folder)) {
			$this->folder = "inbox";
		}
		
		if (isset($GLOBALS[order]) && empty($this->order)) {
			$desc = false;
			$order = $this->order;
		}
		else if (isset($this->order) && isset($GLOBALS[order])) {
			if (substr($this->order, -4) == "DESC") {
				$this->order = $GLOBALS[order];
				$desc = false;
			}
			else {
				$this->order = $GLOBALS[order]." DESC";
				$desc = true;
			}
			
			$order = $this->order;
			
			if ($GLOBALS[order] != "date") {
				// The second order options is always the date
				$order .= ", date DESC";
			}
				
		}
		
		// Make sure we have an order expression
		if (empty($order)) {
			$this->order = "date DESC";
			$order = $this->order;
		}
		
		
		
		while(1) {
			$pageno = $this->get_pageno() * $this->config["mails_per_page"];
			$mails = $this->config["mails_per_page"];

			$query = "SELECT id, tos, from_name as froms, subject, is_mime, date, was_read, priority FROM mails WHERE folder = '$this->folder' AND user_id = '$this->userid' ORDER BY $order";

			// Show all mails in FRAME view
			if ($this->config["mailview"] != FRAME_VIEW) {
				$query .= " LIMIT $pageno, $mails";
			}
			
			$res = $this->db_tool->db_query($query);

			if ($res === false) {
				$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
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
					echo("</table>\n");
					echo($strings["l_NoMails"]);
					echo(str_repeat("<br>", $this->config["mails_per_page"] - 1));
					return;
				}
			}

			break;
		}
						
		// Now fill in the mails
		while ($row = $this->db_tool->fetch_array($res)) {
			echo("<tr");
			// If the mail was not read, display it bold
			switch($row["was_read"]) {
				case MAIL_UNREAD:
					echo(" style=\"font-weight: bolder;\"");
					$img = "not_read_mail.gif";
					break;
				case MAIL_READ:
					$img = "read_mail.gif";
					break;
				case MAIL_REPLIED:
					$img = "replied_mail.gif";
					break;
				case MAIL_FORWARDED:
					$img = "forwarded_mail.gif";
					break;
			}
				
			echo(">\n");

			echo("<td style=\"width: 10px; text-align: center;\"><input type=\"checkbox\" name=\"del[]\" value=\"$row[id]\"></td>");
			echo("<td style=\"width: 20px; text-align: center;\">\n");
			if ($row[is_mime]) {
				echo("<img src=\"graphics/attachment_small.gif\" alt=\"$strings[l_Attachment]\" align=\"middle\" border=\"0\" style=\"vertical-align: middle;\">\n");				
			}
			echo("</td><td align=\"center\" style=\"vertical-align: middle\">");
			
			switch ($row["priority"]) {
				case LOWEST_PRIORITY:
					echo("<img src=\"graphics/lowest.gif\" alt=\"lowest\" title=\"$strings[l_LowestPriority]\">");			
					break;
				case LOW_PRIORITY:					
					echo("<img src=\"graphics/low.gif\" alt=\"low\" title=\"$strings[l_LowPriority]\">");	
					break;					
				case HIGH_PRIORITY:					
					echo("<img src=\"graphics/high.gif\" alt=\"high\" title=\"$strings[l_HighPriority]\">");	
					break;
				case HIGHEST_PRIORITY:					
					echo("<img src=\"graphics/highest.gif\" alt=\"highest\" title=\"$strings[l_HighestPriority]\">");	
					break;
				default:
					echo("&nbsp;");
					break;				
			}
						
			echo("</td><td>\n<img src=\"graphics/$img\" alt=\"\" style=\"float: left;\">");
			if ($this->config["mailview"] == IN_VIEW) {
				echo("<a href=\"$GLOBALS[PHP_SELF]?action=showfolder&folder=$this->folder&mail_id=$row[id]\"");
			}
			else if ($this->config["mailview"] == FRAME_VIEW) {
				echo("<a href=\"$GLOBALS[PHP_SELF]?action=mail_cont&mail_id=$row[id]\" target=\"mail\" ");			
			}
			else {
				echo("<a href=\"$GLOBALS[PHP_SELF]?action=showmail&mail_id=$row[id]\"");				
				if ($this->config["mailview"] == POPUP_VIEW) {
					echo(" target=\"_blank\" ");
				}
				
			}
			
			// Don't highlight a mail in FrameView
			if ($this->config["mailview"] != FRAME_VIEW) {
				$this->mail_selected($row[id]);
			}
			
			// If the mail was not read, display it bold (also the link)
			if ($row["was_read"] == MAIL_UNREAD) {
				echo(" style=\"font-weight: bolder;\"");
			}
			else {
				echo(" style=\"font-weight: normal;\"");
			}
			echo(">\n");

			$mailer = new mailer(0);

			if ($this->folder == "outbox" || $this->folder == "sent") {
				$out = htmlspecialchars($this->extract_disp_name($row["tos"]));
			}
			else {
				$out = htmlspecialchars($this->extract_disp_name($row["froms"]));
			}

			// Cut the name if it's too long
			if (strlen($out) > $this->config["subject_size"]) {
				$out = substr($out, 0, $this->config["subject_size"])."...";
			}
			echo($out."</a></td>");
			
			$subject = $mailer->charset_decode($row["subject"]);	
			// Look that the string isn't too long
			if (strlen($subject) > $this->config["subject_size"]) {
				$subject = substr($subject, 0, $this->config["subject_size"])."...";
			}				

			echo("<td>".htmlspecialchars($subject));
			$date = mktime(substr($row[date], 8, 2), substr($row[date], 10, 2), substr($row[date], 12, 2), substr($row[date], 4, 2), substr($row[date], 6, 2), substr($row[date], 0, 4));
			$formatted_date = strftime("%x %X", $date);
			echo("</td>\n<td>".$formatted_date."</td>");
			echo("</tr>\n");
		}
		echo("</table>\n");
		echo("</form>\n");
		// If we have less then $minlines mails, we fill the rest with line-breaks

		if ($this->config["mailview"] != FRAME_VIEW && $count < $this->config["mails_per_page"]) {
			echo(str_repeat("<br>", $this->config["mails_per_page"] - $count));
		}	
	}
	
	function frame_header() {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html>
<head>
<META NAME="description" CONTENT="mailcontent frame">
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">
<style>
	body {background-color: white; color: black;}
</style>
</head>
<body>
<?php		
	}
	
	function frame_footer() {
		echo("</body></html>");	
	}
	
	function show_folder_content() {
		if ($this->config["mailview"] == FRAME_VIEW) {
			$height = $this->config["mails_per_page"];
			echo("<iframe name=\"maillist\" src=\"$GLOBALS[PHP_SELF]?action=list\" width=\"100%\" style=\"height: ".$height."em;\" frameborder=\"0\" marginwidth=\"0\" marginheight=\"0\"></iframe>");
		}
		else {
			$this->show_list_headers();	
			$count = $this->list_mails();		
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
		$cont = $mail->strip_comment($cont, $comment);

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
		$this->show_mail_content();
	}

	function show_mail_content($mailer = 0) {
		global $strings, $action, $mail_id;

		if ($mailer == 0) {
			$mailer = $this->cur_mailer;
		}
		

		if ($this->cur_message_id == 0 || $this->cur_mailer == 0) {
			$content = "\r\n\r\n  ======== $strings[l_NoMsgSelected] ========";
			if ($action == "printmail" || $action == "showmail" || $action == "getattachment" || $action == "mail_cont") {
				$content = nl2br($content);
			}
			if ($this->config["mailview"] == FRAME_VIEW) {
				return $content;
			}
			
			echo($content);
			return;
		}
		$content = "";
		if ($action == "showsources") {
			if ($mail_id != "att") {
				$query = "SELECT mail FROM mails WHERE id='$mail_id' AND user_id='$this->userid'";
				$res = $this->db_tool->db_query($query);
				if ($res === false) {
					$content = "\r\n\r\n  ======== ERROR while trying to get sources! ========";
				}
				else {
					$row = $this->db_tool->fetch_row($res);
					$content = $row[0];
				}
			}
			else {
				$m = $this->att_mailer[$mail_id];
				$content = $m->headers->generate_str(true)."\r\n";
				$content .= $m->body;
			}
			
		}

		if (empty($content)) {
			$get_html = $this->config["mailview"] == FRAME_VIEW && $action == "mail_cont";
			$content = $mailer->get_text_body($get_html);
		}

		if ($this->config["mailview"] == FRAME_VIEW && $action == "mail_cont" || $action == "notification") {
			// We return the content, so that show_mail_frame can handle it correctly
			// This is needed if $content is a HTML mail
			return $content;
		}

		// If we are here, the mail has been read, we tell that the DB
		if ($action != "printmail" ) {
			$this->set_mail_read($this->cur_message_id);
		}		

		$content = stripslashes($content);

		if ($action == "printmail" || $action == "showmail" || $action == "getattachment" || $action == "mail_cont") {

			$content = nl2br($content);
			// Convert http:// entries to hyperlinks
			$this->make_clickable($content);
		}

		if ($action == "showsources") {
			Header("Content-Type: text/plain");
		}
		

		echo $content;
	}
	
	function split_html($content) {
		if (preg_match("|(.*<body.*>)(.*?)|Usi", $content, $matches)) {
			/*foreach($matches as $key => $val) {
				dbg(htmlspecialchars($key)." -> ".htmlspecialchars($val));
			}*/
			return array($matches[1], $matches[2]);
		}
		
		return array("", $content);
		
	}

	
	function show_mail_frame() {
		global $action;
		
		$this->set_cur();
		
		$content = $this->show_mail_content();
		
		if ($action == "showsources") {
			Header("Content-Type: text/plain");
			echo $content;
		}		
		if (is_array($content)) {
			// It's a HTML mail
			$content = $content[0];
			$this->handle_related_part("TEXT/HTML", $content, $this->cur_mailer);
			$split = $this->split_html($content);
			echo $split[0];
			$this->show_frame_head();
			echo $split[1];
			//echo $content;
		}
		else {
			$this->frame_header();
			$this->show_frame_head();
			echo("<span style=\"font-family: monospace\">");			
			$content = nl2br($content);

			// Convert http:// entries to hyperlinks
			$this->make_clickable($content);			
			
			echo $content;
			
			echo("</span>");
			$this->frame_footer();							
		}

		// If we are here, the mail has been read, we tell that the DB
		$this->set_mail_read($this->cur_message_id);
	}
	
	function show_frame_head() {
		//echo("<div ");
		echo("<table width=\"100%\" border=\"0\" cellpadding=\"4\" cellspacing=\"0\" class=\"windowtitle\" style=\"color: white; background-color: gray; font-weight: bold; font-family : Arial, Helvetica, sans-serif; margin-left: 0 !important;\">");
		echo("<tr><td style=\"color: white;\">\n");
		$this->show_head();
		echo("</td><td align=\"right\">\n");
		$this->show_attachments();
		echo("</td></tr>\n");
		$this->show_notification_request();
		echo("</table>\n");
		//echo("</div>");
	}
	
	
	function next_prev() {
		global $goto, $mail_id;

		// Make sure that the parameter is useful
		if (!isset($goto) || empty($goto)) {
			return;
		}
		
		if (!($goto == "prev" || $goto == "next")) {
			return;
		}

		// Get all mail ids
		$query = "SELECT id FROM mails WHERE user_id='$this->userid' ORDER BY $this->order";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
			exit;	
		}

		$old = 0;
		
		// Look for the current id
		while($row = $this->db_tool->fetch_row($res)) {

			if ($row[0] == $mail_id) {
				if ($goto == "next") {
					// Get the next entry.
					// This is the new mail_id
					$row = $this->db_tool->fetch_row($res);
					$mail_id = $row[0];
//					dbg("NEW MAIL_ID: $mail_id");
					return;
				}
				else if ($goto == "prev") {
					// The old entry is the new mail id
					// It it's null, we are at the top
					if ($old == 0) {
						return;
					}
					$mail_id = $old;
					return;
				}
			}
			$old = $row[0];
		}
		
		
		
	}
	
	function show_popup_mail($show_att_mail = false) {
		global $strings, $action, $mail_id;
		
		$this->next_prev();
		
		
		if ($show_att_mail === false) {
			$this->set_cur();
		}
		
		if (isset($this->att_mailer[$mail_id]) && !empty($this->att_mailer[$mail_id])) {
			$mailer = $this->att_mailer[$mail_id];
		}
		else {
			$mailer = $this->cur_mailer;
		}
				
		$style_sheet = $action == "printmail" ? "print.css" : "style.css";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">

<LINK REL="stylesheet" TYPE="text/css" HREF="<?php echo($style_sheet);?>">
<html>

<head>
<title><?php echo($mailer->charset_decode($mailer->subject()));?></title>
<body>
<?php

		if ($action == "showmail" || $action == "getattachment") {
			include("titlebar.inc.php");
			//echo("<table style=\"border: none; margin-bottom: 2em;\">");
			include("popup.toolbar.inc.php");
			echo("<tr class=\"window\" height=\"200%\"><td>");
		}
		
		// Display the headers
		echo("<div class=\"header\">");
		echo("<table cellspacing=\"0\">\n");
		echo("<tr><td width=\"15%\"><b>$strings[l_From]:</b></td><td>".htmlspecialchars(stripslashes($mailer->headers->get("From")))."</td></tr>");
		echo("<tr><td><b>$strings[l_To]:</b></td><td>".htmlspecialchars(stripslashes($mailer->headers->get("To")))."</td></tr>");
		$cc = $mailer->cc();
		if (!empty($cc)) {
			echo("<tr><td><b>Cc:</b></td>".htmlspecialchars($mailer->cc())."</td></tr>");
		}
		echo("<tr><td><b>$strings[l_Date]</b></td><td>".$mailer->headers->get("date")."</td></tr>");
		$subject = $mailer->charset_decode(stripslashes($mailer->subject()));
		//$subject = $mailer->headers->get_decoded("Subject");
		echo("<tr><td><b>$strings[l_Subject]:</b></td><td>".htmlspecialchars($subject)."</td></tr>");
		$priority = $mailer->headers->get("X-Priority");
		$priority = $priority[0];		
		if (!empty($priority) && $priority != NORMAL_PRIORITY) {
			echo("<tr><td><b>$strings[l_Priority]:</b></td><td>");
			if ($priority == HIGHEST_PRIORITY) {
				echo("<span style=\"background-color: #ff3333;\">$strings[l_HighestPriority]</span>");
			}
			if ($priority == HIGH_PRIORITY) {
				echo("<span style=\"background-color: #ff6666;\">$strings[l_HighPriority]</span>");
			}			
			else if ($priority == LOW_PRIORITY) {
				echo("<span style=\"background-color: #9999ff; color: white;\">$strings[l_LowPriority]</span>");
			}
			else if ($priority == LOWEST_PRIORITY) {
				echo("<span style=\"background-color: #4444ff; color: white;\">$strings[l_LowestPriority]</span>");
			}			
			echo("</td></tr>");
		}	
		if ($action == "printmail") {
			// If the mail should be printed we display the names of the attachments
			$bShown = false;
			$i = 1;
			while($mailer->extract_part($i)) {
				$type = $mailer->get_attachment_type($i);
				$name = $mailer->charset_decode($mailer->get_attachment_name($i));			
				echo("<tr><td><b>");
				if (!$bShown) {
					echo($strings["l_Attachment"].":");
					$bShown = true;
				}
				else {
					echo("&nbsp;");
				}
				echo("</b></td><td>$name &lt;$type&gt;</td></tr>");
				$i++;
			}
		}
		else {
			// Otherwise we show the links to the attachment
			if ($mailer->is_mime()) {
				echo("<tr><td><b>$strings[l_Attachment]:</b></td><td>");
			}
			if ($show_att_mail) {
				$this->show_attachments($mailer);
			}
			else {
				$this->show_attachments();
			}
			echo("</td></tr>");
		}

		$this->show_notification_request();
		echo("</table><br>\n");
		
		if ($show_att_mail) {
			echo $this->show_mail_content($mailer);
		}
		else {
			echo $this->show_mail_content();
		}
		
		if ($action == "showmail") {
			echo("</tr></td></table>");
		}
		echo("</div></body></html>");	
	}

	function make_clickable(&$content) {
		$content = preg_replace("|(\s+)(http://.*)(\s+)|U", "\\1<a href=\"\\2\" target=\"_blank\" style=\"width: auto; color: blue; text-decoration: underline;\">\\2</a>\\3", $content);
		$content = preg_replace("|(\s*)([a-zA-Z0-9.-_]*@[a-zA-Z0-9.-_]*)(\s+)|U", "\\1<a href=\"$GLOBALS[PHP_SELF]?action=newmail&addr=\\2\" target=\"_parent\" style=\"width: auto; color: blue; text-decoration: underline;\">\\2</a>\\3", $content);
	}
	

	function set_mail_read($mail_id) {
		$query = "UPDATE mails SET was_read='".MAIL_READ."' WHERE id='$mail_id' AND user_id='$this->userid' AND was_read='".MAIL_UNREAD."'";
		$this->db_tool->db_query($query);
		echo $this->db_tool->db_error();
	}

	function get_foldername() {
		return $this->folders["$this->folder"]["name"];
	}

	function pwd_check($name, $pwd) {
		global $strings;
		// Check if we were called with mailaddress and password to really login.

		if (!empty($name) && !empty($pwd)) {

			$res = $this->db_tool->db_query("SELECT name, pwd FROM users WHERE STRCMP(name, '$name') = 0");
			if ($res === false) {
				$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
				return;
			}
			if ($this->db_tool->affected_rows() == 0) {
				unset($name);
				unset($pwd);
				$this->show_in_window("login", $strings["l_Login"], $strings["l_LoginFail"]);
			}

			$row = $this->db_tool->fetch_array($res)
				or die (mysql_error());

			if ($row[pwd] != $pwd) {
				unset($name);
				unset($pwd);
				$this->show_in_window("login", $strings["l_Login"], $strings["l_LoginFail"]);
			}
			else {
				return 1;
			}
		}

		return 0;
	}

	function create_user() {
		global $name, $email, $pwd, $pwd2, $strings, $user_confirmation;

		if (!isset($name) && !isset($pwd) && !isset($pwd2)) {
			// Seems like new user is called for the first time without any info
			// Just show the form
			$this->show_in_window("newuser", $strings[l_NewUser]);
			exit;
		}

		// Check if the password and the password confirmation are tha same
		if ($pwd != $pwd2) {
			$this->show_in_window("newuser", $strings["l_PwdMismatch1"], $strings["l_PwdMismatch2"]);
			return;
		}

		// Don't allow empty passwords
		if (empty($pwd)) {
			$this->show_in_window("newuser", $strings["l_EmptyPwd1"], $strings["l_EmptyPwd1"]);
			return 0;
		}

		// Everything is fine. Now we insert the new user into the DB
		$activated = !$user_confirmation;
		$query = "INSERT INTO users (name, pwd, email, activated) VALUES ('$name', '$pwd', '$email', '$activated')";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$error = mysql_error();
			if (ereg("^Duplicate entry", $error)) {
				$this->show_in_window("newuser", $strings["l_UserExists1"], $strins["l_UserExists2"]." ".$name);
				return 0;
			}
			else {
				$this->report_error($error, $strings["l_DBError"]);
			}
		}
		$this->userid = $this->db_tool->insert_id();
		
		$query = "INSERT INTO conf (user_id, keep) VALUES ('$this->userid', 1)";
		
		$res = $this->db_tool->db_query($query);
		$this->load_config();
		$this->set_user($name);
		
		if ($user_confirmation) {
			$this->send_confirmation_mail($name, $email);
		}

		return ($this->userid > 0);
	}

	function send_confirmation_mail($name, $email) {
		global $strings;
		
		srand((double)microtime()*1000000);
		$confirm_code = md5(rand());
		$server = "http://".getenv("SERVER_NAME");
		$href  = $server.$GLOBALS[PHP_SELF];
		$href .= "?confirm_code=$confirm_code";

		// Save it in the database
		$query = "INSERT INTO confirmations (user_id, confirm_code) VALUES ('$this->userid', '$confirm_code')";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($strings["l_ConfFail"]."<br><br>".$this->db_tool->db_error(), $strings["l_DBError"]);
			return;
		}

		$mailer = new mailer($this->userid);
		$mailer->to($email);
		$mailer->subject($strings["l_ConfSubject"]);
		$mailer->from("webmaster@".getenv("SERVER_NAME"));		
		$mail_text = $strings["l_ConfMail"]." $server\r\n\r\n".$strings["l_ConfMail2"]."\r\n$href\r\n\r\n".$strings["l_ConfMail3"];
		$mailer->body = $mail_text;
		
		if (!$mailer->send()) {
			$this->report_error($strings["l_ConfMailFail"], $strings["l_MailFail"]);
			return;
		}
	}

	function check_confirmation($confirm_code) {
		global $strings;
		
		$query = "SELECT user_id FROM confirmations WHERE confirm_code='$confirm_code'";
		$res = $this->db_tool->db_query($query);
		$row = $this->db_tool->fetch_array($res);
		$ret = $this->db_tool->affected_rows($res);
		if ($res != 0 && $ret != 0) {
			$query = "UPDATE users SET activated=1 WHERE id='$row[user_id]'";
			$ret &= $this->db_tool->db_query($query);
			
			if ($ret != 0) {
				$query = "DELETE FROM confirmations WHERE confirm_code='$confirm_code'";
				$ret = $this->db_tool->db_query($query);
			}

			if ($ret === false) {
				$this->report_error($strings["l_AccActivation2"], $strings["l_DBError"]);
				exit;
			}			
		}
		
		return $ret;
	}

	function is_activated($name) {
		$id = $this->get_userid($name);
		$query = "SELECT activated FROM users WHERE id='$id'";

		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($strings["l_ActStatus"], $strings["l_DBError"]);
			exit;
		}
		$row = $this->db_tool->fetch_array($res);
		
		return $row["activated"];
		
	}

	function messagebox($msgtext, $msgtitle = "", $msghead = "", $msgstatus = "") {
		$this->show_in_window("report", $msgtitle, $msgstatus, $msgtext, $msghead);
	}

	function store_mail($backup = false) {
		global $notification, $to_x, $cc_x, $bcc_x, $priority, $tos, $ccs, $bccs, $subject, $body, $replyaccount, $submit, $attachment, $attachment_name, $attachment_size, $attachment_type, $strings;

		$body = stripslashes($body);

		if (isset($to_x) || isset($cc_x) || isset($bcc_x)) {
			$backup = true;
		}

		$query = "SELECT fromname, replyto FROM accounts WHERE id = '$replyaccount' AND user_id='$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_AccInfoFail"]);
			exit;
		}
		$row = $this->db_tool->fetch_array($res);

		if ($submit == $strings["l_Send"]) {
			$folder = "outbox";
		}
		else if ($submit == $strings["l_Store"]) {
//			dbg($submit);
			$folder = "drafts";
		}
		else {
			$folder = "inbox";
		}
		$reply = "\"".$row["fromname"]."\" <".$row["replyto"].">";
		$mailer = new mailer($this->userid, $replyaccount);
		$mailer->create($tos, $reply, $subject, $body);
		if (isset($ccs) && !empty($ccs)) {
			$mailer->cc($ccs);
		}
		if (isset($bccs) && !empty($bccs)) {
			$mailer->bcc($bccs);
		}	
		if (isset($notification) && $notification == "on") {
			$mailer->headers->put("Disposition-Notification-To",  $reply);
		}
		
		if (isset($priority) && $priority != NORMAL_PRIORITY) {
			$mailer->headers->put("X-Priority", $priority);
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

			$this->show_in_window("choose", "$GLOBALS[field]: ".$strings["l_ChooseRec"]);			
			exit;
		}
		else {
			// Does the user want to send it directly?
			if ($submit == $strings["l_Send"] && $this->config["send_direct"] == 1) {
				if ($mailer->send()) {
					// The mail could be sent. Store it in the sent folder
					$folder = "sent";
				}
				else if ($submit == $strings["l_Send"]) {
					// The mail could not be sent, keep it in the outbox folder
					$folder = "outbox";
				}
				
			}
			if ($mailer->store($folder)) {
				return $strings["l_FolderMove"].": ".$this->folders[$folder][name];
			}
			else {
				return $strings["l_FolderMoveFail"].": ".$this->folders[$folder][name];
			}
		}
	}
	
	function pic_from_type ($type) {
		switch (strtolower($type)) {
			case "image/gif":
			case "image/jpeg":
			case "image/png":			
			case "image/tiff":			
			case "image/pjpeg":
			case "image/bmp":
			case "image/x-icon":
				$type_pic = "picture.gif";
				break;
			case "video/mpeg":
			case "video/quicktime":
				$type_pic = "movie.gif";
				break;
			case "application/x-zip-compressed":
			case "application/x-gzip-compressed":				
			case "application/zip":			
				$type_pic = "zip.gif";
				break;
			case "message/disposition-notification":
			case "text/plain":
				$type_pic = "text.gif";
				break;
			case "application/rtf":
				$type_pic = "doc.gif";
				break;
			case "text/html":
				$type_pic = "html.gif";
				break;
			case "application/pdf":
				$type_pic = "pdf.gif";
				break;
			case "application/x-msdownload":
				$type_pic = "app.gif";
				break;
			case "message/rfc822":
				$type_pic = "email.gif";
				break;
			case "audio/wav":
			case "autio/mp3":
				$type_pic = "sound.gif";
				break;
			default:
				$type_pic = "unknown.gif";	
		}
		
		return $type_pic;
	}
	
	function pic_from_ending($ending) {
		switch(strtolower($ending)) {
			case "zip":
			case "gzip":
			case "bz2":
			case "arj":
			case "ace":
			case "rar":
			case "zoo":
			case "uha":				
				$type_pic = "zip.gif";
				break;
			case "doc":
			case "sdw":
			case "rtf":
				$type_pic = "doc.gif";
				break;
			case "txt":
			case "diz":
				$type_pic = "text.gif";
				break;						
			case "html":
			case "htm":
			case "php":
			case "asp":
			case "phtml":
				$type_pic = "html.gif";
				break;
			case "avi":
			case "mpg":
			case "mpeg":
			case "asf":
			case "wmv":
			case "mov":
				$type_pic = "movie.gif";
				break;	
			case "exe":
			case "vbs":
			case "sh":
			case "bat":
				$type_pic = "app.gif";
				break;
			case "mp3":
			case "wav":
			case "snd":
			case "au":
				$type_pic = "sound.gif";
				break;
			default:
				$type_pic = "unknown.gif";						
		}
		
		return $type_pic;
	}
		
	
	function show_attachments($of_part = 0, $att_path = 0, $level = 1) {
		global $mail_id;
		
		if (!isset($mail_id)) {
			$mail_id = $this->cur_message_id;
		}

		if ($this->cur_message_id == 0) {
			return;
		}
		
		if ($of_part != 0) {
			$mailer = $of_part;
		}
		else if ($this->cur_mailer == 0 || $this->cur_mailer->id != $GLOBALS[mail_id]) {		
			$this->cur_mailer = new mailer($this->userid);
			$this->cur_mailer->load($this->cur_message_id);
			$mailer = $this->cur_mailer;
		}
		else {
			$mailer = $this->cur_mailer;
		}
		
		$i = 1;
				
		if ($op_part == 0) {
			$c_type = $mailer->get_header("Content-Type");
			$type = strtoupper($c_type["CONTENT-TYPE"]);
//			dbg($type);
			$is_multipart = (substr($type, 0, strlen("MULTIPART/")) == "MULTIPART/");
			if (!empty($type) && $type != "TEXT/PLAIN" && !$is_multipart) {
//				dbg("NOT TEXT/PLAIN");
				// Grrr. A mail which is only an attachment :-(
				$mailer->mimeparts[1] = $mailer;
				$fake = true;
			}
		}
		
		if ($att_path == 0) {
			unset($att_path);
			$att_path = array();
			$att_path[1] = 1;
		}
		

		
		//echo "BODY:<br><br>".nl2br($mailer->body)."<br><br>";
		while($fake === true || $mailer->extract_part($i)) {
	
			if (!$fake && sizeof($mailer->mimeparts[$i]->mimeparts) > 0) {
				$att_path[$level] = $i;
				$this->show_attachments($mailer->mimeparts[$i], $att_path, $level + 1);
				// Don't show an icon for it, since it's a multipart part
				echo('<img src="graphics/space.gif" alt="|">');
				$i++;
				continue;
			}
			
			$type = $mailer->get_attachment_type($i);
			$type_pic = $this->pic_from_type($type);

			$att_name = $mailer->get_attachment_name($i);
			// Try to get the file-extension to determint the type of the file
			// if it's only an "octet-stream"
			if ($type == "application/octet-stream") {
				$ending = substr($att_name, strrpos($att_name, ".") + 1, strlen($att_name));
				$type_pic = $this->pic_from_ending($ending);
			}
			if (!empty($att_name)) {
				$att_name .= "\r\n";
			}
			$att_name .= "$type";
			
			$att_path[$level] = $i;
			if ($fake) {
				$att_path[$level] = 0;		
			}
			
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=getattachment&mail_id=");
			
			echo("$GLOBALS[mail_id]");
			
			foreach($att_path as $key => $att_pid) {
				echo("&att$key=$att_pid");
			}
			
			echo("\" title=\"$att_name\"");
			// Open attachments in a new window in IN_VIEW AND POPUP_VIEW
			if ($this->config["mailview"] != SEP_VIEW) {
				echo(" target=\"_blank\"");
			}
			echo(" style=\"width: auto;\"><img src=\"graphics/$type_pic\" border=\"0\" alt=\"$att_name\" style=\"vertical-align: top; margin: 0, 0, 0, 0;\"></a>\n");
			$i++;
			
			$fake = false;			
		}
	}
	
	function show_head() {
		global $strings;
		
		if ($this->cur_message_id == 0) {
			return;
		}
		if ($this->cur_mailer == 0 || $this->cur_mailer->id != $GLOBALS[mail_id]) {
			$this->cur_mailer = new mailer($this->userid);
			$this->cur_mailer->load($this->cur_message_id);
		}

		echo("<b>$strings[l_From]:</b> ".htmlspecialchars(stripslashes($this->cur_mailer->from()))."&nbsp;<b>$strings[l_To]:</b> ".htmlspecialchars(stripslashes($this->cur_mailer->to())));

		$cc = $this->cur_mailer->cc();
		if (!empty($cc)) {
			echo("&nbsp;<b>Cc:</b> ".htmlspecialchars($this->cur_mailer->cc()));
		}
		$subject = $this->cur_mailer->charset_decode(stripslashes($this->cur_mailer->subject()));
		//$subject = $this->cur_mailer->headers->get_decoded("Subject");
		echo("<br><b>$strings[l_Subject]:</b> ".htmlspecialchars($subject));
	}
	
	function get_attachment() {
		global $strings, $action, $mail_id;
		
		$this->set_cur();

		if (isset($this->att_mailer[$mail_id]) && !empty($this->att_mailer[$mail_id])) {
			$mailer = $this->att_mailer[$mail_id];
		}
		else {
			$mailer = $this->cur_mailer;
		}

		$i = 1;
		while(isset($GLOBALS["att$i"])) {
			$att_path[$i] = $GLOBALS["att$i"];
			$i++;
		}

		$data = $mailer->get_attachment_data($att_path);

		if ($data === false) {
			$this->report_error($strings["l_AttFail"], $strings["l_Error"]);
			exit;
		}
		
		$part = $mailer->get_attachment($att_path);
		
		$disp = "Content-Disposition: ".str_replace("\r\n\t", " ", $part->headers->get("Content-Disposition"));
		$c_type = $part->get_header("Content-Type");
		$type = $c_type["CONTENT-TYPE"];

		if (strtoupper($type) == "MESSAGE/DISPOSITION-NOTIFICATION") {
			// Show this as text
			$type = "Content-Type: text/plain";
		}
		else {
			$type = "Content-Type: ".str_replace("\r\n\t", " ", $part->headers->get("Content-Type"));
		}
		
		$disp = str_replace("attachment;", "inline;", $disp);

		// Check for multipart/related inbound elements
		$this->handle_related_part(strtoupper($c_type["CONTENT-TYPE"]), $data, $mailer);


		// If the attachment is a message, we open it like a mail
		if (preg_match("|message/rfc822|i", $type)) {
			// Generate a fake mail_id for the attached mail
			/*srand((double)microtime()*1000000);
			$rand = md5(rand());			
			$mail_id = substr($rand, 0, 4)."att";	*/
			$mail_id = $mail_id."att";
			if (!isset($this->att_mailer[$mail_id])) {
				$m = new mailer($this->userid);
				$m->from_text($data);
				$this->att_mailer[$mail_id] = $m;
//				dbg("NEW ATT_MAILER[ $mail_id ]");
			}
			$this->show_popup_mail(true);
			return;
		}

		/*echo "DISP: $disp<br>";
		echo "TYPE: $type<br>";*/
		
		Header($disp);
		Header($type);

		echo($data);

		exit;
	}

	function handle_related_part ($type, &$data, $mailer) {

		if ($type == "TEXT/HTML" && preg_match_all("/cid:(.[^\">]*)/", $data, $matches)) {

			foreach($matches[1] as $cid) {

				// Create for each inbound element a temporary file
				
				// Get the data of the object
				$cid_data = base64_decode($mailer->get_cid_body($cid));
				$cid_type = $mailer->get_cid_type($cid);
				
				// Create a uniqe filename
				srand((double)microtime()*1000000);
				$rand = md5(rand());
				
				$tmp_name = "tmp/tmp_".substr($rand, 0, 5);

				// Add a suitalbe extension if possible
				if (preg_match("|image/jpeg|", $cid_type)) {
					$tmp_name .= ".jpg";
				}
				else if (preg_match("|image/gif|", $cid_type)) {
					$tmp_name .= ".gif";
				}
				else if (preg_match("|image/png|", $cid_type)) {
					$tmp_name .= ".png";
				}				
				else {
					$tmp_name .= ".tmp";
				}

				$this->tmp_files[] = $tmp_name;

				// Create the temporary file with the object data
				$fp = @fopen($tmp_name, "w");
				@fwrite($fp, $cid_data);
				
				// Replace the occurence of the object reference with the location
				// of the newly created temp-file
				$data = str_replace("cid:$cid", $tmp_name, $data);
				
				// Outlook express sometimes ads a <BASE file:path> tag
				// What for? It messes everything up.
				// We just remove it...
				$data = preg_replace("|(<BASE .*>)|Us", "", $data);

			}
		}
	}
	

	function delete_mail() {
		global $ack, $strings;
		if ($this->cur_message_id == 0) {
			$this->show_form("main", $strings["l_Select"]);
			return 0;
		}

		$del_msg = "";

		// When the mail is in the bin folder we ask the user for a confirmation if the wants to delete
		//
		if ($this->folder == "bin") {
			// If $ack is not set, we pop-up the confirmation window
			if (!isset($ack) || empty($ack) ) {
				$this->del = $this->cur_message_id;
				$this->show_ack($strings["l_DelMail"], $strings["l_DelMailQuest"]);
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
						$del_msg = $strings["l_ServerDel"];
					}
					else {
						$del_msg = $strings["l_ServerDelFail"];
					}
				}
				$this->del = "";
			}
		}
		else {
			if ($this->move_to_bin($this->cur_message_id)) {
				$del_msg = $strings["l_FolderMove"].": ".$strings["l_RecycleBin"];
			}
			else {
				$del_msg = $strings["l_FolderMoveFail"].": ".$strings["l_RecycleBin"];
			}
			
			// The selected mail is not delete. Cleare the "reference" to it
			$this->cur_message_id = 0;
			$this->cur_mailer = 0;
		}

		if ($this->config["mailview"] == FRAME_VIEW) {
			$this->show_frame_list();
		}
		else {
			$this->show_form("main", $del_msg);
		}
	}

	function delete_mails($pass_del = 0) {
		global $ack, $delete, $del, $strings;
		
		if (is_array($del)) {
			reset($del);
		}
		
		if ($pass_del != 0) {
			$this->del = $pass_del;
		}
		else if (isset($del) && !empty($del)) {
			$this->del = $del;
		}

		// Decide if the mail is just moved to the bin or if it is really deleted
		if ($this->folder == "bin") {
			// If $ack is not set, we pop-up the confirmation window
			if (!isset($ack) || empty($ack) ) {
				$this->show_ack($strings["l_DelMails"], $strings["l_DelMailsQuest"]);
				return;
			}
			else if($ack == "No") {
				$this->del = "";
				// The user said no :-(
				$del_msg = $strings["l_DelCancel"];
			}
			else {
				$del = $this->del;
				if ((count($this->del) > 0)) {
					$all_succ = $this->trash_mail($del);

					if ($all_succ) {
						$del_msg = $strings["l_AllDel"];
					}
					else {
						$del_msg = $strings["l_AllDelFail"];
					}
				}
				$this->del = "";
			}
		}
		else if (count($this->del) > 0) {
			$all_succ = $this->move_to_bin($this->del);
			
			if ($all_succ) {
				$del_msg = $strings["l_FolderMove"].": ".$strings["l_RecycleBin"];
			}
			else {
				$del_msg = $strings["l_FolderMoveFail"].": ".$strings["l_RecycleBin"];
			}

		}

		if ($this->config["mailview"] == FRAME_VIEW) {
			$this->show_frame_list();
		}
		else {
			$this->show_form("main", $del_msg);
		}
	}
	
	function add_sel_addr($block = false) {
		global $del, $strings;
		
		if (is_array($del)) {
			$query = "SELECT froms FROM mails WHERE id IN (";
			foreach($del as $in) {
				$query .= $in.", ";
			}
			// Remove the last comma
			$query = substr($query, 0, strlen($query) - 2);
			$query .= ") AND user_id='$this->userid'";
		}
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
		}
		
		while ($row = $this->db_tool->fetch_array($res)) {
			// Add the addresses to the DB
			$this->add_address($row[froms], $block);
		}
	}
	
	function delete_blocked() {
		// Delete all mails that contain blocked senders
		$query = "SELECT email FROM addresses WHERE user_id='$this->userid' AND blocked=1";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);			
		}
		
		// Build the WHERE part for th query
		while ($row = $this->db_tool->fetch_array($res)) {
			$where .= " froms LIKE '%$row[email]%' OR";
		}
		if (!empty($where)) {
			// Remove the last OR
			$where = substr($where, 0, strlen($where) - 3);
		}
		
		$query = "SELECT id FROM mails WHERE user_id='$this->userid' AND folder='$this->folder' AND ".$where;

		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);			
		}

		// Now delete these mails
		while($row = $this->db_tool->fetch_array($res)) {
			$del[] = $row[id];
		}

		$this->delete_mails($del);		
	}
		
	function delete_all() {
		// We build an array with all mail ids and pass that to $this->delete_mails()
		$query = "SELECT id FROM mails WHERE user_id='$this->userid' AND folder='$this->folder'";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_AllDelFail"]);
			exit;
		}
		
		while($row = $this->db_tool->fetch_array($res)) {
			$del[] = $row[id];
		}

		$this->delete_mails($del);
	}

	function move_to_bin($id) {
		if (is_array($id)) {
			$query = "UPDATE mails SET folder='bin' WHERE id IN (";
			foreach($id as $in) {
				$query .= $in.", ";
			}
			// Remove the last comma
			$query = substr($query, 0, strlen($query) - 2);
			$query .= ") AND user_id='$this->userid'";
		}
		else {
			// Move the mail to the bin folder
			$query = "UPDATE mails SET folder='bin' WHERE id='$id' AND user_id='$this->userid'";
		}
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			return false;
		}
		return true;
	}

	function trash_mail($id) {
		// If it's just a singel ID, we create an array of size 1
		if (!is_array($id)) {
			$tmp = $id;
			$id = array();
			$id[] = $tmp;	
		}
		// Get the account information about the mails
		$query = "SELECT accounts.*, mails.uidl AS uidl FROM accounts LEFT JOIN mails ON mails.acc_id = accounts.id WHERE mails.id IN (";
		foreach($id as $in) {
			$query .= $in.", ";
		}
		// Remove the last comma
		$query = substr($query, 0, strlen($query) - 2);
		$query .= ") AND mails.user_id = '$this->userid' ORDER BY accounts.id";
		$res = $this->db_tool->db_query($query);

		$row = 0;
		
		if ($res != 0) {
			$cur_account = 0;
			$pop_login = false;
			
			while($this->config["server_del"] == 1 && $row = $this->db_tool->fetch_array($res)) {
				// When we come to a different account and if the settings say to delete the mail,
				// we connect to this server

				if ($row[id] != $cur_account) {
					// Close the connection to the previous server
					if (is_object($pop3) && $pop_login) {
						$pop3->quit();
					}
					
					$pop3 = new pop3("$row[popserver]");
					$pop_login = true;

					$count = $pop3->pop_login($row["poplogin"], $row["poppwd"]);
					$cur_account = $row[id];
					// could we get a connection and are there mails on the server?
					if ($count < 0 || $count === false) {
						// No mails there... So we can't delete anything
						$pop3->quit();
						$pop_login = false;
					}
					
					// Get the mail uidls on the POP-server
					$server_uidls = $pop3->uidl(0, $own_uidl);
				}
				// Are we logged in?
				if ($pop_login) {
					// Get the uidls

					$del_id = 0;
					// Get the mail ID that corresponds the X-UIDL
					foreach($server_uidls as $sid => $uidl) {
						if ($uidl == $row[uidl]) {
							$del_id = $sid;
							break;
						}
					}
					
					// We got the mail id
					if ($del_id != 0) {
						// Now, delete the mail
						$pop3->delete($del_id);
					}
				}				
			}
		}
		
		// Close the connection to the previous server
		if (is_object($pop3) && $pop_login) {
			$pop3->quit();
		}
		
		// Delete the mail from the database
		$query = "DELETE FROM mails WHERE id IN (";
		foreach($id as $in) {
			$query .= $in.", ";
		}
		// Remove the last comma
		$query = substr($query, 0, strlen($query) - 2);		
		$query .= ") AND user_id = '$this->userid' AND folder='bin'";

		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
			return false;
		}
		
		$this->cur_message_id = 0;
		$this->cur_mailer = 0;

		return $this->db_tool->affected_rows() > 0;
	}

	function show_recipients() {
		global $strings;
		
		$query = "SELECT id, email, name FROM addresses WHERE user_id='$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
			return;
		}
		echo('<table width="100%" align="center" border="1">');
		echo('<tr style="text-align: left;"><th>&nbsp;</th><th>'.$strings[l_Name].'</th><th>E-Mail</th></tr>');
		while($row = $this->db_tool->fetch_array($res)) {
			echo("<tr>\n<td>\n");
			echo('<input type="checkbox" name="rec[]" value="'.$row[id].'"></td>');
			echo("<td>".htmlspecialchars(stripslashes($row[name]))."</td><td>");
			echo(htmlspecialchars(stripslashes($row[email]))."</td></tr>\n");
		}
		echo('</table>');
	}

	// List all mails in the current folder
	function show_del_mails() {
		global $strings;
		
		$decoder = new mimepart;
		$query = "SELECT id, tos, froms, subject, date FROM mails WHERE folder='$this->folder' AND user_id='$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), "Could not fetch mail to delete");
			return;
		}
		echo('<table width="100%" align="center" border="1">');
		echo('<tr style="text-align: left;"><th>&nbsp;</th><th>'.$strings[l_To].'</th><th>'.$strings[l_From].'</th><th>'.$strings[l_Subject].'</th></tr>');
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
		global $what, $show, $strings;

		if (!isset($what) || empty($what)) {
			$this->show($this->folder);
		}
		if ($what == "sendonly") {
			$this->send_outbox();
		}
		else if ($what == "sendandreceive") {
			$msg = $this->send_outbox();
			$this->get_mails();
			/*$this->show("sent", $msg);
			return;*/
		}
		else if ($what == "getall") {
			$this->get_mails();
		}
		else {
			// Only get the mails from a specified account
			$this->get_mails($what);
		}
		if ($show == "no") {
			Header("Location: $GLOBALS[PHP_SELF]?action=checkrep");
			exit;
		}
		if ($this->config["mailview"] == FRAME_VIEW) {
			$this->show_frame_list();			
		}
		else {
			$this->show($this->folder, $msg);
		}
	}

	function check_repetitive() {
		global $strings;
		
		$interval = $this->config["check_int"] * 60;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<META HTTP-EQUIV="Refresh" content="<?php echo($interval); ?>;URL=<?php echo($GLOBALS[PHP_SELF]."?action=sendandreceive&what=getall&show=no");?>">
<html>
<head>
<title><?php echo($strings["l_MinWndTitle"]);?></title>
<body>
<?php
	echo($strings["l_MinWnd"]."<br><br>");
	echo($strings["l_MinWndInfo"].": ".$this->config["check_int"]." ".$strings["l_Minutes"]);
?>
</body>
</html>
<?php
		exit;	
	}

	function get_mails($account = "") {
		global $strings;
		
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
		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
			return;
		}

		while($row = $this->db_tool->fetch_array($res)) {
			$pop3 = new pop3("$row[popserver]");
			$count = $pop3->pop_login($row["poplogin"], $row["poppwd"]);
			if ($count < 0 || $count === false) {
				$error_head = "POP3 $strings[l_Error]<br>$strings[l_Account] $row[name]";
				$error_msg = $pop3->ERROR;
				$this->report_error($error_msg, $error_head);
				continue;
			}

			// Get the UIDLs from the server
			$server_uidls = $pop3->uidl(0, $own_uidl);
			
			array_shift($server_uidls);		
			
			/*echo("<h3>SERVER_UIDLS ARRAY</h3>");
			foreach($server_uidls as $key => $val) {
				echo("$key -> $val<br>");
			}	
			echo("<h3>UIDLS ARRAY</h3>");
			foreach($uidls as $key => $val) {
				echo("$key -> $val<br>");
			}*/
						
			// Get the difference between the UIDLS on the server and in the DB
			$get = array_diff($server_uidls, $uidls);			
			
			/*echo("<h3>GET ARRAY</h3>");

			foreach($get as $key => $val) {
				echo("$key -> $val<br>");
			}*/
			
			$mailer = new mailer($this->userid, $row[id]);
			reset($get);

			foreach($get as $num => $uid) {
				$get_mail = $num + 1;

				$mail = $pop3->get_text($get_mail);

				if(empty($mail)) {
					continue;
				}
				
				$store_folder = "inbox";
				
				// Do we have blocked senders?
				$query = "SELECT count(*) as counter FROM addresses WHERE blocked=1 AND user_id='$this->userid'";
				$block_res = $this->db_tool->db_query($query);			
				if ($this->db_tool->affected_rows($block_res) > 0) {
					// Yes, the user defined blocked senders					
					// Check if the mail is form a blocked sender
					// This slows things down, because it has to analy
					$from = $mailer->quick_from($mail);

					if (!empty($from)) {
						$query = "SELECT email FROM addresses WHERE blocked=1 AND user_id='$this->userid' AND email='$from'";
						$block_res = $this->db_tool->db_query($query);
						if ($this->db_tool->affected_rows($block_res) > 0) {
							// This sender is blocked!
							// Store it in the bin folder;
							$store_folder = "bin";
						}
						
					}
				}
								
				// Try to store the mail in the DB
				if (($insert_id = $mailer->store($store_folder, $mail, $server_uidls[$num])) == 0) {
					$this->report_error($mailer->db_tool->db_error(), $strings["l_MailStoreFail"]);
					exit;
				}

				if ($own_uidl) {
					// If we use our own calculated uidls, we have to store it in the DB, too
					$query = "UPDATE mails SET uidl='".$server_uidls[$num]."' WHERE user_id = '$this->userid' AND id = '$insert_id'";
					$update_res = $this->db_tool->db_query($query);
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

	function add_address($mailer, $block = false) {
		// Get the from name from the mail
		if (is_object($mailer)) {
			$from = $mailer->headers->get("From");
		}
		else {
			$from = $mailer;
			$mailer = new mailer($this->userid);
		}
		
		$add = $mailer->get_address($from);
		
		if (!empty($add["address"])) {
			$name = addslashes($add[name]);
			$addr = addslashes($add[address]);
			// Let's see if this entry already exists
			$query = "SELECT name, email FROM addresses WHERE name='$name' AND email='$addr' AND user_id='$this->userid'";
			$res = $this->db_tool->db_query($query);
			if ($this->db_tool->affected_rows() == 0) {
				// Add the new entry
				$GLOBALS["name"] = $add["name"];
				$GLOBALS["email"] = $add["address"];
				$GLOBALS["blocked"] = $block ? 1: 0;
				$this->store_address();
			}
			
			if ($block) {
				// Mark any other entries with the same e-mail address as blocked, too
				// This is needed if an entry which should be blocked already exists
				$query = "UPDATE addresses SET blocked=1 WHERE user_id='$this->userid' AND email='$addr'";
				$res = $this->db_tool->db_query($query);				
			}
			
		}
		
	}

	function delete_user() {
		global $ack, $strings;
		
		// If $ack is not set, we pop-up the confirmation window
		if (!isset($ack) || empty($ack) ) {
			$this->show_ack($strings["l_DelUser"], $strings["l_DelUserQuest"]);
			return;
		}
		else if($ack == "No") {
			// The user said no :-(
			$msg = $strings["l_DelCancel"];
		}
		else {
			// We remove the user...
			$query = "DELETE FROM mails WHERE user_id='$this->userid'";
			$res = $this->db_tool->db_query($query);
			if ($res === false) {
				$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
				return false;
			}
			
			// We remove the user...
			$query = "DELETE FROM conf WHERE user_id='$this->userid'";
			$res = $this->db_tool->db_query($query);
			if ($res === false) {
				$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
				return false;
			}
			
			// We remove the user...
			$query = "DELETE FROM accounts WHERE user_id='$this->userid'";
			$res = $this->db_tool->db_query($query);
			if ($res === false) {
				$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
				return false;
			}
			
			// We remove the user...
			$query = "DELETE FROM addresses WHERE user_id='$this->userid'";
			$res = $this->db_tool->db_query($query);
			if ($res === false) {
				$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
				return false;
			}
					
			$query = "DELETE FROM users WHERE id='$this->userid'";
			$res = $this->db_tool->db_query($query);
			if ($res === false) {
				$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
				return false;
			}
			
			session_destroy();
			$this->messagebox($strings["l_UserDeleted2"], $strings["l_UserDeleted1"], $strings["l_UserDeleted1"]);
			exit;
		}	
	}
	

	function show_config() {
		global $strings;
		
		echo($strings["l_Language"].": <select name=\"language\">");
		$dirhandle = opendir("./lang");
		while ($filename = readdir($dirhandle)) {
			if (preg_match("/^lang\.(.*)\.inc\.php$/", $filename, $match)) {
				echo("<option value=\"$match[1]\"");
				if ($match[1] == $this->config["language"]) {
					echo(" selected");
				}
				echo(">$match[1]</option>");
			}
		}		
		closedir($dirhandle);
		
		echo("</select>\n<br><br><hr>");
		echo("<center><input type=\"submit\" name=\"changepwd\" value=\"".$strings["l_ChangePwd"]."\">&nbsp;");
		echo("<input type=\"submit\" name=\"deluser\" value=\"".$strings["l_DelUser"]."\"></center>");
		echo("<br><fieldset>");
	
			echo('<input type="checkbox" name="keep" value="1"');
			if ($this->config["keep"] == 1) {
				echo(' checked');
			}
			echo("> ".$strings["l_Keep"]."<br>");
			
			echo('<input type="checkbox" name="server_del" value="1"');
			if ($this->config["server_del"]) {
				echo(' checked');
			}		
			echo("> ".$strings["l_RemoveDel"]."<br>");
			
			echo('<input type="checkbox" name="auto_check" value="1"');
			if ($this->config["auto_check"]) {
				echo(' checked');
			}			
			echo("> ".$strings["l_CheckMails"]."<br>");
			
			echo('<input type="checkbox" name="send_direct" value="1"');		
			if ($this->config[send_direct]) {
				echo(' checked');
			}			
			echo("> ".$strings["l_SendDirect"]."<br>");		
	
			echo('<input type="checkbox" name="add_address" value="1"');		
			if ($this->config[add_address]) {
				echo(' checked');
			}			
			echo("> ".$strings["l_AddAddress"]."<br><hr>");
			echo($strings["l_CheckRep"].' <input name="check_int" value="'.$this->config["check_int"].'" size="2"> '.$strings["l_Minutes"].'<br><br>');
			
			echo("</fieldset>\n<br><fieldset><legend><b>".$strings["l_View"]."</b></legend>\n");
			echo('<table cellspacing="2" cellpadding="2" width="100%">');	
	
			echo("\n<tr><td style=\"width: 35%;\"><input type=\"radio\" name=\"view\" value=\"".FULL_VIEW."\"");
			if ($this->config["view"] == FULL_VIEW) {
				echo(" checked");
			}
			echo("> $strings[l_FullView]</td><td style=\"width: 65%;\">\n");
			echo("\n<input type=\"radio\" name=\"mailview\" value=\"".IN_VIEW."\"");
			if ($this->config["mailview"] == IN_VIEW) {
				echo(" checked");
			}		
			echo("> $strings[l_InView]</td></tr><tr>\n");		
	
			echo("<td><input type=\"radio\" name=\"view\" value=\"".MID_VIEW."\"");
			if ($this->config["view"] == MID_VIEW) {
				echo(" checked");
			}		
			echo("> $strings[l_MidView]</td><td>\n");
			
			echo("\n<input type=\"radio\" name=\"mailview\" value=\"".SEP_VIEW."\"");
			if ($this->config["mailview"] == SEP_VIEW) {
				echo(" checked");
			}		
			echo("> $strings[l_SepView]</td></tr><tr>\n");
			
			echo("<td><input type=\"radio\" name=\"view\" value=\"".MIN_VIEW."\"");
			if ($this->config["view"] == MIN_VIEW) {
				echo(" checked");
			}		
			echo("> $strings[l_MinView]</td>\n");
			echo("<td><input type=\"radio\" name=\"mailview\" value=\"".POPUP_VIEW."\"");
			if ($this->config["mailview"] == POPUP_VIEW) {
				echo(" checked");
			}		
			echo("> $strings[l_PopupView]</td></tr>\n");
			
			echo("<tr><td>&nbsp;</td>");
			echo("<td><input type=\"radio\" name=\"mailview\" value=\"".FRAME_VIEW."\"");
			if ($this->config["mailview"] == FRAME_VIEW) {
				echo(" checked");
			}		
			echo("> $strings[l_FrameView]</td></tr>");
				
			echo("</table><br><table width=\"100%\">");
		
			echo('<tr><td width="50%">'.$strings["l_Show"].' <input name="mails_per_page" value="'.$this->config["mails_per_page"].'" size="2"> '.$strings["l_MailsPerPage"].'</td>');
			echo('<td>'.$strings["l_Show"].' <input name="mail_rows" value="'.$this->config["mail_rows"].'" size="2"> '.$strings["l_Lines"].'</td>');
			echo('</tr><tr>');
			echo('<td>'.$strings["l_Show"].' <input name="subject_size" value="'.$this->config["subject_size"].'" size="2"> '.$strings["l_SubjectSize"].'</td>');
			echo('<td>'.$strings["l_Show"].' <input name="address_size" value="'.$this->config["address_size"].'" size="2"> '.$strings["l_AddressSize"].'</td>');
			
			echo('</tr><tr>');
			echo('<td>'.$strings["l_Show"].' <input name="folder_view_width" value="'.$this->config["folder_view_width"].'" size="2"> '.$strings["l_FolderViewWidth"].'</td>');
			echo('<td>&nbsp;</td>');		
			
			echo('</tr></table>');
		echo("</fieldset><br><fieldset>");
			echo("<legend><b>$strings[l_Singature]</b></legend>");
			echo('<textarea name="signature" cols="50" rows="4">');
			echo($this->config[signature]);
			echo('</textarea><br><br>');
			echo('<input type="checkbox" name="app_sig" value="1"');
			if ($this->config[app_sig]) {
				echo(' checked');
			}			
			echo('> '.$strings[l_AppSig].'<br><br>');
		echo("</fieldset>");
		
	}

	function store_config() {
		global $conf_submit, $view, $mailview, $keep, $server_del, $auto_check, $signature, $language;
		global $subject_size, $check_int, $address_size, $app_sig, $send_direct, $mail_rows;
		global $mails_per_page, $add_address, $poppercookie, $strings, $folder_view_width;

		if (isset($conf_submit) && $conf_submit == $strings["l_Store"]) {
			$query  = "UPDATE conf SET send_direct='$send_direct', keep='$keep', check_int='$check_int', ";
			$query .= "server_del='$server_del', auto_check='$auto_check', signature='$signature', ";
			$query .= "app_sig='$app_sig', mails_per_page='$mails_per_page', mail_rows='$mail_rows', ";
			$query .= "add_address='$add_address', language='$language', subject_size='$subject_size', ";
			$query .= "address_size='$address_size', view='$view', mailview='$mailview', ";
			$query .= "folder_view_width='$folder_view_width'  WHERE user_id = '$this->userid'";
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
				$this->config["subject_size"] = $subject_size;
				$this->config["address_size"] = $address_size;
				$this->config["check_int"] = $check_int;
				$this->config["view"] = $view;							
				$this->config["mailview"] = $mailview;
				$this->config["folder_view_width"] = $folder_view_width;
				if ($this->config["language"] != $language) {
					$this->config["language"] = $language;
					$this->update_lang();
				}

			}
		}
	}

	function change_pwd() {
		global $pwd, $pwd2, $strings;
		
		if (empty($pwd)) {
			$this->show_in_window("changepwd", $strings["l_EmptyPwd1"], $strings["l_EmptyPwd2"]);
			return;
		}
		if ($pwd != $pwd2) {
			$this->show_in_window("changepwd", $strings["l_PwdMismatch1"], $strings["l_PwdMismatch2"]);
			return;
		}
		
		$query = "UPDATE users SET pwd='$pwd' WHERE id='$this->userid'";
		$res = $this->db_tool->db_query($query);

		if ($res === false) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
			return false;
		}
		
		$this->show_form("main", $strings["l_PwdChanged"]);
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
		global $strings;
		
		$query = "SELECT id FROM mails WHERE user_id='$this->userid' AND folder='outbox'";

		$res = $this->db_tool->db_query($query);
		if ($res === false) {
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
				$query = "UPDATE mails SET folder='sent', was_read = ".MAIL_READ." WHERE id='$row[id]' AND user_id='$this->userid'";
				$update_res = $this->db_tool->db_query($query);
				if ($update_res === false) {
					$this->report_error($this->db_tool->db_error(), $strings["l_FolderMoveFail"].": ".$strings["l_Sent"]);
					return;
				}
			}
		}
		//echo('<span style="width: '.$width.'px; background-color: #3A6EA5;">&nbsp;</span>');
		//flush();
		//$this->next_timer($t, "fertig");
		$errors = $count - $send_counter;

		$msg = $strings["l_MailSentSucc"].": $send_counter. $errors ".$strings["l_Error"];
	}
}
?>
