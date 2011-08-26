<?php // Copyright (c) 2011, SWITCH - Serving Swiss Universities ?>

<!-- Identity Provider Selection: Start -->
<h1><?php echo getLocalString('settings'); ?></h1> 
<form id="IdPList" name="IdPList" method="post" onSubmit="return checkForm()" action="<?php echo $actionURL ?>">
	<div id="userInputArea">
		<p><?php echo getLocalString('permanent_cookie_notice'); ?></p>
		<div align="center">
			<select name="permanent_user_idp" id="userIdPSelection" disabled="disabled"> 
				<option value="-"><?php echo $permanentUserIdPName ?></option>
			</select>
			<input type="submit" accesskey="c" name="clear_user_idp" value="<?php echo getLocalString('delete_permanent_cookie_button') ?>">
			<?php if (isValidShibRequest()) : ?>
			<input type="submit" accesskey="s" name="Select" name="permanent" value="<?php echo getLocalString('goto_sp') ?>" onClick="showPermanentConfirmation()">
			<?php endif ?>
		</div>
	</div>
</form>

<p><?php echo getLocalString('additional_info') ?></p>
<!-- Identity Provider Selection: End -->
