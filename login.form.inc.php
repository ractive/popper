<?php global $poppercookie;?>

<center>
<h2>Popper Login</h2>

<form method="post" action="<?php echo("$GLOBALS[PHP_SELF]");?>">
	<table cellpadding="5">
		<tr>
			<td><?php echo($strings["l_LoginName"]);?>:</td>
			<td><input type="text" name="name" value="<?php echo($poppercookie["user"]);?>"><br></td>
		</tr>
		<tr>
			<td><?php echo($strings["l_Pwd"]);?>:</td>
			<td><input type="password" name="pwd"></td>
		</tr>
	</table>
	<input type="checkbox" name="set_cookie"
	<?php
		if ($poppercookie["user"]) {
			echo(" checked");
		}
	?>
	> 
	<?php echo($strings["l_Remember"]."<br><br>");
	if ($status_msg == $strings["l_PwdWrong"]) {
		echo("<a href=\"$GLOBALS[PHP_SELF]?action=lostpwd\" style=\"color: blue;\">$strings[l_LostPwd]</a><br><br>");
	}
	?>
	<input class="button" type="submit" name="login" value="<?php echo($strings["l_Login"]);?>">
</form>
</center>