<?php
/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

class cal {

	var $month = 0;

	function cal() {
		$this->month = 0;
	}
	

	function show() {
		if ($GLOBALS[show] == "next") {
			$this->month++;
		}
		else if ($GLOBALS[show] == "prev") {
			$this->month--;
		}
		else if ($GLOBALS[show] == "current") {
			$this->month = 0;
		}	
		
		$this->show_month();
	}

	function show_month() {
		global $strings;
		
		$time = time();
		$time = mktime(0, 0, 0, date("m", $time) + $this->month, date("j"), date("y", $time));
		$days_of_month = date("t", $time);
		$days_of_last_month = date("t", mktime(0, 0, 0, date("m", $time) - 1, 1, date("y", $time)));
		$start_day = (date("w", mktime(0, 0, 0, date("m", $time), 1, date("y", $time))) + 6) % 7;
		$today = date("j", $time);
	
		// Get the past monday of this week
		$monday = mktime(0, 0, 0, date("m"), date("j") - date("w") + 1, date("y"));
?>		
		<table width="100%" border="1">
			<caption style="font-weight: bolder; font-size: x-large;"><?php echo(ucfirst(strftime("%B %Y", $time)));?></caption>
			<tr>
				<th style="width: 14%; font-size: smaller;"><?php echo(ucfirst(strftime("%A", $monday)));?></th>
				<th style="width: 14%; font-size: smaller;"><?php echo(ucfirst(strftime("%A", mktime(0, 0, 0, date("m", $monday), date("j", $monday) + 1, date("y", $monday)))));?></th>
				<th style="width: 14%; font-size: smaller;"><?php echo(ucfirst(strftime("%A", mktime(0, 0, 0, date("m", $monday), date("j", $monday) + 2, date("y", $monday)))));?></th>
				<th style="width: 14%; font-size: smaller;"><?php echo(ucfirst(strftime("%A", mktime(0, 0, 0, date("m", $monday), date("j", $monday) + 3, date("y", $monday)))));?></th>
				<th style="width: 14%; font-size: smaller;"><?php echo(ucfirst(strftime("%A", mktime(0, 0, 0, date("m", $monday), date("j", $monday) + 4, date("y", $monday)))));?></th>
				<th style="width: 14%; font-size: smaller;"><?php echo(ucfirst(strftime("%A", mktime(0, 0, 0, date("m", $monday), date("j", $monday) + 5, date("y", $monday)))));?></th>
				<th style="font-size: smaller;"><?php echo(ucfirst(strftime("%A", mktime(0, 0, 0, date("m", $monday), date("j", $monday) + 6, date("y", $monday)))));?></th>
			</tr>
<?php
			for($i = 0; $i < 6 * 7; $i++) {
				$style = "height: 5em; ";
				// The previous month
				if ($i < $start_day) {
					$cur_day = $days_of_last_month - $start_day + $i + 1;
					$style .= " color: silver;";
				}
				// The current month
				else if ($i >= $start_day && ($i - $start_day) < $days_of_month){
					$cur_day = $i-$start_day + 1;
					// If it's today, add a background color to the style
					if ($cur_day == $today && $this->month == 0) {
						$style .= " font-weight: bolder; background-color: #FFFFDD;";
					}
				}
				// Next month
				else {
					$cur_day = $i - $start_day - $days_of_month + 1;
					$style .= " color: silver;";
				}
				if (($i % 7) == 0) {
					echo("</tr>\n<tr>\n");
				}
				echo("<td style=\"$style\" valign=\"top\">$cur_day</td>\n");
			}
	
?>
			</tr>
			<tr class="calcontrol">
				<td>
					 <a href="<?php echo("$GLOBALS[PHP_SELF]");?>?action=showfolder&folder=cal&show=prev">&lt;&lt;&lt; <?php echo($strings["l_Previous"]);?></a>
				</td>
				<td colspan="2">
				</td>
				<td>
					<a href="<?php echo("$GLOBALS[PHP_SELF]");?>?action=showfolder&folder=cal&show=current"><?php echo($strings["l_Current"]);?></a>
				</td>
				<td colspan="2">
				</td>
				<td>
					<a href="<?php echo("$GLOBALS[PHP_SELF]");?>?action=showfolder&folder=cal&show=next"><?php echo($strings["l_Next"]);?> &gt;&gt;&gt;</a>
				</td>
			</tr>
		</table>	
<?php
	}
	
