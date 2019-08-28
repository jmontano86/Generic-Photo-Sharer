<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/10/2018
 * Time: 6:19
 */

require_once('assets/includes/common_requires.php');
$username = get_get_value(User::USERNAME_KEY);
$code = get_get_value(User::PASSWORD_RESET_KEY);
?>

<!doctype html>
<html lang="en">
<head>
    <?php require_once('assets/includes/common_head_content.php'); ?>
    <script>
        loadContent('assets/includes/change_password_content.php', function() {
            changePassword('<?php echo $username; ?>', '<?php echo $code; ?>');
        });
    </script>
</head>
<body>
<div id="navbar_area"></div>
<h1>Welcome to Generic Sharer!</h1>
<div>
    This is Generic Sharer, the premier generic social media site!
</div>
</body>
</html>
