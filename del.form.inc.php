<form class="mail" action="<?php echo($_SERVER['PHP_SELF']."?action=deletemails")?>" method="post">
<?php
	$this->show_del_mails();
?>
<br>
<center>
	<input class="button" type="submit" value="<?php echo($_SESSION['strings']['l_Delete']); ?>" name="delete">
</center>
<br>
</form>
