<?php // Copyright (c) 2011, SWITCH - Serving Swiss Universities ?>

<!-- Identity Provider Selection: Start -->
<h1><?php echo getLocalString('permanent_select_header'); ?></h1> 
<form id="IdPList" name="IdPList" method="post" onSubmit="return checkForm()" action="<?php echo $actionURL ?>">
	<table class="login_field" width="500">
		<tr>
			<td>
				<!-- Value permanent must be a number which is equivalent to the days the cookie shall be valid -->
				<input name="permanent" type="hidden" value="100">
				<p><?php echo getLocalString('permanently_remember_selection') ?></p>
				<p>
					<select name="user_idp"> 
						<option value="-" <?php echo $defaultSelected ?>><?php echo getLocalString('select_idp') ?> ...</option>
					<?php printDropDownList($IDProviders, $selectedIDP) ?>
					</select>
					<input type="submit" name="Select" accesskey="s" value="<?php echo getLocalString('save_button') ?>" >
				</p>
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
