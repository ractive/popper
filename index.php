<?php

/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/


	function is_logged_in() {
		global $logged_in;
		return (isset($logged_in) && session_is_registered("logged_in") && $logged_in == 1);
	}
	
	global $strings, $locale;
	
	@set_time_limit(60);

	include("class.popper.inc.php");
	
	session_start();

	if ($poppercookie["language"]) {
		$lang_file = "lang/lang.".$poppercookie["language"].".inc.php";
	}
	else {
		if (isset($popper->config["language"]) && !empty($popper->config["language"])) {
			$lang_file = "lang/lang.".$popper->config["language"].".inc.php";
		}
		else {
			$lang_file = "lang/lang.english.inc.php";
		}
	}
	
	if (!file_exists($lang_file)) {
		$lang_file = "lang/lang.english.inc.php";	
	}
	include($lang_file);
	setlocale(LC_ALL, $locale);
	
// added Leo - themes groups colors/fonts... options - they are stored under shared/themes
// TODO : get it from user config
	$themeid = "leofav";
	include "shared/themes/$themeid.php";
	
	if (!isset($popper) || !is_object($popper)) {
		$popper = new popper;
	}
	
	if (isset($action) && $action == "help") {
		$popper->show_form("help");
		exit;
	}
	
	if (!isset($popper->db_tool)) {
		$popper->db_tool = new db_tool("popper.inc.php");
	}	
	
	if (isset($login) || ($poppercookie["user"] && !is_logged_in())) {
		if (!isset($login) || !isset($pwd)) {
			$popper->show_in_window("login", $strings["l_Login"]);
			exit;
		}

 		if (!$popper->pwd_check($name, $pwd)) {
 			exit;
 		}

		if (!$popper->is_activated($name)) {
			$popper->messagebox($strings["l_AccActivation1"], $strings["l_AccActivation2"], $strings["l_AccActivation2"], $strings["l_Welcome"]);		
			return;			
		}

		if ($HTTP_COOKIE_VARS["poppercookie"] && $set_cookie != "on") {
			// This indicates that the user previously set the cookie, but don't
			// want to have it anymore
			// We delete the cookies
			setcookie ("poppercookie[user]", "", time() - 3600);		
			setcookie ("poppercookie[language]", "", time() - 3600);
			unset($poppercookie);
		}

		// The password was ok. Now we start the session
		$logged_in = 1;
		$popper->set_user($name);
		
		session_register("popper", "logged_in");

		if (isset($set_cookie) && $set_cookie && !$HTTP_COOKIE_VARS["poppercookie"]) {
			// Set the cookies if the user wants that and if it's not set yet.
			setcookie("poppercookie[language]", $popper->config["language"], time() + 365 * 24 * 3600);
			setcookie("poppercookie[user]", $name, time() + 365 * 24 * 3600);
		}
		
		if (!$popper->has_account()) {
			$popper->show_in_window("newaccount", $strings["l_Account"], $strings["l_AccInfo"]);		
			return;
		}
		$popper->show();
	}
	else if (isset($newuser)) {
		if ($popper->create_user()) {
			require("config.inc.php");
			echo $GLOBALS[user_confirmation];
			if ($GLOBALS[user_confirmation]) {
				$popper->messagebox($strings["l_AccCreated1"], $strings["l_AccCreated2"], $strings["l_AccCreated2"], $strings["l_Welcome"]);		
			}
			else {
				$popper->messagebox($strings["l_ConfSucc1"], $strings["l_AccCreated2"], $strings["l_AccCreated2"], $strings["l_Welcome"]);
			}
		
		}
	}
	else if (isset($check)) {
		// The user calle the confirmation link
		if ($popper->check_confirmation($check)) {
			// The check was successful
			$popper->messagebox($strings["l_ConfSucc1"], $strings["l_ConfSucc2"], $strings["l_ConfSucc2"], $strings["l_Welcome"]);
		}
		else {
			// The checkID could not be confirmed
			$popper->messagebox($strings["l_ConfFail1"], $strings["l_ConfFail2"], $strings["l_ConfFail2"], $strings["l_TryAgain"]);
		}
	}
	else if (is_logged_in()) {
	
		// Remove previously created tmp-files
		$popper->unlink_tmp();
		
		if (isset($deluser)) {
			if (!isset($ans)) {
				$popper->show_form("deluserack");
			}
			if ($ans == "No") {
				$popper->show_form("main", $strings["l_DelCancel"]);
			}
			if (!$popper->delete_user()) {
				$popper->messagebox($popper->get_user().$strings["l_UserDelFail"]."\n", $strings["l_Error"]);
			}
			else {
				session_destroy();
				$popper->messagebox($popper->get_user().$strings["l_UserDel"], $strings["l_UserDel2"]);
			}
		}

		switch( $action) {
			case "showfolder":
				$popper->show($folder);
				break;
			case "showsources":
				$popper->show_sources();
				break;
			case "logoff":
				session_destroy();
				$popper->messagebox($strings["l_LogoffMsg"], $strings["l_Logoff"], "$popper->name ".$strings["l_LoggedOff"], $strings["l_GoodBye"]);
				return;
				break;
			case "newmail":
				$popper->show_in_window("newmail", $strings["l_NewMail"]);
				break;
			case "printmail":
				$popper->show_print();
				break;			
			case "reply":
				$popper->show_in_window("newmail", $strings["l_ReplyMail"]);
				break;
			case "replytoall":
				$popper->show_in_window("newmail", $strings["l_ReplyAllMail"]);
				break;
			case "forward":
				$popper->show_in_window("newmail", $strings["l_ForwardMail"]);
				break;
			case "showmail":
				$popper->show_in_window("newmail", $strings["l_ShowMail"]);
				break;
			case "choosen":
				$popper->show_in_window("newmail", $strings["l_Choosen"]);
				break;			
			case "cancel":
				if (isset($old_uri)) {
					Header("Location: $old_uri");
				}
				else {
					$popper->show_form("main");
				}
				return;
				break;
			case "accounts":
				$popper->show_form("accounts");
				break;
			case "addressbook":
				$popper->show_form("addressbook");
				break;
			case "newaccount":
				$popper->show_in_window("newaccount", $strings["l_Account"], $strings["l_FillInAll"]);
				break;
			case "newaddress":
				$popper->show_in_window("newaddress", $strings["l_Address"]);
				break;
			case "storemail":
				$msg = $popper->store_mail();
				$popper->show_form("main", $msg);
				break;
			case "storeaccount":
				$popper->store_account();
				$popper->show_form("accounts");
				break;
			case "storeaddress":
				$popper->store_address();
				$popper->show_form("addressbook");
				break;
			case "deladdress":
				$popper->del_address();
				break;
			case "delaccount":
				$popper->del_account();
				break;
			case "checkrep":
				$popper->check_repetitive();
				break;			
			case "sendandreceive":
				if (!isset($what)) {
					$popper->show_in_window("sendreceive", $strings["l_SendReceive"]);
				}
				else {
					$popper->send_and_receive();
				}
				break;
			case "getattachment":
				$popper->get_attachment();
				break;
			case "config":
				if (isset($GLOBALS[conf_submit])) {
					$popper->store_config();
		
					$lang_file = "lang/lang.".$popper->config["language"].".inc.php";

					include($lang_file);
					
					setlocale(LC_ALL, $locale);	
					
					$popper->show();
				}
				else {
					$popper->show_in_window("config", $strings["l_Config"]);
				}
				break;			
			case "deletemail":
				$popper->delete_mail();
				break;
			case "deletemails":
				$popper->delete_mails();
				break;
			case "deleteall":
				$popper->delete_all();
				break;			
			default:
				$popper->show();
				break;
		}
/*		else {
			$popper->show();
		}*/
	}
	else {
		$popper->show_in_window("home", $strings["l_Welcome"]);
	}
	$cururi = getenv("REQUEST_URI");
	if ($uri != $cururi) {
		$old_uri = $uri;
	}
	$uri = $cururi;
	session_register("old_uri");
	session_register("uri");
?>
