	<tr class="toolbar">
		<td colspan="3">
		<img src="graphics/toolbar_left.gif" alt="|">
<?php	
		global $mail_id;
		
		$frame_view = ($this->config['mailview'] == $_SESSION['FRAME_VIEW']);
		
		echo("<a href=\"".$_SERVER['PHP_SELF']."?action=newmail\"><img src=\"graphics/newmail.gif\" alt=\"".$_SESSION['strings']['l_NewMail']."\" title=\"".$_SESSION['strings']['l_NewMail']."\" border=\"0\"></a>\n");

			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
 			if ($this->config['mailview'] != $_SESSION['POPUP_VIEW']) {	
				// Enable the read button only when a message is selected
				$disable = (!$frame_view && (!isset($_SESSION['DATA']['mail_id']) || ($_SESSION['DATA']['mail_id'] == 0)));
				if ($disable) {
					echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/read_dis.gif\" alt=\"".$_SESSION['strings']['l_Select']."\" title=\"".$_SESSION['strings']['l_Select']."\">\n");
					echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/prop_dis.gif\" alt=\"".$_SESSION['strings']['l_Select']."\" title=\"".$_SESSION['strings']['l_Select']."\">\n");
				}
				else {
					echo("<a href=\"".$_SERVER['PHP_SELF']."?action=openmail&mail_id=$mail_id\"><img src=\"graphics/read.gif\" alt=\"".$_SESSION['strings']['l_OpenMail']."\" title=\"".$_SESSION['strings']['l_OpenMail']."\" border=\"0\"></a>\n");
					echo("<a href=\"".$_SERVER['PHP_SELF']."?action=showsources&mail_id=$mail_id\"");
					if ($frame_view) {
						echo(" target=\"mail\"");
					}
					
					echo("><img src=\"graphics/prop.gif\" alt=\"".$_SESSION['strings']['l_ShowSources']."\" title=\"".$_SESSION['strings']['l_ShowSources']."\" border=\"0\"></a>\n");					
				}
				echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
				
				// Enable the Reply, forward etc. buttons only when a message is selected and
				// if we're not in the outbox

				$disable = !$frame_view && ($this->cur_message_id == 0 || $this->folder == "outbox");
				if ($disable) {

					echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/reply_dis.gif\" alt=\"".$_SESSION['strings']['l_Select']."\" title=\"".$_SESSION['strings']['l_Select']."\">\n");
					echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/replytoall_dis.gif\" alt=\"".$_SESSION['strings']['l_Select']."\" title=\"".$_SESSION['strings']['l_Select']."\">\n");
					echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/forward_dis.gif\" alt=\"".$_SESSION['strings']['l_Select']."\" title=\"".$_SESSION['strings']['l_Select']."\">\n");
				}
				else {
					echo("<a href=\"".$_SERVER['PHP_SELF']."?action=reply&mail_id=$mail_id\"><img src=\"graphics/reply.gif\" alt=\"".$_SESSION['strings']['l_ReplyMail']."\" title=\"".$_SESSION['strings']['l_ReplyMail']."\" border=\"0\"></a>\n");
					echo("<a href=\"".$_SERVER['PHP_SELF']."?action=replytoall&mail_id=$mail_id\"><img src=\"graphics/replytoall.gif\" alt=\"".$_SESSION['strings']['l_ReplyAllMail']."\" title=\"".$_SESSION['strings']['l_ReplyAllMail']."\" border=\"0\"></a>\n");
					echo("<a href=\"".$_SERVER['PHP_SELF']."?action=forward&mail_id=$mail_id\"><img src=\"graphics/forward.gif\" alt=\"".$_SESSION['strings']['l_ForwardMail']."\" title=\"".$_SESSION['strings']['l_ForwardMail']."\" border=\"0\"></a>\n");					
				}

				echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");

				// Enable the delete button only when a message is selected
				$disable = (!$frame_view && $this->cur_message_id == 0);
				if ($disable) {
					echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/print_dis.gif\" alt=\"".$_SESSION['strings']['l_Select']."\" title=\"".$_SESSION['strings']['l_Select']."\">\n");
					echo("<img style=\"margin: 0px, 1px, 0px, 1px;\" src=\"graphics/cancel_dis.gif\" alt=\"".$_SESSION['strings']['l_Select']."\" title=\"".$_SESSION['strings']['l_Select']."\">\n");
				}
				else {
					echo("<a href=\"".$_SERVER['PHP_SELF']."?action=printmail&mail_id=$mail_id\" target=\"blank_\"><img src=\"graphics/print.gif\" alt=\"".$_SESSION['strings']['l_Print']."\" title=\"".$_SESSION['strings']['l_Print']."\" border=\"0\"></a>\n");
					echo("<a href=\"".$_SERVER['PHP_SELF']."?action=deletemail&mail_id=$mail_id\"");
					if ($frame_view) {
						echo(" target=\"maillist\"");
					}
					
					echo("><img src=\"graphics/cancel.gif\" alt=\"".$_SESSION['strings']['l_DeleteSel']."\" title=\"".$_SESSION['strings']['l_DeleteSel']."\" border=\"0\"></a>\n");
				}
			} // end of if ($this->config['mailview'] == $_SESSION['IN_VIEW'])
			echo("<a href=\"".$_SERVER['PHP_SELF']."?action=deleteall\"");
			if ($frame_view) {
				echo(" target=\"maillist\"");
			}			
			echo("><img src=\"graphics/cancelm.gif\" alt=\"".$_SESSION['strings']['l_DeleteAll']."\" title=\"".$_SESSION['strings']['l_DeleteAll']."\" border=\"0\"></a>\n");
			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
			echo("<a href=\"".$_SERVER['PHP_SELF']."?action=sendandreceive\"");
			if ($frame_view) {
				echo(" target=\"maillist\"");
			}
			echo("><img src=\"graphics/sendandreceive.gif\" alt=\"".$_SESSION['strings']['l_SendReceive']."\" title=\"".$_SESSION['strings']['l_SendReceive']."\" border=\"0\"></a>\n");				
			echo("<a href=\"".$_SERVER['PHP_SELF']."?action=checkrep\" target=\"blank_\"><img src=\"graphics/get_rep.gif\" alt=\"".$_SESSION['strings']['l_GetRep']."\" title=\"".$_SESSION['strings']['l_GetRep']."\" border=\"0\"></a>\n");				
			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
			echo("<a href=\"".$_SERVER['PHP_SELF']."?action=addressbook\"");
			if ($frame_view) {
				echo(" target=\"maillist\"");
			}			
			echo("><img src=\"graphics/address.gif\" alt=\"".$_SESSION['strings']['l_AddressBook']."\" title=\"".$_SESSION['strings']['l_AddressBook']."\" border=\"0\"></a>\n");				
			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
			echo("<a href=\"".$_SERVER['PHP_SELF']."?action=accounts\"");
			if ($frame_view) {
				echo(" target=\"maillist\"");
			}			
			echo("><img src=\"graphics/account.gif\" alt=\"".$_SESSION['strings']['l_Accounts']."\" title=\"".$_SESSION['strings']['l_Accounts']."\" border=\"0\"></a>\n");								
			echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
			echo("<a href=\"".$_SERVER['PHP_SELF']."?action=config\"><img src=\"graphics/config.gif\" alt=\"".$_SESSION['strings']['l_Config']."\" title=\"".$_SESSION['strings']['l_Config']."\" border=\"0\"></a>\n");												
?>	
		</td>
	</tr>
