<table width="100%" border="0" cellpadding="0" cellspacing="0">
<?php
	// Don't show the scrollbar and the folder title in Mainview because all mails are shown
	// and the title is shown in the frame
	if ($this->config["mailview"] != FRAME_VIEW) {
		echo("<tr><td class=\"windowtitle\">");
		echo($this->get_foldername());
		echo("</td>");

		echo("<td class=\"scrollbar\" valign=\"top\">");	
		// update the page_no.
		$mail_count = $this->count_mails();
		if (isset($GLOBALS[page])) {
			$this->update_pageno($GLOBALS[page], $mail_count);
		}
		$page_no = $this->get_pageno();

		if ($page_no != 0) {
			$fromto = $strings["l_FromTo"]." ";
			$fromto .= ($page_no - 1) * $this->config["mails_per_page"] + 1;
			$fromto .= " $strings[l_ToM] ";
			$fromto .= $page_no * $this->config["mails_per_page"];
			
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=showfolder&folder=$this->folder&page=top\"><img src=\"graphics/top.gif\"  border=\"0\" title=\"$strings[l_FirstP]\" alt=\"$strings[l_FirstP]\"></a>");
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=showfolder&folder=$this->folder&page=prev\"><img src=\"graphics/arrowup.gif\"  border=\"0\" title=\"$fromto\" alt=\"$fromto\"></a>");				
		}
		else {
			echo('<img src="graphics/arrowup_dis.gif" alt="" border="0">');
		}
		echo("</td></tr>");
	}
		
?>
	<tr>
		<td class="mailwindow">
			<?php $this->show_folder_content(); ?>
		</td>

<?php
		if ($this->config["mailview"] != FRAME_VIEW) {
			echo("<td class=\"scrollbar\" valign=\"bottom\">");				
			$fromto = $strings["l_FromTo"]." ";
			$fromto .= ($page_no + 1) * $this->config["mails_per_page"] + 1;
			$fromto .= " $strings[l_ToM] ";
			$to_no = ($page_no + 2) * $this->config["mails_per_page"];
			if ($mail_count < $to_no) {
				$to_no = $mail_count;
			}
			$fromto .= $to_no;

			if (($page_no + 1) * $this->config["mails_per_page"] < $mail_count) {
				echo("<a href=\"$GLOBALS[PHP_SELF]?action=showfolder&folder=$this->folder&page=next\"><img src=\"graphics/arrowdown.gif\" border=\"0\" title=\"$fromto\" alt=\"$fromto\"></a>");
				echo("<a href=\"$GLOBALS[PHP_SELF]?action=showfolder&folder=$this->folder&page=last\"><img src=\"graphics/last.gif\"  border=\"0\" title=\"$strings[l_LastP]\" alt=\"$strings[l_LastP]\"></a>");		
			}
			else {
				echo('<img src="graphics/arrowdown_dis.gif" alt="" border="0">');
			}
			echo("</td>");
		}
		
?>
	</tr>
</table>
<?php
	// Does the user want to see tha mail in the main window?
	if ($this->config["mailview"] == IN_VIEW) {
?>
<table width="100%" border="0" cellpadding="4" cellspacing="0" class="windowtitle">
<tr>
	<td>
<?php
		$this->show_head();
?>
	</td>
	<td align="right">
<?php	
		$this->show_attachments();
?>
	</td>
</tr>
<?php	
		$this->show_notification_request();
?>
</table>
<form>
<textarea readonly rows="<?php echo($this->config["mail_rows"]);?>" cols="80" style="width: 100%;">
<?php
	$this->show_mail_content()
?>
</textarea>
</form>
<?php
	}
	else if ($this->config["mailview"] == FRAME_VIEW) {
		echo("<iframe src=\"$GLOBALS[PHP_SELF]?action=mail_cont\" scrolling=\"yes\" name=\"mail\" width=\"100%\" style=\"height: ".$this->config["mail_rows"]."em; border-top: medium solid silver;\" frameborder=\"0\" marginwidth=\"0\" marginheight=\"0\"></iframe>");		
	}
?>