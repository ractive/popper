<?php

/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

include("class.mimepart.inc.php");

class mail extends mimepart {

	var $mail_text;
	
	function mail() {
		$this->headers = new headers;
	}
	
	function create($to, $from, $subject, $body) {
		$this->to($to);
		$this->from($from);
		$this->subject($subject);
		//$this->headers["Content-Type"] = "text/plain;\tcharset=\"us-ascii\"";
		$this->body = $body;
	}

	function to($to = false) {
		if ($to === false) {
			return $this->get_pretty_header("To");
		}
		$this->headers->put("To", $to);
	}

	function cc($cc = false) {
		if ($cc === false) {
			$cc = $this->headers->get("Cc");
			return $this->get_pretty_header("Cc");
		}
		
		$this->headers->put("Cc", $cc);
	}	
	
	function bcc($bcc = false) {
		if ($bcc === false) {
			$bcc = $this->headers->get("Bcc");
			return $this->get_pretty_header("Bcc");
		}
		
		$this->headers->put("Bcc", $bcc);
	}	
		
	function from($from = false) {
		if ($from === false) {
			$from = $this->headers->get("From");
			return $this->get_pretty_header("From");
		}
		
		$this->headers->put("From", $from);
	}	
	
	function subject($subject = false) {
		if ($subject === false) {
			$subject = $this->headers->get("Subject");
			return $this->get_pretty_header("Subject");
		}
		if (empty($subject)) {
			$subject = "(No subject)";
		}
		$this->headers->put("Subject", $subject);
	}
	
	function add_header($header) {
		// TODO
		trim($header);
		if (empty($header)) {
			return;
		}
		$header .= "\r\n\r\n";
		$header_arr = $this->extract_headers($header);

		//$this->headers = array_merge($this->headers, $header_arr);
	}
	
	function attach_file($path, $filename, $type = "application/octet-stream", $encoding = "base64", $description = "", $disposition = "") {
		if (empty($encoding)) {
			$encoding = "base64";
		}
		if (substr(phpversion(), 0, 5) >= "4.0.3") {
	        $tmp_path = "tmp/".basename($path);
	        $move = move_uploaded_file($path, $tmp_path);
	        if (!$move) {
	                return false;
	        }
	        $path = $tmp_path;		
        }
		
		$fp = @fopen($path, "rb");
		if (!$fp) {
			return false;
		}
		$data = fread($fp, filesize($path));
		fclose($fp);
		@unlink($fp);
		$type .= ";\tname=\"$filename\";";
		$disposition .= "attachment; filename=\"$filename\"";
	
		return $this->attach_data($data, $type, $encoding, $description, $disposition);
	}
	
	function attach_data(&$data, $type = "application/octet-stream", $encoding = "base64", $description = "", $disposition = "") {
		if (stristr($type, "text/plain") && !stristr($type, "charset=")) {
			// append the default charset
			$type .= ';\tcharset="us-ascii"';
		}

		if (strcasecmp($encoding, "base64") == 0) {
			$data = chunk_split(base64_encode($data));
		}
		else if (strcasecmp($encoding, "quoted-printable") == 0) {
			// ???? I don't want to program a quoted-printable encoder...
		}
		
		$part = "Content-Type: ".$type."\r\n";
		if (!empty($encoding)) {
			$part .= "Content-Transfer-Encoding: ".$encoding."\r\n";
		}
		if (!empty($description)) {
			$part .= "Content-Description: ".$description."\r\n";
		}
		
		if (!empty($disposition)) {
			$part .= "Content-Disposition: ".$disposition."\r\n";			
		}
		
		$part .= "\r\n";
		$part .= $data;
		
		$new_part = new mimepart($part);
		
		if (count($this->mimeparts) == 0) {
			// This is the first MIME-Part
			// It means that we have to make a new MIME part out of the mail-body
			$this->attach_body();
		}
		
		// Add the new part to the current parts
		$this->mimeparts[] = $new_part;
	}
	
	function send() {
		$this->headers->put("X-Mailer", "popper");
		$headers = $this->generate_header_str(false);
		$body = $this->generate_body(false, false);
		$to = stripslashes($this->headers->get("To"));
		$subject = $this->subject();
		/*echo "TO: $to<br>";
		echo nl2br($headers);
		echo nl2br($body);*/
		return mail($to, $subject, $body, rtrim($headers));
	}
}

?>