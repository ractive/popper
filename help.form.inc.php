<tr class="window">
	<td>
		&nbsp;
	</td>
	<td class="help" width="70%">
	<br><br>
<center><h2>Welcome to the POPPER help</h2></center>

<h3>Content</h3>
<ol>
	<li><a href="#intro">Intro</a></li>
	<li><a href="#license">License and terms of use</li>
	<li><a href="#create">Create new User Account</li>
	<li><a href="#toolbar">The toolbar</li>
	<li><a href="#titlebar">The titlebar</li>
	<li><a href="#new">Compose a new message</li>
	<li><a href="#read">Reading mails</li>
	<li><a href="#address">Addressbook</li>
	<li><a href="#account">Account</li>
	<li><a href="#config">Configuration</li>
	<li><a href="#problems">Problems and known bugs</li>
	<li><a href="#contact">Contact</li>
</ol>

<h3><a name="intro">Introduction</a></h3>
Popper is a web mail-client. This means that you can access (read/answer) your mails from any browser.<br>
The difference to other web-mail services like hotmail etc. is, that you can access your personal
mailboxes on POP3 servers.<br>
Normally you use a program like Outlook, KMail, Netscape Messenger, Eudora etc. to access these mailboxes.
Problems arise when you want to read your mails on other places that at home. Think about vacation :-).<br>
It's annoying to configure every time a new mail-client to read your mails.<br>
This is where popper can help you. It gives you the possibility to read your mails from (almost ;-) any
browser from anywhere...<br>

<h3><a name="license">License and terms of use</a></h3>
This program is Copyright (C) 2000 by Jean-Pierre Bergamin<br>
under the terms of the General Public License. This means that you can freely use and redistribute it.<br>
To get more information about the licensing, <a href="http://www.gnu.org/copyleft/gpl.html">see here</a><br>
The source-code can be found <a href="http://www.ractive.ch/gpl/popper.html">here</a><br>
<h3>Features</h3>
Popper is a fully qualified mail-client. You can read, answer, forward your mails, compose new messages, send
attachments, see received attachments and documents and much more. Just like any other mail-client, too :-)
<ul>
	<li>
		Access your mailboxes on any POP3 server
	</li>
	<li>
		Manage multiple POP3 accounts
	</li>
	<li>
		Compose new messages
	</li>
	<li>
		Answer and forward your mails
	</li>
	<li>
		Send and receive attachments
	</li>
	<li>
		Online addressbook
	</li>
</ul>