	function show_week() {
		global $strings;
		
		$time = time();
		$time = mktime(0, 0, 0, date("m", $time) + $this->month, date("j"), date("y", $time));
		$days_of_month = date("t", $time);
		$days_of_last_month = date("t", mktime(0, 0, 0, date("m", $time) - 1, 1, date("y", $time)));
		$start_day = (date("w", mktime(0, 0, 0, date("m", $time), 1, date("y", $time))) + 6) % 7;
		$today = date("j", $time);
	
		// Get the past monday of this week
		$monday = mktime(0, 0, 0, date("m"), date("j") - date("w") + 1, date("y"));
?>		
		<table width="100%" border="1">
			<caption style="font-weight: bolder; font-size: x-large;"><?php echo(ucfirst(strftime("%V %Y", $time)));?></caption>
			<tr>
				<th style="width: 14%; font-size: smaller;"><?php echo(ucfirst(strftime("%A", $monday)));?></th>
				<th style="width: 14%; font-size: smaller;"><?php echo(ucfirst(strftime("%A", mktime(0, 0, 0, date("m", $monday), date("j", $monday) + 1, date("y", $monday)))));?></th>
				<th style="width: 14%; font-size: smaller;"><?php echo(ucfirst(strftime("%A", mktime(0, 0, 0, date("m", $monday), date("j", $monday) + 2, date("y", $monday)))));?></th>
				<th style="width: 14%; font-size: smaller;"><?php echo(ucfirst(strftime("%A", mktime(0, 0, 0, date("m", $monday), date("j", $monday) + 3, date("y", $monday)))));?></th>
				<th style="width: 14%; font-size: smaller;"><?php echo(ucfirst(strftime("%A", mktime(0, 0, 0, date("m", $monday), date("j", $monday) + 4, date("y", $monday)))));?></th>
				<th style="width: 14%; font-size: smaller;"><?php echo(ucfirst(strftime("%A", mktime(0, 0, 0, date("m", $monday), date("j", $monday) + 5, date("y", $monday)))));?></th>
				<th style="font-size: smaller;"><?php echo(ucfirst(strftime("%A", mktime(0, 0, 0, date("m", $monday), date("j", $monday) + 6, date("y", $monday)))));?></th>
			</tr>
<?php
			for($i = 0; $i < 6 * 7; $i++) {
				$style = "height: 5em; ";
				// The previous month
				if ($i < $start_day) {
					$cur_day = $days_of_last_month - $start_day + $i + 1;
					$style .= " color: silver;";
				}
				// The current month
				else if ($i >= $start_day && ($i - $start_day) < $days_of_month){
					$cur_day = $i-$start_day + 1;
					// If it's today, add a background color to the style
					if ($cur_day == $today && $this->month == 0) {
						$style .= " font-weight: bolder; background-color: #FFFFDD;";
					}
				}
				// Next month
				else {
					$cur_day = $i - $start_day - $days_of_month + 1;
					$style .= " color: silver;";
				}
				if (($i % 7) == 0) {
					echo("</tr>\n<tr>\n");
				}
				echo("<td style=\"$style\" valign=\"top\"><a href=\"\">$cur_day</a></td>\n");
			}
	
?>
			</tr>
			<tr class="calcontrol">
				<td>
					 <a href="<?php echo("$GLOBALS[PHP_SELF]");?>?action=showfolder&folder=cal&show=prev">&lt;&lt;&lt; <?php echo($strings["l_Previous"]);?></a>
				</td>
				<td colspan="2">
				</td>
				<td>
					<a href="<?php echo("$GLOBALS[PHP_SELF]");?>?action=showfolder&folder=cal&show=current"><?php echo($strings["l_Current"]);?></a>
				</td>
				<td colspan="2">
				</td>
				<td>
					<a href="<?php echo("$GLOBALS[PHP_SELF]");?>?action=showfolder&folder=cal&show=next"><?php echo($strings["l_Next"]);?> &gt;&gt;&gt;</a>
				</td>
			</tr>
		</table>	
<?php	
	}
	
	function show_week5() {
	
	}
	
	function show_day() {
	
	}

}

?>