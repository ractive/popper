/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

<?php

/*
Further details to the definition of a mail:

STANDARD FOR THE FORMAT OF ARPA INTERNET TEXT MESSAGES
http://rfc.fh-koeln.de/rfc/html/rfc0822.html


MIME (Multipurpose Internet Mail Extensions) Part One:
Mechanisms for Specifying and Describing
the Format of Internet Message Bodies
http://rfc.fh-koeln.de/rfc/html/rfc1521.html
*/

include "class.pop3.inc";
//include "class.mailer.inc";

class mimepop3 extends pop3 {

	var $message;
	var $headers;

	function mimepop3() {
		$this->headers = array();
	}

	// Take a date like "Mon, 4 Sep 2000 20:20:37 +0200" and make a timestamp out of it
	function make_timestamp($date) {
		$date = explode(" ", $date);
		$time = explode(":", $date[4]);

		$months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		$i = 0;
		foreach($months as $month) {
			$i++;
			if ($month == $date[2])
				break;
		}
		return mktime($time[0], $time[1], $time[2], $i, $date[1], $date[3]);
	}

	function extract_headers(&$part) {
		$header_arr = array();
		reset($part);
		while(list($key, $val) = each($part)) {

			if (preg_match("/^\s.+/", $val)) {
				// A whitespace at the beginning of a new line
				// followed by nonwhitespace characters indicates,
				// that it belongs to the previous line
				$header .= $val;
			}
			else {
				if (!empty($header)) {
					// We have a header part, which we process now
					$header = str_replace("\r\n", " ", $header);
					preg_match_all("/^(.[^:]+):(.*)/", $header, $matches);
					for ($i=0; $i< count($matches[0]); $i++) {
						$header_arr[$matches[1][$i]] .= trim($matches[2][$i]);
					}
				}

				// If the line is empty, the header part is finished.
				if ($val == "\r\n" || empty($val)) {
					return $header_arr;
				}
				// When there's no whitespace at the beginning of a new line,
				// a new header part begins.
				$header = $val;
			}
		}

		return $header_arr;
	}

	function explode_parts($boundary) {
		$parts = array();
		$part = array();
		$i = 0;
		reset($this->message);
		// Go through each line of the message
		while(list($key, $line) = each($this->message)) {
			// If a line is the boudary...
			if ($line == $boundary) {
				// ... the current part is stored in an array
				$parts[$i++] = $part;
				$part = array();
				continue;
			}
			// Add the current line to the array
			$part[] = $line;
		}
		$parts[$i++] = $part;

		return $parts;
	}

	function extract_body(&$part) {
		reset($part);
		while(list($key, $line) = each($part)) {
			if ($b_body == false && $line == "\r\n") {
				// We reached the body
				$b_body = true;
				continue;
			}
			if ($b_body) {
				$body .= $line;
			}
		}
		return $body;
	}

	// This function returns an array which contains all headers.
	function get_ext($msgNum = "") {
		// Get the message. Every array element is a single line.
      	$message = $this->get($msgNum);

		$mail = new mime_mail($message);

		return $mail;
	}
}
?>