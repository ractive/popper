	<tr class="toolbar">
		<td colspan="3">
		<img src="graphics/toolbar_left.gif" alt="|">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=newmail"); ?>"><img src="graphics/newmail.gif" alt="<?php echo($strings["l_NewMail"]);?>" title="<?php echo($strings["l_NewMail"]);?>" border="0"></a>
		<img src="graphics/spacing.gif" alt="|">
<?php
			// Enable the read button only when a message is selected
			$disable = ($this->cur_message_id == 0);
			if ($disable) {
?>
			<img style="margin: 0px, 1px, 0px, 1px;" src="graphics/read_dis.gif" alt="Read" title="<?php echo($strings["l_Select"]);?>">
			<img src="graphics/prop_dis.gif" alt="Headers" title="Show sources" border="0">
<?php
			}
			else {
?>
			<a href="<?php echo("$GLOBALS[PHP_SELF]");?>?action=showmail"><img src="graphics/read.gif" alt="<?php echo($strings["l_OpenMail"]);?>" title="<?php echo($strings["l_OpenMail"]);?>" border="0"></a>
			<a href="<?php echo("$GLOBALS[PHP_SELF]");?>?action=showsources"><img src="graphics/prop.gif" alt="<?php echo($strings["l_ShowSources"]);?>" title="<?php echo($strings["l_ShowSources"]);?>" border="0"></a>
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
				<img style="margin: 0px, 1px, 0px, 1px;" src="graphics/reply_dis.gif" alt="<?php echo($strings["l_ReplyMail"]);?>" title="<?php echo($strings["l_Select"]);?>">
				<img style="margin: 0px, 1px, 0px, 1px;" src="graphics/replytoall_dis.gif" alt="<?php echo($strings["l_ReplyAllMail"]);?>" title="<?php echo($strings["l_Select"]);?>">
				<img style="margin: 0px, 1px, 0px, 1px;" src="graphics/forward_dis.gif" alt="<?php echo($strings["l_ForwardMail"]);?>" title="<?php echo($strings["l_Select"]);?>">
<?php
			}
			else {
?>
				<a href="<?php echo("$GLOBALS[PHP_SELF]?action=reply");?>"><img src="graphics/reply.gif" alt="<?php echo($strings["l_ReplyMail"]);?>" title="<?php echo($strings["l_ReplyMail"]);?>" border="0"></a>
				<a href="<?php echo("$GLOBALS[PHP_SELF]?action=replytoall");?>"><img src="graphics/replytoall.gif" alt="<?php echo($strings["l_ReplyAllMail"]);?>" title="<?php echo($strings["l_ReplyAllMail"]);?>" border="0"></a>
				<a href="<?php echo("$GLOBALS[PHP_SELF]?action=forward");?>"><img src="graphics/forward.gif" alt="<?php echo($strings["l_ForwardMail"]);?>" title="<?php echo($strings["l_ForwardMail"]);?>" border="0"></a>
<?php
			}
?>
		<img src="graphics/spacing.gif" alt="|">
<?php
			// Enable the delete button only when a message is selected
			$disable = ($this->cur_message_id == 0);
			if ($disable) {
?>
				<img style="margin: 0px, 1px, 0px, 1px;" src="graphics/print_dis.gif" alt="<?php echo($strings["l_Select"]);?>" title="<?php echo($strings["l_Select"]);?>">
				<img style="margin: 0px, 1px, 0px, 1px;" src="graphics/cancel_dis.gif" alt="<?php echo($strings["l_Select"]);?>" title="<?php echo($strings["l_Select"]);?>">				
				<a href="<?php echo("$GLOBALS[PHP_SELF]?action=deleteall");?>"><img src="graphics/cancelm.gif" alt="<?php echo($strings["l_DeleteAll"]);?>" title="<?php echo($strings["l_DeleteAll"]);?>" border="0"></a>					
<?php
			}
			else {
?>
				<a href="<?php echo("$GLOBALS[PHP_SELF]?action=printmail");?>" target="_blank"><img src="graphics/print.gif" alt="<?php echo($strings["l_Print"]);?>" title="<?php echo($strings["l_Print"]);?>" border="0"></a>
				<a href="<?php echo("$GLOBALS[PHP_SELF]?action=deletemail");?>"><img src="graphics/cancel.gif" alt="<?php echo($strings["l_DeleteSel"]);?>" title="<?php echo($strings["l_DeleteSel"]);?>" border="0"></a>
				<a href="<?php echo("$GLOBALS[PHP_SELF]?action=deleteall");?>"><img src="graphics/cancelm.gif" alt="<?php echo($strings["l_DeleteAll"]);?>" title="<?php echo($strings["l_DeleteAll"]);?>" border="0"></a>								
<?php
			}
?>
		<img src="graphics/spacing.gif" alt="|">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=sendandreceive");?>"><img src="graphics/sendandreceive.gif" alt="<?php echo($strings["l_SendReceive"]);?>" title="<?php echo($strings["l_SendReceive"]);?>" border="0"></a>
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=checkrep");?>" target="_blank"><img src="graphics/get_rep.gif" alt="<?php echo($strings["l_GetRep"]);?>" title="<?php echo($strings["l_GetRep"]);?>" border="0"></a>		
		<img src="graphics/spacing.gif" alt="|">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=addressbook");?>"><img src="graphics/address.gif" alt="<?php echo($strings["l_AddressBook"]);?>" title="<?php echo($strings["l_AddressBook"]);?>" border="0"></a>
		<img src="graphics/spacing.gif" alt="|">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=accounts");?>"><img src="graphics/account.gif" alt="<?php echo($strings["l_Accounts"]);?>" title="<?php echo($strings["l_ManageAcc"]);?>" border="0"></a>
		<img src="graphics/spacing.gif" alt="|">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=config");?>"><img src="graphics/config.gif" alt="<?php echo($strings["l_Config"]);?>" title="<?php echo($strings["l_Config"]);?>" border="0"></a>		
		</td>
	</tr>