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
	// Check for PHP >4.1.0
	if (version_compare(phpversion(),'4.1.0','<')) {
		$msg  = "<h1>Your version of PHP is not supported!</h1>\n<br />\n<br />\nInstall a version ";
		$msg .= "of PHP >=4: <a href='http://www.php.net' title='PHP Homepage'>http://www.php.net</a>";
		die($msg);
	}
	
	// Set the session
	setup_session();
	
	// Clear any prior $_SESSION['DATA']
	if (isset($_SESSION['DATA'])) {
		$_SESSION['DATA'] = "";
	}
	
  // Process POST last, so that those settings will take precedence
	// Process GET variables
	process_get_vars();
	// Process POST variables
	process_post_vars();
	
	$_SESSION['action'] = ($_SESSION['DATA']['action'] ? $_SESSION['DATA']['action'] : "");
	
	@set_time_limit(60);

	include("class.popper.inc.php");
	include("class.cal.inc.php");
	
	set_language();
	
	// If needed, setup the persistent session data objects.
	if (!isset($_SESSION['popper']) || !is_object($_SESSION['popper'])) {
		$_SESSION['popper'] = new popper;
	}
	
	if (!isset($_SESSION['calendar']) || !is_object($_SESSION['calendar'])) {
		$_SESSION['calendar'] = new cal;
	}
	
	switch ($_SESSION['action'])
	{
	case "process_login":
		process_login();
		break;
	case $_SESSION['strings']['l_NewUser']:
		process_new_user();
		break;
	case "confirm":
		process_confirmation($_SESSION['DATA']['confirm_code']);
		break;
	default:
		process_action();
		break;
	}
	
	takedown_session();
	
/*==                                End Main
==========================================================================*/



/*==========================================================================
                                 Begin Functions                          */

function setup_session() {
	session_start();
	unserialize_session();
	if (!isset($_SESSION['PHPSESSID']) && (!isset($_SESSION['in_session']) || $_SESSION['in_session'] < 1)) {
		$_SESSION['in_session'] = 1;
		$hdr = $_SERVER['PHP_SELF'].(($_SERVER['QUERY_STRING'] != "") ? "?".$_SERVER['QUERY_STRING'] : "");
		Header("Location: ".$hdr);
		exit_popper();
	}
	elseif (!$_SESSION['config_loaded']) {
		require_once("config.inc.php");
	}
}

/*DevNote: roytoo
This is very insecure!  Need to validate input and screen for PHP [htmlspecialchars()]
and MySQL [mysql_real_escape_string()] injection attacks.
:DevNote*/
function process_post_vars() {
	
	foreach ( $_POST as $post => $pvar) {
		$_SESSION['DATA'][strtolower($post)] = htmlspecialchars($pvar);
	}
}

/*DevNote: roytoo
This is very insecure!  Need to validate input and screen for PHP [htmlspecialchars()]
and MySQL [mysql_real_escape_string()] injection attacks.
:DevNote*/
function process_get_vars() {
	
/*DevNote: roytoo
As a security precaution, need to limit to a know list.
:DevNote*/
	foreach ( $_GET as $get => $gvar) {
		$_SESSION['DATA'][strtolower($get)] = htmlspecialchars($gvar);
	}
}

function set_language() {
	$lang_file = language_get();
	require($lang_file);
	setlocale(LC_ALL, $_SESSION['locale']);
}

function process_login() {
	
	if (!isset($_SESSION['DATA']['name']) || !isset($_SESSION['DATA']['pwd'])) {
		$msg = $_SESSION['strings']['l_FillInAll'];
		$_SESSION['popper']->show_in_window("login", $_SESSION['strings']['l_Login'], $_SESSION['strings']['l_FillInAll']);
		exit_popper();
	}
	
	if (!$_SESSION['popper']->pwd_check($_SESSION['DATA']['name'], $_SESSION['DATA']['pwd'])) {
		$_SESSION['popper']->show_in_window("login", $_SESSION['strings']['l_Login'], $_SESSION['strings']['l_LoginFail']);
		exit_popper();
	}
	
	if (!$_SESSION['popper']->is_activated($_SESSION['DATA']['name'])) {
		$_SESSION['popper']->messagebox($_SESSION['strings']['l_AccActivation1'], $_SESSION['strings']['l_AccActivation2'], $_SESSION['strings']['l_AccActivation2'], $_SESSION['strings']['l_Welcome']);		
		return;			
	}
	
	// The password was good and there is at least one account.
	// Save the info to the session
/*DevNote: roytoo
Setting $_SESSION['logged_in'] = 1 is a security hole since it is easy to guess and gain access 
to a valid user's accounts.  It is better to set $_SESSION['logged_in'] to a hash composed of 
the PHPSESSID, users name and password [i.e. md5($_SESSION['PHPSESSID']+$_SESSION['DATA']['name']+$_SESSION['DATA']['pwd'])].  
Even this is not completely secure if an attacker has access to the server's session
save path and popper is not on an https:// server.

Need to add info about this topic to INSTALL?
:DevNote*/
	$_SESSION['logged_in'] = 1;	
	$_SESSION['popper']->set_user($_SESSION['DATA']['name']);
	
	if ($_COOKIE['poppercookie'] && ($_SESSION['DATA']['set_cookie'] != "on")) {
		cookie_unset();
	}

	if (isset($_SESSION['DATA']['set_cookie']) && $_SESSION['DATA']['set_cookie'] && !$_COOKIE['poppercookie']) {
		cookie_set();
	}
	
	if (!$_SESSION['popper']->has_account()) {
		$_SESSION['popper']->show_in_window("newaccount", $_SESSION['strings']['l_Account'], $_SESSION['strings']['l_AccInfo']);		
		return;
	}
	$_SESSION['popper']->show();
}