<h3><a name="create">Create new User Account</a></h3>
<img src="graphics/help_newuser.jpg" alt="" style="float: left;">
First you have to create an account for the mail-client (if you don't already have one). Select "Create new user" on
the welcome screen.<br>
<br>
Enter your desired username, a VALID e-mail address and your password. These informations are only needed to access
the mail-client.<br>
To check the validity of your entries, you'll have to confirm your subscription for this client. You will get an
E-mail which contains a link. Click on this link or copy it to your webbrowser to activate your account.<br>
<br clear=all>
After you activated your account, it's time to set up a first mailbox account.
<br>
<h3>Set up mail-account</h3>
You must provide some information about your mailbox before you can access it. This is done by setting up a new
mail-account.<br>
The single entries are shortly explained here:
<dl>
	<dt>Accountname:</dt>
	<dd>You can freely choose a name for this account. Choose one that describes your account. I.e. "Office-Mail"</dd>
	<dt>From name:</dt>
	<dd>This is the name that the recipients see in the "From field" if you send out a mail. Choose anything you want</dd>
	<dt>Reply to email:</dt>
	<dd>Enter the e-mail address where replies should be sent to.</dd>
	<dt>POP-Server</dt>
	<dd>Enter the name of the POP-server of your mailbox. This is something like pop.hereiam.com.</dd>
	<dt>POP-Loginname:</dt>
	<dd>This is the name you need to login to your mailbox</dd>
	<dt>Password:</dt>
	<dd>Enter here the password that is used to access your mailbox</dd>
</dl>

When you login for the first time you have to set up your first account. You can later still add more mail accounts.

<h3><a name="toolbar">The toolbar</a></h3>
<p><img src="graphics/help_toolbar.jpg" alt="Toolbar image"></p>
The toolbar is the most important part of this mail-client. All the commands are executed through this bar.<br>
<p>The single functions are described below:</p>
<img src="graphics/newmail.gif" alt="New mail image" style="float: left;">
This lets you compose a new mail<br>
<br clear=all>
<img src="graphics/read.gif" alt="Read image" style="float: left;">
Open a selected mail in a separate window<br>
<br clear=all>
<img src="graphics/prop.gif" alt="Properties image" style="float: left;">
Show the source code of the mail<br>
<br clear=all>
<img src="graphics/reply.gif" alt="Reply image" style="float: left;">
Reply to a selected mail. The original text is (if possible) indented<br>
<br clear=all>
<img src="graphics/replytoall.gif" alt="Reply to all image" style="float: left;">
Reply to all the recipients of this mail<br>
<br clear=all>
<img src="graphics/forward.gif" alt="Forward image" style="float: left;">
Forward the selected message to a receiver<br>
<br clear=all>
<img src="graphics/print.gif" alt="Printer image" style="float: left;">
This will open a new window with the currently selected mail in a printer friendly style. If you want to print a HTML mail,
open the attached mail and print it from this window.<br>
<br clear=all>
<img src="graphics/cancel.gif" alt="Delete image" style="float: left;">
Delete the selected mail. The mail first is put into the recycle bin.<br>
If you want to totally delete the mail, delete it in the recycle bin<br>
<br clear=all>
<img src="graphics/cancelm.gif" alt="Delete image" style="float: left;">
Delete ALL mails in the current folder.<br>
<br clear=all>
<img src="graphics/sendandreceive.gif" alt="Send and receive image" style="float: left;">
Send your mails in the outbox and receive mails from your accounts. Send only will only send the mails stored in the outbox.
"Send and receive all" will send all mails and get the mails from all your accounts. "Get all" will download the mails from
all your accounts<br>
Additionally the names of all the accounts you set up will appear. Selecting on of them only
downloads the mails from this account.<br>
<br clear=all>
<img src="graphics/get_rep.gif" alt="Send and receive image" style="float: left;">
This opens a new window. If you leave this window open, your accounts are checked for new mail every X minutes. You can set
the interval X in the configuration.<br>
<br clear=all>
<img src="graphics/address.gif" alt="New mail image" style="float: left;">
Open the addressbook<br>
<br clear=all>
<img src="graphics/account.gif" alt="New mail image" style="float: left;">
Manage your mail accounts. You can change the settings, delete account or create new ones<br>
<br clear=all>
<img src="graphics/config.gif" alt="New mail image" style="float: left;">
Configure the mail-client
<br clear=all>

<h3><a name="toolbar">The mail toolbar</a></h3>
<p><img src="graphics/help_sel_tool.jpg" alt="Toolbar image"></p>
This small toolbar allows to perform actions on selected mails.<br>
Select the desired mails by checking the small box in front of them.
<p>The single functions are described below:</p>
<img src="graphics/del.gif" alt="Del" style="float: left;">
The selected mails will be deleted<br>
<br clear=all>
<img src="graphics/add_addr.gif" alt="Addressbook" style="float: left;">
The sender addresses of the selected mails will be put into the addressbook<br>
<br clear=all>
<img src="graphics/stop.gif" alt="Ban" style="float: left;">
Future mails from the senders of the selected mails will be moved automatically to the recycle bin after retrieval.
This allows you to filter SPAM mails.<br>
<br clear=all>

<h3><a name="titlebar">The titlebar</a></h3>
Beside the name of the currently logged in users you also find the logout button
<img src="graphics/logoff.gif" alt="Logoff button"> in the titlebar<br>
When you press this button you will be logged off. Please logoff if you finished reading your mails!<br>
<br>
When you hit the logoff button withing a child window, the current operation is cancelled.<br>
<br>
<b>By clicking the logo in the left upper corner <img src="graphics/logo.gif" alt="Logo"> you always change
back to the main view!</b>

<h3><a name="new">Compose a new message</a></h3>
You can start composing a new message by clicking on the "New Mail" button <img src="graphics/newmail.gif" alt="New mail image"> or directly
clicking on an addressbook entry in the left hand corner.<br>
By clicking on the addressbook symbols <img src="graphics/small_address.gif" alt="Addressbook symbol"> near the "To", "Cc" and "Bcc" field
you can select recipients from the addressbook.<br>
If you want to send the mail, click the "Send" button at the bottom. When you click the "Save" button, the message will be
stored in the "Drafts" folder. You then can open and modify it later.<br>

<h3><a name="read">Reading mails</a></h3>
First you have to download the mails from your mailboxes. You can do that by clicking the "Send and receive" button
<img src="graphics/sendandreceive.gif" alt="Send and receive image"> in the toolbar<br>
See the toolbar section for additional information...<br>
<br>
To display the mail content, simply click on the mail. According to your settings in the configuration the mail will
be either shown in the textbox beneatch or in an own window.<br>
Additionally you can press the "Read mail" button <img src="graphics/read.gif" alt="Read image"> in the toolbar
to display the mail in a separate window.<br>
<br>
If there's a clip <img src="graphics/attachment_small.gif" alt="Clip image"> in front of a mail entry, the mail contains an attachment.<br>
The attachments are shown in the header of the mails. Clicking on an attachment opens it in a new browser window. 
You also can right-click and choose "Save target as..." to save the attachment.<br>

<h3><a name="address">Addressbook</a></h3>
The simplest way to add an entry to your addressbook is to click "Add contact..." on the left hand side. Alternatively you
can choose the addressbook-button in the toolbar to manage your addresses.<br>
By clicking on a name in the addressbook list in the left bottom corner, you'll see the details of the entry. When you
click on an email address, you'll compose a message for this recipient.<br>
To manage your addressbook, go to the addressbook window by cklicking the button in the toolbar
<img src="graphics/address.gif" alt="Addressbook image"><br>
<br>
<img src="graphics/home.gif" alt="Home image" style="float: left;">
Change back to the main view<br>
<br clear=all>
<img src="graphics/newaddress.gif" alt="New Address image" style="float: left;">
Enter a new contact<br>
<br clear=all>
<img src="graphics/properties.gif" alt="Properties image" style="float: left;">
Change the properties of the selected contact<br>
<br clear=all>
<img src="graphics/cancel.gif" alt="Delete image" style="float: left;">
Delete the selected contact<br>
<br clear=all>
<img src="graphics/block_big.gif" alt="Ban image" style="float: left;">
Show the addresses that are marked as banned addresses. All mails sent from these senders will be put automatically
in the recycle bin after retrieval.
<br clear=all>

<h3><a name="accounts">Accounts</a></h3>
You can add different mail-account settings. This allows you to get mails from different mailboxes.<br>
To manage your accounts, go to the accounts window by clicking the account-button in the toolbar
<img src="graphics/account.gif" alt="New mail image"><br>
<br>
<img src="graphics/home.gif" alt="Home image" style="float: left;">
Change back to the main view<br>
<br clear=all>
<img src="graphics/newaddress.gif" alt="New address image" style="float: left;">
Add a new account<br>
<br clear=all>
<img src="graphics/properties.gif" alt="Properties image" style="float: left;">
Change the properties of the selected account<br>
<br clear=all>
<img src="graphics/cancel.gif" alt="Delete image" style="float: left;">
Delete the selected account
<br clear=all>

<h3><a name="config">Configuration</a></h3>
You can configure the behaviour some parts of this mail-client. Press the configure button
<img src="graphics/config.gif" alt=""> to enter the configuration-dialog.
<center><p><img src="graphics/help_config_en.jpg" alt="Configuration dialg image"></p></center>
<dl>
	<dt>Language:</dt>
	<dd>Choose your preferred language</dd>	
	<dt>Keep mails on server:</dt>
	<dd>This option decides if the mails should be delete from the server after they have been received.<br>
	If the mails are deleted, there's no possibility to get them with another client. I'd toggle this setting on</dd>
	<dt>Remove mails from server after deletion:</dt>
	<dd>When a mail is delete in the recycle bin, it's also removed from the server.</dd>
	<dt>Check for mails after login:</dt>
	<dd>If you turn this option on, the mails from all the accounts are fetched after you log on.</dd>
	<dt>Send mails directly:</dt>
	<dd>Composed messages are not stored in the oubox if this option is switched one, but they are sent directly</dd>
	<dt>Signature:</dt>
	<dd>Enter a signature that will be appended to your messages</dd>
	<dt>Append signature to each message:</dt>
	<dd>Only if this option is turned on, your signature that you entered above is insertet at the end of
	every new message</dd>
	<dt>Add e-mail addresses of incoming e-mails to the address-book:</dt>
	<dd>If you choose this option, every new E-Mail address or incoming mails is stored in the address-book</dd>	
	<dt>Check for new mails every X minutes:</dt>
	<dd>If you open the auto-fetch window with <img src="graphics/get_rep.gif" alt="Send and receive image">, your account are checked for new mail ever X minutes</dd>
	<dt>View</dt>
	<dd>
	<ul>
		<li><b>"Folderlist and contacts"</b> shows you the folders and the contacts in the main view</li>
		<li><b>"Folderlist"</b> only shows you the folderlist in the main view</li>
		<li><b>"Folders only"</b> just shows the pictures of the folders</li>
		<li><b>"Show mails in the same window"</b> shows the selected mail in a textarea beneath the list</li>
		<li><b>"Show mails in an own window"</b> opens the mail in the same browser window</li>
		<li><b>"Show mails in a popup window"</b> opens the mail in a new browser window</li>
		<li><b>"Use frames"</b> uses frames to display the maillist and the mail content. This is only recommended
		if you are using a browser with Version 5 and above.</li>
	</ul>
	Be aware that the shown read-status (read/unread etc) of the mails in the mail list is not updated until the page
	is loaded again. This affects the ""Show mails in a popup window" and "Use frames" views. To reload the page, simply
	hit the appropriate folder again.
	</dd>
	<dt>Show X Mails per page:</dt>
	<dd>Here you can set how many mails you see in the list at once. Adapt this value to your needs</dd>
	<dt>Show X lines at once:</dt>
	<dd>This settings determines how big the textarea (that shows up the mail content) is. Adapting this value may help adjusting the page to your screensize</dd>
	<dt>Show X characters of the subject:</dt>
	<dd>If a subject line is longer than X characters, it will be truncated. This helps to minimize the table widht of the main screen.</dd>
	<dt>Show X characters of the address:</dt>
	<dd>The same things as above with the addresses. This also helps you to reduce the page size</dd>
</dl>

<h3><a name="problems">Problems and known bugs</a></h3>
No program is perfect. Unfotunately also this one :-(<br>
There are a few problems that can arrive:
<ul>
	<li>
		If there's an error message like "MySQL has gone away", it's indicating that a mail which is too big
		was tried to download or an attachment that was sent is too big.<br>
		Normally, the maximum value for attachments is about 500KB. This value depends of various setting of
		the provider this client is running on.<br>
		I hope finding a solution to catch this error.
	</li>
	<li>
		When you open the auto-fetch window, and after that i.e. an attachment, the attachment is shown in the same window if you are using Internet Explorer.
		To open attachments etc. surely in a new window, hold down the shift-key while you click on the link.
	</li>
</ul>

<h3><a name="contact">Contact</a></h3>
Don't hesitate to contact me if you have questions or other things you want to share :-)<br>
<br>
Jean-Pierre Bergamin<br>
<br>
E-Mail: <a href="mailto:james@ractive.ch">james@ractive.ch</a><br>
<br>
Homepage: <a href="http://popper.ractive.ch">http://popper.ractive.ch</a><br>
Forum: <a href="http://forum.ractive.ch">http://forum.ractive.ch</a><br>
<p>&nbsp;</p>
	</td>
	<td>
	&nbsp;
	</td>
</tr>