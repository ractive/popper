<?
/* 
	this page displays the mail list panel ?
*/

// div added Leo
?>
<div class="windowtitle">
<?php echo($this->get_foldername()); ?>
</div>
<?

// commented Leo - unused since we use the scrollingPanel, a CSS simulated iFrame

//		<td class="scrollbar" valign="top">
/* <?php
			// update the page_no.
			$mail_count = $this->count_mails();
			if (isset($GLOBALS[page])) {
				$this->update_pageno($GLOBALS[page], $mail_count);
			}
			$page_no = $this->get_pageno();

			if ($page_no != 0) {
				$fromto = "Show mails ";
				$fromto .= ($page_no - 1) * $this->config["mails_per_page"] + 1;
				$fromto .= " to ";
				$fromto .= $page_no * $this->config["mails_per_page"];
				echo("<a href=\"$GLOBALS[PHP_SELF]?action=showfolder&folder=$this->folder&page=prev\"><img src=\"graphics/arrowup.gif\"  border=\"0\" title=\"$fromto\" alt=\"$fromto\"></a>");
			}
			else {
				echo('<img src="graphics/arrowup_dis.gif" alt="" border="0">');
			}

?>

		</td>
*/
?>
<div class="scrollingPanel">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<tr>
		<td class="mailwindow">
			<?php $this->show_folder_content(); ?>
		</td>
<?
/*
		<td class="scrollbar" valign="bottom">
<?php
			$fromto = "Show mails ";
			$fromto .= ($page_no + 1) * $this->config["mails_per_page"] + 1;
			$fromto .= " to ";
			$to_no = ($page_no + 2) * $this->config["mails_per_page"];
			if ($mail_count < $to_no) {
				$to_no = $mail_count;
			}
			$fromto .= $to_no;

			if (($page_no + 1) * $this->config["mails_per_page"] < $mail_count) {
				echo("<a href=\"$GLOBALS[PHP_SELF]?action=showfolder&folder=$this->folder&page=next\"><img src=\"graphics/arrowdown.gif\" border=\"0\" title=\"$fromto\" alt=\"$fromto\"></a>");
			}
			else {
				echo('<img src="graphics/arrowdown_dis.gif" alt="" border="0">');
			}
?>
		</td>
	*/
?>
	</tr>
</table>
</div>
<?php

/*************************************************
 *
 *	current mail panel
 *
 **************************************************/
?>
<div class="windowtitle" style="height: 40px;">
<table width="100%">
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
</table>
</div>
<form>
<textarea readonly rows="<?php echo($this->config["mail_rows"]);?>" cols="80" style="width: 100%;">
<?php
	$this->show_mail_content()
?>
</textarea>
</form>