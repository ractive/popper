<?php

/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

class headers {
	
	var $header_arr = array();
	
	function get($name) {
		$element = $this->header_arr[strtolower($name)];
		return $element["content"];
	}
	
	function put($name, $content) {
		$element["name"] = $name;
		$element["content"] = $content;
		$this->header_arr[strtolower($name)] = $element;
	}
	
	function get_header_str(&$part) {
		$pos = strpos($part, "\r\n\r\n");
		return substr($part, 0, $pos);
	}	
	
	function extract($part) {
		// Clear previous values
		$this->header_arr = array();
		@set_time_limit(60);
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
					$this->put($matches[1], $matches[2]);
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
		
		//$this->print_array($this->header_arr);
	}

	function print_array($arr) {
		foreach($arr as $key => $val) {
			echo("$key --> $val<br>\r\n");
		}
	}	
	
	function generate_str($all_headers = true) {
		$header_str = "";
		foreach($this->header_arr as $element_name => $element) {
			// Don't include these two header fileds, because the PHP mail
			// function will add them itself
			if ($all_headers || ($element_name != "subject" && $element_name != "to")) {
				$header_str .= $element["name"].": ".$element["content"]."\r\n";
			}
		}
		
		return str_replace("\t", "\r\n\t", $header_str);
	}	
	
	// Strip Header comments from the string
	// Comments are in brackets outside of quotes
	function strip_comment($string) {
		$len = strlen($string);
		$ret_str = "";
		$quoted = false;
		$in_brackets = 0;
		for($i = 0; $i < $len; $i++) {
			$char = $string[$i];

			if (!$quoted && $in_brackets && $char == ')') {
				$in_brackets--;
				continue;
			}
			if (!$quoted && $char == '(') {
				$in_brackets++;
				continue;
			}
			if (!$quoted && $in_brackets) {
				continue;
			}
			if ($char == '"') {
				$quoted = !$quoted;
			}
			// We are not in brackets. Copy the character to the new string
			$str_ret .= $char;
		}
		return $str_ret;
	}	
	
	function split_header($string, $split_char) {
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
	
	function get_splitted($name) {
		$header = stripslashes($this->get($name));

		if (!$header) {
			return false;
		}
		$parameter = $this->split_header($header, ";");
		//$this->print_array($parameter);
		$header_arr[$name] = $parameter[0];
		reset($parameter);
		next($parameter);
		while(list($key, $p) = each($parameter)) {
			$split = $this->split_header($p, "=");
			$header_arr[$split[0]] = $this->un_quote($split[1]);
		}
				
		return $header_arr;
	}	
}

?>