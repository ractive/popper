<?php

/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

	if (isset($_SESSION['DATA']['addr_id']) && $_SESSION['DATA']['addr_id'] != 0) {
		$query = "SELECT email, name FROM addresses WHERE id='".$_SESSION['DATA']['addr_id']."' AND user_id='".$this->userid."'";
		$to_res = $this->db_tool->db_query($query);
		if ($to_res == 0) {
			$this->report_error($this->db_tool->db_error(), "DB Error");
			return;
		}
		$to_row = $this->db_tool->fetch_array($to_res);
	}
	else if (isset($_SESSION['DATA']['addr']) && !empty($_SESSION['DATA']['addr'])) {
		$to_row['email'] = $_SESSION['DATA']['addr'];
		$to_row['name'] = "";
	}
	

	// Check if we come from the "Choose recipients" form
	if (isset($_SESSION['DATA']['rec'])) {
		$in = implode(", ", $_SESSION['DATA']['rec']);
		$query = "SELECT email, name FROM addresses WHERE user_id='".$this->userid."' AND id IN ($in)";
		$res = $this->db_tool->db_query($query);
		if ($res == 0) {
			$this->report_error($this->db_tool->db_error(), "Could not fetch recipients");
			return;
		}
		while($row = $this->db_tool->fetch_array($res)) {
			if (!empty($row[name])) {
				$rec .= "\"".$row['name']."\" <";
			}
			$rec .= stripslashes($row['email']);
			if (!empty($row[name])) {
				$rec .= ">";
			}
			$rec .= ", "; 
		}
		// Remove the last ", "
		$rec = trim(substr($rec, 0, strlen($rec) - 2));
		if (!empty($rec)) {
			switch(trim($_SESSION['DATA']['recipient'])) {
				case "TO":
					$this->cur_mailer->to($rec);
					break;
				case "CC":
					$this->cur_mailer->cc($rec);
					break;
				case "BCC":
					$this->cur_mailer->bcc($rec);			
					break;
			}
		}
		$mailer = $this->cur_mailer;
	}
	else {
//		dbg("NEW CUR_MAILER");
		$this->cur_mailer = new mailer($this->userid);	
		$mailer = $this->cur_mailer;
	}

	if (isset($_SESSION['DATA']['action']) && ($_SESSION['DATA']['action'] == "reply" || $_SESSION['DATA']['action'] == "replytoall")) {
		if ($this->cur_message_id == 0) {
			$this->show();
			return;
		}
		if (!$mailer->load_reply($this->cur_message_id, $_SESSION['DATA']['action'] == "replytoall")) {
			$this->report_error($mailer->db_tool->db_error(), "DB Error");
			return;
		}
		$read_status = $_SESSION['MAIL_REPLIED'];
		$query = "UPDATE mails SET was_read='$read_status' WHERE id='".$this->cur_message_id."' AND user_id='".$this->userid."'";
		$res = $this->db_tool->db_query($query);

	}
	else if (isset($_SESSION['DATA']['action']) && $_SESSION['DATA']['action'] == "forward") {
		if ($this->cur_message_id == 0) {
			$this->show();
			return;
		}
		if (!$mailer->load_forward($this->cur_message_id, $_SESSION['DATA']['action'] == "replytoall")) {
			$this->report_error($mailer->db_tool->db_error(), "DB Error");
			return;
		}
		$read_status = $_SESSION['MAIL_FORWARDED'];		
		$query = "UPDATE mails SET was_read='$read_status' WHERE id='".$this->cur_message_id."' AND user_id='".$this->userid."'";
		$res = $this->db_tool->db_query($query);		

	}
	else if (isset($_SESSION['DATA']['action']) && $_SESSION['DATA']['action'] == "openmail") {
		if ($this->cur_message_id == 0) {
			$this->show();
			return;
		}
		// When the mail is in one of these folders you only can read them, but not modify
		$readonly = ($this->folder == 'sent' || $this->folder == 'inbox' || $this->folder == 'bin');

		if (!$mailer->load($this->cur_message_id)) {
			$this->report_error($mailer->db_tool->db_error(), "DB Error");
			return;
		}
	}
	else if (isset($_SESSION['DATA']['action']) && $_SESSION['DATA']['action'] == "showattmail") {
		if ($this->cur_mailer == 0) {
			$this->show();
			return;
		}
		
		$mailer = $this->cur_mailer;
		
		$readonly = 1;
	}
	
	if (!$readonly) {
		$query = "SELECT id, replyto, name FROM accounts WHERE user_id='".$this->userid."'";
		$acc_res = $this->db_tool->db_query($query);
	}
