<?php

class gui {
	
	var $view;
	var $css;
	
	function gui($view, $use_css) {
		$this->view = $view;
		$this->css = $use_css;
	}
	
	function report_error($error) {
		die($error);
	}
	
	
	function report_dberror($error) {
		$this->report_error($error);
	}

	function show_in_window($form, $title="", $status_msg="", $msgboxtext = "", $msgboxhead="") {
		global $top_part, $bottom_part, $strings, $action, $poppercookie;
		
		if ($this->view == FRAME_VIEW && $form != "config" && $form != "changepwd" && $form != "newmail") {
			$this->show_small_window($form, $title, $status_msg, $msgboxtext, $msgboxhead);
			return;
		}
		
		/*if ($form != "choose" && $action != "choosen") {
			$this->set_cur();
		}*/

		include($top_part);
		include("titlebar.inc.php");
		include("childwindow.inc.php");
		include("statusbar.inc.php");
		include($bottom_part);
	}

	function show_small_window($form, $title = "", $status_msg = "", $msgboxtext = "", $msgboxhead = "") {
		global $strings;

		$this->frame_header();
		echo("<table class=\"windowborder\" width=\"50%\" border=\"0\" align=\"center\" style=\"margin-top: 2em;\">");
		echo("<tr class=\"titlebar\"><td>$title</td></tr>");
		echo("<tr class=\"childwindow\"><td>");
		if (file_exists($form.".toolbar.inc.php")) {	
			include($form.".toolbar.inc.php");
		}
		include($form.".form.inc.php");
		echo("</td></tr></table>");					
		$this->frame_footer();
	}

	function show_form($form, $status_msg = "") {
		global $account_id, $addr_id, $strings, $action, $poppercookie;

		if (isset($account_id) && !empty($account_id)) {
			$this->cur_account_id = $account_id;
		}
		if (isset($addr_id) && !empty($addr_id)) {
			$this->cur_address_id = $addr_id;
		}
		global $top_part, $bottom_part;
		if ($this->config["mailview"] == FRAME_VIEW && ($form == "addressbook" || $form == "accounts")) {
			$this->frame_header();
			echo("<table class=\"windowborder\" width=\"100%\" border=\"0\" align=\"center\">");
		}
		else {
			include($top_part);
			include("titlebar.inc.php");
		}
		if(file_exists($form.".toolbar.inc.php")) {
			if ($this->folder == "cal") {
				include("cal.toolbar.inc.php");				
			}
			else {
				include($form.".toolbar.inc.php");
			}
		}
		include($form.".form.inc.php");
		if ($this->config["mailview"] == FRAME_VIEW && ($action == "addressbook" || $action == "accounts")) {
			echo("</table></form>");
			$this->frame_footer();
		}		
		else {
			include("statusbar.inc.php");
			include($bottom_part);
		}
	}

	function show_addresses($addr, $selected_id, $blocked) {
		global $strings;
		
		if ($blocked) {
			echo("<h3>".$strings["l_Blocked"]."</h3>");
		}
		
		echo("<table>\n");
		echo("<tr><th align=\"left\" width=\"50%\">Name</th><th align=\"left\">E-Mail</th></tr>\n");
		if (is_array($addr)) {
			foreach($addr as $add) {
				echo("<tr");
				if ($selected_id == $add["id"]) {
					echo(' class="selected"');
				}				
				echo("><td>\n");
				echo("<a href=\"$GLOBALS[PHP_SELF]?action=addressbook&addr_id=$add[id]");
				if ($blocked) {
					echo("&show=blocked");
				}
				echo("\" title=\"$strings[l_ShowDetails]\"");
				echo(">$add[name]</a></td>\n");
				echo("<td><a href=\"$GLOBALS[PHP_SELF]?action=newmail&addr_id=$add[id]\" title=\"$strings[l_SendMail]\" target=\"_parent\">$add[email]</a></td></tr>\n");
			}
		}
		
		echo("</table>\n");
	}
	
	function show_accounts($accs, $selected_acc) {
		global $strings;
		
		echo("<table width=\"100%\">\n");
		echo("<tr>");

		echo("<th align=\"left\">$strings[l_Name]</th><th align=\"left\">$strings[l_Server]</th></tr>\n");
		if (is_array($accs)) {
			foreach($accs as $acc) {
				echo("<tr");
				if ($selected_acc == $acc["id"]) {
					echo(' class="selected"');
				}
				echo("><td>\n");
				echo("<a href=\"$GLOBALS[PHP_SELF]?action=accounts&account_id=$acc[id]\" title=\"$strings[l_ShowDetails]\"");
				//$this->account_selected($acc[id]);
				echo(">$acc[name]</a></td>\n");
				echo("<td>$acc[popserver]</td></tr>\n");
			}
		}
		echo("</table>\n");
	}

