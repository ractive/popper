/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

<center>
<h2>Welcome to the ultimate PHP POP3 Client</h2>
Please login or create an account if you don't have already one ...

<form method="get" action="<?php echo("$GLOBALS[PHP_SELF]");?>">
	<input class="button" type="submit" name="login" value="Login"><br>
	<input class="button" type="submit" name="newuser" value="Create New User">
</form>
</center>