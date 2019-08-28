<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/23/2018
 * Time: 7:18
 */

require_once('LoadableContent.php');
require_once('User.php');
require_once('SharerDatabase.php');
require_once('ImageSet.php');

$file_key = ImageSet::FILE_KEY;

$js = <<<JS
function dropper() {
    var dragCount = 0;
    var files = [];
    var uploadCount = 0;
    
    $(document)
        .on('dragover', function(event) {
            event.stopPropagation();
            event.preventDefault();
        })
        .on('dragenter', function(event) {
            event.stopPropagation();
            event.preventDefault();
            dragCount++;
            if(dragCount === 1) {
                showDialog();
            }
        })
         .on('dragleave', function(event) {
            event.stopPropagation();
            event.preventDefault();
            dragCount--;
            if(dragCount === 0) {
                hideDialog();
            }
        })
         .on('drop', function(event) {
            event.stopPropagation();
            event.preventDefault();
            var droppedFiles = [].slice.call(event.originalEvent.dataTransfer.files);
            files = files.concat(droppedFiles);
            updateFileList();
        })
    
    
    function showDialog(){
        files = [];
        uploadCount = 0;
        updateFileList();
        $.get('assets/actions/get_username.php', function(data) {
            if (data.role === '' || data.role === 'user') {
                $('#no_dropper_dialog').dialog({
                width: 600,
                modal: true,
                buttons: {
                    "OK": function() {
                        hideNoDropperDialog();
                    }
                }
            });                
            } else {
                $('#dropper_dialog').dialog({
                width: 600,
                modal: true,
                buttons: {
                    "OK": function() {
                        files.forEach(uploadFile);
                    },
                    "Cancel": function() {
                        hideDialog();
                    }
                }
            });
            }
        });

    }
    function hideDialog() {
        $('#dropper_dialog').dialog('close');
        dragCount = 0;
    }
    function hideNoDropperDialog() {
        $('#no_dropper_dialog').dialog('close');
        dragCount = 0;
    }
    function updateFileList() {
        var html = '';
        
        if (uploadCount === 0) {
            $('#uploaded_files').text('');
        } else if (uploadCount === 1) {
            $('#uploaded_files').text('Uploaded 1 file');
        } else {
            $('#uploaded_files').text('Uploaded ' + uploadCount +' files');
        }
        if (files.length === 0) {
            $('#uploading_files').hide();
            return;
        } else if (files.length === 1) {
            $('#upload_message').text('Click OK to upload the following file:')
        } else {
            $('#upload_message').text('Click OK to upload the following files:')
        }
        $('#uploading_files').show();
        files.forEach(function(file) {
           html += '<li>' + file.name + '</li>'; 
        });
        $('#uploading_files_list').html(html);
    }
    function uploadFile(file) {
        var fd = new FormData();
        
        fd.append('{$file_key}', file);
        $.ajax('assets/actions/upload_file.php', {
           type: 'POST',
           processData: false,
            contentType: false,
            data: fd,
            success: function() {
               files.splice(files.indexOf(file), 1);
               uploadCount++;
               updateFileList();
            }
        });
    }
}
JS;

$html = <<<HTML
<div id="dropper_dialog" title="Upload Image">
    <div>Drop your content here.</div>
    <div id="uploading_files">
        <p id="upload_message">Click OK to upload the following files:</p>
        <ul id="uploading_files_list"></ul>
    </div>
    <p id="uploaded_files"></p>
</div>
<div id="no_dropper_dialog" title="Operation is Not Allowed">
    <div>Only verified users are allowed to upload content to this site. Please register an 
    account, log in, and verify your account.</div>
</div>
HTML;

$css = <<<CSS

.ui-dialog-titlebar-close {
    display: none;
}

#dropper_dialog {
    display: none;
}

#no_dropper_dialog {
    display: none;
}

fieldset {
    padding: 20px;
}

fieldset input {
    display: block;
    margin-bottom: 12px;
    width: 30em;
}

fieldset label {
    display: block;
}

#input_fields {
    float: left;
    width: 40%;
}

.linked {
    color: green;
}
.linked:hover {
    cursor:pointer;
}
CSS;




$obj = new LoadableContent($js, $html, $css);
$obj->load();