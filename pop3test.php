<?
	include("class.pop3.inc.php");
	include("class.mailer.inc.php");
	include("class.db_tool.inc.php");

	$pop3 = new pop3();
	$pop3->DEBUG = true;

	function pop_login ($login, $pass) {
		global $pop3;

		# connect to the pop server
		if ( ! $pop3->connect("localhost") ) {
		$error_msg .= sprintf("Failed to connect to POP server %s on port %s.<br><small>%s</small>", $auth->auth["server"], $auth->auth["port"], $pop3->ERROR);
		}

		# login to the POP server
		$count = $pop3->apop($login, $pass);

		if ( $count < 0 ) {
		$error_msg .= "Failed to login to POP server<br><small>$pop3->ERROR</small>";
		}

		return $count;
	}

	$login = 'username';
	$pass = 'yourpassword';
	$count = pop_login($login,$pass);
	echo("<h1>$count Mails warten...</h1>");
	if($count == -1) {
	    echo "<H1>Login Failed: $pop3->ERROR</H1>\n";
	    exit;
	}


	// ONLY USE THIS IF YOUR PHP VERSION SUPPORTS IT!
	//register_shutdown_function($pop3->quit());

	if ($count < 1) {
	    //echo "Login OK: Inbox EMPTY<BR>\n";
	}
	else {
	    //echo "Login OK: Inbox contains [$Count] messages<BR>\n";
	}

	//echo("<br>Command pop_list():<br>");
	$messages = $pop3->pop_list();

	echo("<h1>Get from POP-SERVER</h1>");
	$mailer = new mailer(0);
	for($i = 1; $i < $count + 1; $i++) {
		//echo("<h4>Mail $i</h4><br>");
		$mail = $pop3->get_text($i);
		//echo(nl2br($mail));
		if(empty($mail)) {
			continue;
		}

		if ($mailer->store("inbox", $mail)) {
			echo("Mail received and stored<br>");
		}
		else {
			echo("Mail not stored<br><br>");
		}
	}

	$mailer = new mailer(0);
	$mailer->create("james@localhost", "thomas@localhost", "Create Mailer", "This is the body\r\n\r\nJames");

	$mailer->store();

	$mailer->send();




	echo("<h1>Create/store/send mit mimeparts</h1>");
	$mimepart = new mimepart;
	$mimepart->from_file("/wa02.jpg", "", "image/jpeg");

	$mimepart_arr[] = $mimepart;

	$email = $mailer->create("james@localhost", "thomas@localhost", "MIME Mailer", "This is the REAL body\r\n\r\nJames", "", $mimepart_arr);

	$mailer->store();

	$mailer->send();

	echo("<h1>Create/store/send mit attach</h1>");
	$email = $mailer->create("james@localhost", "thomas@localhost", "MIME Mailer mit attach", "This is the REAL body\r\n\r\nJames");
	$mailer->attach_file("/wa02.jpg", "", "image/jpeg");
	$mailer->store();
	$mailer->send();

	echo("<h1>Get from DB</h1>");
	unset($mailer);
	$mailer = new mailer(0);

	$query = "SELECT id FROM mails WHERE user_id = 0";
	$db_tool = new db_tool("popper.inc.php");
	$res = $db_tool->db_query($query);

	while($row = $db_tool->fetch_array($res)) {
		$mailer->load($row[id]);
	}
?>