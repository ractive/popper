<form class="mail" action="<?php echo("$GLOBALS[PHP_SELF]?action=config")?>" method="post">
<?php
	$this->show_config();
?>
<br>
<center>
<input class="button" type="submit" name="conf_submit" value="<?php echo($strings["l_Store"]);?>">&nbsp;
<input class="button" type="reset" value="<?php echo($strings["l_Reset"]);?>">
</center>
</form>