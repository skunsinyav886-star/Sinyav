<?php
require_once './init/init.php';
$user = loggedInUser();
$isAdmin = isAdmin();
include './includes/header.inc.php';
include './includes/navbar.inc.php';
$available_pages = ['login', 'register', 'logout', 'dashboard', 'profile', 'user/list', 'user/create'];
$logged_in_pages = ['dashboard', 'profile'];
$non_logged_in_pages = ['login', 'register'];
$admin_pages = ['user/list', 'user/create'];

$page = '';
if (isset($_GET['page'])) {
    $page = $_GET['page']; // logout
}
if (in_array($page, $logged_in_pages) && empty($user)) {
    header('Location: ./?page=login');
}
if (in_array($page, $non_logged_in_pages) && !empty($user)) {
    header('Location: ./?page=dashboard');
}
if (in_array($page, $available_pages)) {
    if(in_array($page, $admin_pages) && !$isAdmin) {
        header('Location: ./?page=dashboard');
    }
    include './pages/' . $page . '.php';
} else {
    // header('Location: ./?page=dashboard');
    header('Location: ./?page=login');
}
include './includes/footer.inc.php';
