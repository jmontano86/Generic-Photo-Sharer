<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/23/2018
 * Time: 7:18
 */

require_once('LoadableContent.php');
require_once('SharerDatabase.php');
require_once('User.php');

$name_key = SharerDatabase::NAME_KEY;
$owner_key = SharerDatabase::OWNER_KEY;
$description_key = SharerDatabase::DESCRIPTION_KEY;
$imageset_id_key = SharerDatabase::IMAGE_SET_ID_KEY;
$sharing_key = SharerDatabase::SHARING_KEY;

$js = <<<JS
function showImageView(imageSet, updateCallback, deleteCallback, ownerChangeCallback) {

    $('#image_view_name').off().text(imageSet.{$name_key});
    $('#image_view_owner').html('Owner: <span id="owner_span">' + imageSet.{$owner_key} + 
    '</span>');
    $('#owner_span').click(function() {
        ownerChangeCallback(imageSet.{$owner_key});
        $('#image_view_dialog').dialog('close');
    });
    $('#image_view_description_container').html('<div id="image_view_description"></div>');
    $('#image_view_image').attr('src', 'PageImage/' + imageSet.{$imageset_id_key});
    
    $.get('assets/actions/get_username.php', function(curUser) {
        
        if(curUser.username === imageSet.{$owner_key} || curUser.role === 'admin') {
            $('#image_view_name')
                .attr('contentEditable', true)
                .css('border', '1px dashed grey')
                .keypress(function(event) {
                    if(event.keyCode === 10 || event.keyCode === 13) {
                        event.preventDefault();
                    }
                })
                .keyup(function() {
                    var name = encodeURI($('#image_view_name').text());
                    $.get('assets/actions/update_name.php?{$name_key}=' + name + 
                    '&{$imageset_id_key}=' + imageSet.{$imageset_id_key});
                    imageSet.{$name_key} = $('#image_view_name').text();
                    updateCallback();
                });
            $('#image_view_description').rte(function(elem, html) {
               var desc = encodeURI(html);
               $.get('assets/actions/update_description.php?{$description_key}= ' +
               desc + '&{$imageset_id_key}=' + imageSet.{$imageset_id_key});
            }).css('border', '1px dashed grey').css('color', 'black');
            $('#image_view_description .rte_edit_box').html(imageSet.{$description_key});
            $('#image_view_sharing').html('Sharing: <input id="radio_private" type="radio" name="sharing" ' +
             'value="private">Private, <input id="radio_public" type="radio" name="sharing" value="public">' +
              'Public');     
            if(imageSet.{$sharing_key} === 'public') {
                $('#radio_private').off().prop('checked',false);
                $('#radio_public').off().prop('checked',true);
                
            } else {
                $('#radio_private').off().prop('checked',true);
                $('#radio_public').off().prop('checked',false);
            }
            $('#radio_private').click(function()  {
                $.get('assets/actions/update_sharing.php?{$sharing_key}=private&{$imageset_id_key}=' + 
                imageSet.{$imageset_id_key});     
                imageSet.{$sharing_key} = 'private';
                updateCallback();
            });
            $('#radio_public').click(function()  {
                $.get('assets/actions/update_sharing.php?{$sharing_key}=public&{$imageset_id_key}=' + 
                imageSet.{$imageset_id_key});
                imageSet.{$sharing_key} = 'public';
                updateCallback();
            });
            var deleteButton = $('<div>Delete</div>').button().click(function() {
                $.get('assets/actions/delete_imageset.php?{$imageset_id_key}=' + imageSet.{$imageset_id_key});                  
                $('#image_view_dialog').dialog('close');
                deleteCallback();
                return;
            });
            $('#image_view_delete').html(deleteButton);

        } else {
            $('#image_view_name')
                .attr('contentEditable', false)
                .css('border', '0px'); 
            $('#image_view_description').html(imageSet.{$description_key});
            $('#image_view_sharing').html('Sharing: ' + imageSet.{$sharing_key});
            $('#image_view_delete').html();
        }
    });  
    
    $('#image_view_dialog').dialog({
        modal: true,
        closeText: '',
        width: 835.2,
        position: {my: 'top', at: 'top+20', of: window}        
    })
        .parent()
        .children('.ui-dialog-titlebar')
        .children('.ui-dialog-titlebar-close')
        .css('display', 'block');    
    
}
JS;

$html = <<<HTML
<div id="image_view_dialog" title="Image View">
    <h3 id="image_view_name"></h3>
    <table id="image_view_metadata">
        <tr>
            <td align="left" width="33%" id="image_view_owner"></td>
            <td align="center" width="34%" id="image_view_delete"></td>
            <td align="right" width="33%" id="image_view_sharing"></td>
        </tr>
    </table>
    <img id="image_view_image">
    <div id="image_view_description_container"></div>
</div>
HTML;

$css = <<<CSS
#image_view_name {
    margin: 10px 0px 10px 0px;
    overflow: hidden;
    white-space: nowrap;
    width: 800px;
}

#image_view_image {
    margin: 10px 0px 10px 0px;
}

#image_view_description {
    width: 800px;
    height: 200px;
    margin: 10px 0px 10px 0px;
    border: 1px solid black;
}

#image_view_metadata {
    border: 0px;
    width: 100%;
    margin: 10px 0px 0px 0px;
    padding: 0px;
    border-spacing: 0px;
}

#owner_span {
    cursor: pointer;
    color: green;
}

CSS;

$obj = new LoadableContent($js, $html, $css);
$obj->load();