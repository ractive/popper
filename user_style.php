/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/
<?php
	$filename = "themes/".$theme.".inc.php";
	if (!file_exists($filename)) {
		$filename = "themes/outlook.inc.php";
	}
	
	include($filename);
?>

/*****************************

 General styles

*****************************/

body {
	font-family : <?php echo($theme["font-family"]);?>;
	font-size: <?php echo($theme["font-size"]);?>;
	color: <?php echo($theme["text-color"]);?>;
	background-color: <?php echo($theme["page-background"]);?>;
}

table {
	font-size: <?php echo($theme["font-size"]);?>;
}


}

textarea {
	font-size: <?php echo($theme["font-size"]);?>;
}

/*****************************

 Selected items

*****************************/

.selected {
	color: <?php echo($theme["highlight-text"]);?> !important;
	background-color: <?php echo($theme["highlight"]);?>;
}

/*****************************

 Window table border

*****************************/

.windowborder {
	color: White;
	background-color: <?php echo($theme["window-border"]);?>;
	border: thin outset <?php echo($theme["window-border"]);?>;		
}

/*****************************

 Window

*****************************/

.window {
	color: <?php echo($theme["window-text"]);?>;
	background-color: <?php echo($theme["window-background"]);?>;
}

.window a {
	color: <?php echo($theme["window-text"]);?>;
	background-color: inherit;
	text-decoration: none;
	width: 100%;
}

.window a:hover {
	color: inherit;
	background-color: silver;
	text-decoration: underline;
}

.window form {
	margin: 0, 0, 0, 0;
	padding: 0, 0, 0, 0;
}

.window fieldset {
	margin: 3px, 3px, 3px, 3px;
	padding: 5px, 5px, 5px, 5px;	
}

/*****************************

 Mailwindow

*****************************/

.mailwindow a {
	font-weight: bold;
}

.mailwindow a:visited {
	font-weight: normal;
}

/*****************************

 Titlebar

*****************************/

.titlebar {
	color: <?php echo($theme["caption-text"]);?>;
	background-color: <?php echo($theme["titlebar"]);?>;
}

.titlebar a {
	width: auto;
}

/*****************************

Toolbar

*****************************/

.toolbar {
	color: <?php echo($theme["button-text"]);?>;
	background-color: <?php echo($theme["3DFace"]);?>;
	background-image: none;
	background-repeat: repeat;
}

.toolbar img {
	margin-top: 2pt;
	border: thin solid <?php echo($theme["3DFace"]);?>;
}

.toolbar a {
	color: <?php echo($theme["text-color"]);?>;
	background-color: <?php echo($theme["toolbar-color"]);?>;
	border : 1px solid <?php echo($theme["3DFace"]);?>;
	/*padding: 10px, 10px, 10px, 10px; problems with Opera 4.02*/
	width: auto;
}

.toolbar a:visited {
	color: <?php echo($theme["button-text"]);?>;
	background-color: <?php echo($theme["3DFace"]);?>;
}

.toolbar a:hover {
	color: <?php echo($theme["button-text"]);?>;
	background-color: <?php echo($theme["3DFace"]);?>;
	text-decoration: none;
	border-top: 1px solid <?php echo($theme["3DHighlight"]);?>;
	border-left: 1px solid <?php echo($theme["3DHighlight"]);?>;
	border-bottom: 1px solid <?php echo($theme["3DShadow"]);?>;
	border-right: 1px solid <?php echo($theme["3DShadow"]);?>;
}

.toolbar td {
	border-top: 1px solid <?php echo($theme["3DHighlight"]);?>;
	border-bottom: 2px groove <?php echo($theme["3DHighlight"]);?>;
	border-left: 1px solid <?php echo($theme["3DHighlight"]);?>;
	border-right: 1px solid <?php echo($theme["3Ddark-shadow"]);?>;
}


/*****************************

 Header

*****************************/

.header table {
	background-color: <?php echo($theme["3DFace"]);?>;
	width=100%;
	border-bottom: 1px solid <?php echo($theme["3Ddark-shadow"]);?>;
}

.header tr {
	width=100%;
	background-color: <?php echo($theme["3DFace"]);?>;
}

.header hr {
	width: 100%;
	text-align: left;
}

.header p {
	width: 100%;
}

/*****************************

 Statusbar

*****************************/

.statusbar {
	color: Red;
	background-color: <?php echo($theme["3DFace"]);?>;
	font-weight: bold;
	font-size: normal;
}

.statusbar td {
	border: thin inset <?php echo($theme["3DShadow"]);?>;
}

/*****************************

 Scrollbar

*****************************/

.scrollbar {
	color: black;
	background-color: <?php echo($theme["scrollbar"]);?>;
}

td.scrollbar {
	width: 16px;
}

/*****************************

 Window title

*****************************/

.windowtitle {
	color: <?php echo($theme["highlight-text"]);?>;
	background-color: <?php echo($theme["3DShadow"]);?>;
	font-weight: bold;
	margin-bottom: 0;
}

.windowtitle a {
	color: <?php echo($theme["highlight-text"]);?>;
	background-color: inherit;
	font-weight: bold;
	text-decoration: none;
}

.windowtitle a:hover {
	color: <?php echo($theme["highlight-text"]);?>;
	background-color: inherit;
	font-weight: bold;
	text-decoration: underline;
}

/*****************************

 Child window

*****************************/

.childwindow {
	color: Black;
	background-color: <?php echo($theme["3DFace"]);?>;
	border: thin outset <?php echo($theme["window-border"]);?>;
	width: 60%;
	margin-top: 5%;
	margin-bottom: 5%;
}

.childwindow form {
	margin-top: 10pt;
	margin-bottom: 10pt;
}

.childwindow form td {
	text-align: left;
}

/*****************************

 Button

*****************************/

.button {
	width: 150px;
	color: <?php echo($theme["button-text"]);?>;
	background-color: <?php echo($theme["button-face"]);?>;	
}

/*****************************

 Smallbutton

*****************************/

.smallbutton {
	color: <?php echo($theme["button-text"]);?>;
	background-color: <?php echo($theme["button-face"]);?>;
	text-decoration: none;
	border-top: 1px solid <?php echo($theme["ThreeDHighlight"]);?>;
	border-left: 1px solid <?php echo($theme["ThreeDHighlight"]);?>;
	border-bottom: 1px solid <?php echo($theme["3Ddark-shadow"]);?>;
	border-right: 1px solid <?php echo($theme["3Ddark-shadow"]);?>;
	margin: 3px; 3px; 3px; 3px;
}

/*****************************

 Calendar control

*****************************/

.calcontrol {

}

.calcontrol a {
	color: red;
	background-color: inherit;
	text-decoration: none;
}

.calcontrol a:hover {
	color: white;
	background-color: blue;
}

.calcontrol td {
	border: thin none white;
	text-align: center;
}

/*****************************

 Mail window

*****************************/

.mail {

}

.mail label {
	font-weight: bolder;
}

.mail img {
	vertical-align: middle;
}

/*****************************

 Help window

*****************************/

.help {

}

.help dt {
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-weight : bolder;
	padding-bottom : 1ex;
	padding-top : 2ex;
}

.help h3 {
	padding-top: 1ex;
	border-top: medium solid silver;
}

.help li {
	margin-top: 3pt;
	margin-bottom: 3pt;
}

.help a {
	width: auto;
}

.help img {
	vertical-align: middle;
}
