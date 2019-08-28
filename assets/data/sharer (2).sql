-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2018 at 06:26 AM
-- Server version: 10.1.29-MariaDB
-- PHP Version: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sharer`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_user` (IN `name` VARCHAR(64), IN `emailaddr` VARCHAR(128), IN `hash` VARCHAR(256), IN `role` VARCHAR(16))  MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT 'Adds new user after registration'
INSERT INTO users (Username, Email, Hash, Role)
VALUES (name, emailaddr, hash, role)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `change_column_value` (IN `name` VARCHAR(64), IN `value` VARCHAR(256), IN `colname` VARCHAR(64))  MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT 'Updates value of a column based on username'
BEGIN
	SET @query = CONCAT('UPDATE users SET ', colname, ' = ? WHERE \t\t\tUsername = ?;');
	PREPARE statement FROM @query;
	SET @value = value;
	SET @name = name;
	EXECUTE statement USING @value, @name;
	DEALLOCATE PREPARE statement;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `change_role` (IN `name` VARCHAR(64), IN `newrole` VARCHAR(16))  MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT 'Updates role when user is verified.'
UPDATE users
SET Role = newrole
WHERE Username = name$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_imageset` (IN `param_id` INT, IN `param_user` VARCHAR(64))  MODIFIES SQL DATA
    COMMENT 'Delete imageset and associated images'
BEGIN
	DECLARE user_name VARCHAR(64);
    DECLARE user_role VARCHAR(16);
    
    DECLARE imset_owner VARCHAR(64);
    DECLARE imset_oid 	INT;
    DECLARE imset_pid	INT;
    DECLARE imset_tid	INT;
    
    DECLARE user_cursor CURSOR FOR 
    	SELECT Username, Role
        FROM   users
        WHERE  Username = param_user;
        
    DECLARE imset_cursor CURSOR FOR
    	SELECT Owner, OriginalImageID, PageImageID, ThumbnailImageID
        FROM   imagesets
        WHERE  ImageSetID = param_id;
       
        
    OPEN imset_cursor;
    	BEGIN
            DECLARE CONTINUE HANDLER FOR NOT FOUND
    			BEGIN
                    SET imset_oid = -1;
                    SET imset_owner = '';
                    SET imset_pid = -1;
                    SET imset_tid = -1;
                END;
       		FETCH imset_cursor INTO imset_owner, imset_oid, imset_pid, imset_tid;
    	END;
    CLOSE imset_cursor;
        
    OPEN user_cursor;
    	BEGIN
            DECLARE CONTINUE HANDLER FOR NOT FOUND
            BEGIN
                SET user_name = '';
                SET user_role = '';
            END;
         	FETCH user_cursor INTO user_name, user_role;
        END;
    CLOSE user_cursor;
    IF (imset_owner = param_user OR user_role = 'admin') 
        AND (imset_oid != -1) THEN
		
        DELETE 
        FROM   imagesets
        WHERE  ImageSetID = param_id;
        
        DELETE 
        FROM   images
        WHERE  ImageID IN (imset_oid, imset_pid, imset_tid);
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_gallery` (IN `param_owner` VARCHAR(64), IN `param_user` VARCHAR(64), IN `param_start` INT, IN `param_length` INT)  READS SQL DATA
    DETERMINISTIC
    COMMENT 'Fetch the images for a gallery view'
SELECT		ImageSetID, 
			Owner, 
        	Filename, 
        	Sharing,
            Description
FROM		imagesets
LEFT JOIN	Users ON param_user = Username
WHERE 		(Role = 'admin'
OR			Username = Owner
OR			Sharing = 'public')
AND			(param_owner = ''
OR			Owner = param_owner)
ORDER BY	ImageSetID DESC
LIMIT		param_start, param_length$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_image` (IN `set_id` INT, IN `col_name` VARCHAR(64), IN `cur_user` VARCHAR(64))  READS SQL DATA
    SQL SECURITY INVOKER
    COMMENT 'Fetches image based on image id of passed column'
BEGIN
	SET @query = CONCAT('SELECT MimeType, Data, Size, Filename, Width, Height FROM Images JOIN Imagesets ON ImageID = ', col_name, ' LEFT JOIN users ON ? = Username WHERE ImageSetID = ? AND (Owner = Username OR Sharing = ''public'' OR Role = ''admin'');');
    PREPARE statement FROM @query;
    SET @cur_user = cur_user;
	SET @set_id = set_id;

    EXECUTE statement USING @cur_user, @set_id;
    DEALLOCATE PREPARE statement;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `lookup_user` (IN `name` VARCHAR(256))  READS SQL DATA
    COMMENT 'Lookup user data based on username'
SELECT Username,
       Email,
       Hash,
       Role,
       VerificationCode,
       ResetCode
FROM   users
WHERE  Username = name$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `lookup_usernames` (IN `email` VARCHAR(256))  READS SQL DATA
    SQL SECURITY INVOKER
    COMMENT 'Lookup usernames associated with email address'
SELECT Username
FROM Users
WHERE Email = email$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_imageset_description` (IN `param_id` INT, IN `param_desc` LONGBLOB, IN `param_user` VARCHAR(64))  MODIFIES SQL DATA
    COMMENT 'Update description of an imageset'
