	<tr class="toolbar">
		<td colspan="3">
		<img src="graphics/toolbar_left.gif" alt="|">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=newmail"); ?>"><img src="graphics/newmail.gif" alt="New mail" title="New mail" border="0"></a>
		<img src="graphics/spacing.gif" alt="|">
<?php
			// Enable the read button only when a message is selected
			$disable = ($this->cur_message_id == 0);
			if ($disable) {
?>
			<img style="margin: 0px, 1px, 0px, 1px;" src="graphics/read_dis.gif" alt="Read" title="Select a mail to read it">
			<img src="graphics/prop_dis.gif" alt="Headers" title="Show sources" border="0">
<?php
			}
			else {
?>
			<a href="<?php echo("$GLOBALS[PHP_SELF]");?>?action=showmail"><img src="graphics/read.gif" alt="Open" title="Open mail" border="0"></a>
			<a href="<?php echo("$GLOBALS[PHP_SELF]");?>?action=showsources"><img src="graphics/prop.gif" alt="Headers" title="Show sources" border="0"></a>
<?php
			}
?>
			<img src="graphics/spacing.gif" alt="|">
<?php
			// Enable the Reply, forward etc. buttons only when a message is selected and
			// if we're not in the outbox
			$disable = ($this->cur_message_id == 0 || $this->folder == "outbox");
			if ($disable) {
?>
				<img style="margin: 0px, 1px, 0px, 1px;" src="graphics/reply_dis.gif" alt="Reply" title="Select a mail first">
				<img style="margin: 0px, 1px, 0px, 1px;" src="graphics/replytoall_dis.gif" alt="Reply to all" title="Select a mail first">
				<img style="margin: 0px, 1px, 0px, 1px;" src="graphics/forward_dis.gif" alt="Forward" title="Select a mail first">
<?php
			}
			else {
?>
				<a href="<?php echo("$GLOBALS[PHP_SELF]?action=reply");?>"><img src="graphics/reply.gif" alt="Reply" title="Reply" border="0"></a>
				<a href="<?php echo("$GLOBALS[PHP_SELF]?action=replytoall");?>"><img src="graphics/replytoall.gif" alt="Reply to all" title="Reply to all" border="0"></a>
				<a href="<?php echo("$GLOBALS[PHP_SELF]?action=forward");?>"><img src="graphics/forward.gif" alt="Forward" title="Forward" border="0"></a>
<?php
			}
?>
			<img src="graphics/spacing.gif" alt="|">
			<a href="<?php echo("$GLOBALS[PHP_SELF]?action=newfolder&parent=$this->folder");?>"><img src="graphics/newfolder.gif" alt="New Folder" title="New Folder" border="0"></a>
<?php
			$disable = ($this->cur_message_id == 0);
			if ($disable) {
?>
				<img style="margin: 0px, 1px, 0px, 1px;" src="graphics/move_dis.gif" alt="Move to folder" title="Select a mail first" border="0">
<?php
			}
			else {
?>
				<a href="<?php echo("$GLOBALS[PHP_SELF]?action=move");?>"><img src="graphics/move.gif" alt="Move to folder" title="Move to folder" border="0"></a>
<?php
			}
?>
			<img src="graphics/spacing.gif" alt="|">
<?php
			// Enable the delete button only when a message is selected
			$disable = ($this->cur_message_id == 0);
			if ($disable) {
?>
				<img style="margin: 0px, 1px, 0px, 1px;" src="graphics/cancel_dis.gif" alt="Reply" title="Select a mail first">
				<a href="<?php echo("$GLOBALS[PHP_SELF]?action=deletemails");?>"><img src="graphics/cancelm.gif" alt="Delele" title="Delete multiple message" border="0"></a>
<?php
			}
			else {
?>
				<a href="<?php echo("$GLOBALS[PHP_SELF]?action=deletemail");?>"><img src="graphics/cancel.gif" alt="Delele" title="Delete message" border="0"></a>
				<a href="<?php echo("$GLOBALS[PHP_SELF]?action=deletemails");?>"><img src="graphics/cancelm.gif" alt="Delele" title="Delete multiple message" border="0"></a>
<?php
			}
?>
		<img src="graphics/spacing.gif" alt="|">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=sendandreceive");?>"><img src="graphics/sendandreceive.gif" alt="Send and receive" title="Send and receive" border="0"></a>
		<img src="graphics/spacing.gif" alt="|">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=addressbook");?>"><img src="graphics/address.gif" alt="Address book" title="Address book" border="0"></a>
		<img src="graphics/spacing.gif" alt="|">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=accounts");?>"><img src="graphics/account.gif" alt="Accounts" title="Manage your accounts" border="0"></a>
		<img src="graphics/spacing.gif" alt="|">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=config");?>"><img src="graphics/config.gif" alt="Accounts" title="Configuration" border="0"></a>
		</td>
	</tr>