/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

<tr class="window">
	<td width="35%" valign="top">
		<?php $this->show_accounts()?>
	</td>

	<td>
	<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="windowtitle">
				Account details
			</td>
		</tr>
		<tr>
			<td>
				<?php $this->show_account(); ?>
			</td>
		</tr>
	</table>
	</td>

</tr>