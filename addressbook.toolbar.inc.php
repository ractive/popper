	<tr class="toolbar">
		<td colspan="3">
		<img src="graphics/toolbar_left.gif" alt="|">
		<?php echo("<a href=\"$GLOBALS[PHP_SELF]?action=showfolder&folder=$this->folder\"><img src=\"graphics/home.gif\" alt=\"$strings[l_MainView]\" title=\"$strings[l_MainView]\" border=\"0\"></a>");?>
		<img src="graphics/spacing.gif" alt="">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=newaddress"); ?>"><img src="graphics/newaddress.gif" alt="<?php echo($strings["l_NewAddress"]);?>" title="<?php echo($strings["l_NewAddress"]);?>" border="0"></a>
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=newaddress&update=1"); ?>"><img src="graphics/properties.gif" alt="<?php echo($strings["l_EditAddress"]);?>" title="<?php echo($strings["l_EditAddress"]);?>" border="0"></a>
		<img src="graphics/spacing.gif" alt="">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=deladdress"); ?>"><img src="graphics/cancel.gif" alt="<?php echo($strings["l_DelAddress"]);?>" title="<?php echo($strings["l_DelAddress"]);?>" border="0"></a>
		</td>
	</tr>