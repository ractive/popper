/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

<?php
	function is_logged_in() {
		global $logged_in;
		return (isset($logged_in) && session_is_registered("logged_in") && $logged_in == 1);
	}

	include("class.popper.inc.php");

	session_start();
	if (!isset($popper)) {
		$popper = new popper;
	}

	if (isset($login)) {

		if (!isset($login) || !isset($pwd)) {
				$popper->show_in_window("login", "Login");
			return;
		}

 		if (!$popper->pwd_check($name, $pwd)) {
			$popper->show_in_window("login", "Login", "Your password was not correct. Please try again");
 			return;
 		}

		if (!$popper->is_activated($name)) {
			$popper->messagebox("Your account is not activated yet! Please follow the instructions you got by mail (to the mail address you entered during the account creation)", "Account not activated", "Account not activated", "Welcome");		
			return;			
		}

		// The password was ok. Now we start the session
		$logged_in = 1;
		$popper->set_user($name);
		session_register("popper", "logged_in");

		$popper->show();
	}
	else if (isset($newuser)) {
		if ($popper->create_user()) {
			$popper->messagebox("Your account was created. Check your mail and confirm the subscription", "Account created", "Account created", "Welcome");		
		}
	}
	else if (isset($check)) {
		// The user calle the confirmation link
		if ($popper->check_confirmation($check)) {
			// The check was successful
			$popper->messagebox("The confirmation was done! You now can login to your account", "Confirmation successful", "Confirmation successful", "Welcome");
		}
		else {
			// The checkID could not be confirmed
			$popper->messagebox("The confirmation could not be done! Make sure you called this site with the whole internet address", "Confirmation not successful", "Confirmation not successful", "Try again");
		}
	}
	else if (is_logged_in()) {
		if (isset($deluser)) {
			if (!isset($ans)) {
				$popper->show_form("deluserack");
			}
			if ($ans == "No") {
				$popper->show_form("main", "User deletion cancelled!");
			}
			if (!$popper->delete_user()) {
				$popper->messagebox("The user ".$popper->get_user()." could not be deleted from the database.<br>\nPlease try again!\n", "Error");
			}
			else {
				session_destroy();
				$popper->messagebox("The user ".$popper->get_user()." has been deleted and all subscriptions has been cancelled!", "User deleted");
			}
		}

		if (isset($action)) {
			if ($action == "showfolder") {
				$popper->show($folder);
			}
			else if ($action == "showsources") {
				$popper->show_sources();
			}
			else if ($action == "logoff") {
				session_destroy();
				$popper->messagebox("Thanks for your visit and come back soon ;-)", "Logoff", "$popper->name logged off.", "Good bye");
				return;
			}
			else if ($action == "newmail") {
				$popper->show_in_window("newmail", "New mail");
			}
			else if ($action == "reply") {
				$popper->show_in_window("newmail", "New mail");
			}
			else if ($action == "replytoall") {
				$popper->show_in_window("newmail", "New mail");
			}
			else if ($action == "forward") {
				$popper->show_in_window("newmail", "New mail");
			}
			else if ($action == "showmail") {
				$popper->show_in_window("newmail", "Show mail");
			}
			else if ($action == "choosen") {
				$popper->show_in_window("newmail", "New mail");
			}			
			else if ($action == "cancel") {
				if (isset($old_uri)) {
					Header("Location: $old_uri");
				}
				else {
					$popper->show_form("main");
				}
				return;
			}
			else if ($action == "accounts") {
				$popper->show_form("accounts");
			}
			else if ($action == "addressbook") {
				$popper->show_form("addressbook");
			}
			else if ($action == "newaccount") {
				$popper->show_in_window("newaccount", "Account", "Please fill in all fields!");
			}
			else if ($action == "newaddress") {
				$popper->show_in_window("newaddress", "Address");
			}
			else if ($action == "storemail") {
				$msg = $popper->store_mail();
				$popper->show_form("main", $msg);
			}
			else if ($action == "storeaccount") {
				$popper->store_account();
				$popper->show_form("accounts");
			}
			else if ($action == "storeaddress") {
				$popper->store_address();
				$popper->show_form("addressbook");
			}
			else if ($action == "deladdress") {
				$popper->del_address();
			}
			else if ($action == "delaccount") {
				$popper->del_account();
			}
			else if ($action == "sendandreceive") {
				if (!isset($what)) {
					$popper->show_in_window("sendreceive", "Send and receive");
				}
				else {
					$popper->send_and_receive();
				}
			}
			else if ($action == "getattachment") {
				$popper->get_attachment();
			}
			else if ($action == "config") {
				if (isset($GLOBALS[conf_submit])) {
					$popper->store_config();
					$popper->show();
				}
				else {
					$popper->show_in_window("config", "Configuration");
				}
			}			
			else if ($action == "deletemail") {
				$popper->delete_mail();
			}
			else if ($action == "deletemails") {
				$popper->delete_mails();
			}
			else {
				$popper->show();
			}
		}
		else {
			$popper->show();
		}
	}
	else {
		if (count($GLOBALS[HTTP_GET_VARS]) > 0) {
			// We keep the request information in case the user called us
			// with parameters. This is especially useful if the
			// user wants to answer a mail, but first hat to login.
			$request = $GLOBALS[REQUEST_URI];
			session_register("request");
		}
		$popper->show_in_window("home", "Welcome");
	}
	$cururi = getenv("REQUEST_URI");
	if ($uri != $cururi) {
		$old_uri = $uri;
	}
	$uri = $cururi;
	session_register("old_uri");
	session_register("uri");
	/*echo("URI: $uri<br>");
	echo("OLD_URI: $old_uri<br>");
	echo("cur_message_id: $popper->cur_message_id<br>");
	echo("Page: ".$popper->get_pageno()."<br>");
	echo("MPP: $popper->mails_per_page");*/

?>
