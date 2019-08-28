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
    <script>
        $(
            function() {
                loadContent('assets/includes/gallery_content.php', function() {
                    showGallery();
                });
            }
        )
    </script>
</head>
<body>
    <div id="navbar_area"></div>
    <h1 id="main_header">Welcome to Generic Sharer!</h1>

</body>
</html>
