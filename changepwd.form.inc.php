<center>
<h2><?php echo($_SESSION['strings']['l_ChangePwd']);?></h2>

<form method="post" action="<?php echo($_SERVER['PHP_SELF']);?>">
	<table cellpadding="5">
		<tr>
			<td><?php echo($_SESSION['strings']['l_Pwd']);?>:</td>
			<td><input type="password" name="pwd" value=""><br></td>
		</tr>
		<tr>
			<td><?php echo($_SESSION['strings']['l_ConfPwd']);?>:</td>
			<td><input type="password" name="pwd2"></td>
		</tr>
	</table>
	<input class="button" type="submit" name="changepwd" value="<?php echo($_SESSION['strings']['l_Go']);?>">
	<input type="hidden" name="action" value="changepwd">
</form>
</center>