	function show_address($addr) {
		global $strings;
		
		if (!isset($addr["name"])) {
			echo("$strings[l_ClickName]<br><br><br><br><br><br><br><br><br><br><br>");
			return;
		}
		
?>
		<table cellpadding="3" cellspacing="3">
			<tr>
				<td><b><?php echo($strings[l_Name]); ?>:</b></td>
				<td><?php echo($addr["name"]); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_LastName]); ?>:</b></td>
				<td><?php echo($addr["lastname"]); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_NickName]); ?>:</b></td>
				<td><?php echo($addr["nickname"]); ?></td>
			</tr>
			<tr>
				<td><b>E-Mail:</b></td>
				<td><?php echo($addr["email"]); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_Address]); ?> 1:</b></td>
				<td><?php echo($addr["address1"]); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_Address]); ?> 2:</b></td>
				<td><?php echo($addr["address2"]); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_ZIP]); ?>:</b></td>
				<td><?php echo($addr["zip"]); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_Town]); ?>:</b></td>
				<td><?php echo($addr["town"]); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_State]); ?>:</b></td>
				<td><?php echo($addr["state"]); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_Country]); ?>:</b></td>
				<td><?php echo($addr["country"]); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_Phone]); ?>:</b></td>
				<td><?php echo($addr["phone"]); ?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_MobilePhone]); ?>:</b></td>
				<td><?php echo($addr["mobile"]); ?></td>
			</tr>
			<tr>
				<td><b>Homepage:</b></td>
				<td>
				<?php
					if (!empty($addr[homepage])) {
						echo("<a href=\"$addr[homepage]\">$addr[homepage]</a>");
					}
				?>
				</td>
			</tr>
		</table>
<?php	
	}
	
	function show_account($acc) {
		global $strings;
		
		if (!isset($acc["name"])) {
			echo("$strings[l_ClickName]<br><br><br><br><br><br><br><br><br><br><br>");
			return;
		}		
		
?>
		<table cellpadding="3" cellspacing="3">
			<tr>
				<td><b><?php echo($strings[l_AccountName]); ?></b>:</td>
				<td><?php echo($acc["name"]);?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_From]." ".$strings[l_Name]); ?></b>:</td>
				<td><?php echo($acc["fromname"]);?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_ReplyTo]); ?></b>:</td>
				<td><?php echo($acc["replyto"]);?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_POPServer]); ?></b>:</td>
				<td><?php echo($acc["popserver"]);?></td>
			</tr>
			<tr>
				<td><b><?php echo($strings[l_POPLogin]); ?></b>:</td>
				<td><?php echo($acc["poplogin"]);?></td>
			</tr>
		</table>
		<br><br><br>
<?php
	}
	
	
	function extract_disp_name($name) {
		// Check for an address like "Foobar" <foo@bar.com>
		// and only return the Name part to display
		$name = stripslashes($name);
		$mail = new mailer(0);
		// Decode the charset
		$cont = $mail->charset_decode($name);
		
		// Strip out header comments
		$cont = $mail->strip_comment($cont, $comment);

		// Get the parts that are either quoted or in angle brackets
		preg_match("/(\"(.*?)\"\s*)*<(.*?)>/", $cont, $matches);

		// We prefer the quoted representation
		if (!empty($matches[2])) {
			return $matches[2];
		}
		// Then the string in brackets
		if (!empty($matches[3])) {
			return $matches[3];
		}
		// If nothing did match, we just return the original header content

		return $cont;
	}	
	
	function show_recipients($rec) {
		global $strings;
		
		echo('<table width="100%" align="center" border="1">');
		echo("<tr><th>&nbsp;</th><th>".$strings[l_Name]."</th><th>E-Mail</th></tr>");
		if (is_array($res)) {
			foreach($rec as $row) {
				echo("<tr>\n<td>\n");
				echo('<input type="checkbox" name="rec[]" value="'.$row[id].'"></td>');
				echo("<td>".htmlspecialchars($row[name])."</td><td>");
				echo(htmlspecialchars($row[email])."</td></tr>\n");
			}
		}
		echo('</table>');
	}
}

?>