<?php

/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

	function dbg($string) {
		//echo("DEBUG_OUT: $string<br>");
	}
	

	// Check for PHP4
	if (substr(phpversion(), 0, 1) == "3") {
		die("<h1>PHP3 is not supported!</h1>Install PHP4: http://www.php.net");
	}

	define("MAIL_UNREAD", 0);
	define("MAIL_READ", 1);
	define("MAIL_REPLIED", 2);
	define("MAIL_FORWARDED", 3);
	
	define("HIGHEST_PRIORITY", 1);
	define("HIGH_PRIORITY", 2);
	define("NORMAL_PRIORITY", 3);
	define("LOW_PRIORITY", 4);
	define("LOWEST_PRIORITY", 5);
	
	define("FULL_VIEW", 0);
	define("MID_VIEW", 1);
	define("MIN_VIEW", 2);
	
	define("POPUP_VIEW", 0);
	define("IN_VIEW", 1);
	define("SEP_VIEW", 2);	
	define("FRAME_VIEW", 3);			
	
	function is_logged_in() {
		global $logged_in;
		return (isset($logged_in) && session_is_registered("logged_in") && $logged_in == 1);
	}
	
	global $strings, $locale;
	
	@set_time_limit(60);

	include("class.popper.inc.php");
	include("class.cal.inc.php");
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


	if (!isset($popper) || !is_object($popper)) {
		$popper = new popper;
	}
	
	if (!isset($cal) || !is_object($cal)) {
		$cal = new cal();
	}	
	
	if (isset($action) && $action == "lostpwd") {
		$popper->show_in_window("lostpwd");
		exit;
	}
	else if (isset($action) && $action == "sendpwd") {
		$popper->send_pwd();
		exit;
	}	
	else if (isset($action) && $action == "help") {
		$popper->show_form("help");
		exit;
	}
	
	if (!isset($popper->db_tool)) {
		$popper->db_tool = new db_tool("popper.inc.php");
	}	
	
	if (!isset($check) && !isset($newuser) && (isset($login) || ($poppercookie["user"] && !is_logged_in()))) {

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
			setcookie ("poppercookie[theme]", "", time() - 3600);			
			unset($poppercookie);
		}

		// The password was ok. Now we start the session
		$logged_in = 1;
		$popper->set_user($name);
		$month = 0;
		session_register("popper", "logged_in", "month");

		if (isset($set_cookie) && $set_cookie && !$HTTP_COOKIE_VARS["poppercookie"]) {
			// Set the cookies if the user wants that and if it's not set yet.
			setcookie("poppercookie[language]", $popper->config["language"], time() + 365 * 24 * 3600);
			setcookie("poppercookie[user]", $name, time() + 365 * 24 * 3600);
			setcookie("poppercookie[theme]", $popper->config["theme"], time() + 365 * 24 * 3600);
		}
		
		if (!$popper->has_account()) {
			$popper->show_in_window("newaccount", $strings["l_Account"], $strings["l_AccInfo"]);		
			return;
		}
		
		// Make sure the bin is empty also if the user did not logoff
		if ($popper->config["clean_bin"] == 1) {
			$popper->clean_bin();
		}		
		
		//$popper->show();
		// This allows a reload of the page
		Header("Location: $PHP_SELF");
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

		if ($ok == "OK") {
			if ($popper->config["mailview"] == FRAME_VIEW) {
				$popper->show_frame_list();
			}
			else {
				$popper->show("main");
			}
			return;
		}	
		
		if (isset($action)) {
			
			$popper->pre_frame();

			if ($action == "showfolder") {
				$popper->show($folder);
			}
			else if ($action == "list") {
				$popper->show_frame_list();
			}		
			else if ($action == "mail_cont") {
				$popper->show_mail_frame();
			}							
			else if ($action == "showmail") {
				$popper->show_popup_mail();
				//$popper->show_form("newmail", $strings["l_ShowMail"]);
			}			
			else if ($action == "showsources") {
				$popper->show_sources();
			}
			else if ($action == "logoff") {
				if ($popper->config["clean_bin"] == 1) {
					$popper->clean_bin("s");
				}
				session_destroy();
				$popper->messagebox($strings["l_LogoffMsg"], $strings["l_Logoff"], "$popper->name ".$strings["l_LoggedOff"], $strings["l_GoodBye"]);
				unset($popper);
				$popper = 0;
				return;
			}
			else if ($action == "newmail") {
				$popper->show_in_window("newmail", $strings["l_NewMail"]);
			}
			else if ($action == "printmail") {
				$popper->show_popup_mail();
			}			
			else if ($action == "reply") {
				$popper->show_in_window("newmail", $strings["l_ReplyMail"]);
			}
			else if ($action == "replytoall") {
				$popper->show_in_window("newmail", $strings["l_ReplyAllMail"]);
			}
			else if ($action == "forward") {
				$popper->show_in_window("newmail", $strings["l_ForwardMail"]);
			}
			else if ($action == "showmail") {
				$popper->show_popup_mail();
			}
			else if ($action == "openmail") {
				$popper->show_in_window("newmail", $strings["l_ShowMail"]);			
			}
			else if ($action == "choosen") {
				$popper->show_in_window("newmail", $strings["l_Choosen"]);
			}			
			else if ($action == "cancel") {
				if (isset($old_uri) && $popper->config["mailview"] != FRAME_VIEW) {
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
				$popper->show_in_window("newaccount", $strings["l_Account"], $strings["l_FillInAll"]);
			}
			else if ($action == "newaddress") {
				$popper->show_in_window("newaddress", $strings["l_Address"]);
			}
			else if ($action == "storemail") {
				$msg = $popper->store_mail();
				if ($popper->config["mailview"] == FRAME_VIEW) {
					$popper->show_form("main");
				}
				else {		
					Header("Location: $old_uri");
				}
				//$popper->show_form("main", $msg);
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
			else if ($action == "checkrep") {
				$popper->check_repetitive();
			}			
			else if ($action == "sendandreceive") {
				if (!isset($what)) {
					if ($popper->config["mailview"] == FRAME_VIEW) {
						$popper->show_small_window("sendreceive", $strings["l_SendReceive"]);
					}
					else {
						$popper->show_in_window("sendreceive", $strings["l_SendReceive"]);
					}
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
		
					$lang_file = "lang/lang.".$popper->config["language"].".inc.php";

					include($lang_file);
					
					setlocale(LC_ALL, $locale);	
					
					$popper->show();
				}
				else if (isset($changepwd)) {
					$popper->show_in_window("changepwd", $strings["l_ChangePwd"]);
				}
				else if (isset($deluser) || isset($ack)) {
					$popper->delete_user();
				}				
				else {
					$popper->show_in_window("config", $strings["l_Config"]);
				}
			}	
			else if ($action == "changepwd") {
				$popper->change_pwd();
			}					
			else if ($action == "deletemail") {
				$popper->delete_mail();
			}
			else if ($action == "selected") {
				if (isset($GLOBALS["delete_x"]) || isset($GLOBALS["ack"])) {
					$msg = $popper->delete_mails();
					if ($popper->config["mailview"] == FRAME_VIEW) {
						$popper->show_frame_list();
					}
					else {
						$popper->show_form("main", $msg);
					}					
				}
				else if (isset($GLOBALS["add_addr_x"])) {
					$popper->add_sel_addr();
					if ($popper->config["mailview"] == FRAME_VIEW) {
						$popper->show_frame_list();
					}
					else {
						$popper->show();
					}
									
				}
				else if (isset($GLOBALS["block_x"])) {
					// Mark the senders as blocked
					$popper->add_sel_addr(true);
					// Delete the mails
					$msg = $popper->delete_blocked();
					if ($popper->config["mailview"] == FRAME_VIEW) {
						$popper->show_frame_list();
					}
					else {
						$popper->show_form("main", $msg);
					}									
				}
				else if (isset($GLOBALS["move"])) {
					//$popper->move_to();
					$popper->report_error("Not implemented yet", "Not implemented");
				}				
			}
			else if ($action == "deleteall") {
				$msg = $popper->delete_all();
				if ($popper->config["mailview"] == FRAME_VIEW) {
					$popper->show_frame_list();
				}
				else {
					$popper->show_form("main", $msg);
				}					
			}
			else if ($action == "notification") {
				$popper->send_notification();
				if ($popper->config["mailview"] == IN_VIEW) {
					Header("Location: ".$GLOBALS[PHP_SELF]."?action=showfolder&folder=".$popper->folder."&mail_id=".$popper->cur_message_id);
				}
				else if ($popper->config["mailview"] == FRAME_VIEW) {
					$popper->show_mail_frame();
				}
				else {
					Header("Location: ".$GLOBALS[PHP_SELF]."?action=showmail&mail_id=".$popper->cur_message_id);				
				}
				
				
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
