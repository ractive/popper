/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

<center>
<h2>Popper Login</h2>

<form method="post" action="<?php echo("$GLOBALS[PHP_SELF]");?>">
	<table cellpadding="5">
		<tr>
			<td>Loginname:</td>
			<td><input type="text" name="name"><br></td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><input type="password" name="pwd"></td>
		</tr>
	</table>
	<input class="button" type="submit" name="login" value="Login">
</form>
</center>