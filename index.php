<?php

/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000-2005 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

/*==========================================================================
                                   Begin Main                             */	
	// Set the session
	SetupSession();
	
	// Check for PHP >4.1.0
	if (substr(phpversion(), 0, 1) < 4) {
		$msg  = "<h1>PHP3 is not supported!</h1>\n<br />\n<br />\nInstall a version of PHP >=4: <a ";
		$msg .= "href='http://www.php.net' title='PHP Homepage'>http://www.php.net</a>";
		die($msg);
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
	
/*DevNote: roytoo
This is very insecure!  Need to validate input and screen for PHP [htmlspecialchars()]
and MySQL [mysql_real_escape_string()] injection attacks.
:DevNote*/
	$action    = (!isset($_REQUEST['action']) ? "" : strtolower(htmlspecialchars($_REQUEST['action'])));
	$popper    =& $_SESSION['popper']; // Still needs a security patch
	$logged_in = (!isset($_SESSION['logged_in']) ? "" : $_SESSION['logged_in']);
	$cal       =& $_SESSION['cal']; // Still needs a security patch
	$confirm_code     = (!isset($_REQUEST['confirm_code']) ? "" : htmlspecialchars($_REQUEST['confirm_code']));
	$ok 		  = (!isset($_REQUEST['ok']) ? "" : strtolower(htmlspecialchars($_REQUEST['ok'])));
	$what		  = (!isset($_REQUEST['what']) ? "" : htmlspecialchars($_REQUEST['what']));
	
	function is_logged_in() {
		global $logged_in;
		return (isset($logged_in) && isset($_SESSION['logged_in']) && $logged_in == 1);
	}
	
	global $strings, $locale;
	
	@set_time_limit(60);

	include("class.popper.inc.php");
	include("class.cal.inc.php");
	
	$lang_file = LanguageGet();
	include($lang_file);
	setlocale(LC_ALL, $locale);
	
	// If needed, setup the persistent session data objects.
	if (!isset($popper) || !is_object($popper)) {
		$_SESSION['popper'] = new popper;
		$popper =& $_SESSION['popper'];
	}
	
	if (!isset($cal) || !is_object($cal)) {
		$_SESSION['cal'] = new cal;
		$cal =& $_SESSION['cal'];
	}
	
	/*
	// Check for early exit conditions
	switch ($action)
	{
	case "lostpwd":
		$popper->show_in_window("lostpwd");
		exit;
	case "sendpwd":
		$popper->send_pwd();
		exit;
	case "help":
		$popper->show_form("help");
		exit;
	}
	*/
	
	if (!isset($popper->db_tool)) {
		$popper->db_tool = new db_tool("test-popper.inc.php");
	}
	
	switch ($action)
	{
	case "process_login":
		ProcessLogin();
		break;
	case $strings["l_NewUser"]:
		ProcessNewUser();
		break;
	case "confirm":
		ProcessConfirmation($confirm_code);
		break;
	default:
		ProcessAction();
		break;
	}
	
	$cururi = getenv("REQUEST_URI");
	if ($_SESSION['uri'] != $cururi) {
		$_SESSION['old_uri'] = $_SESSION['uri'];
	}
	
	$_SESSION['uri'] = $cururi;

/*==                                End Main
==========================================================================*/



/*==========================================================================
                                 Begin Functions                          */

function SetupSession() {
	session_start();
	if (!isset($_SESSION['PHPSESSID']) && (!isset($_SESSION['in_session']) || $_SESSION['in_session'] < 1)) {
		$_SESSION['in_session'] = 1;
		$hdr = $_SERVER['PHP_SELF'].(($_SERVER['QUERY_STRING'] != "") ? "?".$_SERVER['QUERY_STRING'] : "");
		Header("Location: ".$hdr);
		exit;
	}
	else {
		$_SESSION['in_session']++;
	}
}

function ProcessLogin() {
	global $strings, $popper, $name, $pwd, $set_cookie, $poppercookie;
	
	if (!isset($name) || !isset($pwd)) {
		$msg = $strings["l_FillInAll"];
		$popper->show_in_window("login", $strings["l_Login"], $strings["l_FillInAll"]);
		exit;
	}
	
	if (!$popper->pwd_check($name, $pwd)) {
		$popper->show_in_window("login", $strings["l_Login"], $strings["l_LoginFail"]);
		exit;
	}
	
	if (!$popper->is_activated($name)) {
		$popper->messagebox($strings["l_AccActivation1"], $strings["l_AccActivation2"], $strings["l_AccActivation2"], $strings["l_Welcome"]);		
		return;			
	}
	
	// The password was good and there is at least one account.
	// Save the info to the session
/*DevNote: roytoo
Setting $logged_in = 1 is a security hole since it is easy to guess and gain access 
to a valid user's accounts.  It is better to set $logged_in to a hash composed of 
the PHPSESSID, users name and password [i.e. md5($_SESSION['PHPSESSID']+$name+$pwd)].  
Even this is not completely secure if an attacker has access to the server's session
save path and popper is not on an https:// server.

Need to add info about this topic to INSTALL?
:DevNote*/
	$logged_in = 1;	
	$popper->set_user($name);
	
	$_SESSION["popper"] = $popper;
	$_SESSION["logged_in"] = $logged_in;
	
	if ($HTTP_COOKIE_VARS["poppercookie"] && $set_cookie != "on") {
		CookieUnset();
	}

	if (isset($set_cookie) && $set_cookie && !$HTTP_COOKIE_VARS["poppercookie"]) {
		CookieSet();
	}
	
	if (!$popper->has_account()) {
		$popper->show_in_window("newaccount", $strings["l_Account"], $strings["l_AccInfo"]);		
		return;
	}
	$popper->show();
}

function ProcessNewUser() {
	global $popper, $strings;
	
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

function ProcessConfirmation($code) {
	global $popper;
	
	// The user called the confirmation link
	if ($popper->check_confirmation($code)) {
		// The check was successful
		$popper->messagebox($strings["l_ConfSucc1"], $strings["l_ConfSucc2"], $strings["l_ConfSucc2"], $strings["l_Welcome"]);
	}
	else {
		// Could not be confirmed
		$popper->messagebox($strings["l_ConfFail1"], $strings["l_ConfFail2"], $strings["l_ConfFail2"], $strings["l_TryAgain"]);
	}
}

function ProcessAction() {
	global $action, $popper, $strings, $ok, $what, $poppercookie;
	
	// Remove previously created tmp-files
	$popper->unlink_tmp();

/*DevNote: roytoo
Temporarily masked out the 'ok' functionality since it now interfers with $action processing.
Will need to be re-implemented, most likely by dynamically changing the value of $action based 
upon the contents of $_SESSION['last_action'].
:DevNote*/
	/*if ($ok == "ok") {
		if ($popper->config["mailview"] == FRAME_VIEW) {
			$popper->show_frame_list();
		}
		else {
			$popper->show("main");
		}
		return;
	}
	*/
	
	$popper->pre_frame();
	
	switch($action)
	{
	case "showfolder":
		$popper->show($folder);
		break;
	case "list":
		$popper->show_frame_list();
		break;
	case "mail_cont":
		$popper->show_mail_frame();
		break;
	case "showmail":
		$popper->show_popup_mail();
		//$popper->show_form("newmail", $strings["l_ShowMail"]);
		break;
	case "showsources":
		$popper->show_sources();
		break;
	case "logoff":
		CookieUnset();
		session_destroy();
		$popper->messagebox($strings["l_LogoffMsg"], $strings["l_Logoff"], "$popper->name ".$strings["l_LoggedOff"], $strings["l_GoodBye"]);
		break;
	case "newmail":
		$popper->show_in_window("newmail", $strings["l_NewMail"]);
		break;
	case "printmail":
		$popper->show_popup_mail();
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
		$popper->show_popup_mail();
		break;
	case "openmail":
		$popper->show_in_window("newmail", $strings["l_ShowMail"]);
		break;
	case "choosen":
		$popper->show_in_window("newmail", $strings["l_Choosen"]);
		break;
	case "cancel":
/*DevNote: roytoo
Functionalize this one.
:DevNote*/
		if (isset($_SESSION['old_uri']) && $popper->config["mailview"] != FRAME_VIEW) {
			Header("Location: ".$_SESSION['old_uri']);
		}
		else {
			$popper->show_form("main");
		}
		return;
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
/*DevNote: roytoo
Functionalize this one.
:DevNote*/
		$msg = $popper->store_mail();
		if ($popper->config["mailview"] == FRAME_VIEW) {
			$popper->show_form("main");
		}
		else {
			Header("Location: ".$_SESSION['old_uri']);
		}
		//$popper->show_form("main", $msg);
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
/*DevNote: roytoo
Functionalize this one.
:DevNote*/
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
		break;
	case "getattachment":
		$popper->get_attachment();
		break;
	case "config":
/*DevNote: roytoo
Functionalize this one.
:DevNote*/
		if (isset($GLOBALS['conf_submit'])) {
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
		break;
	case "changepwd":
		$popper->change_pwd();
		break;
	case "deletemail":
		$popper->delete_mail();
		break;
	case "selected":
/*DevNote: roytoo
Functionalize this one.
:DevNote*/
		if (isset($GLOBALS["delete_x"]) || isset($GLOBALS["ack"])) {
			$popper->delete_mails();
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
			$popper->delete_blocked();					
		}
		else if (isset($GLOBALS["move"])) {
			//$popper->move_to();
			$popper->report_error("Not implemented yet", "Not implemented");
		}
		break;
	case "deleteall":
		$popper->delete_all();
		break;
	case "notification":
/*DevNote: roytoo
Functionalize this one.
:DevNote*/
		$popper->send_notification();
		if ($popper->config["mailview"] == IN_VIEW) {
			Header("Location: ".$GLOBALS['PHP_SELF']."?action=showfolder&folder=".$popper->folder."&mail_id=".$popper->cur_message_id);
		}
		else if ($popper->config["mailview"] == FRAME_VIEW) {
			$popper->show_mail_frame();
		}
		else {
			Header("Location: ".$GLOBALS['PHP_SELF']."?action=showmail&mail_id=".$popper->cur_message_id);				
		}
		break;
	case "login":
		$popper->show_in_window("login", $strings["l_Login"]);
		break;		
	case "lostpwd":
		$popper->show_in_window("lostpwd");
		exit;
	case "sendpwd":
		$popper->send_pwd();
		exit;
	case "help":
		$popper->show_form("help");
		exit;
	default:
		$popper->show_in_window("home", $strings["l_Welcome"]);
		break;
	}
}

function CookieSet() {
	// Set the cookies if the user wants that and if it's not set yet.
	setcookie("poppercookie['user']", $name, time() + 365 * 24 * 3600);
	setcookie("poppercookie['language']", $popper->config["language"], time() + 365 * 24 * 3600);
}

function CookieUnset() {
	global $poppercookie;
	
	// This indicates that the user previously set the cookie, but does
	// not want to have it anymore.  So, we delete the cookies
	setcookie ("poppercookie['user']", "", time() - 3600);		
	setcookie ("poppercookie['language']", "", time() - 3600);
	unset($poppercookie);
}

function LanguageGet() {
	global $popper, $poppercookie;
	
	if ($poppercookie["language"]) {
		$langfile = "lang/lang.".$poppercookie["language"].".inc.php";
	}
	else {
		if (isset($popper->config["language"]) && !empty($popper->config["language"])) {
			$langfile = "lang/lang.".$popper->config["language"].".inc.php";
		}
		else {
			$langfile = "lang/lang.english.inc.php";
		}
	}
	
	if (!file_exists($langfile)) {
		$langfile = "lang/lang.english.inc.php";	
	}
	
	return $langfile;
}

/*==                              End Functions
==========================================================================*/

?>
