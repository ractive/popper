<?php
/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

	// Check with which browser we are called.
	if(ereg("MSIE ([0-9]+)",$HTTP_USER_AGENT)) {
  		// IE
  		$isNS = 0;
	}
	elseif (ereg("Mozilla/([0-4]+)",$HTTP_USER_AGENT)) {
  		// NS
  		$isNS = 1;
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">

<html>

<head>
<title>PHP POP3 Client</title>
<META NAME="resource-type" CONTENT="document">
<META HTTP-EQUIV="author" CONTENT="Jean-Pierre Bergamin">
<META NAME="keywords" CONTENT="php, pop3, pop, client, pop client, popclient, free, freeware, asp">
<META NAME="description" CONTENT="">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="-1">
<?php
// Don't use the style shee for Netsape. It looks crappy...
if ($isNS == 0) {
?>
<LINK REL=stylesheet TYPE="text/css" HREF="style.css">
<?php
}
?>
</head>

<?php
// Use additional body tags instead of stylesheets for Netscape
if ($isNS == 1) {
?>
<body text="#FFFFFF" bgcolor="#000000" LINK="Blue">
<?php
}
else {
?>
<body>
<?php
}
	// From here you can enter what you want.....
?>