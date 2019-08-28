<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/10/2018
 * Time: 6:19
 */

require_once('assets/includes/common_requires.php');
?>

<!doctype html>
<html lang="en">
<head>
<?php require_once('assets/includes/common_head_content.php'); ?>
</head>
<body>
<?php
$user = new User();
$user->verify(
        get_get_value(User::USERNAME_KEY),
        get_get_value(User::CODE_KEY)
)
?>
</body>
</html>
