<?php

/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

	// Show the toolbar only if we show an existing mail
	if (isset($GLOBALS[action]) && $GLOBALS[action] == "showmail") {
?>
	<tr class="toolbar">
		<td colspan="3">
			<img src="graphics/toolbar_left.gif" alt="|">
			<a href="<?php echo("$GLOBALS[PHP_SELF]?action=reply");?>"><img src="graphics/reply.gif" alt="Reply" title="Reply" border="0"></a>
			<a href="<?php echo("$GLOBALS[PHP_SELF]?action=replytoall");?>"><img src="graphics/replytoall.gif" alt="Reply to all" title="Reply to all" border="0"></a>
			<a href="<?php echo("$GLOBALS[PHP_SELF]?action=forward");?>"><img src="graphics/forward.gif" alt="Forward" title="Forward" border="0"></a>
			<img src="graphics/spacing.gif" alt="|">
			<a href="<?php echo("$GLOBALS[PHP_SELF]?action=deletemail");?>"><img src="graphics/cancel.gif" alt="Delele" title="Delete message" border="0"></a>
			<img src="graphics/spacing.gif" alt="|">
		</td>
	</tr>
<?php
	}
?>	