function process_new_user() {
	
	if ($_SESSION['popper']->create_user()) {
		if ($_SESSION['DATA']['user_confirmation']) {
			$_SESSION['popper']->messagebox($_SESSION['strings']['l_AccCreated1'], $_SESSION['strings']['l_AccCreated2'], $_SESSION['strings']['l_AccCreated2'], $_SESSION['strings']['l_Welcome']);		
		}
		else {
			$_SESSION['popper']->messagebox($_SESSION['strings']['l_ConfSucc1'], $_SESSION['strings']['l_AccCreated2'], $_SESSION['strings']['l_AccCreated2'], $_SESSION['strings']['l_Welcome']);
		}
	}
}

function process_confirmation($code) {
	
	// The user called the confirmation link
	if ($_SESSION['popper']->check_confirmation($code)) {
		// The check was successful
		$_SESSION['popper']->messagebox($_SESSION['strings']['l_ConfSucc1'], $_SESSION['strings']['l_ConfSucc2'], $_SESSION['strings']['l_ConfSucc2'], $_SESSION['strings']['l_Welcome']);
	}
	else {
		// Could not be confirmed
		$_SESSION['popper']->messagebox($_SESSION['strings']['l_ConfFail1'], $_SESSION['strings']['l_ConfFail2'], $_SESSION['strings']['l_ConfFail2'], $_SESSION['strings']['l_TryAgain']);
	}
}

