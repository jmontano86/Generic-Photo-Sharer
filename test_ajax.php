<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/10/2018
 * Time: 6:25
 */
?>

<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AJAX</title>
    <script src="assets/jquery-ui/external/jquery/jquery.js"></script>
    <script>
        $(
            function() {
                $.get('assets/actions/get_content.php', function (data) {
                    $('#content').html(data.html);
                    $('#count').html(data.count);
                });
                /*
                $('#ohms_button').click(function () {
                    $.get('assets/actions/calc_ohms.php?' + $('fieldset').serialize(), function (data) {
                        $('#results').html(data.volts + ' Volts<br> ' + data.ohms + ' Ohms<br>'
                        + data.amps + ' Amps<br>' + data.watts + ' Watts');
                    });
                });
                */
                $('#ohms_button').click(function () {
                    $.post('assets/actions/calc_ohms.php', $('fieldset').serialize(), function (data) {
                        $('#results').html(data.volts + ' Volts<br> ' + data.ohms + ' Ohms<br>'
                            + data.amps + ' Amps<br>' + data.watts + ' Watts');
                    });
                });
            }
        );
    </script>
</head>
<body>
<p>Here is some fetched content: <span id="content"></span> <span id="count"></span> times.</p>
<fieldset>
    <table>
        <tr>
            <td>Volts: </td><td><input type="text" name="volts"></td>
        </tr>
        <tr>
            <td>Ohms: </td><td><input type="text" name="ohms"></td>
        </tr>
        <tr>
            <td colspan="2"><input id="ohms_button" type="submit" value="Calculate"</td>
        </tr>
    </table>
</fieldset>
<div id="results"></div>
</body>
</html>
