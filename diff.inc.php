<?php
// A replacement for the missing array_diff function in PHP 4.0.0

function array_diff($first, $second) {
	$res = array();
	foreach($first as $key => $val) {
		$found = false;
		foreach($second as $key2 => $val2) {
			if ($val == $val2) {
				$found = true;
				break;
			}
		}
		if (!$found) {
			$res[$key] = $val;
		}
	}	
	
	return $res;	
}
?>