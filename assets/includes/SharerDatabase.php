<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 5/1/2018
 * Time: 6:45
 */

class SharerDatabase {

    const DB_SERVER = '127.0.0.1';
    const DB_USER = 'sharer';
    const DB_PASSWORD = 'sharer';
    const DB_DATABASE = 'sharer';

    const USERS_TABLE_KEY = 'Users';

    const VERIFICATION_CODE_KEY = 'VerificationCode';
    const ROLE_KEY = 'Role';
    const HASH_KEY = 'Hash';
    const EMAIL_KEY = 'Email';
    const USERNAME_KEY = 'Username';
    const RESET_CODE_KEY = 'ResetCode';

    const IMAGES_TABLE = 'Images';
    const IMAGE_ID_KEY = 'ImageID';
    const MIME_TYPE_KEY = 'MimeType';
    const SIZE_KEY = 'Size';
    const WIDTH_KEY = 'Width';
    const HEIGHT_KEY = 'Height';
    const DATA_KEY = 'Data';

    const IMAGESETS_TABLE = 'ImageSets';

    const IMAGE_SET_ID_KEY = 'ImageSetID';
    const OWNER_KEY = 'Owner';
    const DESCRIPTION_KEY = 'Description';
    const TIME_KEY = 'Time';
    const NAME_KEY = 'Filename';
    const SHARING_KEY = 'Sharing';
    const ORIGINAL_IMAGE_ID_KEY = 'OriginalImageID';
    const PAGE_IMAGE_ID_KEY = 'PageImageID';
    const THUMBNAIL_IMAGE_ID_KEY = 'ThumbnailImageID';

    private static $db = NULL;

    private static function connect()
    {
        if(empty(SharerDatabase::$db)) {
            SharerDatabase::$db = new mysqli(
                SharerDatabase::DB_SERVER,
                SharerDatabase::DB_USER,
                SharerDatabase::DB_PASSWORD,
                SharerDatabase::DB_DATABASE
            );
        }
        return SharerDatabase::$db;

    }

    public function __destruct()
    {
        if(!empty(SharerDatabase::$db)) {
            SharerDatabase::$db->close();
            SharerDatabase::$db = NULL;
        }
    }