?>
<h2>
<?php
		if (!$readonly) {
			echo($_SESSION['strings']['l_ComposeMail']);
		}
?>
</h2>
<form class="mail" action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
<table style="width: 100%">
<?php
		$priority = $mailer->headers->get("X-Priority");
		$priority = $priority[0];	
		if ($readonly) {
			if (!empty($priority) && $priority != $_SESSION['NORMAL_PRIORITY']) {
				echo("<tr><td><b>".$_SESSION['strings']['l_Priority'].":</b></td><td ");
				if ($priority == $_SESSION['HIGHEST_PRIORITY']) {
					echo("style=\"background-color: #ff3333;\">".$_SESSION['strings']['l_HighestPriority']);
				}
				if ($priority == $_SESSION['HIGH_PRIORITY']) {
					echo("style=\"background-color: #ff6666;\">".$_SESSION['strings']['l_HighPriority']);
				}			
				else if ($priority == $_SESSION['LOW_PRIORITY']) {
					echo("style=\"background-color: #9999ff; color: white;\">".$_SESSION['strings']['l_LowPriority']);
				}
				else if ($priority == $_SESSION['LOWEST_PRIORITY']) {
					echo("style=\"background-color: #4444ff; color: white;\">".$_SESSION['strings']['l_LowestPriority']);
				}			
				echo("</td></tr>");
			}
		}
		else {
			echo("<tr><td><b>".$_SESSION['strings']['l_Priority'].":</b></td>\n");
			echo("<td><select name=\"priority\" style=\"width: 100%;\">");
			echo("<option value=\"".$_SESSION['NORMAL_PRIORITY']."\"></option>\n");
			echo("<option value=\"".$_SESSION['HIGHEST_PRIORITY']."\" style=\"background-color: #ff3333;\">".$_SESSION['strings']['l_HighestPriority']."</option>\n");
			echo("<option value=\"".$_SESSION['HIGH_PRIORITY']."\" style=\"background-color: #ff6666;\">".$_SESSION['strings']['l_HighPriority']."</option>\n");								
			echo("<option value=\"".$_SESSION['LOW_PRIORITY']."\" style=\"background-color: #9999ff;\">".$_SESSION['strings']['l_LowPriority']."</option>\n");				
			echo("<option value=\"".$_SESSION['LOWEST_PRIORITY']."\" style=\"background-color: #4444ff;\">".$_SESSION['strings']['l_LowestPriority']."</option>\n");				
			echo("</optgroup></td>");
			echo("</td></tr>");
		}
		
?>		
	<tr>
		<td style="width: 15%;"><label for="from" accesskey="F"><?php echo($_SESSION['strings']['l_From']);?>:<label></td>
<?php
		if ($readonly) {
			echo("<td>".$mailer->from()."</td>");
		}
		else {
?>
		<td>
			<select name="replyaccount" style="width: 100%">
<?php
				while($acc_row = $this->db_tool->fetch_array($acc_res)) {
					echo("<option value=\"$acc_row[id]\"");
					if ($acc_row[id] == $mailer->acc_id) {
						echo(" selected");
					}	
					echo(">".stripslashes($acc_row['replyto'])." (".stripslashes($acc_row['name']).")</option>");
				}
?>
			</select>
<?php
		}
?>
		</td>
	</tr>
	<tr>
<?php
		if ($readonly) {
			echo("<td><label for=\"tos\" accesskey=\"T\">To:</label><td>\n");
			echo(htmlspecialchars(stripslashes($mailer->headers->get("To"))));
		}
		else {
			echo("<td><input style=\"vertical-align: middle;\" name=\"to\" type=\"image\" src=\"graphics/small_address.gif\" alt=\"".$_SESSION['strings']['l_To'].":\" value=\"to\"> <label for=\"tos\" accesskey=\"T\">".$_SESSION['strings']['l_To'].":</label></td>");						
			// When the user clicked on an address in the addressbook, the $to_row is set (from above)
			if ($to_row) {
//				dbg("TO_ROW: $to_row[email]");
?>
				<td><input name="tos" size="80" value="<?php echo(stripslashes($to_row['email'])); ?>"></td>
<?php
			}
			else {
?>
				<td><input name="tos" size="80" value="<?php echo(htmlspecialchars(stripslashes($mailer->headers->get("To")))); ?>"></td>
<?php
			}
		}
