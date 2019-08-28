<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/16/2018
 * Time: 20:14
 */

require_once('LoadableContent.php');
require_once('calculator_constants.php');

$in_ohms_key = IN_OHMS_KEY;
$in_volts_key = IN_VOLTS_KEY;

$out_volts_key = OUT_VOLTS_KEY;
$out_ohms_key = OUT_OHMS_KEY;
$out_watts_key = OUT_WATTS_KEY;
$out_amps_key = OUT_AMPS_KEY;

$js = <<<JS
function calculate() {
    $('#calculator').dialog({
        width: 600,
        modal: true,
        buttons: {
            "Calculate": function() {
                $.post("https://localhost/CIS295P/Sharer/assets/actions/do_calculate.php", 
                $('#input_fields input').serialize(),
                function(data) {
                    $('#{$out_volts_key}').val(data.volts);
                    $('#{$out_ohms_key}').val(data.ohms);
                    $('#{$out_watts_key}').val(data.watts);
                    $('#{$out_amps_key}').val(data.amps);
                })  ;

            },
            "Done": function() {
                $('#calculator').dialog('close');
            }
        }
    });   
}
JS;
$html = <<<HTML
<div id="calculator" title="Ohm's Law Calculator">
    <fieldset id="input_fields">
        <legend>Enter Volts and Ohms:</legend>
        <label for="{$in_volts_key}">Volts</label>
        <input type="text" name="{$in_volts_key}">
        <label for="{$in_ohms_key}">Ohms</label>
        <input type="text" name="{$in_ohms_key}">
    </fieldset>
    <fieldset id="output_fields">
        <legend>Results:</legend>
        <label for="{$out_volts_key}">Volts</label>
        <input type="text" id="{$out_volts_key}" disabled>
        <label for="{$out_ohms_key}">Ohms</label>
        <input type="text" id="{$out_ohms_key}" disabled>
        <label for="{$out_amps_key}">Amps</label>
        <input type="text" id="{$out_amps_key}" disabled>
        <label for="{$out_watts_key}">Watts</label>
        <input type="text" id="{$out_watts_key}" disabled>
    </fieldset>
</div>
HTML;
$css = <<<CSS
#calculator {
    display: none;
}

fieldset {
    padding: 20px;
}

fieldset input {
    display: block;
    margin-bottom: 12px;
}

fieldset label {
    display: block;
}

#input_fields {
    float: left;
    width: 40%;
}

#output_fields {
    float: right;
    width: 40%;
}

.ui-dialog-titlebar-close {
    display: none;
}
CSS;




$obj = new LoadableContent($js, $html, $css);
$obj->load();