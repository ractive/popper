	<tr class="toolbar">
		<td colspan="3">
		<img src="graphics/toolbar_left.gif" alt="|">
<?php	
		global $mail_id, $action;
		
		if ($this->config["mailview"] == SEP_VIEW && $action != "getattachment") {
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=showfolder&folder=".$this->folder."&mail_id=$mail_id\">");			
			echo("<img src=\"graphics/home.gif\" alt=\"$strings[l_MainView]\" title=\"$strings[l_MainView]\" border=\"0\"></a>\n");
			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");				
		}
		else {
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=showmail&mail_id=");	
			if ($action == "getattachment") {
				echo($this->cur_message_id);
			}
			else {
				echo($mail_id);
			}
			echo("\"><img src=\"graphics/back.gif\" alt=\"$strings[l_MainView]\" title=\"$strings[l_MainView]\" border=\"0\"></a>\n");			
			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");	
		}
		


		
		echo("<a href=\"$GLOBALS[PHP_SELF]?action=showsources&mail_id=$mail_id\"><img src=\"graphics/prop.gif\" alt=\"$strings[l_ShowSources]\" title=\"$strings[l_ShowSources]\" border=\"0\"></a>\n");					
		echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
		
		// Enable the Reply, forward etc. buttons only when a message is selected and
		// if we're not in the outbox

		$disable = ($this->cur_message_id == 0 || $this->folder == "outbox" || $this->folder == "sent");
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

		echo("<a href=\"$GLOBALS[PHP_SELF]?action=printmail&mail_id=$mail_id\" target=\"blank_\"><img src=\"graphics/print.gif\" alt=\"$strings[l_Print]\" title=\"$strings[l_Print]\" border=\"0\"></a>\n");
		
		if ($this->config["mailview"] != POPUP_VIEW) {	
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=deletemail&mail_id=$mail_id\"><img src=\"graphics/cancel.gif\" alt=\"$strings[l_DeleteSel]\" title=\"$strings[l_DeleteSel]\" border=\"0\"></a>\n");
		}
		
		if ($action != "getattachment") {
			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=showmail&goto=prev&mail_id=$mail_id\"><img src=\"graphics/back.gif\" alt=\"$strings[l_PrevMail]\" title=\"$strings[l_PrevMail]\" border=\"0\"></a>\n");		
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=showmail&goto=next&mail_id=$mail_id\"><img src=\"graphics/next.gif\" alt=\"$strings[l_NextMail]\" title=\"$strings[l_NextMail]\" border=\"0\"></a>\n");				
		}
	
?>	
		</td>
	</tr>