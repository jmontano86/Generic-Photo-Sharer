<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/11/2018
 * Time: 6:26
 */
require_once('assets/includes/calculator_constants.php');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ohm's Law Calculator</title>

    <script src="assets/jquery-ui/external/jquery/jquery.js"></script>
    <script src="assets/jquery-ui/jquery-ui.js"></script>
    <link rel="stylesheet" href="assets/jquery-ui/jquery-ui.css">
    <script src="assets/includes/calculator.js.php"></script>

    <link rel="stylesheet" href="assets/includes/calculator.css.php">
    <script src="assets/includes/loadContent.js.php"></script>
    <script>
        $(
            function() {
                $('#show_button').button().click(function () {
                    loadContent('assets/includes/calculator_content.php', function() {
                        calculate();
                    });
                });
            }
        )
    </script>
</head>
<body>
<div id="show_button">Show Calculator</div>
</body>
</html>
