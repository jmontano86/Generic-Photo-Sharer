<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/30/2018
 * Time: 6:22
 */

class User
{
    const REGISTER_USERNAME_KEY = 'username';
    const REGISTER_EMAIL_KEY = 'email';
    const REGISTER_PASSWORD_KEY = 'password';
    const REGISTER_PASSWORD_CONFIRMATION_KEY = 'password_confirmation';
    const USERNAME_KEY = 'username';
    const CODE_KEY = 'code';
    const ROLE_KEY = 'role';
    const EMAIL_KEY = 'email';
    const HASH_KEY = 'hash';
    const PASSWORD_RESET_KEY = 'reset';

    const CHANGE_PASSWORD_KEY = 'password';
    const CHANGE_PASSWORD_CONFIRMATION_KEY = 'password_confirmation';
    const CHANGE_PASSWORD_USERNAME_KEY = 'username';
    const CHANGE_PASSWORD_CODE_KEY = 'code';

    const LOGIN_USERNAME_KEY = 'username';
    const LOGIN_PASSWORD_KEY = 'password';

    const PASSWORD_RESET_USERNAME_KEY = 'username';
    const SEND_USERNAMES_EMAIL_KEY = 'email';

    const USER_HASH = 'Hash';
    const USER_USERNAME = 'Username';
    const USER_EMAIL = 'Email';
    const USER_ROLE = 'Role';

    const NEW_USER_ROLE = 'user';
    const VERIFIED_ROLE = 'verified';
    const ADMIN_ROLE = 'admin';

    const STATUS_ERROR = 'Error';
    const STATUS_OK = 'ok';

    const E_NO_USERNAME = '<b><span style="color:red;">Error: No username was supplied!</span></b>';
    const E_NO_RESET_CODE = '<b><span style="color:red;">Error: No reset code was supplied!</span></b>';
    const E_NO_EMAIL = '<b><span style="color:red;">Error: No email address was supplied!</span></b>';
    const E_NO_PASSWORD = '<b><span style="color:red;">Error: No password was supplied!</span></b>';
    const E_NO_PASSWORD_CONFIRM = '<b><span style="color:red;">Error: No password confirmation was supplied!</span></b>';
    const E_NO_PASSWORD_MATCH = '<b><span style="color:red;">Error: The password and password confirmation must
        match!</span></b>';
    const E_INVALID_EMAIL = '<b><span style="color:red;">Error: Invalid email address was supplied!</span></b>';
    const E_USERNAME_EXISTS = '<b><span style="color:red;">Error: User already registered!</span></b>';
    const E_NO_SUCH_USERNAME = '<b><span style="color:red;">Error: Username does not exist!</span></b>';

    const E_PASSWORD_INCORRECT = '<b><span style="color:red;">Error: The username/password combination
        was not found!</span></b>';
    const E_CODE_INCORRECT = '<b><span style="color:red;">Error: The reset code that was supplied
        was invalid. Please check your email for the <b>latest</b> password reset link!</span></b>';


    const CODE_CHARS = '0123456789abcdefghijklmnopqrstuvwxyzQWERTYUIOPASDFGHJKLZXCVBNM';
    const CODE_CHAR_LEN = 62;
    const VERIFICATION_CODE_LEN = 10;
    const RESET_CODE_LEN = 10;

    public function register($username, $email, $password, $password_confirm)
    {
        if (empty($username)) {
            return User::get_status_object(User::STATUS_ERROR,
                User::E_NO_USERNAME);
        }
        if (empty($email)) {
            return User::get_status_object(User::STATUS_ERROR,
                User::E_NO_EMAIL);
        }
        if (empty($password)) {
            return User::get_status_object(User::STATUS_ERROR,
                User::E_NO_PASSWORD);
        }
        if (empty($password_confirm)) {
            return User::get_status_object(User::STATUS_ERROR,
                User::E_NO_PASSWORD_CONFIRM);
        }
        /* Since i'm using @localhost as my domain, I Comment this out to allow registration
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return User::get_status_object(User::STATUS_ERROR,
                User::E_INVALID_EMAIL);
        }
        */
        if ($password !== $password_confirm) {
            return User::get_status_object(User::STATUS_ERROR,
                User::E_NO_PASSWORD_MATCH);
        }
        $db = new SharerDatabase();
        $user = $db->lookup_user($username);
        if ($user) {
            return User::get_status_object(User::STATUS_ERROR,
                User::E_USERNAME_EXISTS);
        }

        $db->add_user($username, $email, password_hash($password, PASSWORD_DEFAULT), User::NEW_USER_ROLE);
        $this->set_user($username);
        $this->set_role(User::NEW_USER_ROLE);
        $this->set_email($email);
        $this->set_hash(password_hash($password, PASSWORD_DEFAULT));
        $this->send_verification();
        return User::get_status_object(User::STATUS_OK, null);
    }

