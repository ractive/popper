<center>
<h2><?php echo($strings["l_NewFolder"]);?></h2>

<form method="post" action="<?php echo($GLOBALS[PHP_SELF]);?>">
	<table cellpadding="5">
		<tr>
			<td><label for="name" accesskey="N"><?php echo($strings["l_FolderName"]);?>:</label></td>
			<td><input type="text" name="foldername" tabindex="1"><br></td>
		</tr>
	</table>
	<br><hr><br>
	<input class="button" type="submit" name="newfolder" value="<?php echo($strings["l_Create"]);?>" tabindex="4">
	<input type="hidden" name="action" value="storefolder">	
</form>

</center>