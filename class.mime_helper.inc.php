<?
class mime_helper {

	function get_header_str(&$part) {
		$pos = strpos($part, "\r\n\r\n");
		return substr($part, 0, $pos);
	}

	function extract_whole_body(&$mail) {
		$divider = "\r\n\r\n";
		$pos = strpos($mail, $divider);
		return substr($mail, $pos + strlen($divider));		
	}

	// Extract part number $num with boundary $boundary from $mail
	function extract_part($num, $boundary, &$mail) {
		$boundary = "\r\n--$boundary\r\n";
		$endboundary = "\r\n--$boundary--";
		$body = $this->extract_whole_body($mail);
		$pos = strpos($mail, $boundary);
		if ($pos === false) {
			// No boundary was found
			return false;
		}
		$pos += strlen($boundary);
		$pos2 = strpos($mail, $boundary, $pos + strlen($boundary));
		if ($pos2 === false) {
			// The 2nd boundary was not found. Check for end boundary
			$pos2 = strpos($mail, $endboundary, $pos + strlen($boundary));
			if ($pos2 === false) {
				// The endboundary is also not present. The mail is corrupt.
				// We assume that the end of the mail is the end of the part
				$pos2 = strlen($mail);
			}
		}
		
		return substr($mail, $pos, $pos2 - $pos);
	}

	function extract_headers(&$part) {
		$header_arr = array();
		$header_str = $this->get_header_str($part)."\r\n";
		$line = strtok($header_str, "\n");
		while(1) {
			// Remove trailing whitespaces (the \r)
			$line = rtrim($line);

			if (preg_match("/^\s/", $line)) {
				// The new line starts with a whitespace. This indicates
				// That the headerpart is "folded" (multiline) and that
				// the current line belongs to the same header entity
				$header .= " $line";
				$line = strtok("\n");
				continue;
			}
			else if (!empty($header)) {
				// The $header now contains a header which we split up into its parts now
				if (preg_match("/^(.*?): (.*)$/", $header, $matches)) {
					$header_arr[$matches[1]] = $matches[2];
				}
			}

			if (!$line) {
				// This was the last line
				break;
			}
					
			// A new header entity begins
			$header = $line;

			$line = strtok("\n");
		}
		
		return $header_arr;
	}
	
	function un_quote($string) {
		preg_match("/\"(.*?)\"/", trim($string), $matches);
		return $matches[1];
	}
	
	function split_header ($string, $split_char) {
		$quoted = false; // true if in a quoted part
		$in_brackets = 0; // Reference counter of open brackets
		$split_arr = array();
		$old_pos = 0; // The last position of the $split_char
		$len = strlen($string);
		// Run throught the string
		for($i = 0; $i < $len; $i++) {
			$char = $string[$i];

			// $quoted == true after the first occurence of "
			// $quoted == false after the 2nd occurence of "
			// Quotes inside brackets are ignored
			if (!$in_brackets && $char == '"') {
				$quoted = !$quoted;
				continue;
			}
			// Brackets inside a quote are ignored
			else if (!$quoted && $char == '(') {
				$in_brackets++; // Increase the open brackets reference counter
				continue;
			}
			// Check for closing bracket if one was opened previously
			// Brackets inside a quote are ignored	
			else if (!$quoted && $in_brackets && $char == ')') {
				$in_brackets--; // Decrease the open brackets reference counter
				continue;
			}
			
			if (!$in_brackets && !$quoted && $char == $split_char) {
				// We found a string. Add it to the array
				$s = trim(substr($string, $old_pos, $i - $old_pos));
				$split_arr[] = $s;
				$old_pos = $i + 1;
			}
			
		}
		
		// Add the last part of the string to the array
		$s = trim(substr($string, $old_pos, $len - $old_pos));
		if (!empty($s)) {
			$split_arr[] = $s;
		}
		
		return $split_arr;
	}
	
	
	
	function print_array($arr) {
		foreach($arr as $key => $val) {
			echo("$key --> $val<br>");
		}
	}
}
?>