<?
/*
	Panel class
	
DESCRIPTION 

	DHTML panel component generator. 
	Generated panel is 100% client-side driven.
	The content of each panel is given as a file to include()

SYNOPSIS

	$pan = new Panel();
	$pan->addPanel( "pan1", "First Panel", "file1.html" );
	$pan->addPanel( "pan2", "2nd Panel", "file2.html" );
	
	echo "<html><head>";
	$pan -> DisplayHead();	
	echo "</head><body>";
	$pan -> Display();
	echo "</body></html>";
	
 Size
	Quite compact, judge by yourself
	Head code (CSS+javascript) : about 900 bytes
	each panel adds about 130 bytes, excluding panel content.

CHANGES
	0.2	+ setTitleBgColor()
	0.3	o bug fixes
			+ extractGlobalVars()
			+ setOpenPanel()
	0.4	o addPanel() added callback support
			o Display() added callback support
			+ setTitleCSSClass()
	
VERSION
	0.4
	
LASTMOD
	ven, 23 fév 2001 14:36:22 GMT

*/

class Panel
{
	var 
		// default property values
		$width = '400px',
		$height = '300px',
		$panelBgColor = 'white',
		$titleBgColor = '#bcbcbc',
		$titleTextColor = 'black',
		$titleCSSClass = 'panelTitle',
		$extractGlobalVars= false;
		
	function Panel()
	{
		$this->panels = array();		
	}

	// true to extract $GLOBALS array before including panel content files
	function extractGlobalVars( $bool = true )
	{
		$this->extractGlobalVars = $bool;
	}
	
	function setOpenPanel( $uid )
	{
		$this->openPanelId = $uid;
	}
	
	// $pan->setWidth( "300px" );
	function setWidth( $w )
	{
		$this->width = $w;
	}
		
	// $pan->setHeight( "500px" );
	function setHeight( $h )
	{
		$this->height = $h;
	}

	function setPanelBgColor( $color )
	{
		$this->panelBgColor = $color;
	}
			
	function setTitleBgColor( $color )
	{
		$this->titleBgColor = $color;
	}
	function setTitleCSSClass( $cssclass )
	{
		$this->titleCSSClass = $cssclass;
	}
				
	function addPanel( $uid, $title, $action )
	{
		if( is_array( $action ) ) {
			$this->panels[] = array( 
				'uid' => $uid,
				'title' => $title,
				'object' => $action[0],
				'method' => $action[1],
			);
		
		} else {
			$this->panels[] = array( 
				'uid' => $uid,
				'title' => $title,
				'file' => $action
			);
		}
	}

	/***
		outputs hidden content ( CSS + javascript )
		distinct from Display() for the case when a single page contains multiple panels
	***/
	function DisplayHead()
	{
		if( defined( "panel_displayhead") )
			return;
			
		define( "panel_displayhead", 1 );
		
?>
<script language="javascript">
var curPanelID = '<?echo $this->openPanelId ?>';

/*
	open or close a panel element
*/
function PanelOpen( objId )
{
	obj = document.getElementById( objId );
	if( obj ) {
		curPanel = document.getElementById( curPanelID );
		if( curPanel ) {
			curPanel.style.display = "none";
		}	
		if( ( ! obj.style.display ) || ( obj.style.display == "none" ) ) {
			obj.style.display = "block";
			curPanelID = objId;
		} else {
			obj.style.display = "none";
		}
	}
}
</script>
<style type="text/css">

div.panelTitle {
	color: <?echo $this->titleTextColor ?>;
	background-color: <?echo $this->titleBgColor ?>;
	border: 1px outset white;
	cursor: pointer;
	padding: 3px;
	margin: 0;
}

div.panelTitle:hover {
	color: #6080A0;
}

div.panelContent {
	background-color: <?echo $this->panelBgColor ?>;
	padding: 2px;
	border: 1px inset gray;
	height: <?echo $this->height ?>;
	overflow: auto;
/*	margin: 0; */
}

</style>
<?

	} // DisplayHead()



/***********************************
 *
 *	Display the panels
 *	
 **********************************/
	function Display()
	{	
		if( $this->extractGlobalVars )
			extract( $GLOBALS );
							
		$this->DisplayHead();		
		reset( $this->panels );

		echo "<div style=\"width: $this->width; height: $this->height\">\n";
		while( list($k,$itm) = each($this->panels) )
		{
			$panelid = $itm['uid'];
			echo "<div class=\"$this->titleCSSClass\" onclick=\"PanelOpen('$panelid');\">$itm[title]</div>\n";

			if( $panelid == $this->openPanelId )
				$display = "block";
			else
				$display = "none";
				
			echo "<div id=\"$panelid\" class=\"panelContent\" style=\"display: $display\">\n";
			if( $itm['file'] ) {
				include( $itm['file'] );
			} else {
				$obj = $itm['object']; $method = $itm['method'];
				$obj->$method();
			}
			echo "</div>\n";
		}
		echo "</div>\n";

	} // display()

}
?>
