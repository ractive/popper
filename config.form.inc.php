<form class="mail" action="<?php echo("$GLOBALS[PHP_SELF]?action=config")?>" method="post">
<?php
	$this->show_config();
?>
<br>
<hr>
<center>
<input class="button" type="submit" name="conf_submit" value="Store">&nbsp;
<input class="button" type="reset" value="Reset">
</center>
</form>