<center>
<?php
	echo("<h2>".nl2br($msgboxhead)."</h2>");
	echo("<br>");
	echo(nl2br($msgboxtext));
?>
<br>
<br>
<form action=<?php echo($GLOBALS[PHP_SELF]);?> method="get">
<input class="button" type="submit" name="ok" value="OK">
</form>
</center>
