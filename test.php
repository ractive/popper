<?php
//include("class.db_tool.inc.php");
//include("class.mailer.inc.php");
include("class.popper.inc.php");

$mailer = new mailer(1);
$pop = new popper;
$mailer->user_id = 1;

$pop->cur_message_id = 13;
$pop->cur_mailer = 0;
$pop->userid = 1;
$mail_id = 13;

if (!$mailer->load(13)) {
	echo("\r\n\r\n  ======== ERROR while trying to get mail! ========");
}
else {
	echo("Extract Part<br>");
	//echo(nl2br($mailer->get_text_body()));
	$mailer->extract_part(1);
	//$mailer->print_array($mailer->mimeparts[1]->headers->header_arr["content-type"]);
	//echo($mailer->mimeparts[1]->mimeparts[1]->decode($mailer->mimeparts[1]->mimeparts[1]->body));	
	//echo(nl2br($mailer->get_attachment(1)));
	$pop->show_attachments();
}

?>