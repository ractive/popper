	<tr class="toolbar">
		<td colspan="3">
		<img src="graphics/toolbar_left.gif" alt="|">
		<?php 
			if ($this->config["mailview"] != FRAME_VIEW) {
				echo("<a href=\"$GLOBALS[PHP_SELF]?action=showfolder&folder=$this->folder\"><img src=\"graphics/home.gif\" alt=\"".$_SESSION["strings"]["l_MainView"]."\" title=\"".$_SESSION["strings"]["l_MainView"]."\" border=\"0\"></a>");
				echo("<img src=\"graphics/spacing.gif\" alt=\"|\">");
			}
		?>

		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=newaddress"); ?>"><img src="graphics/newaddress.gif" alt="<?php echo($_SESSION["strings"]["l_NewAddress"]);?>" title="<?php echo($_SESSION["strings"]["l_NewAddress"]);?>" border="0"></a>
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=newaddress&update=1"); ?>"><img src="graphics/properties.gif" alt="<?php echo($_SESSION["strings"]["l_EditAddress"]);?>" title="<?php echo($_SESSION["strings"]["l_EditAddress"]);?>" border="0"></a>
		<img src="graphics/spacing.gif" alt="|">
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=deladdress");?>"><img src="graphics/cancel.gif" alt="<?php echo($_SESSION["strings"]["l_DelAddress"]);?>" title="<?php echo($_SESSION["strings"]["l_DelAddress"]);?>" border="0"></a>
		<img src="graphics/spacing.gif" alt="|">
<?php
		if ($GLOBALS[show] == "blocked") {
?>
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=addressbook");?>"><img src="graphics/address.gif" alt="<?php echo($_SESSION["strings"]["l_AddressBook"]);?>" title="<?php echo($_SESSION["strings"]["l_AddressBook"]);?>" border="0"></a>
<?php	
		}
		else {
?>		
		<a href="<?php echo("$GLOBALS[PHP_SELF]?action=addressbook&show=blocked");?>"><img src="graphics/block_big.gif" alt="<?php echo($_SESSION["strings"]["l_ShowBlocked"]);?>" title="<?php echo($_SESSION["strings"]["l_ShowBlocked"]);?>" border="0"></a>
<?php		
	}
?>
		</td>
	</tr>