?>
	</tr>
	<tr>
<?php
		if ($readonly) {
			echo("<td><label for=\"ccs\" accesskey=\"C\">CC:</label></td>\n");
			echo("<td>".htmlspecialchars($mailer->cc())."</td>\n");
		}
		else {
			echo("<td><input style=\"vertical-align: middle;\" name=\"cc\" type=\"image\" src=\"graphics/small_address.gif\" alt=\"CC:\" value=\"cc\"> <label for=\"ccs\" accesskey=\"C\">CC:</label></td>");			
			echo("<td><input name=\"ccs\" size=\"80\" value=\"".htmlspecialchars(stripslashes($mailer->headers->get("Cc")))."\"></td>");
		}
?>
	</tr>
	<tr>
<?php
		if ($readonly) {
			echo("<td><label for=\"bccs\" accesskey=\"B\">BCC:</label></td>\n");
			echo("<td>".htmlspecialchars($mailer->bcc())."</td>\n");
		}
		else {
			echo("<td><input style=\"vertical-align: middle;\" name=\"bcc\" type=\"image\" src=\"graphics/small_address.gif\" alt=\"BCC:\" value=\"bcc\"> <label for=\"bccs\" accesskey=\"B\">BCC:</label></td>");			
			echo("<td><input name=\"bccs\" size=\"80\" value=\"".htmlspecialchars(stripslashes($mailer->headers->get("bcc")))."\"></td>");
		}
?>
	</tr>
	<tr>
		<td><label for="subject" accesskey="S"><?php echo($_SESSION['strings']['l_Subject']); ?>:</label></td>
<?php
		if ($readonly) {
			echo("<td>".htmlspecialchars($mailer->charset_decode($mailer->subject()))."</td>\n");
		}
		else {
			echo("<td><input name=\"subject\" size=\"80\" value=\"".htmlspecialchars($mailer->charset_decode($mailer->subject()))."\"></td>");
		}
?>
	</tr>
</table>
<textarea name="body" rows="<?php echo($this->config['mail_rows']);?>" cols="80" <?php if ($readonly) echo(" readonly ");?>>
<?php
	echo($mailer->decode($mailer->get_text_body()));
	// Show the signature if the user wants to...
	if (!$readonly && !isset($_SESSION['DATA']['rec'])) {
		$query = "SELECT signature, app_sig FROM conf WHERE user_id = '".$this->userid."'";
		$res = $this->db_tool->db_query($query);
		if ($res) {
			$row = $this->db_tool->fetch_array($res);
			if ($row['app_sig']) {
				echo("\r\n\r\n".$row['signature']);
			}
		}
	}

?>
</textarea><br><br>
<?php
		if ($readonly) {
			echo('<img src="graphics/clip.gif" alt="Attachment" title="Attachment" border="0">');
			if ($_SESSION['DATA']['action'] == "showattmail") {
				$this->show_attachments($this->cur_mailer);
			}
			else {
				$this->show_attachments();
			}
		}
		else {
?>
		<input type="checkbox" name="notification"> <?php echo($_SESSION['strings']['l_Notification']);?><br><br>
		<img src="graphics/clip.gif" alt="Attachment" title="Attachment" border="0"> <input style="width: 60%;" type="file" name="attachment">
<?php
		}
?>

<?php
		if (!$readonly) {
?>
<p>
<hr><br>
<center>
<input class="button" type="submit" value="<?php echo($_SESSION['strings']['l_Send']);?>" name="submit" style="margin-right: 15px;">
<input class="button" type="submit" value="<?php echo($_SESSION['strings']['l_Store']);?>" name="submit" style="margin-right: 15px;">
<input class="button" type="reset" value="<?php echo($_SESSION['strings']['l_Reset']);?>"></p>
<input type="hidden" name="action" value="storemail">
<input type="hidden" name="update" value="<?php if ($_SESSION['DATA']['action'] == "openmail") echo("1"); else echo("0");?>">
<?php
	}

?>
</center>
</form>
