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

$send_usernames_email_key = User::SEND_USERNAMES_EMAIL_KEY;
$status_error = USER::STATUS_ERROR;
$js = <<<JS
function sendUsernames() {
    $('#send_usernames_dialog input').val('');
    $('#send_usernames_dialog').dialog({
        width: 600,
        modal: true,
        buttons: {
            "OK": function() {
                $.get('assets/actions/send_usernames_email.php?' + 
                $('#send_usernames_dialog input').serialize(), function() {
                    $('#send_usernames_dialog').dialog('close');
                    sendUsernamesNotice();
                });
            },
            "Cancel": function() {
                $('#send_usernames_dialog').dialog('close');
            }
        }
    });
}
function sendUsernamesNotice() {
    $('#send_usernames_notice').dialog({
        modal: true,
        width: 450,
        buttons: {
            "OK": function() {
                $('#send_usernames_notice').dialog('close');
            }
        }
    });
}
JS;

$html = <<<HTML
<div id="send_usernames_dialog" title="Look up your username">
    <p id="send_usernames_header">Please enter your email address and click ok. Your 
    registered username(s) will be sent to your email address.</p>
    <fieldset>
        <label for="{$send_usernames_email_key}">Email: </label>
        <input type="text" name="{$send_usernames_email_key}" id="{$send_usernames_email_key}" 
         value="">
    </fieldset>
         
</div>
<div id="send_usernames_notice" title="Your username(s) have been sent!">
    <p>Your username(s) has been sent to the email address you entered.
    Please check your email. If it does not arrive soon, please be sure to 
    check your junk or spam folders.</p>
</div>
HTML;

$css = <<<CSS

.ui-dialog-titlebar-close {
    display: none;
}

#send_usernames_dialog {
    display: none;
}

#send_usernames_notice {
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