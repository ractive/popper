/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" style="border-bottom: medium solid silver;">
	<tr>
		<td class="windowtitle">
			<?php echo($this->get_foldername()); ?>
		</td>
		<td class="scrollbar" valign="top">
<?php
			// update the page_no.
			$mail_count = $this->count_mails();
			if (isset($GLOBALS[page])) {
				$this->update_pageno($GLOBALS[page], $mail_count);
			}
			$page_no = $this->get_pageno();

			if ($page_no != 0) {
				$fromto = "Show mails ";
				$fromto .= ($page_no - 1) * $this->mails_per_page + 1;
				$fromto .= " to ";
				$fromto .= $page_no * $this->mails_per_page;
				echo("<a href=\"$GLOBALS[PHP_SELF]?action=showfolder&folder=$this->folder&page=prev\"><img src=\"graphics/arrowup.gif\"  border=\"0\" title=\"$fromto\" alt=\"$fromto\"></a>");
			}
			else {
				echo('<img src="graphics/arrowup_dis.gif" alt="" border="0">');
			}
?>

		</td>
	</tr>
	<tr>
		<td class="mailwindow">
			<?php $this->show_folder_content(); ?>
		</td>
		<td class="scrollbar" valign="bottom">
<?php
			$fromto = "Show mails ";
			$fromto .= ($page_no + 1) * $this->mails_per_page + 1;
			$fromto .= " to ";
			$to_no = ($page_no + 2) * $this->mails_per_page;
			if ($mail_count < $to_no) {
				$to_no = $mail_count;
			}
			$fromto .= $to_no;

			if (($page_no + 1) * $this->mails_per_page < $mail_count) {
				echo("<a href=\"$GLOBALS[PHP_SELF]?action=showfolder&folder=$this->folder&page=next\"><img src=\"graphics/arrowdown.gif\" border=\"0\" title=\"$fromto\" alt=\"$fromto\"></a>");
			}
			else {
				echo('<img src="graphics/arrowdown_dis.gif" alt="" border="0">');
			}
?>
		</td>
	</tr>
</table>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" class="windowtitle">
<tr>
	<td>
<?php
		$this->show_head();
?>
	</td>
	<td>
<?php	
		$this->show_attachments();
?>
	</td>
</tr>
</table>
<form>
<textarea readonly rows="<?php echo($this->mail_rows);?>" style="width: 100%;">
<?php
	$this->show_mail_content()
?>
</textarea>
</form>