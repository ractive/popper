<center>
<h2><?php echo($_SESSION['greeting']);?></h2>
<?php echo($_SESSION['strings']['l_WelcomeLogin']);?>

<form method="get" action="<?php echo($GLOBALS['PHP_SELF']);?>">
	<input class="button" type="submit" name="action" value="<?php echo($_SESSION['strings']['l_Login']);?>"><br>
	<input class="button" type="submit" name="action" value="<?php echo($_SESSION['strings']['l_NewUser']);?>">
</form>
</center>
