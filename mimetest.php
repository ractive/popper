<?php
	include("class.db_tool.inc.php");
	include("class.mailer.inc.php");
	
	/*$path = "mail.txt";
	$fp = fopen($path, "rb");
	$mail = fread($fp, filesize($path));

	$mime = new mailer(1, 1);
	$mime->from_text(&$mail);
	
	$is_mime = $mime->is_mime();
	//echo $is_mime ? "YES" : "NO";
	
	$body = $mime->get_text_body();
	echo(nl2br($body));*/
	
	/*$att = 5;
	$name = $mime->get_attachment_name($att);

	//$mime->get_text_body($body);	
	echo($mime->get_attachment($att));*/
	//	echo($mime->get_attachment(3));
	
	//$next = $mime->next_timer($start, "extract_header 2");
	
	
	//$mime->next_timer($next, "EHA");
	
	
	$mailer = new mailer(1, 1);
	//$mailer->create("james@localhost", "james@ractive.ch", "Test", "Test\r\n\r\nalskdjfl");
	//$mail->attach_file("graphics/gd92.gif", "gd92.gif", "image/gif");
	//$mail->attach_file("graphics/cal.jpg", "cal.jpg", "image/jpeg");	
	//$mail->send();
	//$body = $mailer->generate_body();
	//echo(nl2br($body));	
	//$mailer->store("inbox");
	//unset($mailer);
	//$mailer = new mailer(1, 1);
	$mailer->load(27);
	echo("<h1>Subject</h1>");
	$sub = $mailer->headers["Subject"];
	echo $sub."<br>";
	echo(nl2br($mailer->charset_decode($sub)));
	//$body = $mailer->generate_body();
	/*$mailer->load(1);
	$data = $mailer->get_attachment(4);
	echo $mailer->part_count;*/
	//echo($data);
	/*Header("Content-Disposition: inline; filename=\"gd.jpg\"");
	Header("Content-Type: image/jpeg; name=\"gd.jpg\"");	
	echo $data;*/
	//$mailer->send();
	//echo(nl2br($body));	
?>