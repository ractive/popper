<table class="windowborder" width="100%" border="0" align="center">
	<tr class="titlebar">
		<td colspan="3">
			<table class="titlebar" width="100%" border="0">
				<tr>
					<td>
						<a href="<?php echo($_SERVER['PHP_SELF']);?>"><img src="graphics/logo.gif" alt="" title="<?php echo($_SESSION['strings']['l_MainView']);?>" style="vertical-align: middle;" border="0"></a><?php echo($_SESSION['titlebar']." <b>".$this->name."</b>");?>
					</td>
					<td>
						<a href="<?php echo($_SERVER['PHP_SELF']."?action=logoff");?>"><img src="graphics/logoff.gif" alt="<?php echo($_SESSION['strings']['l_Logoff']);?>" title="<?php echo($_SESSION['strings']['l_Logoff']);?>" align="right" border="0"></a>
						<a href="<?php echo($_SERVER['PHP_SELF']."?action=help");?>" target="_blank"><img src="graphics/help.gif" alt="<?php echo($_SESSION['strings']['l_Help']);?>" title="<?php echo($_SESSION['strings']['l_Help']);?>" align="right" border="0"></a>	
					</td>
				</tr>
			</table>
		</td>
	</tr>
