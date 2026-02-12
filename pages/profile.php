<?php
$user = loggedInUser();
$oldPasswdErr = $newPasswdErr = '';

if (isset($_POST['changePasswd'])) {
    $oldPasswd = trim($_POST['oldPasswd']);
    $newPasswd = trim($_POST['newPasswd']);
    $confirmNewPasswd = trim($_POST['confirmNewPasswd']);
    if (empty($oldPasswd))
        $oldPasswdErr = 'please input your old password';
    if (empty($newPasswd))
        $newPasswdErr = 'please input your new password';
    if ($newPasswd !== $confirmNewPasswd)
        $newPasswdErr = 'password does not match';
    if (!isUserHasPassword($oldPasswd))
        $oldPasswdErr = 'password is incorrect';

    if (empty($oldPasswdErr) && empty($newPasswdErr)) {
        if (setUserNewPassowrd($newPasswd)) {
            header('Location: ./?page=logout');
            exit;
        }
    }
}
?>

<div id="status-alert-container" class="container-fluid mt-2" style="display:none;">
    <div id="status-alert-box" class="alert py-3" role="alert"
        style="border-radius: 8px; border: none; font-weight: 500;">
        <span id="status-alert-text"></span>
    </div>
</div>

<style>
    .profile-card {
        text-align: center;
        padding: 40px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .profile-avatar {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: 0.3s;
    }

    .profile-avatar:hover {
        opacity: 0.8;
    }

    .btn-pill {
        padding: 10px 25px;
        border-radius: 50px;
        font-weight: 500;
        transition: 0.3s;
        border: none;
    }

    .btn-upload {
        background-color: #0d6efd;
        color: white;
    }

    .btn-upload:hover {
        background-color: #0b5ed7;
        color: white;
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background-color: #bb2d3b;
        color: white;
    }

    .status-success {
        background-color: #d1e7dd !important;
        color: #0f5132 !important;
    }

    .status-danger {
        background-color: #f8d7da !important;
        color: #842029 !important;
    }

    #preview-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }

    .modal-custom {
        background: white;
        padding: 30px;
        border-radius: 20px;
        text-align: center;
        width: 300px;
    }
</style>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="profile-card">
                <img id="display-avatar"
                    src="<?php echo ($user && !empty($user->photo)) ? 'assets/' . $user->photo : 'assets/images/emptyuser.png'; ?>"
                    class="profile-avatar" onclick="document.getElementById('hidden-input').click()">

                <div class="mt-4 d-flex flex-column align-items-center">
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn-pill btn-delete" id="delete-init">Delete</button>
                        <button class="btn-pill btn-upload" id="upload-now-db">Upload</button>
                    </div>

                    <div id="confirm-delete-section" class="mt-3" style="display: none;">
                        <span class="text-danger small fw-bold me-2">Are you sure?</span>
                        <button id="confirm-delete-btn" class="btn btn-sm btn-danger rounded-pill px-3">Yes</button>
                        <button id="cancel-delete-btn" class="btn btn-sm btn-light rounded-pill px-3 ms-1">No</button>
                    </div>
                </div>


                <input type="file" id="hidden-input" style="display:none" accept="image/*">
            </div>
        </div>

        <div class="col-md-7">
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 20px;">
                <form method="post" action="./?page=profile">
                    <h4 class="mb-4 fw-bold">Change Password</h4>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Old Password</label>
                        <input name="oldPasswd" type="password"
                            class="form-control rounded-3 <?php echo $oldPasswdErr ? 'is-invalid' : '' ?>">
                        <div class="invalid-feedback"><?php echo $oldPasswdErr ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">New Password</label>
                        <input name="newPasswd" type="password"
                            class="form-control rounded-3 <?php echo $newPasswdErr ? 'is-invalid' : '' ?>">
                        <div class="invalid-feedback"><?php echo $newPasswdErr ?></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Confirm New Password</label>
                        <input name="confirmNewPasswd" type="password" class="form-control rounded-3">
                    </div>
                    <button type="submit" name="changePasswd" class="btn btn-dark w-100 py-2 rounded-3">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="preview-overlay">
    <div class="modal-custom">
        <h5 class="fw-bold mb-3">Update Photo?</h5>
        <img id="modal-img" src=""
            style="width:140px; height:140px; border-radius:50%; object-fit:cover; margin-bottom:20px; border: 3px solid #eee;">
        <div class="d-flex justify-content-center gap-2">
            <button id="confirm-preview-btn" class="btn btn-primary px-4 rounded-pill">Save</button>
            <button id="cancel-preview" class="btn btn-light px-4 rounded-pill">Cancel</button>
        </div>
    </div>
</div>

<script>
    const hiddenInput = document.getElementById('hidden-input');
    const previewOverlay = document.getElementById('preview-overlay');
    const modalImg = document.getElementById('modal-img');
    const displayAvatar = document.getElementById('display-avatar');

    // The "Upload Now" button in the form
    const uploadNowBtn = document.getElementById('upload-now-db');

    const deleteInitBtn = document.getElementById('delete-init');
    const confirmDeleteSection = document.getElementById('confirm-delete-section');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');

    const alertContainer = document.getElementById('status-alert-container');
    const alertBox = document.getElementById('status-alert-box');
    const alertText = document.getElementById('status-alert-text');

    // This variable stores the file temporarily for the preview
    let pendingFile = null;

    function showAlert(msg, className) {
        alertText.innerText = msg;
        alertBox.className = `alert py-3 ${className}`;
        alertContainer.style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        setTimeout(() => { alertContainer.style.display = 'none'; }, 3000);
    }

    // --- STEP 1: PICKING THE FILE ---
    hiddenInput.onchange = function () {
        if (this.files[0]) {
            const file = this.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];

            if (!allowedTypes.includes(file.type)) {
                showAlert("File extension not allowed.", "status-danger");
                this.value = "";
                return;
            }


            const reader = new FileReader();
            reader.onload = (e) => {
                modalImg.src = e.target.result;
                previewOverlay.style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }
    };

    // --- STEP 1.5: MODAL SAVE (PREVIEW ONLY) ---
    document.getElementById('confirm-preview-btn').onclick = () => {
        // We move the file to "pending" and update the page view only
        pendingFile = hiddenInput.files[0];
        displayAvatar.src = modalImg.src;
        previewOverlay.style.display = 'none';
    };

    // --- STEP 2: ACTUAL DATABASE UPLOAD ---
    uploadNowBtn.onclick = () => {
        // If user hasn't chosen a new file or has no preview ready
        if (!pendingFile) {
            showAlert("please select the photo to upload", "status-danger");
            return;
        }

        const fd = new FormData();
        fd.append('profile_image', pendingFile);

        fetch('init/func/auth.func.init.php', { method: 'POST', body: fd })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert("Upload successfully", "status-success");
                    pendingFile = null; // Reset so they can't spam upload same file
                    hiddenInput.value = "";
                } else {
                    showAlert(data.message, "status-danger");
                }
            })
            .catch(err => showAlert("Error connecting to server.", "status-danger"));
    };

    document.getElementById('cancel-preview').onclick = () => {
        previewOverlay.style.display = 'none';
        hiddenInput.value = "";
    };

    // --- DELETE LOGIC ---
    deleteInitBtn.onclick = () => {
        confirmDeleteSection.style.display = 'block';
        deleteInitBtn.style.display = 'none';
    };

    cancelDeleteBtn.onclick = () => {
        confirmDeleteSection.style.display = 'none';
        deleteInitBtn.style.display = 'inline-block';
    };

    confirmDeleteBtn.onclick = () => {
        const fd = new FormData();
        fd.append('action', 'delete');
        fetch('init/func/auth.func.init.php', { method: 'POST', body: fd })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    displayAvatar.src = "assets/images/emptyuser.png";
                    confirmDeleteSection.style.display = 'none';
                    deleteInitBtn.style.display = 'inline-block';
                    pendingFile = null; // Clear any unsaved preview
                    showAlert("Photo removed.", "status-danger");
                }
            });
    };
</script>
