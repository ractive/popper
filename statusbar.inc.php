	<tr>
		<td colspan="3">
			<table class="statusbar"  style="width: 100%;" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<?php
							if (isset($status_msg)) {
								echo("$status_msg");
							}
							else {
								echo("<p style=\"font-size: small;\">&nbsp;</p>");
							}
						?>
					</td>
					<td width="10%">
						<p style="text-align: right; font-size: x-small; color: black;">
							<?php echo(strftime("%x"));?>
						</p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>