<form class="mail" action="<?php echo("$GLOBALS[PHP_SELF]?action=storefolder")?>" method="post">
<center>
<p>Enter the name of the new folder.</p>
<p>The parent folder is <b><?php echo($this->get_foldername($GLOBALS["parent"])); ?></b></p>
<input name="foldername" size="30">
<br>
<hr>
<br>
<input class="button" type="submit" name="conf_submit" value="Store">&nbsp;
<input class="button" type="reset" value="Reset">
<input type="hidden" name="parent" value="<?php echo($GLOBALS["parent"]); ?>">
</center>
</form>