<?php

include("class.mimepart.inc.php");

$mailer = new mimepart(0);
$text = "=?iso-8859-1?q?someone?= <someone@example.com>";
echo preg_replace("/=\?(.*?)\?([QB])\?(.*?)\?=/sexi", $mailer->decode("\\3", "\\2"), $text);
?>