UPDATE imagesets
JOIN   users ON param_user = Username
SET    Description = param_desc
WHERE  (Username = Owner
OR	   Role = 'admin')
AND	   ImageSetID = param_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_imageset_name` (IN `param_id` INT, IN `param_name` VARCHAR(256), IN `param_user` VARCHAR(64))  MODIFIES SQL DATA
    COMMENT 'Update the name for an image.'
UPDATE imagesets
JOIN   users ON param_user = Username
SET    Filename = param_name
WHERE  (Username = Owner
OR	   Role = 'admin')
AND	   ImageSetID = param_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_imageset_sharing` (IN `param_id` INT, IN `param_sharing` VARCHAR(64), IN `param_user` VARCHAR(64))  MODIFIES SQL DATA
    COMMENT 'Updates sharing level of imageset'
UPDATE imagesets
JOIN   users ON param_user = Username
SET    Sharing = param_sharing
WHERE  (Username = Owner
OR	   Role = 'admin')
AND	   ImageSetID = param_id$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `create_imageset` (`param_user` VARCHAR(64), `param_name` VARCHAR(256), `param_sharing` VARCHAR(16), `param_type` VARCHAR(256), `param_orig_size` INT, `param_orig_width` INT, `param_orig_height` INT, `param_orig_data` LONGBLOB, `param_page_size` INT, `param_page_width` INT, `param_page_height` INT, `param_page_data` LONGBLOB, `param_thumb_size` INT, `param_thumb_width` INT, `param_thumb_height` INT, `param_thumb_data` LONGBLOB) RETURNS INT(11) MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT 'Create an imageset and associated images'
BEGIN
	SET @orig_id = insert_image(param_type, param_orig_size, param_orig_width, param_orig_height, param_orig_data);
    SET @page_id = insert_image(param_type, param_page_size, param_page_width, param_page_height, param_page_data);
    SET @thumb_id = insert_image(param_type, param_thumb_size, param_thumb_width, param_thumb_height, param_thumb_data);
    RETURN insert_imageset(param_user, param_name, param_sharing, @orig_id, @page_id, @thumb_id);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `insert_image` (`type` VARCHAR(256), `sz` INT, `w` INT, `h` INT, `d` LONGBLOB) RETURNS INT(11) MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT 'Inserts new image and returns ImageID'
BEGIN
	INSERT INTO images (MimeType, Size, Width, Height, Data)
	VALUES (type, sz, w, h, d);
    
    RETURN LAST_INSERT_ID();
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `insert_imageset` (`name` VARCHAR(64), `file` VARCHAR(256), `share` VARCHAR(16), `orig` INT, `page` INT, `thumb` INT) RETURNS INT(11) MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT 'Inserts a new imageset and returns ID'
BEGIN
	INSERT INTO imagesets (Owner, Filename, Sharing, OriginalImageID, 
                           PageImageID, ThumbnailImageID) 
	VALUES (name, file, share, orig, page, thumb);
    
    RETURN LAST_INSERT_ID();
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `test_hello` () RETURNS VARCHAR(256) CHARSET utf8 NO SQL
    DETERMINISTIC
    SQL SECURITY INVOKER
    COMMENT 'My Test Function'
RETURN CONCAT('Hello', ' ', 'world!')$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `ImageID` int(11) NOT NULL,
  `MimeType` varchar(256) NOT NULL,
  `Size` int(11) NOT NULL,
  `Width` int(11) NOT NULL,
  `Height` int(11) NOT NULL,
  `Data` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `imagesets`
--

CREATE TABLE `imagesets` (
  `ImageSetID` int(11) NOT NULL,
  `Owner` varchar(64) NOT NULL,
  `Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Description` longblob NOT NULL,
  `Filename` varchar(256) NOT NULL,
  `Sharing` varchar(16) NOT NULL,
  `OriginalImageID` int(11) NOT NULL,
  `PageImageID` int(11) NOT NULL,
  `ThumbnailImageID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(64) NOT NULL,
  `hash` varchar(128) NOT NULL,
  `email` varchar(256) NOT NULL,
  `role` varchar(16) NOT NULL,
  `VerificationCode` text NOT NULL,
  `ResetCode` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`ImageID`);

--
-- Indexes for table `imagesets`
--
ALTER TABLE `imagesets`
  ADD PRIMARY KEY (`ImageSetID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `ImageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `imagesets`
--
ALTER TABLE `imagesets`
  MODIFY `ImageSetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
