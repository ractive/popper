/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

<?

//include("class.db_tool.inc.php");
include("class.mail.inc.php");

class mailer extends mail{

	var $db_tool;
	var $user_id;
	var $acc_id;
	var $folder;

	function mailer($user_id, $acc_id = 0) {
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
		$this->headers["To"] = "";
		$this->headers["Cc"] = "";
		$this->headers["Bcc"] = "";

		return true;
	}

	function load_reply($id, $to_all = false) {
		if (!$this->load($id)) {
			return false;
		}

		$body = $this->get_text_body();

		$this->body = "> ".str_replace("\n", "\n> ", $body);

		if (!empty($this->headers["Reply-To"])) {
			$to = $this->headers["Reply-To"];
		}
		else {
			$to = $this->headers["From"];
		}
		$subject = $this->subject();
		$this->subject("Re: ".$subject);

		if ($to_all) {
			$to .= ", ".$this->to();
			//leave the CC files intact
		}
		else {
			// Clear the CC field
			$this->headers["Cc"] = "";
		}

		$this->to($to);

		return true;
	}
	
	// Take a date like "Mon, 4 Sep 2000 20:20:37 +0200" and make a timestamp for use in the MYSQL DB
	function make_timestamp($date) {
		if (empty($date)) {
			return "NOW()";
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
		$type = trim($cont_type["Content-Type"]);	
		return (strcasecmp($type, "text/plain") != 0);
	}
	
	function store($folder, $msg = false) {
		if ($msg) {
			$this->headers = $this->extract_headers($msg);
		}
		else if ($msg === false) {
			$msg = $this->generate_body(true, true);
		}

		$is_mime = $this->is_mime();
		$uidl = $this->headers["X-UIDL"];

		//Escape special chars like ' and "
		$msg = addslashes($msg);
		$tos = addslashes($this->headers["To"]);
		$froms = addslashes($this->headers["From"]);
		$subject = addslashes($this->headers["Subject"]);
		$date = $this->make_timestamp($this->headers["Date"]);
		if ($folder == "sent") {
			$was_read = 1;
		}
		$query = "INSERT INTO mails (tos, froms, subject, date, folder, user_id, mail, uidl, acc_id, is_mime, was_read) VALUES ('$tos', '$froms', '$subject', $date, '$folder', '$this->user_id', '$msg', '$uidl', '$this->acc_id', '$is_mime', '$was_read')";
		$res = $this->db_tool->db_query($query);

		return $res != 0;	
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