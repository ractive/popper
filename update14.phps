<?php

	function get_from_name(&$mail) {
		global $mp;
		
		if (preg_match("/^From: (.*)$/Um", $mail, $matches)) {		
			if ($matches) {
				$addr = $mp->get_address($matches[1]);
				$name = $addr["name"];
				if (!empty($name)) {
					return $name;
				}
				else {
					return $addr["address"];
				}
				
			}
			else {
				return "";
			}
		}
	}
	
	include("class.db_tool.inc.php");
	include("class.mimepart.inc.php");
	include("config.inc.php");
	
	$db = new db_tool("popper.inc.php");
	$mp = new mimepart;
	

	
  		//$t = $this->start_timer("Sending mail");
  		flush();
	// Get all the mails
	$query = "SELECT id, mail FROM mails ORDER by id";
	$res = $db->db_query($query);
	if ($res == 0) {
		die("The mails in the database could not be read.<br>MySQL-Error message:".$db->db_error());		
	}
	
	$count = $db->affected_rows($res);

	$width = 60; //500/$count;
	echo("<h2>Updating database</h2><b>Please stand by, this can take a while:</b><br>");
	
	echo("<h3>Updating the mails in the database</h3><hr>");
	$i = 1;
	while($row = $db->fetch_row($res)) {
		$mail = $row[1];
		// Get the from name...
		$from_name = addslashes(get_from_name($mail));
		// ... and put it in the database
		$query = "UPDATE mails SET from_name='$from_name' WHERE id=$row[0]";
		$up_res = $db->db_query($query);
		if ($up_res == 0) {
			die("The mail with id ".$row[0]." could not be updated.<br>MySQL-Error message:".$db->db_error());
		}
		$percent = floor($i/$count * 100);
		//echo("Mail with id $row[0] updated ($percent%)<br>");
		echo('<span style="width: '.$width.'px; text-align: right; background-color: blue; color: white; border-right: 1px solid white; margin-bottom: 1px;">'.$row[0].'</span>');		
		flush();
		$i++;
	}
?>
<h3>The database could be successfully updated</h3>
popper (c) Jean-Pierre Bergamin