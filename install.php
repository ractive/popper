<?php
if ($action=="") {
$action="table";
}
if ($action=="table") {

echo "
<HTML><HEAD><TITLE>Popper System Table Installation</TITLE></HEAD><BODY bgcolor=\"#CCCCCC\">
<form action=\"install.php?action=make\" method=\"post\">
  <p>&nbsp; </p>
  <table width=\"90%\">
    <tr>
      <td colspan=\"3\">&nbsp;</td>
    </tr>
    <tr>
      <td width=\"27%\"><font face=\"MS sans serif\">Server Host Name/IP Address</font></td>
      <td colspan=\"2\">
        <input name=\"host\" size=\"14\">
      </td>
    </tr>
    <tr>
      <td width=\"27%\"><font face=\"MS sans serif\">Database Username</font></td>
      <td colspan=\"2\">
        <input name=\"dusername\" size=\"14\">
      </td>
    </tr>
    <tr>
      <td width=\"27%\"><font face=\"MS sans serif\">Database Password</font></td>
      <td colspan=\"2\">
        <input name=\"dpassword\" size=\"14\">
      </td>
    </tr>
    <tr>
      <td width=\"27%\"><font face=\"MS sans serif\">Database Name </font></td>
      <td colspan=\"2\">
        <input name=\"dname\" size=\"14\">
      </td>
    </tr>
    <tr>
     <td colspan=\"2\">&nbsp; </td>
      <td width=\"100%\">
        <input type=\"submit\" name=\"submit\" value=\"Make Tables\">
      </td>
    </tr>
  </table>
  <p> <BR>
    <BR>
    <BR>
    <BR>
  </p>
</form>
</BODY></HTML>";
}

if ($action=="make") {
$connection = mysql_connect("$host","$dusername", "$dpassword") or die ("Unable to connect to MySQL server.");
$db = mysql_select_db("$dname") or die ("Unable to select requested database.");

$sql = "CREATE TABLE accounts (
  id int(11) DEFAULT '0' NOT NULL auto_increment,
  user_id int(11) DEFAULT '0' NOT NULL,
  popserver varchar(255),
  poplogin varchar(255),
  poppwd varchar(255),
  fromname varchar(255),
  replyto varchar(255),
  name varchar(255) DEFAULT '' NOT NULL,
  PRIMARY KEY (id))";
				$result = mysql_query($sql);

				$sql = "CREATE TABLE addresses (
  id int(11) DEFAULT '0' NOT NULL auto_increment,
  email varchar(255) DEFAULT '' NOT NULL,
  name varchar(255) DEFAULT '' NOT NULL,
  address1 varchar(255),
  address2 varchar(255),
  town varchar(255),
  state varchar(255),
  country varchar(255),
  phone varchar(255),
  mobile varchar(255),
  homepage varchar(255),
  user_id int(11),
  nickname varchar(255),
  lastname varchar(255),
  zip varchar(16),
  PRIMARY KEY (id))";
				$result = mysql_query($sql);

				$sql = "CREATE TABLE app (
  id int(11) DEFAULT '0' NOT NULL auto_increment,
  user_id int(11),
  name varchar(255) DEFAULT '' NOT NULL,
  description text,
  start datetime,
  stop datetime,
  PRIMARY KEY (id))";
				$result = mysql_query($sql);

				$sql = "CREATE TABLE conf (
  id int(11) DEFAULT '0' NOT NULL auto_increment,
  user_id int(11) DEFAULT '0' NOT NULL,
  keep tinyint(1),
  server_del tinyint(1),
  auto_check tinyint(1),
  signature text,
  app_sig tinyint(1),
  send_direct tinyint(1),
  add_address tinyint(4),
  mail_rows int(11) NOT NULL DEFAULT 20,
  mails_per_page int(11) NOT NULL DEFAULT 20,
  language varchar(20) NOT NULL DEFAULT 'english',
  subject_size int(11) NOT NULL DEFAULT 50,
  address_size int(11) NOT NULL DEFAULT 20,
  check_int int(11) NOT NULL DEFAULT 15,
  PRIMARY KEY (id),
  UNIQUE user_id (user_id))";
				$result = mysql_query($sql);

				$sql = "CREATE TABLE confirmations (
  id int(11) DEFAULT '0' NOT NULL,
  user_id int(11) DEFAULT '0' NOT NULL,
  check char(32)
)";
				$result = mysql_query($sql);

				$sql = "CREATE TABLE mails (
  id int(11) DEFAULT '0' NOT NULL auto_increment,
  folder varchar(255),
  user_id int(11),
  uidl varchar(255) DEFAULT '' NOT NULL,
  tos text,
  froms text,
  subject text,
  is_mime tinyint(1),
  was_read tinyint(1),
  acc_id int(11),
  mail longtext,
  date varchar(14),
  PRIMARY KEY (id),
  KEY uidl (uidl)
)";
				$result = mysql_query($sql);

				$sql = "CREATE TABLE users (
  id int(11) DEFAULT '0' NOT NULL auto_increment,
  name varchar(255) DEFAULT '' NOT NULL,
  pwd varchar(255),
  email varchar(255),
  activated tinyint(4),
  PRIMARY KEY (id),
  UNIQUE name (name)
)";
				$result = mysql_query($sql);

				echo "
				<html>
<head>
<title>Table Set Up Complete</title>
</head>

<body bgcolor=\"#CCCCCC\" text=\"#000000\">
<CENTER>Tables Made
</body>
</html>";
				}


				?>
