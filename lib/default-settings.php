<?php // Copyright (c) 2019, SWITCH?>

<!-- Identity Provider Selection: Start -->
<h1><?php echo getLocalString('permanent_select_header'); ?></h1>
<form id="IdPList" name="IdPList" method="post" onSubmit="<?php echo printSubmitAction() ?>" action="<?php echo $actionURL ?>">
    <div id="userInputArea">
        <p class="promptMessage"><?php echo getLocalString('permanent_cookie'); ?></p>
        <p><?php echo getLocalString('select_idp'); ?></p>
        <div style="text-align: center">
            <select name="user_idp" id="userIdPSelection" class="userIdPSelection" tabindex="0">
                <?php
          // If we use select2, we don't want IDP to be in DOM, but to use AJAX instead
                if (!isUseSelect2()) {
                    echo '<option value="-" ' . $defaultSelected . '>' . getLocalString('select_idp') . ' ...</option>';
                    printDropDownList($IDProviders, $selectedIDP);
                }
                ?>
            </select>

            <input type="submit" name="Select" accesskey="s" value="<?php echo getLocalString('save_button') ?>" >
        </div>
        <!-- Value permanent must be a number which is equivalent to the days the cookie should be valid -->
        <input name="permanent" type="hidden" value="100">
    </div>
</form>

<p><?php echo getLocalString('additional_info') ?></p>
<!-- Identity Provider Selection: End -->