function process_action() {
	
	// Remove previously created tmp-files
	$_SESSION['popper']->unlink_tmp();

/*DevNote: roytoo
Temporarily masked out the 'ok' functionality since it now interfers with $_SESSION['action'] processing.
Will need to be re-implemented, most likely by dynamically changing the value of $_SESSION['action'] based 
upon the contents of $_SESSION['last_action'].
:DevNote*/
	if ($_SESSION['DATA']['ok'] == "ok") {
		if ($_SESSION['popper']->config['mailview'] == $_SESSION['FRAME_VIEW']) {
			$_SESSION['popper']->show_frame_list();
		}
		else {
			$_SESSION['popper']->show("main");
		}
		return;
	}
	
	
	$_SESSION['popper']->pre_frame();
	
	switch($_SESSION['action'])
	{
	case "showfolder":
		$_SESSION['popper']->show($folder);
		break;
	case "list":
		$_SESSION['popper']->show_frame_list();
		break;
	case "mail_cont":
		$_SESSION['popper']->show_mail_frame();
		break;
	case "showmail":
		$_SESSION['popper']->show_popup_mail();
		//$_SESSION['popper']->show_form("newmail", $_SESSION['strings']['l_ShowMail']);
		break;
	case "showsources":
		$_SESSION['popper']->show_sources();
		break;
	case "logoff":
		cookie_unset();
		session_destroy();
		$_SESSION['popper']->messagebox($_SESSION['strings']['l_LogoffMsg'], $_SESSION['strings']['l_Logoff'], $_SESSION['popper']->name." ".$_SESSION['strings']['l_LoggedOff'], $_SESSION['strings']['l_GoodBye']);
		break;
	case "newmail":
		$_SESSION['popper']->show_in_window("newmail", $_SESSION['strings']['l_NewMail']);
		break;
	case "printmail":
		$_SESSION['popper']->show_popup_mail();
		break;
	case "reply":
		$_SESSION['popper']->show_in_window("newmail", $_SESSION['strings']['l_ReplyMail']);
		break;
	case "replytoall":
		$_SESSION['popper']->show_in_window("newmail", $_SESSION['strings']['l_ReplyAllMail']);
		break;
	case "forward":
		$_SESSION['popper']->show_in_window("newmail", $_SESSION['strings']['l_ForwardMail']);
		break;
	case "showmail":
		$_SESSION['popper']->show_popup_mail();
		break;
	case "openmail":
		$_SESSION['popper']->show_in_window("newmail", $_SESSION['strings']['l_ShowMail']);
		break;
	case "choosen":
		$_SESSION['popper']->show_in_window("newmail", $_SESSION['strings']['l_Choosen']);
		break;
	case "cancel":
/*DevNote: roytoo
Functionalize this one.
:DevNote*/
		if (isset($_SESSION['old_uri']) && ($_SESSION['popper']->config['mailview'] != $_SESSION['FRAME_VIEW'])) {
			Header("Location: ".$_SESSION['old_uri']);
		}
		else {
			$_SESSION['popper']->show_form("main");
		}
		return;
	case "accounts":
		$_SESSION['popper']->show_form("accounts");
		break;
	case "addressbook":
		$_SESSION['popper']->show_form("addressbook");
		break;
	case "newaccount":
		$_SESSION['popper']->show_in_window("newaccount", $_SESSION['strings']['l_Account'], $_SESSION['strings']['l_FillInAll']);
		break;
	case "newaddress":
		$_SESSION['popper']->show_in_window("newaddress", $_SESSION['strings']['l_Address']);
		break;
	case "storemail":
/*DevNote: roytoo
Functionalize this one.
:DevNote*/
		$msg = $_SESSION['popper']->store_mail();
		if ($_SESSION['popper']->config['mailview'] == $_SESSION['FRAME_VIEW']) {
			$_SESSION['popper']->show_form("main");
		}
		else {
			Header("Location: ".$_SESSION['old_uri']);
		}
		//$_SESSION['popper']->show_form("main", $msg);
		break;
	case "storeaccount":
		$_SESSION['popper']->store_account();
		$_SESSION['popper']->show_form("accounts");
		break;
	case "storeaddress":
		$_SESSION['popper']->store_address();
		$_SESSION['popper']->show_form("addressbook");
		break;
	case "deladdress":
		$_SESSION['popper']->del_address();
		break;
	case "delaccount":
		$_SESSION['popper']->del_account();
		break;
	case "checkrep":
		$_SESSION['popper']->check_repetitive();
		break;
	case "sendandreceive":
/*DevNote: roytoo
Functionalize this one.
:DevNote*/
		if (!isset($_SESSION['DATA']['what'])) {
			if ($_SESSION['popper']->config['mailview'] == $_SESSION['FRAME_VIEW']) {
				$_SESSION['popper']->show_small_window("sendreceive", $_SESSION['strings']['l_SendReceive']);
			}
			else {
				$_SESSION['popper']->show_in_window("sendreceive", $_SESSION['strings']['l_SendReceive']);
			}
		}
		else {
			$_SESSION['popper']->send_and_receive();
		}
		break;
	case "getattachment":
		$_SESSION['popper']->get_attachment();
		break;
	case "config":
/*DevNote: roytoo
Functionalize this one.
:DevNote*/
		if (isset($_SESSION['DATA']['conf_submit'])) {
			$_SESSION['popper']->store_config();
			
			$lang_file = "lang/lang.".$_SESSION['popper']->config['language'].".inc.php";
			
			include($lang_file);
			
			setlocale(LC_ALL, $_SESSION['locale']);	
			
			$_SESSION['popper']->show();
		}
		else if (isset($changepwd)) {
			$_SESSION['popper']->show_in_window("changepwd", $_SESSION['strings']['l_ChangePwd']);
		}
		else if (isset($deluser) || isset($ack)) {
			$_SESSION['popper']->delete_user();
		}
		else {
			$_SESSION['popper']->show_in_window("config", $_SESSION['strings']['l_Config']);
		}
		break;
	case "changepwd":
		$_SESSION['popper']->change_pwd();
		break;
	case "deletemail":
		$_SESSION['popper']->delete_mail();
		break;
	case "selected":
/*DevNote: roytoo
Functionalize this one.
:DevNote*/
		if (isset($_SESSION['DATA']['delete_x']) || isset($_SESSION['DATA']['ack'])) {
			$_SESSION['popper']->delete_mails();
		}
		else if (isset($_SESSION['DATA']['add_addr_x'])) {
			$_SESSION['popper']->add_sel_addr();
			if ($_SESSION['popper']->config['mailview'] == $_SESSION['FRAME_VIEW']) {
				$_SESSION['popper']->show_frame_list();
			}
			else {
				$_SESSION['popper']->show();
			}
		}
		else if (isset($_SESSION['DATA']['block_x'])) {
			// Mark the senders as blocked
			$_SESSION['popper']->add_sel_addr(true);
			// Delete the mails
			$_SESSION['popper']->delete_blocked();					
		}
		else if (isset($_SESSION['DATA']['move'])) {
			//$_SESSION['popper']->move_to();
			$_SESSION['popper']->report_error("Not implemented yet", "Not implemented");
		}
		break;
	case "deleteall":
		$_SESSION['popper']->delete_all();
		break;
	case "notification":
/*DevNote: roytoo
Functionalize this one.
:DevNote*/
		$_SESSION['popper']->send_notification();
		if ($_SESSION['popper']->config['mailview'] == $_SESSION['IN_VIEW']) {
			Header("Location: ".$_SERVER['PHP_SELF']."?action=showfolder&folder=".$_SESSION['popper']->folder."&mail_id=".$_SESSION['popper']->cur_message_id);
		}
		else if ($_SESSION['popper']->config['mailview'] == $_SESSION['FRAME_VIEW']) {
			$_SESSION['popper']->show_mail_frame();
		}
		else {
			Header("Location: ".$_SERVER['PHP_SELF']."?action=showmail&mail_id=".$_SESSION['popper']->cur_message_id);				
		}
		break;
	case $_SESSION['strings']['l_Login']:
		$_SESSION['popper']->show_in_window("login", $_SESSION['strings']['l_Login']);
		break;		
	case "lostpwd":
		$_SESSION['popper']->show_in_window("lostpwd");
		exit_popper();
	case "sendpwd":
		$_SESSION['popper']->send_pwd();
		exit_popper();
	case "help":
		$_SESSION['popper']->show_form("help");
		exit_popper();
	default:
		$_SESSION['popper']->show_in_window("home", $_SESSION['strings']['l_Welcome']);
		break;
	}
}

