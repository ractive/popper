/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

<tr class="window">
	<td width="25%" valign="top">
		<?php $this->show_folders() ?>
		<p class="windowtitle">
			<a href="<?php echo("$GLOBALS[PHP_SELF]?action=newaddress");?>">Add contact...</a>
		</p>
		<?php $this->show_addresses() ?>
	</td>
	<td>
<?php
	if ($this->folder == "cal") {
		include("cal.form.inc.php");
	}
	else {
		include("mail.form.inc.php");
	}
?>
	</td>
</tr>