<tr class="window">
	<td colspan="3">
		<table class="childwindow" align="center">
			<tr class="titlebar">
				<td>
					<table class="titlebar" width="100%" border="0">
						<tr>
							<td style="text-align: left;">
<?php
								if (empty($title)) {
									echo("&nbsp;");
								}
								else {
									echo("$title");
								}
?>
							</td>
							<td><a href="<?php echo("$GLOBALS[PHP_SELF]?action=cancel");?>"><img src="graphics/logoff.gif" alt="<?php echo($strings["l_Cancel"]);?>" title="<?php echo($strings["l_Cancel"]);?>" align="right" border="0"></a></td>
						</tr>
					</table>
				</td>
			</tr>
<?php
		if(file_exists($form.".toolbar.inc.php")) {
			include($form.".toolbar.inc.php");
		}
?>
			<tr>
				<td>
					<?php include($form.".form.inc.php");?>
				</td>
			</tr>
		</table>
	</td>
</tr>
			