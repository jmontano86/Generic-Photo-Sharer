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

$register_username_key = User::REGISTER_USERNAME_KEY;
$register_email_key = User::REGISTER_EMAIL_KEY;
$register_password_key = User::REGISTER_PASSWORD_KEY;
$register_password_confirmation_key = User::REGISTER_PASSWORD_CONFIRMATION_KEY;
$status_error = User::STATUS_ERROR;
$js = <<<JS
function register() {
    $('#register_dialog input').val('');
    $('#register_error_message').html('');
    $('#register_dialog').dialog({
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
                        '/assets/actions/do_register.php',  
                $('#register_dialog input').serialize() + '&sess_id=' + sess_id,
                function(data) {
                    if(data.status === "{$status_error}") {
                        $('#register_error_message').html(data.message)
                    } else {
                        $('#register_dialog').dialog('close');
                        updateNavbar();
                    }
                });

            },
            "Cancel": function() {
                $('#register_dialog').dialog('close');
            }
        }
    });
}
JS;

$html = <<<HTML
<div id="register_dialog" title="Please Create Your Account">
    <div id="register_error_message"></div>
    <p id="register_header">Please enter your username, email address, and your password: </p>
    <fieldset>
        <label for="{$register_username_key}">Username</label>
        <input type="text" name="{$register_username_key}" id="{$register_username_key}" 
         value="">
        <label for="{$register_email_key}">Email</label>
        <input type="text" name="{$register_email_key}" id="{$register_email_key}" 
         value="">
        <label for="{$register_password_key}">Password</label>
        <input type="password" name="{$register_password_key}" id="{$register_password_key}" 
         value="">
        <label for="{$register_password_confirmation_key}">Confirm Password</label>
        <input type="password" name="{$register_password_confirmation_key}" id="{$register_password_confirmation_key}" 
         value="">
    </fieldset>
</div>
HTML;

$css = <<<CSS

.ui-dialog-titlebar-close {
    display: none;
}

#register_dialog {
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