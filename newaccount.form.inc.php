<?php

/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

	if ($GLOBALS["update"] == 1) {
		$query = "SELECT * FROM accounts WHERE id = '$this->cur_account_id' AND user_id='$this->user_id'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), $strings["l_DBError"]);
			return;
		}
		$row = $this->db_tool->fetch_array($res);
	}
?>
<br>
<center>
<h2>
<?php echo($strings["l_NewAcc"]);?>
</h2>

<form action="<?php echo("$GLOBALS[PHP_SELF]");?>" method="post">
	<table cellpadding="5">
		<tr>
			<td><?php echo $strings["l_AccName"];?>:</td>
			<td><input name="name" size="25" value="<?php echo(stripslashes($row["name"]));?>"></td>
		</tr>
		<tr>
			<td><?php echo $strings["l_From"]."-".$strings["l_Name"];?>:</td>
			<td><input name="fromname" size="25" value="<?php echo(stripslashes($row["fromname"]));?>"></td>
		</tr>
		<tr>
			<td><?php echo $strings["l_ReplyTo"];?>:</td>
			<td><input name="replyto" size="25" value="<?php echo(stripslashes($row["replyto"]));?>"></td>
		</tr>
		<tr>
			<td><?php echo $strings["l_POPServer"];?>:</td>
			<td>
<?php
		// Check which mail-server are allowed...
		if ($GLOBALS["only_localhost"]) {
			// Only localhost is allowed
?>
			<input name="popserver" size="25" value="localhost" readonly>
<?php
		}
		else if (count($GLOBALS["allowed_servers"]) > 0) {
			// Display the list of allowed servers
			echo("<select name=\"popserver\" style=\"width: 25ex\">\n");
			foreach($GLOBALS["allowed_servers"] as $server) {
				echo("<option value=\"$server\"");
					if ($row[popserver] == $server) {
						echo(" selected");
					}
				echo(">$server</option>\n");
			}
			echo("</select>\n");
		}
		else {
			// All servers are allowed
?>
			<input name="popserver" size="25" value="<?php echo("$row[popserver]");?>">
<?php
		}
?>

			</td>
		</tr>
		<tr>
			<td>POP Loginname:</td>
			<td><input name="poplogin" size="25" value="<?php echo("$row[poplogin]");?>"></td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><input type="password" name="poppwd" size="25" value="<?php echo("$row[poppwd]");?>"></td>
		</tr>
	</table>
	<br><hr><br>
	&nbsp;
	<input class="button" type="submit" value="<?php echo $strings["l_Store"];?>">
	<input class="button" type="reset" value="<?php echo $strings["l_Reset"];?>" style="width: 150px;">
	<input type="hidden" name="update" value="<?php echo($GLOBALS["update"]);?>">
	<input type="hidden" name="action" value="storeaccount">
</form>
</center>