<?php // Copyright (c) 2011, SWITCH - Serving Swiss Universities ?>

<!-- Identity Provider Selection: Start -->
<h1><?php echo getLocalString('settings'); ?></h1> 
<form id="IdPList" name="IdPList" method="post" onSubmit="return checkForm()" action="<?php echo $actionURL ?>">
	<table class="userInputArea" width="500">
		<tr>
			<td>
				<p><?php echo getLocalString('permanent_cookie_notice'); ?></p>
				<div align="center">
					<div class="selectedIdP" style="float: left">
						<?php echo $permanentUserIdPName ?>
					</div>
						<input style="float: left" type="submit" accesskey="c" name="clear_user_idp" value="<?php echo getLocalString('delete_permanent_cookie_button') ?>">
						<?php if (isValidShibRequest()) : ?>
						<input style="float: left" type="submit" accesskey="s" name="Select" name="permanent" value="<?php echo getLocalString('goto_sp') ?>" onClick="showPermanentConfirmation()">
						<?php endif ?>
				</div>
			</td>
		</tr>
	</table>
</form>


<table border="0" cellpadding="1" cellspacing="0">
	<tr>
		<td valign="top">
			<p><?php echo getLocalString('switch_description') ?></p>
		</td>
	</tr>
</table>
<!-- Identity Provider Selection: End -->
