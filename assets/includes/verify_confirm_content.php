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

$email = User::get_email();
$js = <<<JS
function verify() {
    $('#verify_dialog input').val('');
    $('#verify_error_message').html('');
    $('#verify_dialog').dialog({
        width: 600,
        modal: true,
        buttons: {
            "OK": function() {
                $.get('assets/actions/send_verification_email.php', function() {
                    $('#verify_dialog').dialog('close');
                    verifyNotice();
                });
            },
            "Cancel": function() {
                $('#verify_dialog').dialog('close');
            }
        }
    });
}
function verifyNotice() {
    $('#verify_notice').dialog({
        modal: true,
        width: 450,
        buttons: {
            "OK": function() {
                $('#verify_notice').dialog('close');
            }
        }
    });
}
JS;

$html = <<<HTML
<div id="verify_dialog" title="Verify Your Account?">
    <p id="verify_header">Please click OK below to begin the verification process. An email will
     be sent to $email with a link containing your verification code. Please click that link
     to verify your account. Be sure to check your spam or junk mail folders if you do not 
     see the verification email soon.</p>
</div>
<div id="verify_notice" title="Your account verification link has been sent!">
    <p>A verification link has been sent to the registered email address for your
    account. Please check your email. If it does not arrive soon, please be sure to 
    check your junk or spam folders.</p>
</div>
HTML;

$css = <<<CSS

.ui-dialog-titlebar-close {
    display: none;
}

#verify_dialog {
    display: none;
}

#verify_notice {
    display: none;
}
CSS;




$obj = new LoadableContent($js, $html, $css);
$obj->load();