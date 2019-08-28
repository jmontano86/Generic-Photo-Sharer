<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 5/20/2018
 * Time: 10:40
 */

class ImageSet
{
    const SHARING_PRIVATE = 'private';
    const SHARING_PUBLIC = 'public';

    const PAGE_IMAGE_WIDTH = 800;
    const THUMBNAIL_SIZE = 128;

    const FILE_KEY = 'file';
    const FILE_TYPE = 'type';
    const FILE_SIZE = 'size';
    const FILE_TMP_NAME = 'tmp_name';
    const FILE_NAME = 'name';

    const IMAGESET_ID_KEY = 'id';
    const IMAGESET_SIZE_TYPE_KEY = 'size';

    private $m_id = 0;

    public function __construct($file)
    {
        $type = $file[ImageSet::FILE_TYPE];
        $name = $file[ImageSet::FILE_NAME];
        $img = new Imagick();

        $orig_size = $file[ImageSet::FILE_SIZE];
        $orig_data = file_get_contents($file[ImageSet::FILE_TMP_NAME]);

        $img->readImageBlob($orig_data);
        $img = $img->coalesceImages();
        $orig_d = $img->getImageGeometry();

        $page = ImageSet::create_page_image($img);

        $page_data = $page->getImageBlob();
        $page->coalesceImages();
        $page_d = $page->getImageGeometry();
        $page_size = strlen($page_data);

        $thumb = ImageSet::create_thumbnail_image($page);

        $thumb_data = $thumb->getImageBlob();
        $thumb->coalesceImages();
        $thumb_d = $thumb->getImageGeometry();
        $thumb_size = strlen($thumb_data);


        /*
        $orig_id = new Image($type, $orig_size, $orig_d['width'], $orig_d['height'], $orig_data);
        $page_id = new Image($type, $page_size, $page_d['width'], $page_d['height'], $page_data);
        $thumb_id = new Image($type, $thumb_size, $thumb_d['width'], $thumb_d['height'], $thumb_data);

        $db = new SharerDatabase();
        $this->m_id = $db->insert_imageset(
            User::get_user(),
            $name,
            ImageSet::SHARING_PRIVATE,
            $orig_id->get_id(),
            $page_id->get_id(),
            $thumb_id->get_id()
        );
        */
        $db = new SharerDatabase();
        $db->create_imageset(
            User::get_user(), $name, ImageSet::SHARING_PRIVATE,
            $type, $orig_size, $orig_d['width'], $orig_d['height'], $orig_data,
            $page_size, $page_d['width'], $page_d['height'], $page_data,
            $thumb_size, $thumb_d['width'], $thumb_d['height'], $thumb_data
        );
    }

    private static function create_page_image($source_img)
    {
        $img = $source_img->coalesceImages();
        $d = $img->getImageGeometry();

        foreach ($img as $frame) {
            $frame->scaleImage(ImageSet::PAGE_IMAGE_WIDTH, 0);
            $frame->setImagePage(0, 0, 0, 0);
        }

        return $img;
    }

    public function get_id()
    {
        return $this->m_id;
    }


    private static function create_thumbnail_image($source_img)
    {
        $img = $source_img->coalesceImages();
        $d = $img->getImageGeometry();

        if ($d['width'] <= $d['height']) {
            foreach ($img as $frame) {
                $frame->scaleImage(ImageSet::THUMBNAIL_SIZE, 0);
                $thumb_d = $img->getImageGeometry();
                $frame->cropImage(
                    ImageSet::THUMBNAIL_SIZE,
                    ImageSet::THUMBNAIL_SIZE,
                    0,
                    ($thumb_d['height'] - ImageSet::THUMBNAIL_SIZE) / 2
                );
                $frame->setImagePage(0, 0, 0, 0);
            }

        } else {
            foreach ($img as $frame) {
                $frame->scaleImage(0, ImageSet::THUMBNAIL_SIZE);
                $thumb_d = $img->getImageGeometry();
                $frame->cropImage(
                    ImageSet::THUMBNAIL_SIZE,
                    ImageSet::THUMBNAIL_SIZE,
                    ($thumb_d['width'] - ImageSet::THUMBNAIL_SIZE) / 2,
                    0
                );
                $frame->setImagePage(0, 0, 0, 0);
            }
        }
        return $img;
    }

    public static function fetch_image($set_id, $size_type_key, $username)
    {
        $db = new SharerDatabase();
        return $db->fetch_image($set_id, $size_type_key, $username);
    }

    public static function update_imageset_name($id, $filename, $user) {
        $db = new SharerDatabase();
        $db->update_imageset_name($id, $filename, $user);
    }

    public static function update_imageset_description($id, $description, $user) {
        $db = new SharerDatabase();
        $db->update_imageset_description($id, $description, $user);
    }

    public static function update_imageset_sharing($id, $sharing, $user) {
        $db = new SharerDatabase();
        $db->update_imageset_sharing($id, $sharing, $user);
    }

    public static function delete_imageset($id, $user) {
        $db = new SharerDatabase();
        $db->delete_imageset($id, $user);
    }



}