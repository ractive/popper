<form class="mail" action="<?php echo($_SERVER['PHP_SELF']."?action=choosen")?>" method="post">
<?php

	$this->show_recipients();
?>
<br>
<center>
	<input class="button" type="submit" value="<?php echo(strtoupper($_SESSION['DATA']['field'])); ?>" name="recipient">
</center>
<br>
</form>
