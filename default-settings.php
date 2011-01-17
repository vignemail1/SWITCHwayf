<?php // Copyright (c) 2011, SWITCH - Serving Swiss Universities ?>

<!-- Identity Provider Permanent Settings: Start-->
<h1><?php echo getLocalString('permanent_select_header') ?></h1> 
<p class="switchaai">
<?php echo getLocalString('permanent_cookie') ?>
</p>
<div class="inner-box">
<form id="IdPList" name="IdPList" method="post" onSubmit="return checkForm()" action="<?php echo $actionURL ?>">
	<!-- Value permanent must be a number which is equivalent to the days the cookie shall be valid -->
	<input name="permanent" type="hidden" value="100">
	<p><strong><?php echo getLocalString('permanently_remember_selection') ?></strong></p>
	<p>
		<select name="user_idp"> 
			<option value="-" <?php echo $defaultSelected ?>><?php echo getLocalString('select_idp') ?> ...</option>
		<?php printDropDownList($IDProviders, $selectedIDP) ?>
		</select>
		<input type="submit" name="Select" accesskey="s" value="<?php echo getLocalString('save_button') ?>" >
	</p>
</form>
</div>
<!-- Identity Provider Permanent Settings: End-->
