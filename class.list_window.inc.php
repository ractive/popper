<?php

include("class.window.inc.php");

define("LEFT", 0);
define("CENTER", 1);
define("RIGHT", 2);	
	
class list_window extends window {
	
	var $table = array();
	var $cols = array();
	
	function insert_column($col_no, $heading, $align = CENTER, $width = -1) {
		$entry = array("heading" => $heading, "align" => $align, "width" => $width);
		$this->cols[$col_no] = $entry;
	}
	
	function insert_item($item_no, $sub_item, $content) {
		$this->table[$item_no][$sub_item] = $content;
	}
	
	function show_content() {
		//echo('<div style="overflow: auto; height: 300px; width: auto; margin: 0;">');
		echo("<table class=\"window\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n");
		$this->show_header();
		foreach($this->table as $tr) {
			echo("<tr>\n");
			foreach($tr as $td) {
				echo("<td>$td</td>\n");
			}
			echo("</tr>\n");
		}
		echo("</table>\n");
		//echo('</div>');
	}
	
	function show_header() {
		echo("<tr>\n");
		foreach($this->cols as $col) {
			echo("<th class=\"smallbutton\" ");
			if ($col["width"] != -1 || $col["align"] != CENTER) {
				echo(' style="');
				if ($col["width"] != -1) {
					echo("width: ".$col["width"]."px; ");
				}
				if ($col["align"] == LEFT) {
					echo("text-align: LEFT;");
				}
				else if ($col["align"] == RIGHT) {
					echo("text-align: right;");
				}
				else if ($col["align"] == CENTER) {
					echo("text-align: center;");
				}
				echo('"');
			}
			echo(">".$col["heading"]."</th>\n");
		}
		echo("</tr>\n");
	}
}
?>