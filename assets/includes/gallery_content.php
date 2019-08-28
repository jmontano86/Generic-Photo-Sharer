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
require_once('ImageSetList.php');
require_once('ImageSet.php');

$owner_key = ImageSetList::OWNER_KEY;
$start_key = ImageSetList::START_KEY;
$length_key = ImageSetList::LENGTH_KEY;

$sd_imageset_id = SharerDatabase::IMAGE_SET_ID_KEY;
$sd_owner_key = SharerDatabase::OWNER_KEY;
$sd_name_key = SharerDatabase::NAME_KEY;
$sd_sharing_key = SharerDatabase::SHARING_KEY;

$is_public = ImageSet::SHARING_PUBLIC;

$js = <<<JS
function showGallery() {
    var end = 0;
    var SEGMENT_LENGTH = 100;
    var scrollTop = 0;
    var scrolling = true;
    var MAX_DISTANCE_FROM_BOTTOM = -1024;
    var owner = '';
    loadContent('assets/includes/rte_content.php');
    
    $(document).tooltip({
        content: function() {
            return $(this).prop('title'); 
        },
        items: '.thumb'
    });
    
    $(window).scroll(onScroll);
    $(document).on('userchange', userChange);
    
        $(document).on('home', function() {
            owner = '';
            userChange();
        });
    function userChange() {
        end = 0;
        scrollTop = 0;
        scrolling = true;
        window.scrollTo(0, 0);
        $('#thumbs').html('');
        fetchGallery();
    }
    
    function onScroll() {
        if (scrolling) return;
        scrolling = true;
        scrollTop = $(window).scrollTop();
        var distanceFromBottom = scrollTop + $(window).height() - $(document).height();
        if (distanceFromBottom > MAX_DISTANCE_FROM_BOTTOM) {
            fetchGallery();
        } else {
            scrolling = false;
        }
    }
    
    function setThumbMetadata(thumb, imageSet) {
        var title = '<b>Owner: </b> ' + imageSet.{$sd_owner_key} + '<br><b>Name:</b> ' + 
            imageSet.{$sd_name_key} + '<br><b>Sharing:</b> ' + imageSet.{$sd_sharing_key};
        thumb.attr('title', title);
        if(imageSet.{$sd_sharing_key} === '{$is_public}') {
                thumb.addClass('thumb_public');
                thumb.removeClass('thumb_private');
        } else {
                thumb.addClass('thumb_private');
                thumb.removeClass('thumb_public');
        }
    }
    
    function updateGallery(list) {
        end += list.length;
        list.forEach(function(imageSet) {
            
            var thumb = $('<div class="thumb"><img class="thumb_image" src="ThumbImage/' + 
            imageSet.{$sd_imageset_id} + '"></div>');
            thumb.click(function() {
                loadContent('assets/includes/image_view_content.php', function() {
                    showImageView(imageSet, function () {
                        setThumbMetadata(thumb, imageSet);
                    }, function() {
                        thumb.remove();
                    }, function(newOwner) {
                        owner = newOwner;
                        userChange();
                    });
                });
            });

            $('#thumbs').append(thumb);
            setThumbMetadata(thumb, imageSet);
        });

        scrolling = false;
    }
    
    function fetchGallery() {
        if(owner === '') {
            $('#main_header').text('Welcome to Generic Sharer');
        } else {
            $('#main_header').text('Image Gallery for ' + owner);           
        }
        $.get('assets/actions/fetch_gallery.php?{$owner_key}=' + encodeURI(owner) + '&{$start_key}=' + end + 
        '&{$length_key}=' + SEGMENT_LENGTH, function(data) {
            updateGallery(data);
        });
    }
}
JS;

$html = <<<HTML
<div id="thumbs"></div>
HTML;

$css = <<<CSS
.thumb {
    float:left;
    padding: 2px;
    margin: 2px;
    width: 128px;
    height: 128px;
}
.thumb_private {
    border: 2px solid red;
}

.thumb_public {
    border: 2px solid green;
}
.thumb_image {
    margin: 0px;
    padding: 0px;
    display: block;
}
CSS;




$obj = new LoadableContent($js, $html, $css);
$obj->load();