<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 5/6/2018
 * Time: 12:05
 */

?>
<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Sharer</title>
<script src ="assets/jquery-ui/external/jquery/jquery.js"></script>
<script src ="assets/jquery-ui/jquery-ui.js"></script>
<script src ="assets/includes/loadContent.js.php"></script>
<link rel="stylesheet" href="assets/jquery-ui/jquery-ui.css">
<script>
    $(function() {
        loadContent('assets/includes/navbar_content.php', function() {}, '#navbar_area');
        loadContent('assets/includes/dropper_content.php',function() {
           dropper();
        });
    });
</script>
