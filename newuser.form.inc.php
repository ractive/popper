<center>
<h2><?php echo($strings[l_NewUser]);?></h2>

<form method="post" action="<?php echo($GLOBALS[PHP_SELF]);?>">
	<table cellpadding="5">
		<tr>
			<td><label for="name" accesskey="N"><?php echo($strings["l_UserName"]);?>:</label></td>
			<td><input type="text" name="name" tabindex="1"><br></td>
		</tr>
		<tr>
			<td><label for="email" accesskey="E"><?php echo($strings["l_EMailAddress"]);?>:</label></td>
			<td><input type="text" name="email" tabindex="2"><br></td>
		</tr>		
		<tr>
			<td><label for="pwd" accesskey="P"><?php echo($strings["l_Pwd"]);?>:</label></td>
			<td><input type="password" name="pwd" tabindex="3"></td>
		</tr>
		<tr>
			<td><label for="pwd2" accesskey="C"><?php echo($strings["l_ConfPwd"]);?>:</label></td>
			<td><input type="password" name="pwd2" tabindex="4"></td>
		</tr>
	</table>
	<h5>
	<?php echo($strings["l_NewUserMsg"]);?>
	</h5>
	<br><hr><br>
	<input class="button" type="submit" name="newuser" value="Create" tabindex="4">
</form>

</center>