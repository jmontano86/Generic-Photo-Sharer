<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 5/20/2018
 * Time: 12:16
 */

require_once('assets/includes/common_requires.php');
require_once('assets/includes/ImageSet.php');
require_once('assets/includes/User.php');

$set_id = get_get_value(ImageSet::IMAGESET_ID_KEY);
$size_type_key = get_get_value(ImageSet::IMAGESET_SIZE_TYPE_KEY);
$username = User::get_user();

$image_array = ImageSet::fetch_image($set_id, $size_type_key, $username);

header('Content-Type: ' . $image_array[SharerDatabase::MIME_TYPE_KEY]);
header('Content-Length: ' . $image_array[SharerDatabase::SIZE_KEY]);
header('Cache-Control: private, max-age=10800, pre-check=10800');
header('Pragma: cache');

echo $image_array[SharerDatabase::DATA_KEY];