/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

<table class="windowborder" width="100%" border="4" align="center">
	<tr class="titlebar">
		<td colspan="2">
			<table class="titlebar" width="100%" border="0">
				<tr>
					<td>
						<a href="<?php echo("$GLOBALS[PHP_SELF]");?>"><img src="graphics/logo.gif" alt="" title="Main view" style="vertical-align: middle;" border="0"></a>PHP POP3 Client - User <?php echo("<b>$this->name</b>");?>
					</td>
					<td>
						<a href="<?php echo("$GLOBALS[PHP_SELF]?action=logoff");?>"><img src="graphics/logoff.gif" alt="Logoff" title="Logoff" align="right" border="0"></a>
					</td>
				</tr>
			</table>
		</td>
	</tr>