    public function login($username, $password)
    {
        if (empty($username)) {
            return User::get_status_object(User::STATUS_ERROR,
                User::E_NO_USERNAME);
        }
        if (empty($password)) {
            return User::get_status_object(User::STATUS_ERROR,
                User::E_NO_PASSWORD);
        }
        $db = new SharerDatabase();
        $user = $db->lookup_user($username);
        if ($user) {
            if (!password_verify($password, $user[User::USER_HASH])) {
                return User::get_status_object(User::STATUS_ERROR,
                    User::E_PASSWORD_INCORRECT);
            }

            $this->set_user($user[User::USER_USERNAME]);
            $this->set_role($user[User::USER_ROLE]);
            $this->set_email($user[User::USER_EMAIL]);
            $this->set_hash($user[User::USER_HASH]);
            if (password_needs_rehash($this->get_hash(), PASSWORD_DEFAULT)) {
                $db->change_password($username, password_hash($password, PASSWORD_DEFAULT));
            }
            return User::get_status_object(User::STATUS_OK, null);
        }
        return User::get_status_object(User::STATUS_ERROR,
            User::E_NO_SUCH_USERNAME);

    }

    public function set_user($username)
    {
        $_SESSION[User::USERNAME_KEY] = $username;
    }

    public function set_email($email)
    {
        $_SESSION[User::EMAIL_KEY] = $email;
    }

    public function set_role($role)
    {
        $_SESSION[User::ROLE_KEY] = $role;
    }

    public function set_hash($hash)
    {
        $_SESSION[User::HASH_KEY] = $hash;
    }

    public static function get_user()
    {
        if (!isset($_SESSION) || (!isset($_SESSION[User::USERNAME_KEY]))) {
            return '';
        }
        return $_SESSION[User::USERNAME_KEY];
    }

    public static function get_hash()
    {
        if (!isset($_SESSION) || (!isset($_SESSION[User::HASH_KEY]))) {
            return '';
        }
        return $_SESSION[User::HASH_KEY];
    }

    public static function get_role()
    {
        if (!isset($_SESSION) || (!isset($_SESSION[User::ROLE_KEY]))) {
            return '';
        }
        return $_SESSION[User::ROLE_KEY];
    }

    public static function get_email()
    {
        if (!isset($_SESSION) || (!isset($_SESSION[User::EMAIL_KEY]))) {
            return '';
        }
        return $_SESSION[User::EMAIL_KEY];
    }

    private static function get_status_object($status, $message)
    {
        $obj = new StdClass();

        $obj->status = $status;
        $obj->message = $message;

        return $obj;
    }

    public function clear_user()
    {
        $this->set_user('');
        $this->set_role('');
        $this->set_hash('');
        $this->set_email('');
    }

