/*========================================================================
A POP3 web mail-client written in PHP
Copyright (C) 2000 by Jean-Pierre Bergamin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License.

For more information see the file gpl.txt or go to
http://www.gnu.org/copyleft/gpl.html

==========================================================================*/

<?php
	if (isset($GLOBALS[show])) {
		if ($GLOBALS[show] == "next") {
			$GLOBALS[month] = $GLOBALS[month] + 1;
		}
		else if ($GLOBALS[show] == "prev") {
			$GLOBALS[month] = $GLOBALS[month] - 1;
		}
		else if ($GLOBALS[show] == "current") {
			$GLOBALS[month] = 0;
		}
	}
	else {
		$month = 0;
		session_register("month");
	}

	$time = time();
	$time = mktime(0, 0, 0, date("m", $time) + $GLOBALS[month], date("j"), date("y", $time));
	$days_of_month = date("t", $time);
	$days_of_last_month = date("t", mktime(0, 0, 0, date("m", $time) - 1, 1, date("y", $time)));
	$start_day = (date("w", mktime(0, 0, 0, date("m", $time), 1, date("y", $time))) + 6) % 7;
	$today = date("j", $time);

?>
	<table width="100%" border="1">
		<caption style="font-weight: bolder; font-size: x-large;"><?php echo(date("F Y", $time));?></caption>
		<tr>
			<th style="width: 14%; font-size: smaller;">Monday</th>
			<th style="width: 14%; font-size: smaller;">Tuesday</th>
			<th style="width: 14%; font-size: smaller;">Wednesday</th>
			<th style="width: 14%; font-size: smaller;">Thursday</th>
			<th style="width: 14%; font-size: smaller;">Friday</th>
			<th style="width: 14%; font-size: smaller;">Saturday</th>
			<th style="font-size: smaller;">Sunday</th>
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
				if ($cur_day == $today && $GLOBALS[month] == 0) {
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
				 <a href="<?php echo("$GLOBALS[PHP_SELF]");?>?action=showfolder&folder=cal&show=prev">&lt;&lt;&lt; Previous</a>
			</td>
			<td colspan="2">
			</td>
			<td>
				<a href="<?php echo("$GLOBALS[PHP_SELF]");?>?action=showfolder&folder=cal&show=current">Current</a>
			</td>
			<td colspan="2">
			</td>
			<td>
				<a href="<?php echo("$GLOBALS[PHP_SELF]");?>?action=showfolder&folder=cal&show=next">Next &gt;&gt;&gt;</a>
			</td>
		</tr>
	</table>