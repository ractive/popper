<?

/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000, 2001, 2003 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

include("class.mail.inc.php");

class mailer extends mail {

	var $db_tool;
	var $user_id;
	var $acc_id;
	var $folder;

	function mailer($user_id, $acc_id = 0) {
		$this->headers = new headers;
		$this->db_tool = new db_tool("popper.inc.php");
		$this->user_id = $user_id;
		$this->acc_id = $acc_id;
	}

	function load($id) {
		$query = "SELECT * FROM mails WHERE id = '$id' AND user_id='$this->user_id'";

		$res = $this->db_tool->db_query($query);
		if (!$res) {
			return false;
		}

		$row = $this->db_tool->fetch_array($res);
		$this->from_text(stripslashes($row["mail"]));
		$this->userid = $row["user_id"];
		$this->folder = $row["folder"];
		$this->id = $row["id"];
		$this->acc_id = $row["acc_id"];

		return true;
	}

	function load_forward($id) {
		if (!$this->load($id)) {
			return false;
		}

		$body = $this->get_text_body();

		$this->body = "> ".str_replace("\n", "\n> ", $body);

		$this->subject("Fw: ".$this->subject());
		$this->headers->put("To", "");
		$this->headers->put("Cc", "");
		$this->headers->put("Bcc", "");

		return true;
	}

	function load_reply($id, $to_all = false) {
		if (!$this->load($id)) {
			return false;
		}

		$body = $this->get_text_body();

		$this->body = "> ".str_replace("\n", "\n> ", $body);
		$reply_to = $this->headers->get("Reply-To");
		if (!empty($reply_to)) {
			$to = $this->headers->get("Reply-To");
		}
		else {
			$to = $this->headers->get("From");
		}
		$subject = $this->charset_decode($this->subject());
		$this->subject("Re: ".$subject);

		if ($to_all) {
			$to .= ", ".stripslashes($this->headers->get("To"));
			//leave the CC files intact
		}
		else {
			// Clear the CC field
			$this->headers->put("Cc", "");
		}

		$this->to($to);

		return true;
	}
	
	// Take a date like "Mon, 4 Sep 2000 20:20:37 +0200" and make a timestamp for use in the MYSQL DB
	function make_timestamp($date) {
		if (empty($date)) {
			return "NOW()";
		}
		$z = ord($date)-ord("0");
		// If the first char is a number, the date string is formed like this:
		// "21 Nov 2000 05:32:49 UT"; Just add a dummy weekday that it is processed normally
		if ($z >= 0 & $z <= 9) {
			$date = "Sun, ".$date;
		}
		$date = explode(" ", $date);
		$time = explode(":", $date[4]);

		$months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		$i = 0;
		// Is there an easier way to determine at which array position an element (month) is?
		foreach($months as $month) {
			$i++;
			if ($month == $date[2])
				break;
		}

		return date("YmdHis", mktime($time[0], $time[1], $time[2], $i, $date[1], $date[3]));
	}
	
	function is_mime() {
		$cont_type = $this->get_header("Content-Type");

		$type = trim($cont_type["CONTENT-TYPE"]);

		if (empty($type)) {
			// There's no content-type. Assume it's text/plain
			return false;
		}
		return (strcasecmp($type, "text/plain") != 0);
	}
	
	function store($folder, $msg = false, $server_uidl = 0) {
		if ($msg) {
			$this->headers->extract($msg);
		}
		else if ($msg === false) {
			$msg = $this->generate_body(true, true);
		}

		$is_mime = $this->is_mime();
		//Escape special chars like ' and "
		$uidl = addslashes($this->headers->get("X-UIDL"));
		$msg = addslashes($msg);
		$tos = addslashes($this->headers->get("To"));
		$froms = addslashes($this->headers->get("From"));
		$from_name = addslashes($this->get_pretty_header("From"));
		$subject = addslashes($this->headers->get("Subject"));
		if ($this->headers->get("X-MSMail-Priority") == "High") {
			$priority = HIGH_PRIORITY;
		}
		else if ($this->headers->get("X-MSMail-Priority") == "Low") {
			$priority = LOW_PRIORITY;
		}
		else {
			$priority = NORMAL_PRIORITY;
		}
		if ($this->headers->get("X-MSMail-Priority") == "High") {
			$priority = HIGH_PRIORITY;
		}
		else if ($this->headers->get("X-MSMail-Priority") == "Low") {
			$priority = LOW_PRIORITY;
		}
		else {
			$priority = NORMAL_PRIORITY;
		}

		$priority_header = $this->headers->get("X-Priority");

		$priority_header = $priority_header[0];

		if (isset($priority_header)) {
			switch($priority_header) {
				case "1";
					$priority = HIGHEST_PRIORITY;
					break;
				case "2";
					$priority = HIGH_PRIORITY;
					break;
				case "3";
					$priority = NORMAL_PRIORITY;
					break;
				case "4";
					$priority = LOW_PRIORITY;
					break;
				case "5";
					$priority = LOWEST_PRIORITY;
					break;
				default:
					$priority = NORMAL_PRIORITY;
					break;
			}
			
		}
		
		
		
		$date = $this->make_timestamp($this->headers->get("Date"));
		if ($folder == "sent") {
			$was_read = MAIL_READ;
		}
		else {
			$was_read = MAIL_UNREAD;
		}
		
		// Some POP server list uidls on request, but don't store them in
		// the mail as X-UIDL
		if ($server_uidl != 0 && !$uidl) {
			$uidl = $server_uidl;
		}
		$query = "INSERT INTO mails (tos, froms, from_name, subject, date, folder, user_id, mail, uidl, acc_id, is_mime, was_read, priority) VALUES ('$tos', '$froms', '$from_name', '$subject', $date, '$folder', '$this->user_id', '$msg', '$uidl', '$this->acc_id', '$is_mime', '$was_read', '$priority')";
		$res = $this->db_tool->db_query($query);

		return $this->db_tool->insert_id();	
	}

	function start_timer($event) {
		printf("timer: %s<br>", $event);
		list($low, $high) = split(" ", microtime());
		$t = $high + $low;
		flush();

		return $t;
 	}

	function next_timer($start, $event) {
		list($low, $high) = split(" ", microtime());
		$t    = $high + $low;
    	$used = $t - $start;
    	printf("timer: %s (%8.4f)<br>", $event, $used);
    	flush();

		return $t;
	}

}
?>
