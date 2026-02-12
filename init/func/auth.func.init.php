<?php
/**
 * AUTH & PROFILE CORE FUNCTIONS
 * Handling login, password changes, and Profile Photo AJAX requests.
 */

// Prevent any accidental whitespace from breaking JSON responses
ob_start();

require_once __DIR__ . '/../db.init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $db;

// --- PHOTO & AJAX LOGIC ---
$user = loggedInUser();
$userId = $user ? $user->id : null;

if ($userId) {
    // Path to your assets folder relative to this file
    $targetDir = "../../assets/";

    // 1. UPLOAD ACTION
    if (isset($_FILES['profile_image'])) {
        if (ob_get_length())
            ob_clean();
        header('Content-Type: application/json');

        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExt = strtolower(pathinfo($_FILES['profile_image']["name"], PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedExts)) {
            echo json_encode(["status" => "error", "message" => "File extension not allowed."]);
            die();
        }

        $fileName = "user_" . $userId . "_" . time() . "." . $fileExt;

        // Cleanup: Delete the OLD physical file before uploading the new one
        $q = $db->prepare("SELECT photo FROM tbl_users WHERE id = ?");
        $q->bind_param("i", $userId);
        $q->execute();
        $oldResult = $q->get_result()->fetch_object();

        if ($oldResult && !empty($oldResult->photo)) {
            $oldFilePath = $targetDir . $oldResult->photo;
            if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                @unlink($oldFilePath);
            }
        }

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetDir . $fileName)) {
            $stmt = $db->prepare("UPDATE tbl_users SET photo = ? WHERE id = ?");
            $stmt->bind_param("si", $fileName, $userId);
            $stmt->execute();

            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Critical: Failed to move file."]);
        }
        die();
    }

    // 2. DELETE ACTION (Now deletes physical file)
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        if (ob_get_length())
            ob_clean();
        header('Content-Type: application/json');

        // First, find the filename in the database
        $q = $db->prepare("SELECT photo FROM tbl_users WHERE id = ?");
        $q->bind_param("i", $userId);
        $q->execute();
        $result = $q->get_result()->fetch_object();

        if ($result && !empty($result->photo)) {
            $filePath = $targetDir . $result->photo;

            // Delete the physical file from the assets folder
            if (file_exists($filePath) && is_file($filePath)) {
                @unlink($filePath);
            }
        }

        // Second, clear the reference in the database
        $stmt = $db->prepare("UPDATE tbl_users SET photo = NULL WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        echo json_encode(["status" => "success"]);
        die();
    }
}

// --- AUTHENTICATION FUNCTIONS ---

function usernameExists($username)
{
    global $db;
    $query = $db->prepare('SELECT * FROM tbl_users WHERE username = ?');
    $query->bind_param('s', $username);
    $query->execute();
    $result = $query->get_result();
    return $result->num_rows > 0;
}

function registerUser($name, $username, $passwd)
{
    global $db;
    if (usernameExists($username))
        return false;
    $query = $db->prepare('INSERT INTO tbl_users (name,username,passwd) VALUES (?,?,?)');
    $query->bind_param('sss', $name, $username, $passwd);
    $query->execute();
    return $db->affected_rows > 0;
}

function logUserIn($username, $passwd)
{
    global $db;
    $query = $db->prepare('SELECT * FROM tbl_users WHERE username = ? AND passwd = ?');
    $query->bind_param('ss', $username, $passwd);
    $query->execute();
    $result = $query->get_result();
    return ($result->num_rows) ? $result->fetch_object() : false;
}


function loggedInUser()
{
    global $db;
    if (!isset($_SESSION['user_id']))
        return null;
    $user_id = $_SESSION['user_id'];
    $query = $db->prepare('SELECT * FROM tbl_users WHERE id = ?');
    $query->bind_param('i', $user_id);
    $query->execute();
    $result = $query->get_result();
    return ($result->num_rows) ? $result->fetch_object() : null;
}

function isUserHasPassword($passwd)
{
    global $db;
    $user = loggedInUser();
    if (!$user)
        return false;
    $query = $db->prepare("SELECT * FROM tbl_users WHERE id = ? AND passwd = ?");
    $query->bind_param('is', $user->id, $passwd);
    $query->execute();
    $result = $query->get_result();
    return ($result && $result->num_rows > 0);
}

function setUserNewPassowrd($passwd)
{
    $user = loggedInUser();
    if (!$user)
        return false;
    global $db;
    $query = $db->prepare("UPDATE tbl_users SET passwd = ? WHERE id = ?");
    $query->bind_param('si', $passwd, $user->id);
    $query->execute();
    return $query->affected_rows > 0;
}
