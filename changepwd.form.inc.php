<center>
<h2><?php echo($strings["l_ChangePwd"]);?></h2>

<form method="post" action="<?php echo("$GLOBALS[PHP_SELF]");?>">
	<table cellpadding="5">
		<tr>
			<td><?php echo($strings["l_Pwd"]);?>:</td>
			<td><input type="password" name="pwd" value=""><br></td>
		</tr>
		<tr>
			<td><?php echo($strings["l_ConfPwd"]);?>:</td>
			<td><input type="password" name="pwd2"></td>
		</tr>
	</table>
	<input class="button" type="submit" name="changepwd" value="<?php echo($strings["l_Go"]);?>">
	<input type="hidden" name="action" value="changepwd">
</form>
</center>