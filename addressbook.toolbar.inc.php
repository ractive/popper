/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

	<tr class="toolbar">
		<td colspan="2">
		<img src="graphics/toolbar_left.gif" alt="|">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=showfolder&folder=$this->folder"); ?>"><img src="graphics/home.gif" alt="Go to main view" title="Go to main view" border="0"></a>
		<img src="graphics/spacing.gif" alt="">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=newaddress"); ?>"><img src="graphics/newaddress.gif" alt="New address" title="New address" border="0"></a>
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=newaddress&update=1"); ?>"><img src="graphics/properties.gif" alt="Alter address" title="Alter address" border="0"></a>
		<img src="graphics/spacing.gif" alt="">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=deladdress"); ?>"><img src="graphics/cancel.gif" alt="Delete address" title="Delete address" border="0"></a>
		</td>
	</tr>