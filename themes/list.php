<?php
	include("system.inc.php");
	
	foreach($theme as $key => $attr) {
		echo("<p style=\"color: $attr\">$key</p>");
	}

?>