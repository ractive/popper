<form class="mail" action="<?php echo("$GLOBALS[PHP_SELF]?action=deletemails")?>" method="post">
<?php
	$this->show_del_mails();
?>
<br>
<center>
	<input class="button" type="submit" value="Delete" name="delete">
</center>
<br>
</form>