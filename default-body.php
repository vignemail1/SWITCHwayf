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
		<input type="submit" name="Select" accesskey="s" tabindex="10" value="<?php echo getLocalString('select_button') ?>" > 
	</p>
	<p>
		<input tabindex="8" type="checkbox" <?php $rememberSelectionChecked ?> name="session" value="true">
		<span class="warning"><?php echo getLocalString('remember_selection') ?></span><br>
		<?if ($showPermanentSetting) : ?>
		<!-- Value permanent must be a number which is equivalent to the days the cookie shall be valid -->
		<input type="checkbox" tabindex="9" name="permanent" value="100">
		<span class="warning"><?php echo getLocalString('permanently_remember_selection') ?></span>
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
