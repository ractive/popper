<tr class="window">
	<td valign="top" width="240">
<?php 
	
	include "lib/cls.panel.php";
	$panel = new Panel();
	$panel->setWidth( '240px' );
	$panel->setHeight( '540px' );
	
	$panel->setTitleCSSClass( "windowtitle" );
	
	$panel->addPanel( "folders", $strings['l_Folders'],  array( $this, "show_folders" ) );
	
	$panel->addPanel( "abook", $strings['l_AddressBook'],  array( $this, "show_addresses" ) );

	$panel->setOpenPanel( "abook" );
	$panel->Display();
?>	
	</td>
	<td>
<?php
	$this->show_address();
?>
	</td>
</tr>