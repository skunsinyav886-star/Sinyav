<?php
require_once './init/init.php';
include './includes/header.inc.php';
include './includes/navbar.inc.php';


$available_pages = ['login', 'register'];

if (isset($_GET['page'])) {

    $page = $_GET['page'];
    if (in_array($page, $available_pages)) {

        include './pages/' . $page . '.php';
    } else {
        include './pages/error404.php';
    }
} else {
    include './pages/error404.php';

}

include './includes/footer.inc.php';
?>