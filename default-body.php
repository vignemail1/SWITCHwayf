<?php // Copyright (c) 2011, SWITCH - Serving Swiss Universities ?>

<!-- Identity Provider Selection: Start-->
<h1><?php echo getLocalString('header'); ?></h1> 
<p class="switchaai">
	<?php echo $promptMessage ?>
</p>

<form id="IdPList" name="IdPList" method="post" onSubmit="return checkForm()" action="<?php echo $actionURL ?>">
	<p>
		<select name="user_idp"> 
			<option value="-" <?php echo $defaultSelected ?>><?php echo getLocalString('select_idp') ?> ...</option>
		<?php printDropDownList($IDProviders, $selectedIDP) ?>
		</select>
		<input type="submit" name="Select" accesskey="s" value="<?php echo getLocalString('select_button') ?>" > 
	</p>
	<p>
		<input type="checkbox" <?php echo $rememberSelectionChecked ?> name="session" id="rememberForSession" value="true">
		<span class="warning"><label for="rememberForSession"><?php echo getLocalString('remember_selection') ?></label></span><br>
		<?php if ($showPermanentSetting) : ?>
		<!-- Value permanent must be a number which is equivalent to the days the cookie shall be valid -->
		<input type="checkbox" name="permanent" id="rememberPermanent" value="100">
		<span class="warning"><label for="rememberPermanent" /><?php echo getLocalString('permanently_remember_selection') ?></label></span>
		<?php endif ?>
	</p>
</form>
<table border="0" cellpadding="1" cellspacing="0">
	<tr>
		<td valign="top" width="14"><img src="<?php echo $imageURL; ?>/arrow-12.gif" alt="arrow"></td>
		<td valign="top"><p class="switchaai"><?php echo getLocalString('switch_description') ?></p></td>
	</tr>
</table>
<!-- Identity Provider Selection: End-->
