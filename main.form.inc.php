<tr class="window">
<?php
	//$this->load_config();
	
	if ($this->config["view"] == $_SESSION['MIN_VIEW']) {
		$width = "1%";
		$style = "style=\"background-color: gray;\"";
	}
	else {
		$width = $this->config["folder_view_width"]."%";
	}
	
	echo("<td width=\"$width\" valign=\"top\" $style>");

		$this->show_folders();
		// Show the addresslist only in $_SESSION['FULL_VIEW']
		if ($this->config["view"] == $_SESSION['FULL_VIEW']) {
?>
		
			<p class="windowtitle">
				<?php echo("<a href=\"$GLOBALS[PHP_SELF]?action=newaddress\">".$_SESSION["strings"]["l_AddContact"]."</a>");?>
			</p>
			<?php $this->show_addresses() ?>
<?php
		}
?>
	</td>
	<td width="<?php echo(100 - $this->config["folder_view_width"])?>%">
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
