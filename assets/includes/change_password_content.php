<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/23/2018
 * Time: 7:18
 */

require_once('LoadableContent.php');
require_once('User.php');
require_once('SharerDatabase.php');

$change_password_password_key = User::CHANGE_PASSWORD_KEY;
$change_password_password_confirmation_key = User::CHANGE_PASSWORD_CONFIRMATION_KEY;
$change_password_username_key = User::CHANGE_PASSWORD_USERNAME_KEY;
$change_password_code_key = User::CHANGE_PASSWORD_CODE_KEY;
$status_error = User::STATUS_ERROR;
$js = <<<JS
function changePassword(username, code) {
    $('#change_password_dialog input').val('');
    $('#change_password_error_message').html('');
    $('#change_password_dialog').dialog({
        width: 600,
        modal: true,
        buttons: {
            "OK": function() {
                var cookies = document.cookie.split('; ');
                var sess_id = '';
                for(i = 0; i < cookies.length; i++) {
                    if(cookies[i].indexOf('PHPSESSID=' == 0)) {
                        sess_id = cookies[i].substr(cookies[i].indexOf('=') + 1);
                    }
                };
                $.post(
                    "https://" + location.hostname + location.pathname.substr(
                        0, location.pathname.lastIndexOf('/')) +
                        '/assets/actions/do_change_password.php',  
                $('#change_password_dialog input').serialize() + '&sess_id=' + sess_id + 
                '&{$change_password_username_key}=' + username + '&{$change_password_code_key}=' + code,
                function(data) {
                    if(data.status === "{$status_error}") {
                        $('#change_password_error_message').html(data.message)
                    } else {
                        $('#change_password_dialog').dialog('close');
                        updateNavbar();
                    }
                });

            },
            "Cancel": function() {
                $('#change_password_dialog').dialog('close');
            }
        }
    });
}
JS;

$html = <<<HTML
<div id="change_password_dialog" title="Please Enter Your New Password">
    <div id="change_password_error_message"></div>
    <p id="change_password_header">Please enter your new password: </p>
    <fieldset>
        <label for="{$change_password_password_key}">Password</label>
        <input type="password" name="{$change_password_password_key}" id="{$change_password_password_key}" 
         value="">
        <label for="{$change_password_password_confirmation_key}">Confirm Password</label>
        <input type="password" name="{$change_password_password_confirmation_key}" id="{$change_password_password_confirmation_key}" 
         value="">
    </fieldset>
</div>
HTML;

$css = <<<CSS

.ui-dialog-titlebar-close {
    display: none;
}

#change_password_dialog {
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