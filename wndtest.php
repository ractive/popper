<?php

	function start_timer($event) {
		printf("timer: %s\n", $event);
		list($low, $high) = split(" ", microtime());
		$t = $high + $low;
		flush();

		return $t;
 	}

	function next_timer($start, $event) {
		list($low, $high) = split(" ", microtime());
		$t    = $high + $low;
    	$used = $t - $start;
    	printf("timer: %s (%8.4f)\n", $event, $used);
    	flush();

		return $t;
	}
	
	$top_part = "top.inc.php";
	$bottom_part = "bottom.inc.php";
	$in_docroot = false;
	$theme = "system";
	
	include("class.list_window.inc.php");
	include("class.db_man.inc.php");
	include("class.mailer.inc.php");


	$lw = new list_window;
	$db_man = new db_man(1);
	
	/*$start = start_timer("Start");		
	



	$query = "SELECT id, tos, from_name as froms, subject, is_mime, date, was_read, priority FROM mails WHERE folder = 'inbox' AND user_id = '1' ORDER BY subject";
	$res = $db_man->db_tool->db_query($query);
	echo("<table>");
	while($row = $db_man->db_tool->fetch_array($res)) {
			echo("<tr ");
			// If the mail was not read, display it bold
			$img = "not_read_mail.gif";
				
			echo(">\n");

			echo("<td style=\"width: 10px; text-align: center;\"><input type=\"checkbox\" name=\"del[]\" value=\"$row[id]\"></td>");
			echo("<td style=\"width: 20px; text-align: center;\">\n");
			if ($row[is_mime]) {
				echo("<img src=\"graphics/attachment_small.gif\" alt=\"$strings[l_Attachment]\" align=\"middle\" border=\"0\" style=\"vertical-align: middle;\">\n");				
			}
			echo("</td><td align=\"center\" style=\"vertical-align: middle\">");
			
			echo("&nbsp;");
						
			echo("</td><td>\n<img src=\"graphics/$img\" alt=\"\" style=\"float: left;\">");
			echo("<a href=\"$GLOBALS[PHP_SELF]?action=showmail&mail_id=$row[id]\">\n");				

			$mailer = new mailer(0);

			$out = htmlspecialchars($row["froms"]);

			echo($out."</a></td>");
			
			$subject = $mailer->charset_decode($row["subject"]);	
			

			echo("<td>".htmlspecialchars($subject));
			$date = mktime(substr($row[date], 8, 2), substr($row[date], 10, 2), substr($row[date], 12, 2), substr($row[date], 4, 2), substr($row[date], 6, 2), substr($row[date], 0, 4));
			$formatted_date = strftime("%x %X", $date);
			echo("</td>\n<td>".$formatted_date."</td>");
			echo("</tr>\n");
	}
	echo("</table>");
	$start = next_timer($start, "FERTIG");		

	$mail_list = $db_man->get_mail_list("inbox", "subject");	
	echo("<table>");
	foreach($mail_list as $mail) {
		$lw->show_mail($mail);
	}
	echo("</table>");*/
	
	//$start = next_timer($start, "FERTIG");	
	$mail_list = $db_man->get_mail_list("inbox", "subject");	
	
	$lw->insert_column(0, "&nbsp;", LEFT);
	$lw->insert_column(1, "<img src=\"graphics/attachment_small.gif\" alt=\"$strings[l_Attachment]\" align=\"middle\" border=\"0\" style=\"margin: 0, 3px, 0, 3px;\">\n", CENTER);
	$lw->insert_column(2, "From", LEFT);		
	$lw->insert_column(3, "Subject", LEFT);	
	$lw->insert_column(4, "Date", LEFT);			
	$i = 0;
	foreach($mail_list as $row) {
		$lw->insert_item($i, 0, "<input type=\"checkbox\" name=\"del[]\" value=\"$row[id]\">");
		if ($row[is_mime]) {
			$lw->insert_item($i, 1, "<img src=\"graphics/attachment_small.gif\" alt=\"$strings[l_Attachment]\" align=\"middle\" border=\"0\" style=\"vertical-align: middle;\">\n");			
		}
		else {
			$lw->insert_item($i, 1, "&nbsp;");					
		}
		
		$img = "not_read_mail.gif";
		$lw->insert_item($i, 2, "<img src=\"graphics/$img\" alt=\"\" style=\"float: left;\"><a href=\"$GLOBALS[PHP_SELF]?action=showmail&mail_id=$row[id]\">\n".htmlspecialchars($row["froms"])."</a>");
		$mailer = new mailer(0);
		$subject = $mailer->charset_decode($row["subject"]);			
		$lw->insert_item($i, 4, $subject);
		$date = mktime(substr($row[date], 8, 2), substr($row[date], 10, 2), substr($row[date], 12, 2), substr($row[date], 4, 2), substr($row[date], 6, 2), substr($row[date], 0, 4));
		$formatted_date = strftime("%x %X", $date);		
		$lw->insert_item($i, 5, $formatted_date);
		$i++;
	}
	
	$lw->show();
	//$start = next_timer($start, "FERTIG");	
	
	/*$start = start_timer("Start");
	for($i = 0; $i < 100; $i++) {
		$lw->set_title("LISTE");
		$lw->set_status("STATUS");
		
		$lw->insert_column(0, "FROM");
		$lw->insert_column(1, "TO");
		$lw->insert_column(2, "DATE");
		
		$lw->insert_item(0, 0, "mir");	
		$lw->insert_item(0, 1, "dir");
		$lw->insert_item(0, 2, "jetzt");
		$lw->insert_item(1, 0, "mir2");	
		$lw->insert_item(1, 1, "dir2");
		$lw->insert_item(1, 2, "jetzt2");		
		$lw->insert_item(2, 0, "mir3");	
		$lw->insert_item(2, 1, "dir3");
		$lw->insert_item(2, 2, "jetzt3");		
		$lw->insert_item(3, 0, "mir4");	
		$lw->insert_item(3, 1, "dir4");
		$lw->insert_item(3, 2, "jetzt4");		
		$lw->insert_item(4, 0, "mir5");	
		$lw->insert_item(4, 1, "dir5");
		$lw->insert_item(4, 2, "jetzt5");							
		$lw->show();
	}
	$start = next_timer($start, "FERTIG");
	for($i = 0; $i < 100; $i++) {
		$lw = new list_window;
		$lw->show_raw();
	}
	next_timer($start, "FERTIG 2");*/
?>