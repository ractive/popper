<center>
<h2><?php echo($strings["l_MoveTo"]);?></h2>

<form method="post" action="<?php echo($GLOBALS[PHP_SELF]);?>">
	<table cellpadding="5">
		<tr>
			<td><label for="name" accesskey="N"><?php echo($strings["l_FolderName"]);?>:</label></td>
			<td><?php $this->folder_list();?></td>
		</tr>
	</table>
	<br><hr><br>
	<input class="button" type="submit" name="move_to" value="<?php echo($strings["l_MoveTo"]);?>" tabindex="4">
	<input type="hidden" name="action" value="move_to">	
</form>

</center>