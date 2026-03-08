<?php
function createUser($name, $username, $password, $photo)
{
    global $db;

    $image_path = null;
    if (!empty($photo['name'])) {
        $image_path = uploadImage($photo);
    }

    $query = $db->prepare('INSERT INTO tbl_users (name,username,passwd,photo) VALUES (?,?,?,?)');
    $query->bind_param('ssss', $name, $username, $password, $image_path);
    $query->execute();
    if ($db->affected_rows) {
        return true;
    }
    return false;
}

?>