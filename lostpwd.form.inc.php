<center>
<h2><?php echo($_SESSION["strings"]["l_LostPwd"]);?></h2>

<form method="post" action="<?php echo("$GLOBALS[PHP_SELF]?action=sendpwd");?>">
	<table cellpadding="5">
		<tr>
			<td><?php echo($_SESSION["strings"]["l_UserName"]);?>:</td>
			<td><input type="text" name="user" value=""><br></td>
		</tr>
	</table>
	<br>
	<br>
	<?php echo($_SESSION["strings"]["l_LostPwdEx"]);?>
	<br>
	<br>
	<input class="button" type="submit" name="sendpwd" value="<?php echo($_SESSION["strings"]["l_Send"]);?>">
</form>
</center>
