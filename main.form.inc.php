<tr class="window">
<?php
	if ($this->config["view"] == MIN_VIEW) {
		$width = "1%";
		$style = "style=\"background-color: gray;\"";
	}
	else {
		$width = "25%";
	}
	
	echo("<td width=\"$width\" valign=\"top\" $style>");
		echo("<p class=\"windowtitle\">");
		echo($strings["l_Folders"]);
		echo("</p>");
		$this->show_folders();
		// Show the addresslist only in FULL_VIEW
		if ($this->config["view"] == FULL_VIEW) {
?>
		
			<p class="windowtitle">
				<?php echo("<a href=\"$GLOBALS[PHP_SELF]?action=newaddress\">$strings[l_AddContact]</a>");?>
			</p>
			<?php $this->show_addresses() ?>
<?php
		}
?>
	</td>
	<td width="75%">
<?php
	if ($this->folder == "cal") {
		include("cal.form.inc.php");
	}
	else {
		include("mail.form.inc.php");
	}
?>
	</td>
</tr>