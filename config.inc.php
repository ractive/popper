<?php
/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

	// The file which describes the upper part of the HTML code
	$top_part = "top.inc.php";
	// The file which describes the bottom part of the HTML code
	$bottom_part = "bottom.inc.php";
	
	// Allow the access to other mail-servers than localhost? true/false
	$only_localhost = false;
	
	// Allow the access to the mail-servers in the array. Add as much as you like.
	// i.e. $allowed_server = array("pop.domain.com", "pop.otherdomain.com", "pop.thirddomain.com");
	// Leave it empty to access ALL mail-servers.
	$allowed_servers = array();
	
	// If this value is set to true, the user needs to confirm the creation of the account
	// He'll receive an E-mail with a link to activate it.
	// Set it to false if you don't need this
	$user_confirmation = true;

	// Set this var only to true if you don't have access to the document-root and popper.inc.php
	// was moved into the document-root
	$in_docroot = false;

	// Login greeting
	$greeting = "Willkommen beim Mailservice von Jameslinux";
	$titlebar = "Weblook - Benutzer ";
	$browsertitle = "Weblook";
?>