<form class="mail" action="<?php echo("$GLOBALS[PHP_SELF]?action=choosen")?>" method="post">
<?php

	$this->show_recipients();
?>
<br>
<center>
	<input class="button" type="submit" value="<?php echo(strtoupper($GLOBALS[field])); ?>" name="recipient">
</center>
<br>
</form>