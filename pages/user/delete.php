<?php 
    $id = $_GET['id'];
    $targetUser = readUser($id);
    if ($targetUser == null || $targetUser->level == 'admin'){
        header('Location: ./?page=user/list');
    }

    if(deleteUser($id)){
        echo '<div class="alert alert-success" role="alert">
                Delete success. <a href="./?page=user/list" class="alert-link">Back to List</a>
                </div>';
    }
?>
