<?php
require_once './init/init.php';

$user = loggedInUser();

include './includes/header.inc.php';
include './includes/navbar.inc.php';

$available_pages = ['login', 'register', 'logout', 'dashboard'];
$logged_in_pages = ['dashboard'];
$non_logged_in_pages = ['login', 'register'];

$page = $_GET['page'] ?? 'login';

if (in_array($page, $logged_in_pages) && empty($user)) {
    header('Location: ./?page=login');

}

if (in_array($page, $non_logged_in_pages) && !empty($user)) {
    header('Location: ./?page=dashboard');

}

if (in_array($page, $available_pages)) {
    include './pages/' . $page . '.php';
} else {
    header('Location: ./?page=login');

}

include './includes/footer.inc.php';
