<center>
<h2><?php echo($GLOBALS["greeting"]);?></h2>
<?php echo($strings["l_WelcomeLogin"]);?>

<form method="get" action="<?php echo("$GLOBALS[PHP_SELF]");?>">
	<input class="button" type="submit" name="login" value="<?php echo($strings["l_Login"]);?>"><br>
	<input class="button" type="submit" name="newuser" value="<?php echo($strings["l_NewUser"]);?>">
</form>
</center>