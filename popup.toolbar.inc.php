	<tr class="toolbar">
		<td colspan="3">
		<img src="graphics/toolbar_left.gif" alt="|">
<?php
		
		if (($this->config['mailview'] == $_SESSION['SEP_VIEW']) && ($_SESSION['action'] != "getattachment")) {
			echo("<a href=\"".$_SERVER['PHP_SELF']."?action=showfolder&folder=".$this->folder."&mail_id=".$_SESSION['DATA']['mail_id']."\">");			
			echo("<img src=\"graphics/home.gif\" alt=\"".$_SESSION['strings']['l_MainView']."\" title=\"".$_SESSION['strings']['l_MainView']."\" border=\"0\"></a>\n");
			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");				
		}
		else {
			echo("<a href=\"".$_SERVER['PHP_SELF']."?action=showmail&mail_id=");	
			if ($_SESSION['action'] == "getattachment") {
				echo($this->cur_message_id);
			}
			else {
				echo($_SESSION['DATA']['mail_id']);
			}
			echo("\"><img src=\"graphics/back.gif\" alt=\"".$_SESSION['strings']['l_MainView']."\" title=\"".$_SESSION['strings']['l_MainView']."\" border=\"0\"></a>\n");			
			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");	
		}
		


		
		echo("<a href=\"".$_SERVER['PHP_SELF']."?action=showsources&mail_id=".$_SESSION['DATA']['mail_id']."\"><img src=\"graphics/prop.gif\" alt=\"".$_SESSION['strings']['l_ShowSources']."\" title=\"".$_SESSION['strings']['l_ShowSources']."\" border=\"0\"></a>\n");					
		echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
		
		// Enable the Reply, forward etc. buttons only when a message is selected and
		// if we're not in the outbox

		$disable = (($this->cur_message_id == 0) || ($this->folder == "outbox") || ($this->folder == "sent"));
		if ($disable) {
			echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/reply_dis.gif\" alt=\"".$_SESSION['strings']['l_Select']."\" title=\"".$_SESSION['strings']['l_Select']."\">\n");
			echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/replytoall_dis.gif\" alt=\"".$_SESSION['strings']['l_Select']\" title=\"".$_SESSION['strings']['l_Select']."\">\n");
			echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/forward_dis.gif\" alt=\"".$_SESSION['strings']['l_Select']."\" title=\"".$_SESSION['strings']['l_Select']."\">\n");
		}
		else {".['l_ReplyMail']."\" border=\"0\"></a>\n");
			echo("<a href=\"".$_SERVER['PHP_SELF']."?action=replytoall&mail_id=".$_SESSION['DATA']['mail_id']."\"><img src=\"graphics/replytoall.gif\" alt=\"".$_SESSION['strings']['l_ReplyAllMail']."\" title=\"".$_SESSION['strings']['l_ReplyAllMail']."\" border=\"0\"></a>\n");
			echo("<a href=\"".$_SERVER['PHP_SELF']."?action=forward&mail_id=".$_SESSION['DATA']['mail_id']."\"><img src=\"graphics/forward.gif\" alt=\"".$_SESSION['strings']['l_ForwardMail']."\" title=\"".$_SESSION['strings']['l_ForwardMail']."\" border=\"0\"></a>\n");					
		}

		echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");

		echo("<a href=\"".$_SERVER['PHP_SELF']."?action=printmail&mail_id=".$_SESSION['DATA']['mail_id']."\" target=\"blank_\"><img src=\"graphics/print.gif\" alt=\"".$_SESSION['strings']['l_Print']."\" title=\"".$_SESSION['strings']['l_Print']."\" border=\"0\"></a>\n");
		
		if ($this->config['mailview'] != $_SESSION['POPUP_VIEW']) {	
			echo("<a href=\"".$_SERVER['PHP_SELF']."?action=deletemail&mail_id=".$_SESSION['DATA']['mail_id']."\"><img src=\"graphics/cancel.gif\" alt=\"".$_SESSION['strings']['l_DeleteSel']."\" title=\"".$_SESSION['strings']['l_DeleteSel']."\" border=\"0\"></a>\n");
		}
		
		if ($_SESSION['action'] != "getattachment") {
			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
			echo("<a href=\"".$_SERVER['PHP_SELF']."?action=showmail&goto=prev&mail_id=".$_SESSION['DATA']['mail_id']."\"><img src=\"graphics/back.gif\" alt=\"".$_SESSION['strings']['l_PrevMail']."\" title=\"".$_SESSION['strings']['l_PrevMail']."\" border=\"0\"></a>\n");		
			echo("<a href=\"".$_SERVER['PHP_SELF']."?action=showmail&goto=next&mail_id=".$_SESSION['DATA']['mail_id']."\"><img src=\"graphics/next.gif\" alt=\"".$_SESSION['strings']['l_NextMail']."\" title=\"".$_SESSION['strings']['l_NextMail']."\" border=\"0\"></a>\n");				
		}
?>	
		</td>
	</tr>
