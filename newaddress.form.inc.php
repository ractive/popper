/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

<?php
	if ($GLOBALS[update]) {
		$query = "SELECT * FROM addresses WHERE id = '$this->cur_address_id' AND user_id='$this->userid'";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "DB Error");
			return;
		}
		$row = $this->db_tool->fetch_array($res);
	}
?>
<center>
<h2>
Enter the address book entry
</h2>

<form action="<?php echo("$GLOBALS[PHP_SELF]");?>" method="post">
	<table cellpadding="3" cellspacing="3">
		<tr>
			<td style="background-color: inherit; color: Red;">Name:</td>
			<td><input name="name" size="25" value="<?php echo("$row[name]");?>"></td>
		</tr>
		<tr>
			<td>Lastname:</td>
			<td><input name="lastname" size="25" value="<?php echo("$row[lastname]");?>"></td>
		</tr>
		<tr>
			<td>Nickname:</td>
			<td><input name="nickname" size="25" value="<?php echo("$row[nickname]");?>"></td>
		</tr>
		<tr>
			<td>E-Mail:</td>
			<td><input name="email" size="25" value="<?php echo("$row[email]");?>"></td>
		</tr>
		<tr>
			<td>Address 1:</td>
			<td><input name="address1" size="25" value="<?php echo("$row[address1]");?>"></td>
		</tr>
		<tr>
			<td>Address 2:</td>
			<td><input name="address2" size="25" value="<?php echo("$row[address2]");?>"></td>
		</tr>
		<tr>
			<td>ZIP:</td>
			<td><input name="zip" size="7" value="<?php echo("$row[zip]");?>"></td>
		</tr>
		<tr>
			<td>Town:</td>
			<td><input name="town" size="25" value="<?php echo("$row[town]");?>"></td>
		</tr>
		<tr>
			<td>State:</td>
			<td><input name="state" size="25" value="<?php echo("$row[state]");?>"></td>
		</tr>
		<tr>
			<td>Country:</td>
			<td><input name="country" size="25" value="<?php echo("$row[country]");?>"></td>
		</tr>
		<tr>
			<td>Phone:</td>
			<td><input name="phone" size="25" value="<?php echo("$row[phone]");?>"></td>
		</tr>
		<tr>
			<td>Mobile-phone:</td>
			<td><input name="mobile" size="25" value="<?php echo("$row[mobile]");?>"></td>
		</tr>
		<tr>
			<td>Homepage:</td>
			<td><input name="homepage" size="25" value="<?php echo("$row[homepage]");?>"></td>
		</tr>
	</table>
	<br><hr><br>
	&nbsp;
	<input class="button" type="submit" value="Store">
	<input class="button" type="reset" value="Reset">
	<input type="hidden" name="update" value="<?php echo($GLOBALS["update"]);?>">
	<input type="hidden" name="action" value="storeaddress">
</form>
</center>