/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

<tr class="window">
	<td colspan="2">
		<table class="childwindow" align="center">
			<tr class="titlebar">
				<td>
					<table class="titlebar" width="100%" border="0">
						<tr>
							<td style="text-align: left;">
<?php
								if (empty($title)) {
									echo("&nbsp;");
								}
								else {
									echo("$title");
								}
?>
							</td>
							<td><a href="<?php echo("$GLOBALS[PHP_SELF]?action=cancel");?>"><img src="graphics/logoff.gif" alt="Cancel" title="Cancel" align="right" border="0"></a></td>
						</tr>
					</table>
				</td>
			</tr>
<?php
		if(file_exists($form.".toolbar.inc.php")) {
			include($form.".toolbar.inc.php");
		}
?>
			<tr>
				<td>
					<?php include($form.".form.inc.php");?>
				</td>
			</tr>
		</table>
	</td>
</tr>
			