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

$login_username_key = User::LOGIN_USERNAME_KEY;
$login_password_key = User::LOGIN_PASSWORD_KEY;
$status_error = User::STATUS_ERROR;
$js = <<<JS
function login() {
    $('#login_dialog input').val('');
    $('#login_error_message').html('');
    $('#forgot_password').click(function() {
        loadContent('assets/includes/password_reset_content.php', function() {
            passwordReset($('input[type=text][name=username]').val());
            $('#login_dialog').dialog('close');
        })
    });
    $('#forgot_username').click(function() {
        loadContent('assets/includes/send_usernames_content.php', function() {
            sendUsernames();
            $('#login_dialog').dialog('close');
        })
    });
    $('#login_dialog').dialog({
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
                        '/assets/actions/do_login.php', 
                $('#login_dialog input').serialize() + '&sess_id=' + sess_id,
                function(data) {
                    if(data.status === "{$status_error}") {
                        $('#login_error_message').html(data.message)
                    } else {
                        $('#login_dialog').dialog('close');
                        updateNavbar();
                    }
                });

            },
            "Cancel": function() {
                $('#login_dialog').dialog('close');
            }
        }
    });
}
JS;

$html = <<<HTML
<div id="login_dialog" title="Please Login To Your Account">
    <div id="login_error_message"></div>
    <p id="login_header">Please enter your username and password: </p>
    <ul>
        <li id="forgot_password"><span class="linked">Forgot your password?</span></li>
        <li id="forgot_username"><span class="linked">Forgot your username?</span></li>
    </ul>
    <fieldset>
        <label for="{$login_username_key}">Username</label>
        <input type="text" name="{$login_username_key}" id="{$login_username_key}" 
         value="">
        <label for="{$login_password_key}">Password</label>
        <input type="password" name="{$login_password_key}" id="{$login_password_key}" 
         value="">
    </fieldset>
</div>
HTML;

$css = <<<CSS

.ui-dialog-titlebar-close {
    display: none;
}

#login_dialog {
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

.linked {
    color: green;
}
.linked:hover {
    cursor:pointer;
}
CSS;




$obj = new LoadableContent($js, $html, $css);
$obj->load();