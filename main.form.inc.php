<tr class="window">
	<td width="25%" valign="top">
		<?php $this->show_folders() ?>
		<p class="windowtitle">
			<a href="<?php echo("$GLOBALS[PHP_SELF]?action=newaddress");?>">Add contact...</a>
		</p>
		<?php $this->show_addresses() ?>
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