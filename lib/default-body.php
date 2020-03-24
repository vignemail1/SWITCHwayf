<?php // Copyright (c) 2019, SWITCH?>

<!-- Identity Provider Selection: Start -->
<h1><?php echo getLocalString('header'); ?></h1>
<form id="IdPList" name="IdPList" method="post" onSubmit="<?php echo printSubmitAction() ?>" action="<?php echo $actionURL ?>">
	<div id="userInputArea">
		<p class="promptMessage"><?php echo $promptMessage ?></p>
		<div style="text-align: center">

			<select name="user_idp" id="userIdPSelection" class="userIdPSelection" tabindex="0">
				<?php
          // If we use select2, we don't want IDP to be in DOM, but to use AJAX instead
          if (!isUseSelect2()) {
              echo '<option value="-" '.$defaultSelected.'>'.getLocalString('select_idp').' ...</option>';
              printDropDownList($IDProviders, $selectedIDP);
          }
            ?>
			</select>

			<input type="submit" name="Select" accesskey="s" value="<?php echo getLocalString('select_button') ?>">
		</div>
		<div  style="text-align: left">
			<p class="selectOptions">
				<input type="checkbox" <?php echo $rememberSelectionChecked ?> name="session" id="rememberForSession" value="true">
				<label for="rememberForSession"><?php echo getLocalString('remember_selection') ?></label><br>
				<?php if ($showPermanentSetting) : ?>
				<!-- Value permanent must be a number which is equivalent to the days the cookie should be valid -->
				<input type="checkbox" name="permanent" id="rememberPermanent" value="100">
				<label for="rememberPermanent"><?php echo getLocalString('permanently_remember_selection') ?></label>
				<?php endif ?>
			</p>
		</div>
	</div>
</form>

<?php if (getLocalString('additional_info') != '') {
                ?>
<p><?php echo getLocalString('additional_info') ?></p>
<?php
            } ?>
<!-- Identity Provider Selection: End -->
