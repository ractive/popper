<tr class="window">
	<td width="300" valign="top">
		<?php $this->show_folders() ?>
		<p class="windowtitle">
			<?php echo("<a href=\"$GLOBALS[PHP_SELF]?action=newaddress\">$strings[l_AddContact]</a>");?>
		</p>
		<?php $this->show_addresses() ?>
	</td>
	<td width="800">
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