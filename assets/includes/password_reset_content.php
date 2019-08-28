<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/23/2018
 * Time: 7:18
 */

require_once('LoadableContent.php');
require_once('User.php');

session_start();

$password_reset_username_key = User::PASSWORD_RESET_USERNAME_KEY;
$status_error = USER::STATUS_ERROR;
$js = <<<JS
function passwordReset(username) {
    $('#password_reset_dialog input').val(username);
    $('#password_reset_dialog').dialog({
        width: 600,
        modal: true,
        buttons: {
            "OK": function() {
                $.get('assets/actions/send_reset_email.php?' + 
                $('#password_reset_dialog input').serialize(), function() {
                    $('#password_reset_dialog').dialog('close');
                    passwordResetNotice();
                });
            },
            "Cancel": function() {
                $('#password_reset_dialog').dialog('close');
            }
        }
    });
}
function passwordResetNotice() {
    $('#password_reset_notice').dialog({
        modal: true,
        width: 450,
        buttons: {
            "OK": function() {
                $('#password_reset_notice').dialog('close');
            }
        }
    });
}
JS;

$html = <<<HTML
<div id="password_reset_dialog" title="Do you want to reset your password?">
    <p id="password_reset_header">Please enter your username and click ok. A password reset link
    will be sent to the registered email address for your account.</p>
    <fieldset>
        <label for="{$password_reset_username_key}">Username: </label>
        <input type="text" name="{$password_reset_username_key}" id="{$password_reset_username_key}" 
         value="">
    </fieldset>
         
</div>
<div id="password_reset_notice" title="Your password reset link has been sent!">
    <p>A password reset link has been sent to the registered email address for your
    account. Please check your email. If it does not arrive soon, please be sure to 
    check your junk or spam folders.</p>
</div>
HTML;

$css = <<<CSS

.ui-dialog-titlebar-close {
    display: none;
}

#password_reset_dialog {
    display: none;
}

#password_reset_notice {
    display: none;
}

fieldset {
    padding: 20px;
}

fieldset input {
    display: block;
    margin-bottom: 12px;
    width: 30em;
}

fieldset label {
    display: block;
}

#input_fields {
    float: left;
    width: 40%;
}
CSS;




$obj = new LoadableContent($js, $html, $css);
$obj->load();