    private function generate_code($length)
    {
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= USER::CODE_CHARS[rand(0, User::CODE_CHAR_LEN) - 1];
        }
        return $code;
    }

    public function test_generate_code()
    {
        for ($j = 0; $j < 100; $j++) {
            echo $this->generate_code(USER::VERIFICATION_CODE_LEN) . '<br>';
        }
    }

    public function send_verification()
    {
        $sharer_url = ROOT_DIRECTORY;
        $code = $this->generate_code(User::VERIFICATION_CODE_LEN);
        $db = new SharerDatabase();
        $db->store_verification(User::get_user(), $code);
        $encoded_username = urlencode(User::get_user());
        $username = User::get_user();
        $email = User::get_email();
        $subject = 'Please verify your Generic Sharer account';
        $username_key = User::USERNAME_KEY;
        $code_key = User::CODE_KEY;
        $body = <<<BODY
<h1>Welcome to Generic Sharer, {$username}!</h1>
<p>The email address {$email} was used to create an account on the <a href=$sharer_url>Generic Sharer</a>
web site. In order to verify this account, please click on the link below. Only verified users are allowed to post
content and comments, though unregistered users can browse the content posted by others.</p>
<p><a href="{$sharer_url}verify.php?$code_key=$code&$username_key={$encoded_username}">Verify your Account</a></p>
BODY;
        $sharer_email = new SharerEmail(User::get_email(), $subject, $body);
        $sharer_email->send();

    }

    public function send_reset_code($username)
    {
        $sharer_url = ROOT_DIRECTORY;
        $code = $this->generate_code(User::RESET_CODE_LEN);
        $db = new SharerDatabase();
        $db->store_reset_code($username, $code);
        $user = $db->lookup_user($username);
        if ($user) {
            $encoded_username = urlencode($user[User::USER_USERNAME]);
            $subject = 'Please reset your Generic Sharer password';
            $username_key = User::USERNAME_KEY;
            $code_key = User::PASSWORD_RESET_KEY;
            $body = <<<BODY
<h1>Generic Sharer Password!</h1>
<p>The email address {$user[User::USER_EMAIL]} was used to request a password reset on the <a href=$sharer_url>Generic Sharer</a>
web site. In order to reset the password, please click on the link below. </p>
<p><a href="{$sharer_url}reset.php?$code_key=$code&$username_key={$encoded_username}">Reset my Account</a></p>
BODY;
            $sharer_email = new SharerEmail($user[User::USER_EMAIL], $subject, $body);
            $sharer_email->send();
        }
    }

    public function change_password($username, $code, $password, $password_confirm)
    {
        if (empty($username)) {
            return User::get_status_object(User::STATUS_ERROR,
                User::E_NO_USERNAME);
        }
        if (empty($password)) {
            return User::get_status_object(User::STATUS_ERROR,
                User::E_NO_PASSWORD);
        }
        if (empty($code)) {
            return User::get_status_object(User::STATUS_ERROR,
                User::E_NO_RESET_CODE);
        }
        if (empty($password_confirm)) {
            return User::get_status_object(User::STATUS_ERROR,
                User::E_NO_PASSWORD_CONFIRM);
        }
        if ($password !== $password_confirm) {
            return User::get_status_object(User::STATUS_ERROR,
                User::E_NO_PASSWORD_MATCH);
        }
        $db = new SharerDatabase();
        $user = $db->lookup_user($username);
        if ($user) {
            if ($code !== $user[SharerDatabase::RESET_CODE_KEY]) {
                return User::get_status_object(User::STATUS_ERROR,
                    User::E_CODE_INCORRECT);
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $db->change_password($username, $hash);
            $this->set_user($user[User::USER_USERNAME]);
            $this->set_role($user[User::USER_ROLE]);
            $this->set_email($user[User::USER_EMAIL]);
            $this->set_hash($hash);
            return User::get_status_object(User::STATUS_OK, null);
        }

        return User::get_status_object(User::STATUS_ERROR,
            User::E_NO_SUCH_USERNAME);
    }


    public function verify($username, $code)
    {

        $db = new SharerDatabase();
        $user = $db->lookup_user(urldecode($username));

        if ($user[SharerDatabase::VERIFICATION_CODE_KEY] === $code) {
            echo <<<BODY
<div id="navbar_area"></div>
<h1>Thank you for verifying your account!</h1>
<div>
    This is Generic Sharer, the premier generic social media site!
</div>  
BODY;
            $this->set_user($user[User::USER_USERNAME]);
            $db->change_role($user[User::USER_USERNAME], User::VERIFIED_ROLE);
            $this->set_role(User::VERIFIED_ROLE);
            $this->set_email($user[User::USER_EMAIL]);
        } else {
            echo <<<BODY
<div id="navbar_area"></div>
<h1>Problem verifying your account!</h1>
<div>
    We're sorry, but the verification code you provided does not match the username.
    Please check your email to make sure you clicked the link from the <b>most recent</b>
    verification email. 
</div>  
BODY;
        }
    }

    public function send_usernames_email($email)
    {
        $sharer_url = ROOT_DIRECTORY;

        $db = new SharerDatabase();
        $usernames = $db->lookup_usernames($email);

        if (count($usernames) === 0) {
            $body = <<<BODY
<h1>Generic Sharer Username Lookup!</h1>
<p>The email address {$email} was used to look up usernames on the <a href=$sharer_url>Generic Sharer</a>
web site. There were no usernames found that match your email address.</p>
BODY;
        } else if (count($usernames) === 1) {
            $username = htmlentities($usernames[0][SharerDatabase::USERNAME_KEY]);
            $body = <<<BODY
<h1>Generic Sharer Username Lookup!</h1>
<p>The email address {$email} was used to look up usernames on the <a href=$sharer_url>Generic Sharer</a>
web site. Your username is: <b>{$username}</b>.</p>
BODY;
        } else {
            $body = <<<BODY
<h1>Generic Sharer Username Lookup!</h1>
<p>The email address {$email} was used to look up usernames on the <a href=$sharer_url>Generic Sharer</a>
web site. The usernames associated with this email address are: .</p>
<ul>
BODY;
            foreach ($usernames as $username) {
                $body .= '    <li>' . htmlentities($username[SharerDatabase::USERNAME_KEY]) .
                    '    </li>' . "\r\n";
            }
            $body .= '</ul>';
        }
        $subject = 'Your Generic Sharer Username';

        $sharer_email = new SharerEmail($email, $subject, $body);
        $sharer_email->send();
    }
}