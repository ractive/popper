<?php
/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000, 2001, 2003 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

	// Application wide constants
	$_SESSION['POPPER_VERSION'] = "1.42";

	$_SESSION['MAIL_UNREAD'] = 0;
	$_SESSION['MAIL_READ'] = 1;
	$_SESSION['MAIL_REPLIED'] = 2;
	$_SESSION['MAIL_FORWARDED'] = 3;

	$_SESSION['HIGHEST_PRIORITY'] = 1;
	$_SESSION['HIGH_PRIORITY'] = 2;
	$_SESSION['NORMAL_PRIORITY'] = 3;
	$_SESSION['LOW_PRIORITY'] = 4;
	$_SESSION['LOWEST_PRIORITY'] = 5;

	$_SESSION['FULL_VIEW'] = 0;
	$_SESSION['MID_VIEW'] = 1;
	$_SESSION['MIN_VIEW'] = 2;

	$_SESSION['POPUP_VIEW'] = 0;
	$_SESSION['IN_VIEW'] = 1;
	$_SESSION['SEP_VIEW'] = 2;
	$_SESSION['FRAME_VIEW'] = 3;

	// The file which describes the upper part of the HTML code
	$_SESSION['top_part'] = "top.inc.php";

	// The file which describes the bottom part of the HTML code
	$_SESSION['bottom_part'] = "bottom.inc.php";

	// Allow the access to other mail-servers than localhost? true/false
	$_SESSION['only_localhost'] = false;

	// Allow the access to the mail-servers in the array. Add as much as you like.
	// i.e. $allowed_server = array("pop.domain.com", "pop.otherdomain.com", "pop.thirddomain.com");
	// Leave it empty to access ALL mail-servers.
	$_SESSION['allowed_servers'] = array();

	// If this value is set to true, the user needs to confirm the creation of the account
	// He'll receive an E-mail with a link to activate it.
	// Set it to false if you don't need this
	$_SESSION['user_confirmation'] = true;

	// Set this var only to true if you don't have access to the document-root and popper.inc.php
	// was moved into the document-root
	$_SESSION['in_docroot'] = false;

	// Login greeting
	$_SESSION['greeting'] = "Welcome to TestMail @ roytoo.org";
	$_SESSION['titlebar'] = "TestMail - Welcome ";
	$_SESSION['browsertitle'] = "TestMail";

	// Set the config_loaded flag to true
	$_SESSION['config_loaded'] = true;
?>
