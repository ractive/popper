	<tr class="toolbar">
		<td colspan="3">
		<img src="graphics/toolbar_left.gif" alt="|">
<?php	
		global $mail_id;
		
		$frame_view = ($this->config["mailview"] == FRAME_VIEW);
		
		echo("<a href=\"$GLOBALS[PHP_SELF]?action=newmail\"><img src=\"graphics/newmail.gif\" alt=\"$strings[l_NewMail]\" title=\"$strings[l_NewMail]\" border=\"0\"></a>\n");

			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
 			if ($this->config["mailview"] != POPUP_VIEW) {	
				// Enable the read button only when a message is selected
				$disable = !$frame_view && (!isset($GLOBALS[mail_id]) || $GLOBALS[mail_id] == 0);
				if ($disable) {
					echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/read_dis.gif\" alt=\"$strings[l_Select]\" title=\"$strings[l_Select]\">\n");
					echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/prop_dis.gif\" alt=\"$strings[l_Select]\" title=\"$strings[l_Select]\">\n");
				}
				else {
					echo("<a href=\"$GLOBALS[PHP_SELF]?action=openmail&mail_id=$mail_id\"><img src=\"graphics/read.gif\" alt=\"$strings[l_OpenMail]\" title=\"$strings[l_OpenMail]\" border=\"0\"></a>\n");
					echo("<a href=\"$GLOBALS[PHP_SELF]?action=showsources&mail_id=$mail_id\"");
					if ($frame_view) {
						echo(" target=\"mail\"");
					}
					
					echo("><img src=\"graphics/prop.gif\" alt=\"$strings[l_ShowSources]\" title=\"$strings[l_ShowSources]\" border=\"0\"></a>\n");					
				}
				echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
				
				// Enable the Reply, forward etc. buttons only when a message is selected and
				// if we're not in the outbox

				$disable = !$frame_view && ($this->cur_message_id == 0 || $this->folder == "outbox");
				if ($disable) {

					echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/reply_dis.gif\" alt=\"$strings[l_Select]\" title=\"$strings[l_Select]\">\n");
					echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/replytoall_dis.gif\" alt=\"$strings[l_Select]\" title=\"$strings[l_Select]\">\n");
					echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/forward_dis.gif\" alt=\"$strings[l_Select]\" title=\"$strings[l_Select]\">\n");
				}
				else {
					echo("<a href=\"$GLOBALS[PHP_SELF]?action=reply&mail_id=$mail_id\"><img src=\"graphics/reply.gif\" alt=\"$strings[l_ReplyMail]\" title=\"$strings[l_ReplyMail]\" border=\"0\"></a>\n");
					echo("<a href=\"$GLOBALS[PHP_SELF]?action=replytoall&mail_id=$mail_id\"><img src=\"graphics/replytoall.gif\" alt=\"$strings[l_ReplyAllMail]\" title=\"$strings[l_ReplyAllMail]\" border=\"0\"></a>\n");
					echo("<a href=\"$GLOBALS[PHP_SELF]?action=forward&mail_id=$mail_id\"><img src=\"graphics/forward.gif\" alt=\"$strings[l_ForwardMail]\" title=\"$strings[l_ForwardMail]\" border=\"0\"></a>\n");					
				}

				echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");

				// Enable the delete button only when a message is selected
				$disable = (!$frame_view && $this->cur_message_id == 0);
				if ($disable) {
					echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/print_dis.gif\" alt=\"$strings[l_Select]\" title=\"$strings[l_Select]\">\n");
					echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/cancel_dis.gif\" alt=\"$strings[l_Select]\" title=\"$strings[l_Select]\">\n");
				}
				else {
					echo("<a href=\"$GLOBALS[PHP_SELF]?action=printmail&mail_id=$mail_id\" target=\"blank_\"><img src=\"graphics/print.gif\" alt=\"$strings[l_Print]\" title=\"$strings[l_Print]\" border=\"0\"></a>\n");
					echo("<a href=\"$GLOBALS[PHP_SELF]?action=deletemail&mail_id=$mail_id\"");
					if ($frame_view) {
						echo(" target=\"maillist\"");
					}
					
					echo("><img src=\"graphics/cancel.gif\" alt=\"$strings[l_DeleteSel]\" title=\"$strings[l_DeleteSel]\" border=\"0\"></a>\n");
				}
			} // end of if ($this->config["mailview"] == IN_VIEW)
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=deleteall\"");
			if ($frame_view) {
				echo(" target=\"maillist\"");
			}			
			echo("><img src=\"graphics/cancelm.gif\" alt=\"$strings[l_DeleteAll]\" title=\"$strings[l_DeleteAll]\" border=\"0\"></a>\n");
			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=sendandreceive\"");
			if ($frame_view) {
				echo(" target=\"maillist\"");
			}
			echo("><img src=\"graphics/sendandreceive.gif\" alt=\"$strings[l_SendReceive]\" title=\"$strings[l_SendReceive]\" border=\"0\"></a>\n");				
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=checkrep\" target=\"blank_\"><img src=\"graphics/get_rep.gif\" alt=\"$strings[l_GetRep]\" title=\"$strings[l_GetRep]\" border=\"0\"></a>\n");				
			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=addressbook\"");
			if ($frame_view) {
				echo(" target=\"maillist\"");
			}			
			echo("><img src=\"graphics/address.gif\" alt=\"$strings[l_AddressBook]\" title=\"$strings[l_AddressBook]\" border=\"0\"></a>\n");				
			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=accounts\"");
			if ($frame_view) {
				echo(" target=\"maillist\"");
			}			
			echo("><img src=\"graphics/account.gif\" alt=\"$strings[l_Accounts]\" title=\"$strings[l_Accounts]\" border=\"0\"></a>\n");								
			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=config\"><img src=\"graphics/config.gif\" alt=\"$strings[l_Config]\" title=\"$strings[l_Config]\" border=\"0\"></a>\n");												
?>	
		</td>
	</tr>