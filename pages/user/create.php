<?php
$name = $username = $passwd = '';
$nameErr = $usernameErr = $passwdErr = '';
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
    if (empty($passwd)) {
        $passwdErr = 'please input password!';
    }
    if (usernameExists($username)) {
        $usernameErr = 'Username exists!';
    }
    if (empty($nameErr) && empty($usernameErr) && empty($passwdErr)) {
        // if (registerUser($name, $username, $passwd)) {
        //     $name = $username = $passwd = '';
        //     echo '<div class="alert alert-success" role="alert">
        //         Registered. Go to <a href="./?page=login">Login</a>
        //         </div>';
        // }else{
        //     echo '<div class="alert alert-danger" role="alert">
        //         Username exists or Service busy!
        //         </div>';
        // }
        try {
            if (createUser($name, $username, $passwd, $photo)) {
                $name = $username = $passwd = '';
                echo '<div class="alert alert-success" role="alert">
                Create success.
                </div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">
                 Create failed!
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

<form method="post" action="./?page=user/create" enctype="multipart/form-data" class="col-md-8 col-lg-6 mx-auto">
    <h3>Create User</h3>
    <div class="d-flex justify-content-center">
        <input name="photo" type="file" id="profileUpload" hidden>
        <label role="button" for="profileUpload">
            <img src="./assets/image/emptyuser.png"
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
        <input name="passwd" type="password" class="form-control 
        <?php echo empty($passwdErr) ? '' : 'is-invalid' ?>">
        <div class="invalid-feedback">
            <?php echo $passwdErr ?>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>