function cookie_set() {
	
	// Set the cookies if the user wants that and if it's not set yet.
	setcookie("poppercookie['user']", $_SESSION['DATA']['name'], time() + 365 * 24 * 3600);
	setcookie("poppercookie['language']", $_SESSION['popper']->config['language'], time() + 365 * 24 * 3600);
	$_SESSION['poppercookie'] = $_COOKIE['poppercookie'];
}

function cookie_unset() {
	
	// This indicates that the user previously set the cookie, but does
	// not want to have it anymore.  So, we delete the cookies
	setcookie ("poppercookie['user']", "", time() - 3600);		
	setcookie ("poppercookie['language']", "", time() - 3600);
	$_SESSION['poppercookie'] = false;
}

function language_get() {
	
	if (isset($_SESSION['poppercookie']['language'])) {
		$langfile = "lang/lang.".$_SESSION['poppercookie']['language'].".inc.php";
	}
	else {
		if (isset($_SESSION['popper']->config['language']) && !empty($_SESSION['popper']->config['language'])) {
			$langfile = "lang/lang.".$_SESSION['popper']->config['language'].".inc.php";
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

function is_logged_in() {
	return (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == 1);
}

function serialize_session() {
	// Serialize objects for proper storage
	if (isset($_SESSION['popper']) && is_object($_SESSION['popper']))
	{
		$_SESSION['popper'] = serialize($_SESSION['popper']);
	}
	if (isset($_SESSION['calendar']) && is_object($_SESSION['calendar']))
	{
		$_SESSION['calendar'] = serialize($_SESSION['calendar']);
	}
}

function unserialize_session() {
	// Serialize objects for proper storage
	if (isset($_SESSION['popper']) && !is_object($_SESSION['popper']))
	{
		$_SESSION['popper'] = unserialize($_SESSION['popper']);
	}
	if (isset($_SESSION['calendar']) && !is_object($_SESSION['calendar']))
	{
		$_SESSION['calendar'] = unserialize($_SESSION['calendar']);
	}
}

function takedown_session()
{
	// Update $_SESSION['last_action'] if needed
	if ($_SESSION['last_action'] != $_SESSION['action']) {
		$_SESSION['last_action'] = $_SESSION['action'];
	}
	
	// Store the current and last URIs
	$cururi = getenv("REQUEST_URI");
	if ($_SESSION['uri'] != $cururi) {
		$_SESSION['old_uri'] = $_SESSION['uri'];
	}
	$_SESSION['uri'] = $cururi;
	
	unserialize_session();
}

function exit_popper() {
	takedown_session();	
	exit;
}

/*==                              End Functions
==========================================================================*/

?>
