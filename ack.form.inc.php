/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

<center>
<h2>Please confirm deletion</h2>
<form method="post" action="<?php echo("$GLOBALS[REQUEST_URI]");?>">
	Are you sure you want to delete this item?<br><br>
	<input class="button" type="submit" name="ack" value="Yes">&nbsp;
	<input class="button" type="submit" name="ack" value="No">
</form>
</center>