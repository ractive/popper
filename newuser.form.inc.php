<center>
<h2>Create new user</h2>
<h5>The login-name you provide here has nothing to do with the POP3 username. It's just for the identification for this site.</h5>

<form method="post" action="<?php echo($GLOBALS[PHP_SELF]);?>">
	<table cellpadding="5">
		<tr>
			<td><label for="name" accesskey="N">Username:</label></td>
			<td><input type="text" name="name" tabindex="1"><br></td>
		</tr>
		<tr>
			<td><label for="email" accesskey="E">E-Mail address:</label></td>
			<td><input type="text" name="email" tabindex="2"><br></td>
		</tr>		
		<tr>
			<td><label for="pwd" accesskey="P">Password:</label></td>
			<td><input type="password" name="pwd" tabindex="3"></td>
		</tr>
		<tr>
			<td><label for="pwd2" accesskey="C">Confirm password:</label></td>
			<td><input type="password" name="pwd2" tabindex="4"></td>
		</tr>
	</table>
	<h5>
	Make sure that the E-Mail address you enter here is valid! You will get a mail where you must confirm your subscription!
	</h5>
	<br><hr><br>
	<input class="button" type="submit" name="newuser" value="Create" tabindex="4">
</form>

</center>