<?php // Copyright (c) 2012, SWITCH - Serving Swiss Universities ?>

<!-- Identity Provider Selection: Start -->
<h1><?php echo getLocalString('header'); ?></h1> 
<form id="IdPList" name="IdPList" method="post" onSubmit="return checkForm()" action="<?php echo $actionURL ?>">
	<div id="userInputArea">
		<p class="promptMessage"><?php echo $promptMessage ?></p>
		<div align="center">
			<select name="user_idp" id="userIdPSelection"> 
				<option value="-" <?php echo $defaultSelected ?>><?php echo getLocalString('select_idp') ?> ...</option>
			<?php printDropDownList($IDProviders, $selectedIDP) ?>
			</select>
			<input type="submit" name="Select" accesskey="s" value="<?php echo getLocalString('select_button') ?>"> 
		</div>
		<div align="left">
			<p class="selectOptions">
				<input type="checkbox" <?php echo $rememberSelectionChecked ?> name="session" id="rememberForSession" value="true">
				<label for="rememberForSession"><?php echo getLocalString('remember_selection') ?></label><br>
				<?php if ($showPermanentSetting) : ?>
				<!-- Value permanent must be a number which is equivalent to the days the cookie shall be valid -->
				<input type="checkbox" name="permanent" id="rememberPermanent" value="100">
				<label for="rememberPermanent" /><?php echo getLocalString('permanently_remember_selection') ?></label>
				<?php endif ?>
			</p>
		</div>
	</div>
</form>

<p><?php echo getLocalString('additional_info') ?></p>
<!-- Identity Provider Selection: End -->
