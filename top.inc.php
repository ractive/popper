<?php
/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html>
<head>
<title><?php echo($GLOBALS[browsertitle]);?></title>
<META NAME="resource-type" CONTENT="document">
<META HTTP-EQUIV="author" CONTENT="Jean-Pierre Bergamin">
<META NAME="keywords" CONTENT="php, pop3, pop, client, pop client, popclient, free, freeware, asp">
<META NAME="description" CONTENT="">
<!--<META HTTP-EQUIV="Refresh" content="10;URL=<?php echo($GLOBALS[PHP_SELF]."?action=sendandreceive&what=getall");?>">-->
<!--<meta http-equiv="Content-Type" content="text/html; charset="KOI8-R">-->
<!--<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="-1">-->
<?php
	if ($poppercookie["theme"]) {
		$theme = $poppercookie["theme"];

	}
	else {
		$theme = $this->config["theme"];
	}
?>
<LINK REL=stylesheet TYPE="text/css" HREF="user_style.php?theme=<?php echo($theme);?>">
</head>
<body>
<!-- Enter site-specific HTML-Code here -->