    public function lookup_user($username)
    {
        $conn = SharerDatabase::connect();
        $query = 'CALL lookup_user(?)';
        $statement = $conn->prepare($query);
        $statement->bind_param('s', $username);
        $statement->execute();

        $result = $statement->get_result();
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function lookup_usernames($email)
    {
        $conn = SharerDatabase::connect();
        $query = 'CALL lookup_usernames(?)';

        $statement = $conn->prepare($query);
        $statement->bind_param('s', $email);
        $statement->execute();
        $result = $statement->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function add_user($username, $email, $hash, $role)
    {
        $conn = SharerDatabase::connect();
        $query = 'CALL add_user(?, ?, ?, ?);';
        $statement = $conn->prepare($query);
        $statement->bind_param('ssss', $username, $email, $hash, $role);
        $statement->execute();
    }

    private function change_column_value($username, $value, $column)
    {
        $conn = SharerDatabase::connect();
        $query = 'CALL change_column_value(?, ?, ?);';
        $statement = $conn->prepare($query);
        $statement->bind_param('sss', $username, $value, $column);
        $statement->execute();
    }
    
    public function change_password($username, $hash)
    {
        $this->change_column_value($username, $hash, SharerDatabase::HASH_KEY);
        $this->change_column_value($username, '', SharerDatabase::RESET_CODE_KEY);
    }

    public function store_verification($username, $code)
    {
        $this->change_column_value($username, $code, SharerDatabase::VERIFICATION_CODE_KEY);
    }

    public function store_reset_code($username, $code)
    {
        $this->change_column_value($username, $code, SharerDatabase::RESET_CODE_KEY);
    }

    public function change_role($username, $role)
    {
        $conn = SharerDatabase::connect();
        $query = 'CALL change_role(?, ?);';
        $statement = $conn->prepare($query);
        $statement->bind_param('ss', $username, $role);
        $statement->execute();
    }

    public function fetch_gallery($owner, $user, $start, $length)
    {
        $conn = SharerDatabase::connect();
        $query = 'CALL fetch_gallery(?, ?, ?, ?)';

        $statement = $conn->prepare($query);
        $statement->bind_param('ssii', $owner, $user, $start, $length);
        $statement->execute();
        $result = $statement->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function insert_image($type, $size, $width, $height, $data)
    {
        $query = 'SELECT insert_image(?, ?, ?, ?, ?);';
        $conn = SharerDatabase::connect();

        $statement= $conn->prepare($query);
        $statement->bind_param('siiib', $type, $size, $width, $height, $data);

        $statement->send_long_data(4, $data);
        $statement->execute();

        $result = $statement->get_result();
        $ra = $result->fetch_array(MYSQLI_NUM);
        return $ra[0];
    }
    function insert_imageset($owner, $name, $sharing, $orig_id, $page_id, $thumb_id)
    {
        $query = 'SELECT insert_imageset(?, ?, ?, ?, ?, ?);';
        $conn = SharerDatabase::connect();

        $statement= $conn->prepare($query);
        $statement->bind_param('sssiii', $owner, $name, $sharing, $orig_id, $page_id, $thumb_id);

        $statement->execute();

        $result = $statement->get_result();
        $ra = $result->fetch_array(MYSQLI_NUM);
        return $ra[0];
    }

    public function fetch_image($set_id, $size_type_key, $username)
    {
        if ($size_type_key != SharerDatabase::THUMBNAIL_IMAGE_ID_KEY &&
            $size_type_key != SharerDatabase::PAGE_IMAGE_ID_KEY &&
            $size_type_key != SharerDatabase::ORIGINAL_IMAGE_ID_KEY) {
            return NULL;
        }
        $conn = SharerDatabase::connect();

        $query = 'CALL fetch_image(?, ?, ?);';

        $statement = $conn->prepare($query);
        $statement->bind_param('iss', $set_id, $size_type_key, $username);
        $statement->execute();
        $result = $statement->get_result();
        $ra = $result->fetch_array(MYSQLI_ASSOC);
        return $ra;
    }

    public function create_imageset(
            $user, $name, $sharing,
            $type, $orig_size, $orig_width, $orig_height, $orig_data,
            $page_size, $page_width, $page_height, $page_data,
            $thumb_size, $thumb_width, $thumb_height, $thumb_data
        )
    {

        $conn = SharerDatabase::connect();
        $query = 'SELECT create_imageset(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);';
        $statement = $conn->prepare($query);
        $statement->bind_param('ssssiiibiiibiiib',
            $user, $name, $sharing,
            $type, $orig_size, $orig_width, $orig_height, $orig_data,
            $page_size, $page_width, $page_height, $page_data,
            $thumb_size, $thumb_width, $thumb_height, $thumb_data);

        $statement->send_long_data(7, $orig_data);
        $statement->send_long_data(11, $page_data);
        $statement->send_long_data(15, $thumb_data);

        $statement->execute();
        $result = $statement->get_result();
        $ra = $result->fetch_array(MYSQLI_NUM);
        return $ra[0];
    }

    public function update_imageset_name($id, $filename, $user)
    {
        $conn = SharerDatabase::connect();
        $query = 'CALL update_imageset_name(?, ?, ?);';
        $statement = $conn->prepare($query);
        $statement->bind_param('iss', $id, $filename, $user);
        $statement->execute();
    }

    public function update_imageset_sharing($id, $sharing, $user)
    {
        $conn = SharerDatabase::connect();
        $query = 'CALL update_imageset_sharing(?, ?, ?);';
        $statement = $conn->prepare($query);
        $statement->bind_param('iss', $id, $sharing, $user);
        $statement->execute();
    }

    public function delete_imageset($id, $user)
    {
        $conn = SharerDatabase::connect();
        $query = 'CALL delete_imageset(?, ?);';
        $statement = $conn->prepare($query);
        $statement->bind_param('is', $id, $user);
        $statement->execute();
    }
}