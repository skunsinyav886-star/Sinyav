<?php
$id = $_GET['id'];
$targetUser = readUser($id);
if ($targetUser == null || $targetUser->level == 'admin') {
    header('Location: ./?page=user/list');
}
$name = $targetUser->name;
$username = $targetUser->username;
$nameErr = $usernameErr = '';

if (isset($_POST['name'], $_POST['username'], $_POST['passwd'], $_FILES['photo'])) {
    $photo = $_FILES['photo'];
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $passwd = trim($_POST['passwd']);
    if (empty($name)) {
        $nameErr = 'please input name!';
    }
    if (empty($username)) {
        $usernameErr = 'please input username!';
    }
    if ($targetUser->username !== $username && usernameExists($username)) {
        $usernameErr = 'Username exists!';
    }
    if (empty($nameErr) && empty($usernameErr)) {
        try {
            if (updateUser($id, $name, $username, $passwd, $photo)) {
                $targetUser = readUser($id);
                $name = $targetUser->name;
                $username = $targetUser->username;
                echo '<div class="alert alert-success" role="alert">
                Create success.
                </div>';
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger" role="alert">
                 ' . $e->getMessage() . '
                </div>';
        }
    }
}
?>

<form method="post" action="./?page=user/update&id=<?php echo $id; ?>" enctype="multipart/form-data"
    class="col-md-8 col-lg-6 mx-auto">
    <h3>Update User</h3>
    <div class="d-flex justify-content-center">
        <input name="photo" type="file" id="profileUpload" hidden>
        <label role="button" for="profileUpload">
            <img src="<?php echo $targetUser->photo ?? './assets/image/emptyuser.png'; ?>"
                class="rounded img-thumbnail" style="max-width:200px">
        </label>
    </div>
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input name="name" value="<?php echo $name ?>" type="text" class="form-control 
        <?php echo empty($nameErr) ? '' : 'is-invalid' ?>">
        <div class="invalid-feedback">
            <?php echo $nameErr ?>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Username</label>
        <input name="username" value="<?php echo $username ?>" type="text" class="form-control 
        <?php echo empty($usernameErr) ? '' : 'is-invalid' ?>">
        <div class="invalid-feedback">
            <?php echo $usernameErr ?>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input name="passwd" type="password" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>