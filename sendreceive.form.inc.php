<?php

/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

	$query = "SELECT id, name FROM accounts WHERE user_id = '$this->userid'";
	$res = $this->db_tool->db_query($query);
	if ($res == 0) {
		$this->report_error($this->db_tool->db_error(), "DB Error");
		return;
	}
?>
<center>
<h2>Send and receive</h2>

<form method="post" action="<?php echo("$GLOBALS[REQUEST_URI]");?>">
	Please choose your action?
	<p>
	<select name="what" class="button">
		<option value="sendonly">Send only</option>
		<option value="sendandreceive">Send and receive all</option>
		<option value="getall">Get all</option>
<?php
		while($row = $this->db_tool->fetch_array($res)) {
			echo("<option value=\"$row[id]\">".stripslashes($row["name"])."</option>");
		}
?>
	</select>
	</p>
	<p><input class="button" type="submit" value="Go..."></p>
</form>
</center>