<p style="text-align: right; font-size: smaller;">
<a href="http://www.ractive.ch/gpl/popper.html" style="color: #000000;">
popper</a> <?php 
	if (is_object($_SESSION['popper'])) {
		echo $_SESSION['popper']->get_version();
	}
	else {
		echo "popper is not an object \n";
	} ?> (C) by J.P. Bergamin
</p>
</body>
</html>
