<center>
<h2>Popper Login</h2>

<form method="post" action="<?php echo("$GLOBALS[PHP_SELF]");?>">
	<table cellpadding="5">
		<tr>
			<td>Loginname:</td>
			<td><input type="text" name="name"><br></td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><input type="password" name="pwd"></td>
		</tr>
	</table>
	<input class="button" type="submit" name="login" value="Login">
</form>
</center>