/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

	<tr>
		<td colspan="2">
			<table class="statusbar"  style="width: 100%;" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<?php
							if (isset($status_msg)) {
								echo("$status_msg");
							}
							else {
								echo("<p style=\"font-size: small;\">&nbsp;</p>");
							}
						?>
					</td>
					<td width="10%">
						<p style="text-align: right; font-size: x-small; color: black;">
							<?php echo(date("d.m.Y"));?>
						</p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table> 