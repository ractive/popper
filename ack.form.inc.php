<center>
<h2><?php echo($_SESSION["strings"]["l_DelAck2"]);?></h2>
<form method="post" action="<?php echo("$GLOBALS[REQUEST_URI]");?>">
	<?php echo($_SESSION["strings"]["l_DelAck"]);?><br><br>
	<input class="button" type="submit" name="ack" value="Yes">&nbsp;
	<input class="button" type="submit" name="ack" value="No">
</form>
</center>
