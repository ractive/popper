<?php
/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

	$disable = $this->cur_account_id == 0;
?>
	<tr class="toolbar">
		<td colspan="3">
		<img src="graphics/toolbar_left.gif" alt="|">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=showfolder&folder=$this->folder"); ?>"><img src="graphics/home.gif" alt="Go to main view" title="Go to main view" border="0"></a>
		<img src="graphics/spacing.gif" alt="">
<?php
		if ($disable) {
?>
			<a href="<?php echo("$GLOBALS[PHP_SELF]?action=newaccount"); ?>"><img src="graphics/newaddress.gif" alt="New account" title="New account" border="0"></a>
			<img style="margin: 0px, 11px, 0px, 11px;" src="graphics/properties_dis.gif" alt="Alter account" border="0">
			<img src="graphics/spacing.gif" alt="">
			<img style="margin: 0px, 11px, 0px, 11px;" src="graphics/cancel_dis.gif" alt="Delete account" border="0">
<?php
		}
		else {
?>
			<a href="<?php echo("$GLOBALS[PHP_SELF]?action=newaccount"); ?>"><img src="graphics/newaddress.gif" alt="New account" title="New account" border="0"></a>
			<a href="<?php echo("$GLOBALS[PHP_SELF]?action=newaccount&update=1"); ?>"><img src="graphics/properties.gif" alt="Alter account" title="Alter account information" border="0"></a>
			<img src="graphics/spacing.gif" alt="">
			<a href="<?php echo("$GLOBALS[PHP_SELF]?action=delaccount"); ?>"><img src="graphics/cancel.gif" alt="Delete account" title="Delete account" border="0"></a>
<?php
		}
?>
		</td>
	</tr>