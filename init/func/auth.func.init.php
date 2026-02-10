<?php
function usernameExists($username)
{
    global $db;
    $query = $db->prepare('SELECT* FROM tbl_users WHERE username = ?');
    $query->bind_param('s', $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows) {
        return true;
    }
    return false;
}
function registerUser($name, $username, $passwd)
{
    global $db;
    if (usernameExists($username)) {
        return false;
    }
    
    $query = $db->prepare('INSERT INTO tbl_users (name, username, passwd) VALUES (?, ?, ?)');
    $query->bind_param('sss', $name, $username, $passwd);
    $query->execute();
    if ($query->affected_rows) {
        return true;
    }
    return false;
}

function logUserIn($username, $passwd)
{
    global $db;
    $query = $db->prepare('SELECT* FROM tbl_users WHERE username = ? AND passwd = ?');
    $query->bind_param('ss', $username, $passwd);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows) {
        return $result->fetch_object();
    }
    return false;
}
function loggedInUser()
{
    global $db;
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $user_id = $_SESSION['user_id'];
    $query = $db->prepare('SELECT * FROM tbl_users WHERE id =?');
    $query->bind_param('d', $user_id);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows) {
        return $result->fetch_object();
    }
    return null;
}
function isUserHasPassword($passwd)
{
    global $db;
    $user = loggedInUser();
    $query = $db->prepare(
        "SELECT * FROM tbl_users WHERE id = ? AND passwd = ?"
    );
    $query->bind_param('ss', $user->id, $passwd);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows) {
        return true;
    }
    return false;
}

function setUserNewPassowrd($passwd)
{
    global $db;
    $user = loggedInUser();
    $query = $db->prepare(
        "UPDATE tbl_users SET passwd = ? WHERE id = ?"
    );
    $query->bind_param('ss',  $passwd, $user->id);
    $query->execute();
    if ($db->affected_rows) {
        return true;
    }
    return false;
}
