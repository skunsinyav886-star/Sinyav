<?php
$oldPasswd = $newPasswd = $confirmNewPasswd = '';
$oldPasswdErr = $newPasswdErr  = '';

if (isset($_POST['changePasswd'], $_POST['oldPasswd'], $_POST['newPasswd'], $_POST['confirmNewPasswd'])) {
    $oldPasswd = trim($_POST['oldPasswd']);
    $newPasswd = trim($_POST['newPasswd']);
    $confirmNewPasswd = trim($_POST['confirmNewPasswd']);
    if (empty($oldPasswd)) {
        $oldPasswdErr = 'please input your old password';
    }
    if (empty($newPasswd)) {
        $newPasswdErr = 'please input your new password';
    }
    if ($newPasswd !== $confirmNewPasswd) {
        $newPasswdErr = 'password does not match';
    }
     if (!isUserHasPassword($oldPasswd)) {
        $oldPasswdErr = 'password is incorrect';
    }
    if (empty($oldPasswdErr) && empty($newPasswdErr)) {
        if (setUserNewPassowrd($newPasswd)) {
            header('Location: ./?page=logout');
        } else {
            echo '<div class="alert alert-danger" role="alert">
                try aggain.
                </div>';
        }
    }
}



if (isset($_POST['uploadPhoto']) && isset($_FILES['photo'])) {
    $photo = $_FILES['photo'];
    if (empty($photo['name'])) {
        echo '<div class="alert alert-danger" role="alert">
            Please select a photo to upload.
            </div>';
    } else {
        try {
            if (changeProfileImage($photo)) {
                echo '<div class="alert alert-success" role="alert">
                    profile image changed successfully.
                    </div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">
                    failed to change profile image.
                    </div>';
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger" role="alert">
                ' . $e->getMessage() . '
                </div>';
        }
    }
}

if (isset($_POST['deletePhoto'])) {
    deleteProfileImage();
}
?>

<div class="row">
    <div class="col-6">
        <form method="post" action="./?page=profile" enctype="multipart/form-data">
            <div class="d-flex justify-content-center">
                <input name="photo" type="file" id="profileUpload" hidden>
                <label role="button" for="profileUpload">
                    <img src="<?php echo loggedInUser()->photo ?? './assets/images/emptyuser.png' ?>"
                        class="rounded img-thumbnail" style="max-width:200px">
                </label>
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" name="deletePhoto" class="btn btn-danger">Delete</button>
                <button type="submit" name="uploadPhoto" class="btn btn-success">Upload</button>
            </div>
        </form>
    </div>

    <div class="col-6">
        <form method="post" action="./?page=profile" class="col-md-8 col-lg-6 mx-auto">
            <h3>Change Password</h3>
            <div class="mb-3">
                <label class="form-label">Old Password</label>
                <input value="<?php echo $oldPasswd ?>" name="oldPasswd" type="password" class="form-control 
                <?php echo empty($oldPasswdErr) ? '' : 'is-invalid' ?>">
                <div class="invalid-feedback">
                    <?php echo $oldPasswdErr ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input name="newPasswd" type="password" class="form-control 
                <?php echo empty($newPasswdErr) ? '' : 'is-invalid' ?>">
                <div class="invalid-feedback">
                    <?php echo $newPasswdErr ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input name="confirmNewPasswd" type="password" class="form-control">
            </div>
            <button type="submit" name="changePasswd" class="btn btn-primary">Change Password</button>
        </form>
    </div>
</div>