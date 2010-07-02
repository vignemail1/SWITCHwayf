<!-- Identity Provider Permanent Note: Start-->
<h1><?php echo getLocalString('settings'); ?></h1> 
<script language="JavaScript" type="text/javascript">
<!--
function showConfirmation(){
	
	return alert(unescape('<?php echo getLocalString('permanent_cookie_note', 'js') ?>'));
}
-->
</script>
<div class="inner-box">
<p>
	<?php echo getLocalString('permanent_cookie_notice'); ?>
</p>

<form id="IdPList" name="IdPList" method="post" action="<?php echo $actionURL ?>">
		<?php echo $hiddenUserIdPInput ?>
		<div align="center">
			<div class="selectedIdP">
			<?php echo $permanentUserIdPName ?>
			</div>
			<p>
				<input type="submit" tabindex="7" accesskey="c" name="clear_user_idp" value="<?php echo getLocalString('delete_permanent_cookie_button') ?>">
				<?php if (isValidShibRequest()) : ?>
				<input type="submit" accesskey="s" name="Select" name="permanent" value="<?php echo getLocalString('goto_sp') ?>" onClick="showConfirmation()">
				<?php endif ?>
			</p>
		</div>
</form>
</div>
<!-- Identity Provider Permanent Note: End-->
